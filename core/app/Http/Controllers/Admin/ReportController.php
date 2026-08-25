<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\NotificationLog;
use App\Models\Transaction;
use App\Models\UserLogin;
use App\Models\VendorLogin;
use Illuminate\Http\Request;

class ReportController extends Controller
{
    public function transaction(Request $request,$userId = null)
    {
        $pageTitle = 'Transaction Logs';

        $remarks = Transaction::distinct('remark')->orderBy('remark')->get('remark');

        $transactions = Transaction::searchable(['trx','vendor:username'])->filter(['trx_type','remark'])->dateFilter()->orderBy('id','desc')->with('vendor');
        if ($userId) {
            $transactions = $transactions->where('vendor_id',$userId);
        }
        $transactions = $transactions->paginate(getPaginate());

        return view('admin.reports.transactions', compact('pageTitle', 'transactions','remarks'));
    }

    public function loginHistory(Request $request)
    {
        $pageTitle = 'Customer Login History';
        $loginLogs = UserLogin::orderBy('id','desc')->searchable(['user:username'])->dateFilter()->with('user')->paginate(getPaginate());
        $type = 'user';
        return view('admin.reports.logins', compact('pageTitle', 'loginLogs', 'type'));
    }

    public function vendorLoginHistory(Request $request)
    {
        $pageTitle = 'Vendor Login History';
        $loginLogs = VendorLogin::orderBy('id','desc')->searchable(['vendor:username'])->dateFilter()->with('vendor')->paginate(getPaginate());
        $type = 'vendor';
        return view('admin.reports.vendor_logins', compact('pageTitle', 'loginLogs', 'type'));
    }

    public function loginIpHistory($ip)
    {
        $pageTitle = 'Login by - ' . $ip;
        $loginLogs = UserLogin::where('user_ip',$ip)->orderBy('id','desc')->with('user')->paginate(getPaginate());
        $type = 'user';
        return view('admin.reports.logins', compact('pageTitle', 'loginLogs','ip', 'type'));
    }

    public function vendorLoginIpHistory($ip)
    {
        $pageTitle = 'Login by - ' . $ip;
        $loginLogs = VendorLogin::where('vendor_ip',$ip)->orderBy('id','desc')->with('vendor')->paginate(getPaginate());
        $type = 'vendor';
        return view('admin.reports.vendor_logins', compact('pageTitle', 'loginLogs','ip', 'type'));
    }

    public function notificationHistory(Request $request){
        $pageTitle = 'Notification History';
        $logs = NotificationLog::orderBy('id','desc')->searchable(['user:username'])->dateFilter()->with('user')->paginate(getPaginate());
        return view('admin.reports.notification_history', compact('pageTitle','logs'));
    }

    public function emailDetails($id){
        $pageTitle = 'Email Details';
        $email = NotificationLog::findOrFail($id);
        return view('admin.reports.email_details', compact('pageTitle','email'));
    }
}
