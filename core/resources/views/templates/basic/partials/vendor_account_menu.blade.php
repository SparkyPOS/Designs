<nav class="setting-sidebar  overflow-auto">
    <a href="{{ route('vendor.profile.setting') }}" class="setting-sidebar__item {{ menuActive('vendor.profile.setting') }}">@lang('Profile')</a>
    <a href="{{ route('vendor.change.password') }}" class="setting-sidebar__item {{ menuActive('vendor.change.password') }}">@lang('Change Password')</a>
    <a href="{{ route('vendor.twofactor') }}" class="setting-sidebar__item {{ menuActive('vendor.twofactor') }}">@lang('2FA Security')</a>
    <a href="{{ route('vendor.delivery') }}" class="setting-sidebar__item {{ menuActive('vendor.delivery') }}">@lang('Delivery')</a>
    <a href="{{ route('vendor.design.instruction') }}" class="setting-sidebar__item {{ menuActive('vendor.design.instruction') }}">@lang('Design Instruction')</a>
</nav>
