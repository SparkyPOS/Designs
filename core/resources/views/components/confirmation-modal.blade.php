<div id="confirmationModal" class="modal fade" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                @if (request()->routeIs('admin.*'))
                    <h5 class="modal-title">@lang('Confirmation Alert!')</h5>
                @else
                    <h3 class="modal-title">@lang('Confirmation Alert!')</h3>
                @endif
                <button type="button" class="close" data-bs-dismiss="modal" aria-label="Close">
                    <i class="las la-times"></i>
                </button>
            </div>
            <form method="POST">
                @csrf
                <div class="modal-body">
                    <p class="question"></p>
                    {{ $slot }}
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn {{ request()->routeIs('admin.*') ? 'btn--dark' : 'btn--dark btn--xs' }}" data-bs-dismiss="modal">@lang('No')</button>
                    <button type="submit" class="btn {{ request()->routeIs('admin.*') ? 'btn--primary' : 'btn--base btn--xs' }}">@lang('Yes')</button>
                </div>
            </form>
        </div>
    </div>
</div>

@push('script')
    <script>
        (function($) {
            "use strict";
            $(document).on('click', '.confirmationBtn', function() {
                var modal = $('#confirmationModal');
                let data = $(this).data();
                modal.find('.question').text(`${data.question}`);
                modal.find('form').attr('action', `${data.action}`);
                modal.modal('show');
            });
        })(jQuery);
    </script>
@endpush
