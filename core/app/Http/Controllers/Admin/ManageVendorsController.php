<?php

namespace App\Http\Controllers\Admin;

use App\Constants\Status;
use App\Http\Controllers\Controller;
use App\Lib\VendorNotificationSender;
use App\Models\NotificationLog;
use App\Models\Order;
use App\Models\SupportTicket;
use App\Models\Transaction;
use App\Models\Vendor;
use App\Models\Withdrawal;
use App\Rules\FileTypeValidate;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ManageVendorsController extends Controller {

    public function allVendors() {
        $pageTitle = 'All Vendors';
        $vendors   = $this->vendorData();
        return view('admin.vendors.list', compact('pageTitle', 'vendors'));
    }

    public function activeVendors() {
        $pageTitle = 'Active Vendors';
        $vendors   = $this->vendorData('active');
        return view('admin.vendors.list', compact('pageTitle', 'vendors'));
    }

    public function bannedVendors() {
        $pageTitle = 'Banned Vendors';
        $vendors   = $this->vendorData('banned');
        return view('admin.vendors.list', compact('pageTitle', 'vendors'));
    }

    public function emailUnverifiedVendors() {
        $pageTitle = 'Email Unverified Vendors';
        $vendors   = $this->vendorData('emailUnverified');
        return view('admin.vendors.list', compact('pageTitle', 'vendors'));
    }

    public function kycUnverifiedVendors() {
        $pageTitle = 'KYC Unverified Vendors';
        $vendors   = $this->vendorData('kycUnverified');
        return view('admin.vendors.list', compact('pageTitle', 'vendors'));
    }

    public function kycPendingVendors() {
        $pageTitle = 'KYC Pending Vendors';
        $vendors   = $this->vendorData('kycPending');
        return view('admin.vendors.list', compact('pageTitle', 'vendors'));
    }

    public function emailVerifiedVendors() {
        $pageTitle = 'Email Verified Vendors';
        $vendors   = $this->vendorData('emailVerified');
        return view('admin.vendors.list', compact('pageTitle', 'vendors'));
    }

    public function mobileUnverifiedVendors() {
        $pageTitle = 'Mobile Unverified Vendors';
        $vendors   = $this->vendorData('mobileUnverified');
        return view('admin.vendors.list', compact('pageTitle', 'vendors'));
    }

    public function mobileVerifiedVendors() {
        $pageTitle = 'Mobile Verified Vendors';
        $vendors   = $this->vendorData('mobileVerified');
        return view('admin.vendors.list', compact('pageTitle', 'vendors'));
    }

    public function vendorsWithBalance() {
        $pageTitle = 'Vendors with Balance';
        $vendors   = $this->vendorData('withBalance');
        return view('admin.vendors.list', compact('pageTitle', 'vendors'));
    }

    protected function vendorData($scope = null) {
        if ($scope) {
            $vendors = Vendor::$scope();
        } else {
            $vendors = Vendor::query();
        }
        return $vendors->searchable(['username', 'email'])->orderBy('id', 'desc')->paginate(getPaginate());
    }

    public function detail($id) {
        $vendor    = Vendor::findOrFail($id);
        $pageTitle = 'Vendor Detail - ' . $vendor->username;
        $countries = json_decode(file_get_contents(resource_path('views/partials/country.json')));

        $widget['totalOrders']      = Order::paid()->where('vendor_id', $vendor->id)->count();
        $widget['canceledOrders']   = Order::paid()->canceled()->where('vendor_id', $vendor->id)->count();
        $widget['pendingOrders']    = Order::paid()->pending()->where('vendor_id', $vendor->id)->count();
        $widget['processingOrders'] = Order::paid()->processing()->where('vendor_id', $vendor->id)->count();
        $widget['deliveredOrders']  = Order::paid()->delivered()->where('vendor_id', $vendor->id)->count();
        $widget['dispatchedOrders'] = Order::paid()->dispatched()->where('vendor_id', $vendor->id)->count();
        $widget['totalOrderAmount'] = Order::paid()->where('vendor_id', $vendor->id)->sum('subtotal');

        $widget['totalOrderCommission'] = Order::paid()->where('vendor_id', $vendor->id)->sum('commission_amount');
        $widget['pendingWithdraws']     = Withdrawal::pending()->where('vendor_id', $vendor->id)->count();
        $widget['rejectedWithdraws']    = Withdrawal::rejected()->where('vendor_id', $vendor->id)->count();
        $widget['totalWithdraw']        = Withdrawal::where('vendor_id', $vendor->id)->approved()->sum('amount');

        $widget['totalTransaction'] = Transaction::where('vendor_id', $vendor->id)->count();

        $widget['totalTickets']   = SupportTicket::where('vendor_id', $vendor->id)->count();
        $widget['closedTickets']   = SupportTicket::where('vendor_id', $vendor->id)->where('status', Status::TICKET_CLOSE)->count();
        $widget['pendingTickets']   = SupportTicket::where('vendor_id', $vendor->id)->where('status', Status::TICKET_OPEN)->count();
        return view('admin.vendors.detail', compact('pageTitle', 'vendor', 'countries', 'widget'));
    }

    public function kycDetails($id) {
        $pageTitle = 'KYC Details';
        $vendor    = Vendor::findOrFail($id);
        return view('admin.vendors.kyc_detail', compact('pageTitle', 'vendor'));
    }

    public function kycApprove($id) {
        $vendor     = Vendor::findOrFail($id);
        $vendor->kv = Status::KYC_VERIFIED;
        $vendor->save();

        notify($vendor, 'KYC_APPROVE', []);

        $notify[] = ['success', 'KYC approved successfully'];
        return to_route('admin.vendors.kyc.pending')->withNotify($notify);
    }

    public function kycReject(Request $request, $id) {
        $request->validate([
            'reason' => 'required',
        ]);
        $vendor                       = Vendor::findOrFail($id);
        $vendor->kv                   = Status::KYC_UNVERIFIED;
        $vendor->kyc_rejection_reason = $request->reason;
        $vendor->save();

        notify($vendor, 'KYC_REJECT', [
            'reason' => $request->reason,
        ]);

        $notify[] = ['success', 'KYC rejected successfully'];
        return to_route('admin.vendors.kyc.pending')->withNotify($notify);
    }

    public function update(Request $request, $id) {
        $vendor       = Vendor::findOrFail($id);
        $countryData  = json_decode(file_get_contents(resource_path('views/partials/country.json')));
        $countryArray = (array) $countryData;
        $countries    = implode(',', array_keys($countryArray));

        $countryCode = $request->country;
        $country     = $countryData->$countryCode->country;
        $dialCode    = $countryData->$countryCode->dial_code;

        $request->validate([
            'firstname' => 'required|string|max:40',
            'lastname'  => 'required|string|max:40',
            'email'     => 'required|email|string|max:40|unique:vendors,email,' . $vendor->id,
            'mobile'    => 'required|string|max:40',
            'country'   => 'required|in:' . $countries,
        ]);

        $exists = Vendor::where('mobile', $request->mobile)->where('dial_code', $dialCode)->where('id', '!=', $vendor->id)->exists();
        if ($exists) {
            $notify[] = ['error', 'The mobile number already exists.'];
            return back()->withNotify($notify);
        }

        $vendor->mobile    = $request->mobile;
        $vendor->firstname = $request->firstname;
        $vendor->lastname  = $request->lastname;
        $vendor->email     = $request->email;

        $vendor->address      = $request->address;
        $vendor->city         = $request->city;
        $vendor->state        = $request->state;
        $vendor->zip          = $request->zip;
        $vendor->country_name = $country;
        $vendor->dial_code    = $dialCode;
        $vendor->country_code = $countryCode;

        $vendor->ev = $request->ev ? Status::VERIFIED : Status::UNVERIFIED;
        $vendor->sv = $request->sv ? Status::VERIFIED : Status::UNVERIFIED;
        $vendor->ts = $request->ts ? Status::ENABLE : Status::DISABLE;
        if (!$request->kv) {
            $vendor->kv = Status::KYC_UNVERIFIED;
            if ($vendor->kyc_data) {
                foreach ($vendor->kyc_data as $kycData) {
                    if ($kycData->type == 'file') {
                        fileManager()->removeFile(getFilePath('verify') . '/' . $kycData->value);
                    }
                }
            }
            $vendor->kyc_data = null;
        } else {
            $vendor->kv = Status::KYC_VERIFIED;
        }
        $vendor->save();

        $notify[] = ['success', 'Vendor details updated successfully'];
        return back()->withNotify($notify);
    }

    public function addSubBalance(Request $request, $id) {
        $request->validate([
            'amount' => 'required|numeric|gt:0',
            'act'    => 'required|in:add,sub',
            'remark' => 'required|string|max:255',
        ]);

        $vendor = Vendor::findOrFail($id);
        $amount = $request->amount;
        $trx    = getTrx();

        $transaction = new Transaction();

        if ($request->act == 'add') {
            $vendor->balance += $amount;

            $transaction->trx_type = '+';
            $transaction->remark   = 'balance_add';

            $notifyTemplate = 'BAL_ADD';

            $notify[] = ['success', 'Balance added successfully'];
        } else {
            if ($amount > $vendor->balance) {
                $notify[] = ['error', $vendor->username . ' doesn\'t have sufficient balance.'];
                return back()->withNotify($notify);
            }

            $vendor->balance -= $amount;

            $transaction->trx_type = '-';
            $transaction->remark   = 'balance_subtract';

            $notifyTemplate = 'BAL_SUB';
            $notify[]       = ['success', 'Balance subtracted successfully'];
        }

        $vendor->save();

        $transaction->vendor_id    = $vendor->id;
        $transaction->amount       = $amount;
        $transaction->post_balance = $vendor->balance;
        $transaction->charge       = 0;
        $transaction->trx          = $trx;
        $transaction->details      = $request->remark;
        $transaction->save();

        notify($vendor, $notifyTemplate, [
            'trx'          => $trx,
            'amount'       => showAmount($amount, currencyFormat: false),
            'remark'       => $request->remark,
            'post_balance' => showAmount($vendor->balance, currencyFormat: false),
        ]);

        return back()->withNotify($notify);
    }

    public function login($id) {
        Auth::guard('vendor')->loginUsingId($id);
        return to_route('vendor.home');
    }

    public function status(Request $request, $id) {
        $vendor = Vendor::findOrFail($id);
        if ($vendor->status == Status::USER_ACTIVE) {
            $request->validate([
                'reason' => 'required|string|max:255',
            ]);
            $vendor->status     = Status::USER_BAN;
            $vendor->ban_reason = $request->reason;
            $notify[]           = ['success', 'Vendor banned successfully'];
        } else {
            $vendor->status     = Status::USER_ACTIVE;
            $vendor->ban_reason = null;
            $notify[]           = ['success', 'Vendor unbanned successfully'];
        }
        $vendor->save();
        return back()->withNotify($notify);
    }

    public function showNotificationSingleForm($id) {
        $vendor = Vendor::findOrFail($id);
        if (!gs('en') && !gs('sn') && !gs('pn')) {
            $notify[] = ['warning', 'Notification options are disabled currently'];
            return to_route('admin.vendors.detail', $vendor->id)->withNotify($notify);
        }
        $pageTitle = 'Send Notification to ' . $vendor->username;
        return view('admin.vendors.notification_single', compact('pageTitle', 'vendor'));
    }

    public function sendNotificationSingle(Request $request, $id) {
        $request->validate([
            'message' => 'required',
            'via'     => 'required|in:email,sms,push',
            'subject' => 'required_if:via,email,push',
            'image'   => ['nullable', 'image', new FileTypeValidate(['jpg', 'jpeg', 'png'])],
        ]);

        if (!gs('en') && !gs('sn') && !gs('pn')) {
            $notify[] = ['warning', 'Notification options are disabled currently'];
            return to_route('admin.dashboard')->withNotify($notify);
        }

        return (new VendorNotificationSender())->notificationToSingle($request, $id);
    }

    public function showNotificationAllForm() {
        if (!gs('en') && !gs('sn') && !gs('pn')) {
            $notify[] = ['warning', 'Notification options are disabled currently'];
            return to_route('admin.dashboard')->withNotify($notify);
        }

        $notifyToVendor = Vendor::notifyToVendor();
        $vendors        = Vendor::active()->count();
        $pageTitle      = 'Notification to Verified Vendors';

        if (session()->has('SEND_NOTIFICATION') && !request()->email_sent) {
            session()->forget('SEND_NOTIFICATION');
        }

        return view('admin.vendors.notification_all', compact('pageTitle', 'vendors', 'notifyToVendor'));
    }

    public function sendNotificationAll(Request $request) {
        $request->validate([
            'via'                        => 'required|in:email,sms,push',
            'message'                    => 'required',
            'subject'                    => 'required_if:via,email,push',
            'start'                      => 'required|integer|gte:1',
            'batch'                      => 'required|integer|gte:1',
            'being_sent_to'              => 'required',
            'cooling_time'               => 'required|integer|gte:1',
            'number_of_top_order_vendor' => 'required_if:being_sent_to,topOrderVendors|integer|gte:0',
            'number_of_days'             => 'required_if:being_sent_to,notLoginVendors|integer|gte:0',
            'image'                      => ["nullable", 'image', new FileTypeValidate(['jpg', 'jpeg', 'png'])],
        ], [
            'number_of_days.required_if'             => "Number of days field is required",
            'number_of_top_order_vendor.required_if' => "Number of top order vendor field is required",
        ]);

        if (!gs('en') && !gs('sn') && !gs('pn')) {
            $notify[] = ['warning', 'Notification options are disabled currently'];
            return to_route('admin.dashboard')->withNotify($notify);
        }

        return (new VendorNotificationSender())->notificationToAll($request);
    }

    public function countBySegment($methodName) {
        return Vendor::active()->$methodName()->count();
    }

    public function list() {
        $query = Vendor::active();

        if (request()->search) {
            $query->where(function ($q) {
                $q->where('email', 'like', '%' . request()->search . '%')->orWhere('username', 'like', '%' . request()->search . '%');
            });
        }
        $vendors = $query->orderBy('id', 'desc')->paginate(getPaginate());
        return response()->json([
            'success' => true,
            'vendors' => $vendors,
            'more'    => $vendors->hasMorePages(),
        ]);
    }

    public function notificationLog($id) {
        $vendor    = Vendor::findOrFail($id);
        $pageTitle = 'Notifications Sent to ' . $vendor->username;
        $logs      = NotificationLog::where('vendor_id', $id)->with('vendor')->orderBy('id', 'desc')->paginate(getPaginate());
        return view('admin.reports.notification_history', compact('pageTitle', 'logs', 'vendor'));
    }
}
