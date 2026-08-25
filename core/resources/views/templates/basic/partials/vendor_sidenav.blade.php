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
                <a href="{{ route('vendor.home') }}" class="sidebar-menu__link {{ menuActive('vendor.home') }}"
                    data-bs-toggle="tooltip" data-bs-title="@lang('Dashboard')"
                    data-bs-custom-class="tooltip--dashboard-sidebar">
                    <span class="icon"><i class="fas fa-home"></i></span>
                    <span class="text">@lang('Dashboard')</span>
                </a>

                <div class="db-notification__area hide-after-md">
                    <button
                        class="sidebar-menu__link w-100 notification-btn {{ menuActive('vendor.notifications.index') }}"
                        data-bs-toggle="tooltip" data-bs-title="@lang('Notifications')"
                        data-bs-custom-class="tooltip--dashboard-sidebar">
                        <span class=" icon">
                            <i class="fas fa-bell"></i>
                        </span>
                        <span class="text">@lang('Notifications')</span>
                        <span
                            class="badge custom--badge ms-auto badge--danger">{{ $vendorNotificationCount <= 9 ? __($vendorNotificationCount) : __('9+') }}</span>
                    </button>
                    <div class="notification-area position-absolute" id="db-notification">
                        <div class="notification-area__header">
                            <h3 class="notification-area__header-title">@lang('Notifications')</h3>
                            @if (!blank($vendorNotifications))
                                <div class="notification-area__header-actions">
                                    <a href="{{ route('vendor.notifications.read.all') }}"
                                        class="btn btn-outline--light btn--sm">
                                        <i class="fa-solid fa-check-double"></i>
                                        <span class="text ms-2">@lang('Mark all as read')</span>
                                    </a>
                                </div>
                            @endif
                        </div>
                        <div class="notification-area__body">
                            <div class="notification-area__list">
                                @forelse ($vendorNotifications as $notification)
                                    <a href="{{ route('vendor.notifications.read', $notification->id) }}"
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
                                    href="{{ route('vendor.notifications.index') }}">@lang('View All Notifications')</a>
                            </div>
                        </div>
                    </div>
                </div>

                <a href="{{ route('vendor.product.attribute.index') }}"
                    class="sidebar-menu__link {{ menuActive('vendor.product.attribute*') }}" data-bs-toggle="tooltip"
                    data-bs-title="@lang('Product Attribute')" data-bs-custom-class="tooltip--dashboard-sidebar">
                    <span class="icon"><i class="fa-brands fa-dropbox"></i></span>
                    <span class="text">@lang('Product Attribute')</span>
                </a>
                <a href="{{ route('vendor.products.index') }}"
                    class="sidebar-menu__link {{ menuActive('vendor.products*') }}" data-bs-toggle="tooltip"
                    data-bs-title="@lang('Products')" data-bs-custom-class="tooltip--dashboard-sidebar">
                    <span class="icon"><i class="fa-brands fa-product-hunt"></i></span>
                    <span class="text">@lang('Products')</span>
                </a>
                <a href="{{ route('vendor.order.index') }}"
                    class="sidebar-menu__link {{ menuActive('vendor.order*') }}" data-bs-toggle="tooltip"
                    data-bs-title="@lang('Orders')" data-bs-custom-class="tooltip--dashboard-sidebar">
                    <span class="icon"><i class="fa-solid fa-cart-flatbed"></i></span>
                    <span class="text">@lang('Orders')</span>
                    <span class="badge custom--badge ms-auto badge--danger">{{ $pendingOrdersCount }}</span>
                </a>
                <a href="{{ route('vendor.withdraw.history') }}"
                    class="sidebar-menu__link {{ menuActive('vendor.withdraw*') }}" data-bs-toggle="tooltip"
                    data-bs-title="@lang('Withdrawals')" data-bs-custom-class="tooltip--dashboard-sidebar">
                    <span class="icon"><i class="fa-solid fa-wallet"></i></span>
                    <span class="text">@lang('Withdrawals')</span>
                </a>
                <a href="{{ route('vendor.transactions') }}"
                    class="sidebar-menu__link {{ menuActive('vendor.transactions') }}" data-bs-toggle="tooltip"
                    data-bs-title="@lang('Transactions')" data-bs-custom-class="tooltip--dashboard-sidebar">
                    <span class="icon"><i class="fa-solid fa-money-bill-transfer"></i></span>
                    <span class="text">@lang('Transactions')</span>
                </a>
                <a href="{{ route('vendor.reviews') }}"
                    class="sidebar-menu__link {{ menuActive('vendor.reviews') }}" data-bs-toggle="tooltip"
                    data-bs-title="@lang('Reviews')" data-bs-custom-class="tooltip--dashboard-sidebar">
                    <span class="icon"><i class="fa-solid fa-star"></i></span>
                    <span class="text">@lang('Reviews')</span>
                </a>
                <a href="{{ route('vendor.ticket.index') }}"
                    class="sidebar-menu__link {{ menuActive('vendor.ticket*') }}" data-bs-toggle="tooltip"
                    data-bs-title="@lang('Support Tickets')" data-bs-custom-class="tooltip--dashboard-sidebar">
                    <span class="icon"><i class="fa-solid fa-ticket"></i></span>
                    <span class="text">@lang('Support Tickets')</span>
                </a>
                <a href="{{ route('vendor.logout') }}" class="sidebar-menu__link" data-bs-toggle="tooltip"
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
                    <p class="account__email">{{ __(auth('vendor')->user()->email) }}</p>
                </div>
                <div class="account__icon">
                    <i class="fa-solid fa-angle-right"></i>
                </div>
            </button>
            <ul class="dropdown-menu  fade">
                <li>
                    <a class="dropdown-item" href="{{ route('vendor.profile.setting') }}">
                        <i class="fa-solid fa-user fs-15 me-2"></i>
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
</aside>
