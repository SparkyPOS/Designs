@extends('admin.layouts.app')
@section('panel')
    <div class="row">
        <div class="col-lg-12">
            <div class="card">
                <div class="card-body p-0">
                    <div class="table-responsive--md  table-responsive">
                        <table class="table table--light style--two">
                            <thead>
                                <tr>
                                    <th>@lang('Vendor')</th>
                                    <th>@lang('Email-Mobile')</th>
                                    <th>@lang('Country')</th>
                                    <th>@lang('Joined At')</th>
                                    <th>@lang('Balance')</th>
                                    <th>@lang('Action')</th>
                                </tr>
                            </thead>
                            <tbody>
                            @forelse($vendors as $vendor)
                            <tr>
                                <td>
                                    <span class="fw-bold">{{$vendor->fullname}}</span>
                                    <br>
                                    <span class="small">
                                    <a href="{{ route('admin.vendors.detail', $vendor->id) }}"><span>@</span>{{ $vendor->username }}</a>
                                    </span>
                                </td>


                                <td>
                                    {{ $vendor->email }}<br>{{ $vendor->mobileNumber }}
                                </td>
                                <td>
                                    <span class="fw-bold" title="{{ $vendor->country_name }}">{{ $vendor->country_code }}</span>
                                </td>



                                <td>
                                    {{ showDateTime($vendor->created_at) }} <br> {{ diffForHumans($vendor->created_at) }}
                                </td>


                                <td>
                                    <span class="fw-bold">

                                    {{ showAmount($vendor->balance) }}
                                    </span>
                                </td>

                                <td>
                                    <div class="button--group">
                                        <a href="{{ route('admin.vendors.detail', $vendor->id) }}" class="btn btn-sm btn-outline--primary">
                                            <i class="las la-desktop"></i> @lang('Details')
                                        </a>
                                        @if (request()->routeIs('admin.vendors.kyc.pending'))
                                        <a href="{{ route('admin.vendors.kyc.details', $vendor->id) }}" target="_blank" class="btn btn-sm btn-outline--dark">
                                            <i class="las la-user-check"></i>@lang('KYC Data')
                                        </a>
                                        @endif
                                    </div>
                                </td>

                            </tr>
                            @empty
                                <tr>
                                    <td class="text-muted text-center" colspan="100%">{{ __($emptyMessage) }}</td>
                                </tr>
                            @endforelse

                            </tbody>
                        </table><!-- table end -->
                    </div>
                </div>
                @if ($vendors->hasPages())
                <div class="card-footer py-4">
                    {{ paginateLinks($vendors) }}
                </div>
                @endif
            </div>
        </div>


    </div>
@endsection

@push('breadcrumb-plugins')
    <x-search-form placeholder="Username / Email" />
@endpush
