<?php

namespace App\Http\Controllers\User\Auth;

use App\Models\User;
use App\Lib\SocialLogin;
use App\Http\Controllers\Controller;

class SocialiteController extends Controller
{

    public function socialLogin($provider)
    {
        $socialLogin = new SocialLogin($provider, User::class);
        return $socialLogin->redirectDriver();
    }


    public function callback($provider)
    {
        $socialLogin = new SocialLogin($provider, User::class);
        try {
            return $socialLogin->login();
        } catch (\Exception $e) {
            $notify[] = ['error', $e->getMessage()];
            return to_route('home')->withNotify($notify);
        }
    }
}
