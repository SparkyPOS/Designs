<div class="dashboard__top position-sticky top-0 bg-white">
    <div class="container-fluid">
        <div class="dashboard__top-wrapper flex-align">

            <button class="show-sidebar-btn" type="button" data-bs-toggle="offcanvas" data-bs-target="#dashboard-sidebar">
                <i class="las la-bars"></i>
            </button>

            <div class="dashboard__top-logo">
                <a href="{{ route('vendor.home') }}">
                    <img src="{{ siteLogo('dark') }}" alt="@lang('Logo')">
                </a>
            </div>
            <div class="dashboard__top-notification ms-auto">
                <a class="notification-icon" href="{{ route('vendor.notifications.index') }}">
                    <span class="unread-sign">{{ $vendorNotificationCount <= 9 ? __($vendorNotificationCount) : __('9+') }}</span>
                    <span class="top-icon">
                        <i class="fa-regular fa-bell"></i>
                    </span>
                </a>
            </div>
            <div class="dashboard__top-user">
                <div class="dropdown custom--dropdown">
                    <button class="dropdown-toggle" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                        <span class="top-icon">
                            <i class="fa-regular fa-user"></i>
                        </span>
                    </button>
                    <ul class="dropdown-menu">
                        <li>
                            <a class="dropdown-item" href="{{ route('vendor.profile.setting') }}">
                                <i class="fa fa-user fs-15 me-2"></i>
                                <span>@lang('Account Settings')</span>
                            </a>
                        </li>
                        <li class="hr"></li>
                        <li>
                            <a class="dropdown-item danger-item" href="{{ route('vendor.logout') }}">
                                <i class="fa-solid fa-sign-out-alt fs-15 me-2"></i>
                                <span>@lang('Logout')</span>
                            </a>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>
