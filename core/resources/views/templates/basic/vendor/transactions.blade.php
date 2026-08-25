@extends($activeTemplate . 'layouts.master')
@section('content')
    <form class="mb-4">
        <div class="d-flex flex-wrap gap-3">
            <div class="flex-grow-1">
                <label class="form--label">@lang('Transaction Number')</label>
                <input type="search" name="search" value="{{ request()->search }}" class="form--control">
            </div>
            <div class="flex-grow-1 select2-parent min-w-150px">
                <label class="form--label d-block">@lang('Type')</label>
                <select name="trx_type" class="form--control select2" data-minimum-results-for-search="-1">
                    <option value="">@lang('All')</option>
                    <option value="+" @selected(request()->trx_type == '+')>@lang('Plus')</option>
                    <option value="-" @selected(request()->trx_type == '-')>@lang('Minus')</option>
                </select>
            </div>
            <div class="flex-grow-1 select2-parent min-w-200px">
                <label class="form--label d-block">@lang('Remark')</label>
                <select class="form--control select2" data-minimum-results-for-search="-1" name="remark">
                    <option value="">@lang('All')</option>
                    @foreach ($remarks as $remark)
                        <option value="{{ $remark->remark }}" @selected(request()->remark == $remark->remark)>
                            {{ __(keyToTitle($remark->remark)) }}</option>
                    @endforeach
                </select>
            </div>
            <div class="flex-grow-1 align-self-end">
                <button class="btn btn--base w-100"><i class="las la-filter"></i> @lang('Filter')</button>
            </div>
        </div>
    </form>
    <div class="card custom--card">
        <div class="card-body p-0">
            <div class="table-responsive table-responsive--lg">
                <table class="table custom--table">
                    <thead>
                        <tr>
                            <th>@lang('Trx')</th>
                            <th class="text-center">@lang('Transacted')</th>
                            <th>@lang('Amount')</th>
                            <th>@lang('Post Balance')</th>
                            <th>@lang('Detail')</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($transactions as $trx)
                            <tr>
                                <td>
                                    <strong>{{ $trx->trx }}</strong>
                                </td>

                                <td>
                                    {{ showDateTime($trx->created_at) }}<br>{{ diffForHumans($trx->created_at) }}
                                </td>

                                <td>
                                    <span class="fw-bold @if ($trx->trx_type == '+') text--success @else text--danger @endif">
                                        {{ $trx->trx_type }} {{ showAmount($trx->amount) }}
                                    </span>
                                </td>

                                <td>
                                    {{ showAmount($trx->post_balance) }}
                                </td>

                                <td>{{ __($trx->details) }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td class="text-muted text-center" colspan="100%">{{ __($emptyMessage) }}
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    @if ($transactions->hasPages())
        {{ paginateLinks($transactions) }}
    @endif
@endsection

@push('style')
    <style>
        .min-w-150px {
            min-width: 150px !important;
        }
        .min-w-200px {
            min-width: 200px !important;
        }

        .select2-wrapper .select2+.select2-container {
            border-radius: var(--border-radius);
            width: 100% !important;
            height: var(--height);
        }
    </style>
@endpush
