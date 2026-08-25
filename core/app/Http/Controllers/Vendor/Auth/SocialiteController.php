<?php

namespace App\Http\Controllers\Vendor\Auth;

use App\Models\Vendor;
use App\Lib\SocialLogin;
use App\Http\Controllers\Controller;

class SocialiteController extends Controller
{

    public function socialLogin($provider)
    {
        $socialLogin = new SocialLogin($provider, Vendor::class, 'vendor');
        return $socialLogin->redirectDriver();
    }


    public function callback($provider)
    {
        $socialLogin = new SocialLogin($provider, Vendor::class, 'vendor');
        try {
            return $socialLogin->login();
        } catch (\Exception $e) {
            $notify[] = ['error', $e->getMessage()];
            return to_route('home')->withNotify($notify);
        }
    }
}
