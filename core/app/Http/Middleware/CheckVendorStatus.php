<?php

namespace App\Http\Middleware;

use Closure;
use Auth;

class CheckVendorStatus
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        if (Auth::guard('vendor')->check()) {
            $vendor = auth()->guard('vendor')->user();
            if ($vendor->status  && $vendor->ev  && $vendor->sv  && $vendor->tv) {
                return $next($request);
            } else {
                if ($request->is('api/*')) {
                    $notify[] = 'You need to verify your account first. Please logout and re-login';
                    return response()->json([
                        'remark'=>'unverified',
                        'status'=>'error',
                        'message'=>['error'=>$notify],
                        'data'=>[
                            'vendor'=>$vendor
                        ],
                    ]);
                }else{
                    return to_route('vendor.authorization');
                }
            }
        }
        abort(403);
    }
}
