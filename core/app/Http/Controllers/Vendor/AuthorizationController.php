<?php

namespace App\Http\Controllers\Vendor;

use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use App\Http\Controllers\Controller;
use App\Constants\Status;
use App\Lib\Intended;

class AuthorizationController extends Controller
{
    protected function checkCodeValidity($vendor,$addMin = 2)
    {
        if (!$vendor->ver_code_send_at){
            return false;
        }
        if ($vendor->ver_code_send_at->addMinutes($addMin) < Carbon::now()) {
            return false;
        }
        return true;
    }

    public function authorizeForm()
    {
        $vendor = auth('vendor')->user();
        if (!$vendor->status) {
            $pageTitle = 'Banned';
            $type = 'ban';
        }elseif(!$vendor->ev) {
            $type = 'email';
            $pageTitle = 'Verify Email';
            $notifyTemplate = 'EVER_CODE';
        }elseif (!$vendor->sv) {
            $type = 'sms';
            $pageTitle = 'Verify Mobile Number';
            $notifyTemplate = 'SVER_CODE';
        }elseif (!$vendor->tv) {
            $pageTitle = '2FA Verification';
            $type = '2fa';
        }else{
            return to_route('vendor.home');
        }

        if (!$this->checkCodeValidity($vendor) && ($type != '2fa') && ($type != 'ban')) {
            $vendor->ver_code = verificationCode(6);
            $vendor->ver_code_send_at = Carbon::now();
            $vendor->save();
            notify($vendor, $notifyTemplate, [
                'code' => $vendor->ver_code
            ],[$type]);
        }

        return view('Template::vendor.auth.authorization.'.$type, compact('vendor', 'pageTitle'));

    }

    public function sendVerifyCode($type)
    {
        $vendor = auth('vendor')->user();

        if ($this->checkCodeValidity($vendor)) {
            $targetTime = $vendor->ver_code_send_at->addMinutes(2)->timestamp;
            $delay = $targetTime - time();
            throw ValidationException::withMessages(['resend' => 'Please try after ' . $delay . ' seconds']);
        }

        $vendor->ver_code = verificationCode(6);
        $vendor->ver_code_send_at = Carbon::now();
        $vendor->save();

        if ($type == 'email') {
            $type = 'email';
            $notifyTemplate = 'EVER_CODE';
        } else {
            $type = 'sms';
            $notifyTemplate = 'SVER_CODE';
        }

        notify($vendor, $notifyTemplate, [
            'code' => $vendor->ver_code
        ],[$type]);

        $notify[] = ['success', 'Verification code sent successfully'];
        return back()->withNotify($notify);
    }

    public function emailVerification(Request $request)
    {
        $request->validate([
            'code'=>'required'
        ]);

        $vendor = auth('vendor')->user();

        if ($vendor->ver_code == $request->code) {
            $vendor->ev = Status::VERIFIED;
            $vendor->ver_code = null;
            $vendor->ver_code_send_at = null;
            $vendor->save();

            $redirection = Intended::getRedirection();
            return $redirection ? $redirection : to_route('vendor.home');
        }
        throw ValidationException::withMessages(['code' => 'Verification code didn\'t match!']);
    }

    public function mobileVerification(Request $request)
    {
        $request->validate([
            'code' => 'required',
        ]);


        $vendor = auth('vendor')->user();
        if ($vendor->ver_code == $request->code) {
            $vendor->sv = Status::VERIFIED;
            $vendor->ver_code = null;
            $vendor->ver_code_send_at = null;
            $vendor->save();
            $redirection = Intended::getRedirection();
            return $redirection ? $redirection : to_route('vendor.home');
        }
        throw ValidationException::withMessages(['code' => 'Verification code didn\'t match!']);
    }

    public function g2faVerification(Request $request)
    {
        $vendor = auth('vendor')->user();
        $request->validate([
            'code' => 'required',
        ]);
        $response = verifyG2fa($vendor,$request->code);
        if ($response) {
            $redirection = Intended::getRedirection();
            return $redirection ? $redirection : to_route('vendor.home');
        }else{
            $notify[] = ['error','Wrong verification code'];
            return back()->withNotify($notify);
        }
    }
}
