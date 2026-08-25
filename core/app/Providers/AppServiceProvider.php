<?php

namespace App\Providers;

use App\Models\User;
use App\Models\Order;
use App\Models\Vendor;
use App\Lib\Searchable;
use App\Models\Deposit;
use App\Models\Frontend;
use App\Constants\Status;
use App\Models\Withdrawal;
use App\Models\SupportTicket;
use App\Models\UserNotification;
use App\Models\AdminNotification;
use App\Models\VendorNotification;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\ServiceProvider;
use Illuminate\Database\Eloquent\Builder;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        Builder::mixin(new Searchable);
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        if (!cache()->get('SystemInstalled')) {
            $envFilePath = base_path('.env');
            if (!file_exists($envFilePath)) {
                header('Location: install');
                exit;
            }
            $envContents = file_get_contents($envFilePath);
            if (empty($envContents)) {
                header('Location: install');
                exit;
            } else {
                cache()->put('SystemInstalled', true);
            }
        }


        $viewShare['emptyMessage'] = 'Data not found';
        view()->share($viewShare);


        view()->composer('admin.partials.sidenav', function ($view) {
            $view->with([
                'bannedUsersCount'           => User::banned()->count(),
                'emailUnverifiedUsersCount' => User::emailUnverified()->count(),
                'mobileUnverifiedUsersCount'   => User::mobileUnverified()->count(),
                'pendingTicketCount'         => SupportTicket::whereIN('status', [Status::TICKET_OPEN, Status::TICKET_REPLY])->count(),
                'pendingDepositsCount'    => Deposit::pending()->count(),
                'pendingWithdrawCount'    => Withdrawal::pending()->count(),
                'updateAvailable'    => version_compare(gs('available_version'),systemDetails()['version'],'>') ? 'v'.gs('available_version') : false,

                'bannedVendorsCount'           => Vendor::banned()->count(),
                'emailUnverifiedVendorsCount' => Vendor::emailUnverified()->count(),
                'mobileUnverifiedVendorsCount'   => Vendor::mobileUnverified()->count(),
                'kycUnverifiedVendorsCount'   => Vendor::kycUnverified()->count(),
                'kycPendingVendorsCount'   => Vendor::kycPending()->count(),
            ]);
        });

        view()->composer('admin.partials.topnav', function ($view) {
            $view->with([
                'adminNotifications' => AdminNotification::where('is_read', Status::NO)->with('user')->orderBy('id', 'desc')->take(10)->get(),
                'adminNotificationCount' => AdminNotification::where('is_read', Status::NO)->count(),
            ]);
        });

        view()->composer('partials.seo', function ($view) {
            $seo = Frontend::where('data_keys', 'seo.data')->first();
            $view->with([
                'seo' => $seo ? $seo->data_values : $seo,
            ]);
        });

        view()->composer(['Template::partials.user_header', 'Template::partials.user_sidenav'], function ($view) {
            $view->with([
                'userNotifications'     => UserNotification::where('user_id', auth()->id())->where('is_read', Status::NO)->orderBy('id', 'desc')->take(10)->get(),
                'userNotificationCount' => UserNotification::where('user_id', auth()->id())->where('is_read', Status::NO)->count(),
            ]);
        });

        view()->composer(['Template::partials.vendor_header', 'Template::partials.vendor_sidenav'], function ($view) {
            $view->with([
                'vendorNotifications'     => VendorNotification::where('vendor_id', authVendorId())->where('is_read', Status::NO)->orderBy('id', 'desc')->take(10)->get(),
                'vendorNotificationCount' => VendorNotification::where('vendor_id', authVendorId())->where('is_read', Status::NO)->count(),
                'pendingOrdersCount' => Order::where('vendor_id', authVendorId())->pending()->count(),
            ]);
        });

//        if (gs('force_ssl')) {
//            \URL::forceScheme('https');
//        }


        Paginator::useBootstrapFive();
    }
}
