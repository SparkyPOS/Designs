@extends($activeTemplate . 'layouts.master')
@section('content')
    <div class="card custom--card">
        <div class="card-body p-0">
            <div class="table-responsive--lg table-responsive">
                <table class="table">
                    <thead>
                        <tr>
                            <th>@lang('Gateway') | @lang('Order Number')</th>
                            <th class="text-center">@lang('Time')</th>
                            <th class="text-center">@lang('Amount')</th>
                            <th class="text-center">@lang('Conversion')</th>
                            <th class="text-center">@lang('Status')</th>
                            <th>@lang('Details')</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($deposits as $deposit)
                            <tr>
                                <td>
                                    <div>
                                        <span class="fw-bold">
                                            <span class="text-primary">
                                                @if ($deposit->method_code < 5000)
                                                    {{ __($deposit->gateway->name) }}
                                                @else
                                                    @lang('Google Pay')
                                                @endif
                                            </span>
                                        </span>
                                        <br>
                                        <small> {{ $deposit?->order?->order_number ?? null }} </small>
                                    </div>
                                </td>

                                <td class="text-center">
                                    {{ showDateTime($deposit->created_at) }}<br>{{ diffForHumans($deposit->created_at) }}
                                </td>
                                <td class="text-center">
                                    <div>
                                        {{ showAmount($deposit->amount) }} +
                                        <span class="text--danger" data-bs-toggle="tooltip" data-bs-title="@lang('Processing Charge')">
                                            {{ showAmount($deposit->charge) }}
                                        </span>
                                        <br>
                                        <span data-bs-toggle="tooltip" data-bs-title="@lang('Amount with charge')">
                                            {{ showAmount($deposit->amount + $deposit->charge) }}
                                        </span>
                                    </div>
                                </td>
                                <td class="text-center">
                                    <div>
                                        {{ showAmount(1) }} = {{ showAmount($deposit->rate, currencyFormat: false) }}
                                        {{ __($deposit->method_currency) }}
                                        <br>
                                        <span>{{ showAmount($deposit->final_amount, currencyFormat: false) }}
                                            {{ __($deposit->method_currency) }}</span>
                                    </div>
                                </td>
                                <td class="text-center">
                                    @php echo $deposit->statusBadge @endphp
                                </td>
                                @php
                                    $details = [];
                                    if ($deposit->method_code >= 1000 && $deposit->method_code <= 5000) {
                                        foreach (isset($deposit->detail) ? $deposit->detail : [] as $key => $info) {
                                            $details[] = $info;
                                            if ($info->type == 'file') {
                                                $details[$key]->value = route('user.download.attachment', encrypt(getFilePath('verify') . '/' . $info->value));
                                            }
                                        }
                                    }
                                @endphp

                                <td>
                                    @if ($deposit->method_code >= 1000 && $deposit->method_code <= 5000)
                                        <a href="javascript:void(0)" class="btn btn--base btn--xs detailBtn" data-info="{{ json_encode($details) }}" @if ($deposit->status == Status::PAYMENT_REJECT) data-admin_feedback="{{ $deposit->admin_feedback }}" @endif>
                                            <i class="las la-desktop"></i>
                                        </a>
                                    @else
                                        <button type="button" class="btn btn--base btn--xs" data-bs-toggle="tooltip" title="@lang('Automatically processed')">
                                            <i class="las la-check"></i>
                                        </button>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="100%" class="text-center">{{ __($emptyMessage) }}</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    @if ($deposits->hasPages())
        {{ paginateLinks($deposits) }}
    @endif

    {{-- APPROVE MODAL --}}
    <div id="detailModal" class="modal fade custom--modal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h3 class="modal-title">@lang('Details')</h3>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <ul class="list-group list-group--custom userData mb-2"></ul>
                    <div class="feedback"></div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn--dark btn--sm" data-bs-dismiss="modal">
                        @lang('Close')
                    </button>
                </div>
            </div>
        </div>
    </div>
@endsection
@push('breadcrumb-plugins')
    <form>
        <div class="input-group input--group">
            <input type="search" name="search" class="form-control form--control" value="{{ request()->search }}" placeholder="@lang('Search by transactions')">
            <button class="input-group-text bg--base">
                <i class="las la-search"></i>
            </button>
        </div>
    </form>
@endpush

@push('script')
    <script>
        (function($) {
            "use strict";
            $('.detailBtn').on('click', function() {
                var modal = $('#detailModal');

                var userData = $(this).data('info');
                var html = '';
                if (userData) {
                    userData.forEach(element => {
                        if (element.type != 'file') {
                            html += `
                            <li class="list-group-item d-flex justify-content-between align-items-center">
                                <span>${element.name}</span>
                                <span">${element.value}</span>
                            </li>`;
                        } else {
                            html += `
                            <li class="list-group-item d-flex justify-content-between align-items-center">
                                <span>${element.name}</span>
                                <span"><a href="${element.value}" class="text--primary"><i class="fa-regular fa-file"></i> @lang('Attachment')</a></span>
                            </li>`;
                        }
                    });
                }

                modal.find('.userData').html(html);

                if ($(this).data('admin_feedback') != undefined) {
                    var adminFeedback = `
                        <div class="my-3">
                            <strong>@lang('Admin Feedback')</strong>
                            <p>${$(this).data('admin_feedback')}</p>
                        </div>
                    `;
                } else {
                    var adminFeedback = '';
                }

                modal.find('.feedback').html(adminFeedback);


                modal.modal('show');
            });

            var tooltipTriggerList = [].slice.call(document.querySelectorAll('[title], [data-title], [data-bs-title]'))
            tooltipTriggerList.map(function(tooltipTriggerEl) {
                return new bootstrap.Tooltip(tooltipTriggerEl)
            });

        })(jQuery);
    </script>
@endpush
