<?php

namespace App\Http\Controllers\Vendor\Auth;

use App\Constants\Status;
use App\Http\Controllers\Controller;
use App\Models\Vendor;
use App\Models\VendorPasswordReset;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class ResetPasswordController extends Controller
{
    public function showResetForm(Request $request, $token)
    {
        $pageTitle = "Account Recovery";
        $resetToken = VendorPasswordReset::where('token', $token)->where('status', Status::ENABLE)->first();

        if (!$resetToken) {
            $notify[] = ['error', 'Verification code mismatch'];
            return to_route('vendor.password.reset')->withNotify($notify);
        }
        $email = $resetToken->email;
        return view('Template::vendor.auth.passwords.reset', compact('pageTitle', 'email', 'token'));
    }


    public function reset(Request $request)
    {
        $request->validate([
            'email'    => 'required|email',
            'token'    => 'required',
            'password' => 'required|confirmed|min:4',
        ]);

        $reset = VendorPasswordReset::where('token', $request->token)->orderBy('created_at', 'desc')->first();
        $vendor = Vendor::where('email', $reset->email)->first();
        if ($reset->status == Status::DISABLE) {
            $notify[] = ['error', 'Invalid code'];
            return to_route('vendor.login')->withNotify($notify);
        }

        $vendor->password = Hash::make($request->password);
        $vendor->save();
        $reset->status = Status::DISABLE;
        $reset->save();

        $ipInfo = getIpInfo();
        $browser = osBrowser();
        notify($vendor, 'PASS_RESET_DONE', [
            'operating_system' => $browser['os_platform'],
            'browser'          => $browser['browser'],
            'ip'               => $ipInfo['ip'],
            'time'             => $ipInfo['time']
        ], ['email'], false);

        $notify[] = ['success', 'Password changed'];
        return to_route('vendor.login')->withNotify($notify);
    }
}
