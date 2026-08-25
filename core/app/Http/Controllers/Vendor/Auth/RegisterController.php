<?php

namespace App\Http\Controllers\Vendor\Auth;

use App\Constants\Status;
use App\Http\Controllers\Controller;
use App\Lib\Intended;
use App\Models\AdminNotification;
use App\Models\Vendor;
use App\Models\VendorLogin;
use Illuminate\Auth\Events\Registered;
use Illuminate\Foundation\Auth\RegistersUsers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rules\Password;

class RegisterController extends Controller
{

    use RegistersUsers;

    public function __construct()
    {
        parent::__construct();
    }

    public function showRegistrationForm()
    {
        $pageTitle = "Vendor Register";
        Intended::identifyRoute();
        return view('Template::vendor.auth.register', compact('pageTitle'));
    }


    protected function validator(array $data)
    {

        $passwordValidation = Password::min(6);

        if (gs('secure_password')) {
            $passwordValidation = $passwordValidation->mixedCase()->numbers()->symbols()->uncompromised();
        }

        $agree = 'nullable';
        if (gs('agree')) {
            $agree = 'required';
        }

        $validate     = Validator::make($data, [
            'firstname' => 'required',
            'lastname'  => 'required',
            'email'     => 'required|string|email|unique:users',
            'password'  => ['required', 'confirmed', $passwordValidation],
            'captcha'   => 'sometimes|required',
            'agree'     => $agree
        ],[
            'firstname.required'=>'The first name field is required',
            'lastname.required'=>'The last name field is required'
        ]);

        return $validate;
    }

    public function register(Request $request)
    {
        if (!gs('vendor_registration')) {
            $notify[] = ['error', 'Registration not allowed'];
            return back()->withNotify($notify);
        }
        $this->validator($request->all())->validate();

        $request->session()->regenerateToken();

        if (!verifyCaptcha()) {
            $notify[] = ['error', 'Invalid captcha provided'];
            return back()->withNotify($notify);
        }



        event(new Registered($vendor = $this->create($request->all())));

        $this->guard('vendor')->login($vendor);

        return $this->registered($request, $vendor)
            ?: redirect($this->redirectPath());
    }



    protected function create(array $data)
    {
        //User Create
        $vendor            = new Vendor();
        $vendor->email     = strtolower($data['email']);
        $vendor->firstname = $data['firstname'];
        $vendor->lastname  = $data['lastname'];
        $vendor->password  = Hash::make($data['password']);
        $vendor->kv = gs('kv') ? Status::NO : Status::YES;
        $vendor->ev = gs('ev') ? Status::NO : Status::YES;
        $vendor->sv = gs('sv') ? Status::NO : Status::YES;
        $vendor->ts = Status::DISABLE;
        $vendor->tv = Status::ENABLE;
        $vendor->save();

        $adminNotification            = new AdminNotification();
        $adminNotification->vendor_id   = $vendor->id;
        $adminNotification->title     = 'New member registered';
        $adminNotification->click_url = urlPath('admin.vendors.detail', $vendor->id);
        $adminNotification->save();


        //Login Log Create
        $ip        = getRealIP();
        $exist     = VendorLogin::where('vendor_ip', $ip)->first();
        $vendorLogin = new VendorLogin();

        if ($exist) {
            $vendorLogin->longitude    = $exist->longitude;
            $vendorLogin->latitude     = $exist->latitude;
            $vendorLogin->city         = $exist->city;
            $vendorLogin->country_code = $exist->country_code;
            $vendorLogin->country      = $exist->country;
        } else {
            $info                    = json_decode(json_encode(getIpInfo()), true);
            $vendorLogin->longitude    = isset($info['long']) ? implode(',', $info['long']) : '';
            $vendorLogin->latitude     = isset($info['lat']) ? implode(',', $info['lat']) : '';
            $vendorLogin->city         = isset($info['city']) ? implode(',', $info['city']) : '';
            $vendorLogin->country_code = isset($info['code']) ? implode(',', $info['code']) : '';
            $vendorLogin->country      = isset($info['country']) ? implode(',', $info['country']) : '';
        }

        $vendorAgent          = osBrowser();
        $vendorLogin->vendor_id = $vendor->id;
        $vendorLogin->vendor_ip = $ip;

        $vendorLogin->browser = isset($vendorAgent['browser']) ? $vendorAgent['browser'] : '';
        $vendorLogin->os      = isset($vendorAgent['os_platform']) ? $vendorAgent['os_platform'] : '';
        $vendorLogin->save();


        return $vendor;
    }

    public function checkUser(Request $request){
        $exist['data'] = false;
        $exist['type'] = null;
        if ($request->email) {
            $exist['data'] = Vendor::where('email',$request->email)->exists();
            $exist['type'] = 'email';
            $exist['field'] = 'Email';
        }
        if ($request->mobile) {
            $exist['data'] = Vendor::where('mobile',$request->mobile)->where('dial_code',$request->mobile_code)->exists();
            $exist['type'] = 'mobile';
            $exist['field'] = 'Mobile';
        }
        if ($request->username) {
            $exist['data'] = Vendor::where('username',$request->username)->exists();
            $exist['type'] = 'username';
            $exist['field'] = 'Username';
        }
        return response($exist);
    }

    public function registered()
    {
        return to_route('vendor.home');
    }

}
