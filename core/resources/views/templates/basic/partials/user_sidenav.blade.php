<aside class="dashboard__sidebar offcanvas-lg offcanvas-start" id="dashboard-sidebar">
    <div class="dashboard__sidebar-wrapper position-relative">

        <button class="sidebar-collapse d-lg-block d-none">
            <span class="sidebar-collapse__icon">
                <i class="fa-solid fa-down-left-and-up-right-to-center"></i>
            </span>
        </button>

        <button class="sidebar-collapse d-lg-none" data-bs-dismiss="offcanvas" aria-label="Close"
            data-bs-target="#dashboard-sidebar">
            <span class="sidebar-collapse__icon">
                <i class="fa-solid fa-xmark"></i>
            </span>
        </button>

        <div class="sidebar-header">
            <a class="sidebar-logo" href="{{ route('home') }}">
                <img class="logo" src="{{ siteLogo() }}" alt="@lang('logo')">
                <img class="favicon" src="{{ siteFavicon() }}" alt="@lang('favicon')">
            </a>
        </div>

        <div class="sidebar-body">
            <nav class="sidebar-menu">
                <a href="{{ route('user.home') }}" class="sidebar-menu__link {{ menuActive('user.home') }}"
                    data-bs-toggle="tooltip" data-bs-title="@lang('Dashboard')"
                    data-bs-custom-class="tooltip--dashboard-sidebar">
                    <span class="icon"><i class="fas fa-home"></i></span>
                    <span class="text">@lang('Dashboard')</span>
                </a>

                <div class="db-notification__area hide-after-md">
                    <button
                        class="sidebar-menu__link w-100 notification-btn {{ menuActive('user.notifications.index') }}"
                        data-bs-toggle="tooltip" data-bs-title="@lang('Notifications')"
                        data-bs-custom-class="tooltip--dashboard-sidebar">
                        <span class=" icon">
                            <i class="fas fa-bell"></i>
                        </span>
                        <span class="text">@lang('Notifications')</span>
                        <span
                            class="badge custom--badge badge--danger ms-auto">{{ $userNotificationCount <= 9 ? __($userNotificationCount) : __('9+') }}</span>
                    </button>
                    <div class="notification-area position-absolute" id="db-notification">
                        <div class="notification-area__header">
                            <h3 class="notification-area__header-title">@lang('Notifications')</h3>
                            @if (!blank($userNotifications))
                                <div class="notification-area__header-actions">
                                    <a href="{{ route('user.notifications.read.all') }}"
                                        class="btn btn-outline--light btn--sm">
                                        <i class="fa-solid fa-check-double"></i>
                                        <span class="text ms-2">@lang('Mark all as read')</span>
                                    </a>
                                </div>
                            @endif
                        </div>
                        <div class="notification-area__body">
                            <div class="notification-area__list">
                                @forelse ($userNotifications as $notification)
                                    <a href="{{ route('user.notifications.read', $notification->id) }}"
                                        class="notification-area__item">
                                        <div class="notification-area__item-wrapper">
                                            <div class="notification-area__item-icon">
                                                <i class="fa-solid fa-bell"></i>
                                            </div>
                                            <div class="notification-area__item-content">
                                                <h4 class="notification-area__item-title">
                                                    {{ __($notification->title) }}</h4>
                                            </div>
                                        </div>
                                        <div class="notification-area__item-time">
                                            {{ diffForHumans($notification->created_at) }}</div>
                                    </a>
                                @empty
                                    <div class="notification-emtpy">
                                        <img src="{{ getImage('assets/images/empty_list.png') }}" alt="empty">
                                        <p class="mt-3">@lang('No unread notification found')</p>
                                    </div>
                                @endforelse
                            </div>
                            <div class="notification-area__footer">
                                <a class="notification-area__item"
                                    href="{{ route('user.notifications.index') }}">@lang('View All Notifications')</a>
                            </div>
                        </div>
                    </div>
                </div>

                <a href="{{ route('user.order.index') }}" class="sidebar-menu__link {{ menuActive('user.order*') }}"
                    data-bs-toggle="tooltip" data-bs-title="@lang('Orders')"
                    data-bs-custom-class="tooltip--dashboard-sidebar">
                    <span class="icon"><i class="fa-solid fa-list"></i></span>
                    <span class="text">@lang('Orders')</span>
                </a>

                <a href="{{ route('user.deposit.history') }}"
                    class="sidebar-menu__link {{ menuActive('user.deposit.history') }}" data-bs-toggle="tooltip"
                    data-bs-title="@lang('Payments')" data-bs-custom-class="tooltip--dashboard-sidebar">
                    <span class="icon"><i class="fa-solid fa-money-bill"></i></span>
                    <span class="text">@lang('Payments')</span>
                </a>
                <a href="{{ route('user.review.index') }}"
                    class="sidebar-menu__link {{ menuActive('user.review.*') }}" data-bs-toggle="tooltip"
                    data-bs-title="@lang('Reviews')" data-bs-custom-class="tooltip--dashboard-sidebar">
                    <span class="icon"><i class="fa-solid fa-star"></i></span>
                    <span class="text">@lang('Reviews')</span>
                </a>

                <a href="{{ route('ticket.index') }}" class="sidebar-menu__link {{ menuActive('ticket*') }}"
                    data-bs-toggle="tooltip" data-bs-title="@lang('Support Tickets')"
                    data-bs-custom-class="tooltip--dashboard-sidebar">
                    <span class="icon"><i class="fa-solid fa-ticket"></i></span>
                    <span class="text">@lang('Support Tickets')</span>
                </a>
                <a href="{{ route('user.logout') }}" class="sidebar-menu__link" data-bs-toggle="tooltip"
                    data-bs-title="@lang('Logout')" data-bs-custom-class="tooltip--dashboard-sidebar">
                    <span class="icon"><i class="fa-solid fa-sign-out-alt"></i></span>
                    <span class="text">@lang('Logout')</span>
                </a>
            </nav>
        </div>
        <div class="sidebar-footer dropend custom--dropdown">
            <button type="button" class="account flex-align gap-3" data-bs-toggle="dropdown" aria-expanded="false">
                <div class="account__thumb">
                    <i class="fa-solid fa-circle-user"></i>
                </div>
                <div class="account__info">
                    <p class="account__name">@lang('Account')</p>
                    <p class="account__email">{{ __(auth()->user()->email) }}</p>
                </div>
                <div class="account__icon">
                    <i class="fa-solid fa-angle-right"></i>
                </div>
            </button>
            <ul class="dropdown-menu  fade">
                <li>
                    <a class="dropdown-item" href="{{ route('user.profile.setting') }}">
                        <i class="fa-solid fa-user fs-15 me-2"></i>
                        <span>@lang('Account Settings')</span>
                    </a>
                </li>
                <li class="hr"></li>
                <li>
                    <a class="dropdown-item danger-item" href="{{ route('user.logout') }}">
                        <i class="fa-solid fa-sign-out-alt fs-15 me-2"></i>
                        <span>@lang('Logout')</span>
                    </a>
                </li>
            </ul>

        </div>
    </div>
</aside>
