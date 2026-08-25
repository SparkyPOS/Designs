<?php

namespace App\Http\Controllers\Vendor;

use DateTime;
use Carbon\Carbon;
use App\Models\Form;
use App\Models\Order;
use App\Models\Product;
use App\Constants\Status;
use App\Lib\FormProcessor;
use App\Models\DeviceToken;
use App\Models\Transaction;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use App\Lib\GoogleAuthenticator;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;

class VendorController extends Controller {
    public function home() {
        $pageTitle = 'Dashboard';
        $vendor = authVendor();
        $widget['totalWithdrawals'] = $vendor->withdrawals()->approved()->sum('amount');
        $widget['totalOrderAmount'] = $vendor->orders()->paid()->sum('subtotal');

        $topSellProducts = Product::where('vendor_id', $vendor->id)
            ->whereHas('orderDetails')
            ->whereHas('orderDetails.order', function ($query) {
                $query->paid();
            })
            ->withSum('orderDetails', 'quantity')
            ->withSum('orderDetails', 'price')
            ->orderByDesc('order_details_sum_quantity')
            ->take(6)
            ->get();

        $widget['totalOrders']      = Order::where('vendor_id', $vendor->id)->count();
        $widget['canceledOrders']   = Order::paid()->canceled()->where('vendor_id', $vendor->id)->count();
        $widget['pendingOrders']    = Order::pending()->where('vendor_id', $vendor->id)->count();
        $widget['processingOrders'] = Order::paid()->processing()->where('vendor_id', $vendor->id)->count();
        $widget['deliveredOrders']  = Order::paid()->delivered()->where('vendor_id', $vendor->id)->count();
        $widget['dispatchedOrders'] = Order::paid()->dispatched()->where('vendor_id', $vendor->id)->count();
        return view('Template::vendor.dashboard', compact('pageTitle', 'vendor', 'topSellProducts', 'widget'));
    }

    public function salesChart(Request $request) {
        $diffInDays = Carbon::parse($request->start_date)->diffInDays(Carbon::parse($request->end_date));

        $groupBy = $diffInDays > 30 ? 'months' : 'days';
        $format = $diffInDays > 30 ? '%M-%Y'  : '%d-%M-%Y';

        if ($groupBy == 'days') {
            $dates = $this->getAllDates($request->start_date, $request->end_date);
        } else {
            $dates = $this->getAllMonths($request->start_date, $request->end_date);
        }
        $sales = Order::paid()
            ->where('vendor_id', authVendorId())
            ->whereDate('created_at', '>=', $request->start_date)
            ->whereDate('created_at', '<=', $request->end_date)
            ->selectRaw('SUM(subtotal) AS totalAmount')
            ->selectRaw("DATE_FORMAT(created_at, '{$format}') as created_on")
            ->latest()
            ->groupBy('created_on')
            ->get();

        $data = [];

        foreach ($dates as $date) {
            $data[] = [
                'created_on' => $date,
                'sales' => getAmount($sales->where('created_on', $date)->first()?->totalAmount ?? 0)
            ];
        }

        $data = collect($data);

        // Monthly Deposit Report Graph
        $report['created_on']   = $data->pluck('created_on');
        $report['data']     = [
            [
                'name' => trans('Sold'),
                'data' => $data->pluck('sales')
            ]
        ];

        return response()->json($report);
    }

    private function getAllDates($startDate, $endDate) {
        $dates = [];
        $currentDate = new DateTime($startDate);
        $endDate = new DateTime($endDate);

        while ($currentDate <= $endDate) {
            $dates[] = $currentDate->format('d-F-Y');
            $currentDate->modify('+1 day');
        }

        return $dates;
    }

    private function  getAllMonths($startDate, $endDate) {
        if ($endDate > now()) {
            $endDate = now()->format('Y-m-d');
        }

        $startDate = new DateTime($startDate);
        $endDate = new DateTime($endDate);

        $months = [];

        while ($startDate <= $endDate) {
            $months[] = $startDate->format('F-Y');
            $startDate->modify('+1 month');
        }

        return $months;
    }

    public function show2faForm() {
        $ga = new GoogleAuthenticator();
        $vendor = authVendor();
        $secret = $ga->createSecret();
        $qrCodeUrl = $ga->getQRCodeGoogleUrl($vendor->username . '@' . gs('site_name'), $secret);
        $pageTitle = '2FA Security';
        return view('Template::vendor.twofactor', compact('pageTitle', 'secret', 'qrCodeUrl'));
    }

    public function create2fa(Request $request) {
        $vendor = authVendor();
        $request->validate([
            'key' => 'required',
            'code' => 'required',
        ]);
        $response = verifyG2fa($vendor, $request->code, $request->key);
        if ($response) {
            $vendor->tsc = $request->key;
            $vendor->ts = Status::ENABLE;
            $vendor->save();
            $notify[] = ['success', 'Two factor authenticator activated successfully'];
            return back()->withNotify($notify);
        } else {
            $notify[] = ['error', 'Wrong verification code'];
            return back()->withNotify($notify);
        }
    }

    public function disable2fa(Request $request) {
        $request->validate([
            'code' => 'required',
        ]);

        $vendor = authVendor();
        $response = verifyG2fa($vendor, $request->code);
        if ($response) {
            $vendor->tsc = null;
            $vendor->ts = Status::DISABLE;
            $vendor->save();
            $notify[] = ['success', 'Two factor authenticator deactivated successfully'];
        } else {
            $notify[] = ['error', 'Wrong verification code'];
        }
        return back()->withNotify($notify);
    }

    public function transactions() {
        $pageTitle = 'Transactions';
        $remarks = Transaction::distinct('remark')->orderBy('remark')->get('remark');

        $transactions = Transaction::where('vendor_id', authVendorId())->searchable(['trx'])->filter(['trx_type', 'remark'])->orderBy('id', 'desc')->paginate(getPaginate());

        return view('Template::vendor.transactions', compact('pageTitle', 'transactions', 'remarks'));
    }

    public function kycForm() {
        if (authVendor()->kv == Status::KYC_PENDING) {
            $notify[] = ['error', 'Your KYC is under review'];
            return to_route('vendor.home')->withNotify($notify);
        }
        if (authVendor()->kv == Status::KYC_VERIFIED) {
            $notify[] = ['error', 'You are already KYC verified'];
            return to_route('vendor.home')->withNotify($notify);
        }
        $pageTitle = 'KYC Form';
        $form = Form::where('act', 'kyc')->first();
        return view('Template::vendor.kyc.form', compact('pageTitle', 'form'));
    }

    public function kycData() {
        $vendor = authVendor();
        $pageTitle = 'KYC Data';
        abort_if($vendor->kv == Status::VERIFIED, 403);
        return view('Template::vendor.kyc.info', compact('pageTitle', 'vendor'));
    }

    public function kycSubmit(Request $request) {
        $form = Form::where('act', 'kyc')->firstOrFail();
        $formData = $form->form_data;
        $formProcessor = new FormProcessor();
        $validationRule = $formProcessor->valueValidation($formData);
        $request->validate($validationRule);
        $vendor = authVendor();
        foreach (isset($vendor->kyc_data) ? $vendor->kyc_data : [] as $kycData) {
            if ($kycData->type == 'file') {
                fileManager()->removeFile(getFilePath('verify') . '/' . $kycData->value);
            }
        }
        $vendorData = $formProcessor->processFormData($request, $formData);
        $vendor->kyc_data = $vendorData;
        $vendor->kyc_rejection_reason = null;
        $vendor->kv = Status::KYC_PENDING;
        $vendor->save();

        $notify[] = ['success', 'KYC data submitted successfully'];
        return to_route('vendor.home')->withNotify($notify);
    }

    public function userData() {
        $vendor = authVendor();

        if ($vendor->profile_complete == Status::YES) {
            return to_route('vendor.home');
        }

        $pageTitle  = 'User Data';
        $info       = json_decode(json_encode(getIpInfo()), true);
        $mobileCode = isset($info['code']) ? implode(',', $info['code']) : '';
        $countries  = json_decode(file_get_contents(resource_path('views/partials/country.json')));

        return view('Template::vendor.user_data', compact('pageTitle', 'vendor', 'countries', 'mobileCode'));
    }

    public function userDataSubmit(Request $request) {

        $vendor = authVendor();

        if ($vendor->profile_complete == Status::YES) {
            return to_route('vendor.home');
        }

        $countryData  = (array)json_decode(file_get_contents(resource_path('views/partials/country.json')));
        $countryCodes = implode(',', array_keys($countryData));
        $mobileCodes  = implode(',', array_column($countryData, 'dial_code'));
        $countries    = implode(',', array_column($countryData, 'country'));

        $request->validate([
            'country_code' => 'required|in:' . $countryCodes,
            'country'      => 'required|in:' . $countries,
            'mobile_code'  => 'required|in:' . $mobileCodes,
            'username'     => 'required|unique:vendors|min:6',
            'company_name'     => 'required|string|max:255|unique:vendors',
            'mobile'       => ['required', 'regex:/^([0-9]*)$/', Rule::unique('vendors')->where('dial_code', $request->mobile_code)],
        ]);


        if (preg_match("/[^a-z0-9_]/", trim($request->username))) {
            $notify[] = ['info', 'Username can contain only small letters, numbers and underscore.'];
            $notify[] = ['error', 'No special character, space or capital letters in username.'];
            return back()->withNotify($notify)->withInput($request->all());
        }

        $vendor->company_name = $request->company_name;
        $vendor->country_code = $request->country_code;
        $vendor->mobile       = $request->mobile;
        $vendor->username     = $request->username;


        $vendor->address = $request->address;
        $vendor->city = $request->city;
        $vendor->state = $request->state;
        $vendor->zip = $request->zip;
        $vendor->country_name = isset($request->country) ? $request->country : '';
        $vendor->dial_code = $request->mobile_code;

        $vendor->profile_complete = Status::YES;
        $vendor->save();

        return to_route('vendor.home');
    }

    public function delivery() {
        $pageTitle = "Shipping Fee";
        $deliveryDays = authVendor()->delivery_days;
        $shippingFee = authVendor()->shipping_fee;
        return view('Template::vendor.delivery', compact('deliveryDays', 'shippingFee', 'pageTitle'));
    }

    public function updateDelivery(Request $request) {
        $request->validate([
            'delivery_days' => 'required|numeric|gt:0',
            'shipping_fee' => 'required|numeric|gte:0'
        ]);

        $vendor = authVendor();
        $vendor->delivery_days = $request->delivery_days;
        $vendor->shipping_fee = $request->shipping_fee;
        $vendor->save();

        $notify[] = ['success', "Shipping fee updated successfully"];
        return back()->withNotify($notify);
    }

    public function designInstruction() {
        $pageTitle = "Design Fee";
        $designInstructions = authVendor()->design_instruction;
        return view('Template::vendor.design_instruction', compact('designInstructions', 'pageTitle'));
    }

    public function updateDesignInstruction(Request $request) {
        $request->validate([
            'design_instruction' => 'required|array|min:1',
            'design_instruction.*' => 'required|string',
        ],[
            'design_instruction.*.required' => 'The design instruction fields are required'
        ]);

        $vendor = authVendor();
        $vendor->design_instruction = $request->design_instruction;
        $vendor->save();

        $notify[] = ['success', "Design instruction updated successfully"];
        return back()->withNotify($notify);
    }


    public function addDeviceToken(Request $request) {

        $validator = Validator::make($request->all(), [
            'token' => 'required',
        ]);

        if ($validator->fails()) {
            return ['success' => false, 'errors' => $validator->errors()->all()];
        }

        $deviceToken = DeviceToken::where('token', $request->token)->first();

        if ($deviceToken) {
            return ['success' => true, 'message' => 'Already exists'];
        }

        $deviceToken          = new DeviceToken();
        $deviceToken->vendor_id = authVendor()->id;
        $deviceToken->token   = $request->token;
        $deviceToken->is_app  = Status::NO;
        $deviceToken->save();

        return ['success' => true, 'message' => 'Token saved successfully'];
    }

    public function downloadAttachment($fileHash) {
        $filePath = decrypt($fileHash);
        $extension = pathinfo($filePath, PATHINFO_EXTENSION);
        $title = slug(gs('site_name')) . '- attachments.' . $extension;
        try {
            $mimetype = mime_content_type($filePath);
        } catch (\Exception $e) {
            $notify[] = ['error', 'File does not exists'];
            return back()->withNotify($notify);
        }
        header('Content-Disposition: attachment; filename="' . $title);
        header("Content-Type: " . $mimetype);
        return readfile($filePath);
    }
}
