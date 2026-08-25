@extends($activeTemplate . 'layouts.master')
@section('content')
    <div class="card custom--card">
        <div class="card-body p-0">
            <div class="table-responsive--md table-responsive">
                <table class="table">
                    <thead>
                        <tr>
                            <th>@lang('Product')</th>
                            <th class="text-center">@lang('Order Number')</th>
                            <th class="text-center">@lang('Title')</th>
                            <th class="text-center">@lang('Rating')</th>
                            <th>@lang('Action')</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($reviews as $review)
                            <tr>
                                <td>{{ __($review?->product?->name) }}</td>
                                <td>{{ $review?->order?->order_number }}</td>
                                <td>{{ __($review->title) }}</td>
                                <td>{{ getAmount($review->rating) }}</td>
                                <td>
                                    <div class="d-flex align-items-center justify-content-end gap-2">
                                        <a href="{{ route('user.review.edit', $review->id) }}"
                                            class="btn btn--base btn--xs text-nowrap">
                                            <i class="la la-pencil-alt"></i>
                                            <span class="d-none d-md-inline-block">@lang('Edit')</span>
                                        </a>
                                        <button class="btn btn--danger btn--xs text-nowrap confirmationBtn"
                                            data-action="{{ route('user.review.delete', $review->id) }}"
                                            data-question="@lang('Are you sure to delete this review?')">
                                            <i class="la la-trash"></i>
                                            <span class="d-none d-md-inline-block">@lang('Delete')</span>
                                        </button>
                                    </div>
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
    @if ($reviews->hasPages())
        {{ paginateLinks($reviews) }}
    @endif

    <x-confirmation-modal />
@endsection
@push('breadcrumb-plugins')
    <form>
        <div class="input-group input--group">
            <input type="search" name="search" class="form-control form--control" value="{{ request()->search }}"
                placeholder="@lang('Title, Order Number, Product Name')">
            <button class="input-group-text bg--base">
                <i class="las la-search"></i>
            </button>
        </div>
    </form>
@endpush
