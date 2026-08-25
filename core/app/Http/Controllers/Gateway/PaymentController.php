<?php

namespace App\Http\Controllers\Gateway;

use App\Models\User;
use App\Models\Order;
use App\Models\Vendor;
use App\Models\Deposit;
use App\Constants\Status;
use App\Lib\FormProcessor;
use Illuminate\Http\Request;
use App\Models\GatewayCurrency;
use App\Models\AdminNotification;
use App\Http\Controllers\Controller;
use App\Models\VendorNotification;
use App\Models\Transaction;

class PaymentController extends Controller
{
    public function deposit($orderNumber)
    {
        $gatewayCurrency = GatewayCurrency::whereHas('method', function ($gate) {
            $gate->where('status', Status::ENABLE);
        })->with('method')->orderby('name')->get();
        $pageTitle = 'Payment Methods';
        $order = Order::where('order_number', $orderNumber)->where('user_id', auth()->id())->firstOrFail();
        return view('Template::user.payment.deposit', compact('gatewayCurrency', 'pageTitle', 'order'));
    }

    public function depositInsert(Request $request)
    {
        $request->validate([
            'order_id' => 'required|numeric|exists:orders,id',
            'amount' => 'required|numeric|gt:0',
            'gateway' => 'required',
            'currency' => 'required',
        ]);


        $user = auth()->user();
        $order = Order::where('user_id', $user->id)->nonPaid()->findOrFail($request->order_id);

        $gate = GatewayCurrency::whereHas('method', function ($gate) {
            $gate->where('status', Status::ENABLE);
        })->where('method_code', $request->gateway)->where('currency', $request->currency)->first();
        if (!$gate) {
            $notify[] = ['error', 'Invalid gateway'];
            return back()->withNotify($notify);
        }

        if ($gate->min_amount > $request->amount || $gate->max_amount < $request->amount) {
            $notify[] = ['error', 'Please follow deposit limit'];
            return back()->withNotify($notify);
        }

        $charge = $gate->fixed_charge + ($request->amount * $gate->percent_charge / 100);
        $payable = $request->amount + $charge;
        $finalAmount = $payable * $gate->rate;

        $data = new Deposit();
        $data->user_id = $user->id;
        $data->order_id = $order->id;
        $data->method_code = $gate->method_code;
        $data->method_currency = strtoupper($gate->currency);
        $data->amount = $request->amount;
        $data->charge = $charge;
        $data->rate = $gate->rate;
        $data->final_amount = $finalAmount;
        $data->btc_amount = 0;
        $data->btc_wallet = "";
        $data->trx = getTrx();
        $data->success_url = route('user.order.confirm', $order->order_number);
        $data->failed_url = route('user.deposit.history');
        $data->save();
        session()->put('Track', $data->trx);
        return to_route('user.deposit.confirm');
    }


    public function appDepositConfirm($hash)
    {
        try {
            $id = decrypt($hash);
        } catch (\Exception $ex) {
            abort(404);
        }
        $data = Deposit::where('id', $id)->where('status', Status::PAYMENT_INITIATE)->orderBy('id', 'DESC')->firstOrFail();
        $user = User::findOrFail($data->user_id);
        auth()->login($user);
        session()->put('Track', $data->trx);
        return to_route('user.deposit.confirm');
    }


    public function depositConfirm()
    {
        $track = session()->get('Track');
        $deposit = Deposit::where('trx', $track)->where('status', Status::PAYMENT_INITIATE)->orderBy('id', 'DESC')->with('gateway')->firstOrFail();

        if ($deposit->method_code >= 1000) {
            return to_route('user.deposit.manual.confirm');
        }


        $dirName = $deposit->gateway->alias;
        $new = __NAMESPACE__ . '\\' . $dirName . '\\ProcessController';

        $data = $new::process($deposit);
        $data = json_decode($data);


        if (isset($data->error)) {
            $notify[] = ['error', $data->message];
            return back()->withNotify($notify);
        }
        if (isset($data->redirect)) {
            return redirect($data->redirect_url);
        }

        // for Stripe V3
        if (isset($data->session)) {
            $deposit->btc_wallet = $data->session->id;
            $deposit->save();
        }

        $pageTitle = 'Payment Confirm';
        return view("Template::$data->view", compact('data', 'pageTitle', 'deposit'));
    }


    public static function userDataUpdate($deposit, $isManual = null)
    {
        if ($deposit->status == Status::PAYMENT_INITIATE || $deposit->status == Status::PAYMENT_PENDING) {
            $deposit->status = Status::PAYMENT_SUCCESS;
            $deposit->save();

            // update order
            $order = $deposit->order;
            $order->payment_status = Status::PAYMENT_SUCCESS;
            $order->save();


            $vendor = Vendor::find($order->vendor_id);
            $vendor->balance += $order->total_amount;
            $vendor->save();

            $transaction = new Transaction();
            $transaction->vendor_id = $order->vendor_id;
            $transaction->amount = $order->total_amount;
            $transaction->post_balance = $vendor->balance;
            $transaction->charge = 0;
            $transaction->trx_type = '+';
            $transaction->details = 'Payment received for order #' . $order->order_number;
            $transaction->trx = $deposit->trx;
            $transaction->remark = 'order_payment';
            $transaction->save();

            $vendor->balance -= $order->commission_amount;
            $vendor->save();

            $transaction = new Transaction();
            $transaction->vendor_id = $order->vendor_id;
            $transaction->amount = $order->commission_amount;
            $transaction->post_balance = $vendor->balance;
            $transaction->charge = 0;
            $transaction->trx_type = '-';
            $transaction->details = 'Commission deducted for order #' . $order->order_number;
            $transaction->trx = $deposit->trx;
            $transaction->remark = 'order_commission';
            $transaction->save();

            $methodName = $deposit->methodName();

            notify($vendor, $isManual ? 'PAYMENT_APPROVE' : 'PAYMENT_COMPLETE', [
                'method_name' => $methodName,
                'method_currency' => $deposit->method_currency,
                'method_amount' => showAmount($deposit->final_amount, currencyFormat: false),
                'amount' => showAmount($deposit->amount, currencyFormat: false),
                'charge' => showAmount($deposit->charge, currencyFormat: false),
                'rate' => showAmount($deposit->rate, currencyFormat: false),
                'trx' => $deposit->trx
            ]);

            if (!$isManual) {
                $adminNotification            = new AdminNotification();
                $adminNotification->user_id   = $deposit->user_id;
                $adminNotification->title     = 'Payment succeeded via ' . $deposit->methodName();
                $adminNotification->click_url = urlPath('admin.deposit.successful');
                $adminNotification->save();
            }

            $vendorNotification            = new VendorNotification();
            $vendorNotification->vendor_id   = $order->vendor_id;
            $vendorNotification->title     = 'Payment for your order #' . $order->order_number;
            $vendorNotification->click_url = urlPath('vendor.order.details', $order->order_number);
            $vendorNotification->save();
        }
    }

    public function manualDepositConfirm()
    {
        $track = session()->get('Track');
        $data = Deposit::with('gateway')->where('status', Status::PAYMENT_INITIATE)->where('trx', $track)->first();
        abort_if(!$data, 404);
        if ($data->method_code > 999) {
            $pageTitle = 'Confirm Deposit';
            $method = $data->gatewayCurrency();
            $gateway = $method->method;
            return view('Template::user.payment.manual', compact('data', 'pageTitle', 'method', 'gateway'));
        }
        abort(404);
    }

    public function manualDepositUpdate(Request $request)
    {
        $track = session()->get('Track');
        $data = Deposit::with('gateway')->where('status', Status::PAYMENT_INITIATE)->where('trx', $track)->first();
        abort_if(!$data, 404);
        $gatewayCurrency = $data->gatewayCurrency();
        $gateway = $gatewayCurrency->method;
        $formData = $gateway->form->form_data;

        $formProcessor = new FormProcessor();
        $validationRule = $formProcessor->valueValidation($formData);
        $request->validate($validationRule);
        $userData = $formProcessor->processFormData($request, $formData);

        $data->detail = $userData;
        $data->status = Status::PAYMENT_PENDING;
        $data->save();

        $adminNotification = new AdminNotification();
        $adminNotification->user_id = $data->user->id;
        $adminNotification->title = 'Deposit request from ' . $data->user->username;
        $adminNotification->click_url = urlPath('admin.deposit.details', $data->id);
        $adminNotification->save();

        notify($data->user, 'PAYMENT_REQUEST', [
            'method_name' => $data->gatewayCurrency()->name,
            'method_currency' => $data->method_currency,
            'method_amount' => showAmount($data->final_amount, currencyFormat: false),
            'amount' => showAmount($data->amount, currencyFormat: false),
            'charge' => showAmount($data->charge, currencyFormat: false),
            'rate' => showAmount($data->rate, currencyFormat: false),
            'trx' => $data->trx
        ]);

        $notify[] = ['success', 'You have deposit request has been taken'];
        return to_route('user.deposit.history')->withNotify($notify);
    }
}
