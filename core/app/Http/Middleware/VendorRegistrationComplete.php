<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class VendorRegistrationComplete
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $vendor = auth('vendor')->user();

        if (!$vendor->profile_complete) {
            $notify[] = ['info', 'Please complete your profile to go next'];
            return to_route('vendor.data')->withNotify($notify);
        }

        return $next($request);
    }
}
