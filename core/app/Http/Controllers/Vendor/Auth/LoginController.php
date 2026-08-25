<?php

namespace App\Http\Controllers\Vendor\Auth;

use App\Constants\Status;
use App\Models\VendorLogin;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\AuthenticatesUsers;

class LoginController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Login Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles authenticating users for the application and
    | redirecting them to your home screen. The controller uses a trait
    | to conveniently provide its functionality to your applications.
    |
    */

    use AuthenticatesUsers;

    protected $username;

    public function __construct()
    {
        $this->username = $this->findUsername();
    }

    private function findUsername()
    {
        $login = request()->input('username');

        $fieldType = filter_var($login, FILTER_VALIDATE_EMAIL) ? 'email' : 'username';

        request()->merge([$fieldType => $login]);

        return $fieldType;
    }

    /**
     * Where to redirect users after login / registration.
     *
     * @var string
     */
    public $redirectTo = 'vendor/dashboard';

    /**
     * Show the application's login form.
     */
    public function showLoginForm()
    {
        $pageTitle = "vendor Login";
        return view('Template::vendor.auth.login', compact('pageTitle'));
    }

    /**
     * Get the guard to be used during authentication.
     *
     * @return \Illuminate\Contracts\Auth\StatefulGuard
     */
    protected function guard()
    {
        return auth()->guard('vendor');
    }

    public function username()
    {
        return $this->username;
    }

    public function login(Request $request)
    {

        $this->validateLogin($request);

        $request->session()->regenerateToken();

        if (!verifyCaptcha()) {
            $notify[] = ['error', 'Invalid captcha provided'];
            return back()->withNotify($notify);
        }

        // If the class is using the ThrottlesLogins trait, we can automatically throttle
        // the login attempts for this application. We'll key this by the username and
        // the IP address of the client making these requests into this application.
        if (
            method_exists($this, 'hasTooManyLoginAttempts') &&
            $this->hasTooManyLoginAttempts($request)
        ) {
            $this->fireLockoutEvent($request);
            return $this->sendLockoutResponse($request);
        }

        if ($this->attemptLogin($request)) {
            return $this->sendLoginResponse($request);
        }

        // If the login attempt was unsuccessful we will increment the number of attempts
        // to login and redirect the user back to the login form. Of course, when this
        // user surpasses their maximum number of attempts they will get locked out.
        $this->incrementLoginAttempts($request);

        return $this->sendFailedLoginResponse($request);
    }


    public function logout(Request $request)
    {
        $this->guard('vendor')->logout();
        $request->session()->invalidate();
        $notify[] = ['success', 'You have been logged out.'];
        return to_route('vendor.login')->withNotify($notify);
    }

    public function authenticated(Request $request, $user)
    {
        $user->tv = $user->ts == Status::VERIFIED ? Status::UNVERIFIED : Status::VERIFIED;
        $user->save();
        $ip        = getRealIP();
        $exist     = VendorLogin::where('vendor_ip', $ip)->first();
        $vendorLogin = new VendorLogin();

        if ($exist) {
            $vendorLogin->longitude =  $exist->longitude;
            $vendorLogin->latitude =  $exist->latitude;
            $vendorLogin->city =  $exist->city;
            $vendorLogin->country_code = $exist->country_code;
            $vendorLogin->country =  $exist->country;
        } else {
            $info = json_decode(json_encode(getIpInfo()), true);
            $vendorLogin->longitude =  isset($info['long']) ? implode(',', $info['long']) : '';
            $vendorLogin->latitude =  isset($info['lat']) ? implode(',', $info['lat']) : '';
            $vendorLogin->city =  isset($info['city']) ? implode(',', $info['city']) : '';
            $vendorLogin->country_code = isset($info['code']) ? implode(',', $info['code']) : '';
            $vendorLogin->country =  isset($info['country']) ? implode(',', $info['country']) : '';
        }

        $userAgent = osBrowser();
        $vendorLogin->vendor_id = $user->id;
        $vendorLogin->vendor_ip =  $ip;

        $vendorLogin->browser = isset($userAgent['browser']) ? $userAgent['browser'] : '';
        $vendorLogin->os = isset($userAgent['os_platform']) ? $userAgent['os_platform'] : '';
        $vendorLogin->save();
    }
}
