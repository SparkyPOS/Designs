<nav class="setting-sidebar  overflow-auto">
    <a href="{{ route('user.profile.setting') }}" class="setting-sidebar__item {{ menuActive('user.profile.setting') }}">@lang('Profile')</a>
    <a href="{{ route('user.change.password') }}" class="setting-sidebar__item {{ menuActive('user.change.password') }}">@lang('Change Password')</a>
    <a href="{{ route('user.twofactor') }}" class="setting-sidebar__item {{ menuActive('user.twofactor') }}">@lang('2FA Security')</a>
    <a href="{{ route('user.shipping.address') }}" class="setting-sidebar__item {{ menuActive('user.shipping.address') }}">@lang('Shipping Address')</a>
</nav>
