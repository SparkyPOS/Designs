<?php

namespace App\Http\Controllers\Vendor\Auth;

use App\Constants\Status;
use App\Models\Vendor;
use App\Http\Controllers\Controller;
use App\Models\VendorPasswordReset;
use Carbon\Carbon;
use Illuminate\Http\Request;

class ForgotPasswordController extends Controller
{
    public function showLinkRequestForm()
    {
        $pageTitle = "Account Recovery";
        return view('Template::vendor.auth.passwords.email', compact('pageTitle'));
    }

    public function sendResetCodeEmail(Request $request)
    {
        $request->validate([
            'value'=>'required'
        ]);

        if(!verifyCaptcha()){
            $notify[] = ['error','Invalid captcha provided'];
            return back()->withNotify($notify);
        }

        $fieldType = $this->findFieldType();
        $vendor = Vendor::where($fieldType, $request->value)->first();

        if (!$vendor) {
            $notify[] = ['error', 'The account could not be found'];
            return back()->withNotify($notify);
        }

        VendorPasswordReset::where('email', $vendor->email)->delete();
        $code = verificationCode(6);
        $password = new VendorPasswordReset();
        $password->email = $vendor->email;
        $password->token = $code;
        $password->created_at = \Carbon\Carbon::now();
        $password->save();

        $vendorIpInfo = getIpInfo();
        $vendorBrowserInfo = osBrowser();
        notify($vendor, 'PASS_RESET_CODE', [
            'code' => $code,
            'operating_system' => isset($vendorBrowserInfo['os_platform']) ? $vendorBrowserInfo['os_platform'] : '',
            'browser' => isset($vendorBrowserInfo['browser']) ? $vendorBrowserInfo['browser'] : '',
            'ip' => isset($vendorIpInfo['ip']) ? $vendorIpInfo['ip'] : '',
            'time' => isset($vendorIpInfo['time']) ? $vendorIpInfo['time'] : ''
        ],['email']);

        $email = $vendor->email;
        session()->put('pass_res_mail',$email);
        $notify[] = ['success', 'Password reset email sent successfully'];
        return to_route('vendor.password.code.verify')->withNotify($notify);
    }

    public function findFieldType()
    {
        $input = request()->input('value');

        $fieldType = filter_var($input, FILTER_VALIDATE_EMAIL) ? 'email' : 'username';
        request()->merge([$fieldType => $input]);
        return $fieldType;
    }

    public function codeVerify(Request $request){
        $pageTitle = 'Verify Email';
        $email = $request->session()->get('pass_res_mail');
        if (!$email) {
            $notify[] = ['error','Oops! session expired'];
            return to_route('vendor.password.request')->withNotify($notify);
        }
        return view('Template::vendor.auth.passwords.code_verify',compact('pageTitle','email'));
    }

    public function verifyCode(Request $request)
    {
        $request->validate([
            'code' => 'required',
            'email' => 'required'
        ]);
        $code =  str_replace(' ', '', $request->code);

        if (VendorPasswordReset::where('token', $code)->where('email', $request->email)->count() != 1) {
            $notify[] = ['error', 'Verification code doesn\'t match'];
            return to_route('vendor.password.request')->withNotify($notify);
        }
        $notify[] = ['success', 'You can change your password'];
        session()->flash('fpass_email', $request->email);
        return to_route('vendor.password.reset', $code)->withNotify($notify);
    }
}
