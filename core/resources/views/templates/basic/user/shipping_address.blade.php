@extends($activeTemplate . 'layouts.master')
@section('content')
    <section class="settings-container">
        <div class="row">
            <div class="col-xxxl-3 col-xxl-4">
                @include($activeTemplate . '.partials.user_account_menu')
            </div>
            <div class="col-xxxl-9 col-xxl-8">
                <div class="w-100">
                    <div class="setting-content__details table-responsive table-responsive--md">
                        <table class="table">
                            <thead>
                                <th>@lang('SN')</th>
                                <th class="text-center">@lang('Title')</th>
                                <th>@lang('Name')</th>
                                <th>@lang('Address')</th>
                                <th>@lang('Default')</th>
                                <th>@lang('Action')</th>
                            </thead>
                            <tbody>
                                @forelse($addresseses as $address)
                                    <tr>
                                        <td>{{ __($loop->iteration) }}</td>
                                        <td>{{ __($address->title) }}</td>
                                        <td>{{ __($address->fullname) }}</td>
                                        <td>{{ __($address->address) }}</td>
                                        <td>
                                            @if ($address->default)
                                                <span class="badge custom--badge badge--base-two">@lang('Yes')</span>
                                            @else
                                                <span class="badge custom--badge badge--secondary">@lang('No')</span>
                                            @endif
                                        </td>
                                        <td>
                                            <div class="d-flex align-items-center justify-content-end gap-2">
                                                <button class="btn btn--base btn--xs editAddressBtn" data-data="{{ $address }}">
                                                    <i class="la la-pencil-alt"></i>
                                                    <span class="d-none d-md-inline-block">@lang('Edit')</span>
                                                </button>
                                                <button class="btn btn--danger btn--xs confirmationBtn" data-question="@lang('Are you sure to delete this shipping address?')" data-action="{{ route('user.shipping.address.delete', $address->id) }}">
                                                    <i class="la la-trash"></i>
                                                    <span class="d-none d-md-inline-block">@lang('Delete')</span>
                                                </button>
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td class="text-center " colspan="100%">
                                            {{ __($emptyMessage) }}
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <div class="modal custom--modal" id="addressModal" aria-modal="true" role="dialog">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h3 class="modal-title"></h3>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form method="POST" action="">
                        @csrf
                        <div class="row">
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label class="form--label">@lang('Title')</label>
                                    <input type="text" class="form--control" name="title" required>
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="form--label">@lang('First Name')</label>
                                    <input type="text" class="form--control" name="firstname" required>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="form--label">@lang('Last Name')</label>
                                    <input type="text" class="form--control" name="lastname" required>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="form--label">@lang('Mobile')</label>
                                    <div class="input-group ">
                                        <span class="input-group-text mobile-code"></span>
                                        <input type="hidden" name="mobile_code">
                                        <input type="hidden" name="country_code">
                                        <input type="number" name="mobile" value="" class="form-control form--control" required>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="form--label">@lang('Email')</label>
                                    <input type="text" class="form--control" name="email" required>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="form--label">@lang('City')</label>
                                    <input type="text" class="form--control" name="city" required>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="form--label">@lang('State')</label>
                                    <input type="text" class="form--control" name="state" required>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="form--label">@lang('Zip')</label>
                                    <input type="text" class="form--control" name="zip" required>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="form--label">@lang('Country')</label>
                                    <select name="country" class="form--control  select2">
                                        <option value="" selected disabled>@lang('Select One')</option>
                                        @foreach ($countries as $key => $country)
                                            <option data-mobile_code="{{ $country->dial_code }}" value="{{ $country->country }}" data-code="{{ $key }}">
                                                {{ __($country->country) }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>

                            <div class="col-md-12">
                                <div class="form-group">
                                    <label class="form--label">@lang('Address')</label>
                                    <input class="form--control" name="address" required>
                                </div>
                            </div>
                            <div class="col-md-12">
                                <div class="form-group mb-0">
                                    <div class="form-check form--check d-flex align-items-center">
                                        <input class="form-check-input" type="checkbox" id="checkDefault" name="default">
                                        <label class="form-check-label fs-18" for="checkDefault">
                                            @lang('Default Shippng Address')
                                        </label>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-12 mt-3">
                            <button type="submit" class="btn btn--base w-100">@lang('Submit')</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <x-confirmation-modal />
@endsection

@push('breadcrumb-plugins')
    <button class="btn btn--base addNewAddressBtn">
        <i class="las la-plus"></i>
        @lang('Add New')
    </button>
@endpush

@push('script')
    <script>
        (function($) {
            "use strict";
            const modal = $('#addressModal');
            @if ($mobileCode)
                $(`option[data-code={{ $mobileCode }}]`).attr('selected', '');
            @endif


            $('select[name=country]').on('change', function() {
                let mobileCode = $('select[name=country] :selected').data('mobile_code');
                let countryCode = $('select[name=country] :selected').data('code');
                $('input[name=mobile_code]').val((mobileCode != undefined ? mobileCode : ""));
                $('input[name=country_code]').val((countryCode != undefined) ? countryCode : "");
                $('.mobile-code').text('+' + (mobileCode != undefined ? mobileCode : ""));
            });

            $('.addNewAddressBtn').on('click', function() {
                modal.find('form')[0]?.reset();
                modal.find('.modal-title').text('@lang('Add New Shipping Address')');
                modal.find('form').attr('action', "{{ route('user.shipping.address.store') }}");
                modal.find('form').find("[name='country']").val("").trigger('change');
                modal.modal('show');
            });

            $('.editAddressBtn').on('click', function() {
                const data = $(this).data('data');

                modal.find('form')[0]?.reset();
                modal.find('.modal-title').text('@lang('Update Shipping Address')');
                modal.find('form').attr('action', `{{ route('user.shipping.address.store') }}/${ data.id }`);
                modal.find('form').find("[name='title']").val(data.title);
                modal.find('form').find("[name='firstname']").val(data.firstname);
                modal.find('form').find("[name='lastname']").val(data.lastname);
                modal.find('form').find("[name='mobile_code']").val(data.mobile_code);
                modal.find('form').find("[name='mobile']").val(data.mobile);
                modal.find('form').find("[name='email']").val(data.email);
                modal.find('form').find("[name='city']").val(data.city);
                modal.find('form').find("[name='state']").val(data.state);
                modal.find('form').find("[name='zip']").val(data.zip);
                modal.find('form').find("[name='country']").val(data.country).trigger('change');
                modal.find('form').find("[name='address']").val(data.address);
                modal.find('form').find("[name='default']").prop('checked', data.default);

                modal.modal('show');
            });
        })(jQuery);
    </script>
@endpush
