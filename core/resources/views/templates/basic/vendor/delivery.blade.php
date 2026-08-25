@extends($activeTemplate . 'layouts.master')
@section('content')
    <section class="settings-container">
        <div class="row">
            <div class="col-xxxl-3 col-xxl-4">
                @include($activeTemplate . '.partials.vendor_account_menu')
            </div>
            <div class="col-xxxl-5 col-xxl-4">
                <form class="register" method="post" enctype="multipart/form-data">
                    @csrf
                    <div class="setting-content">
                        <div class="setting-content__details">
                            <div class="row">
                                <div class="form-group col-12">
                                    <label class="form--label">@lang('Delivery Days')</label>
                                    <div class="input-group input--group">
                                        <span class="input-group-text"><i class="las la-calendar-day"></i></span>
                                        <input type="number" class="form-control form--control" name="delivery_days" value="{{ getAmount($deliveryDays) }}" required>
                                    </div>
                                </div>
                                <div class="form-group col-12">
                                    <label class="form--label">@lang('Shipping Fee')</label>
                                    <div class="input-group input--group">
                                        <span class="input-group-text">{{ gs('cur_sym') }}</span>
                                        <input type="number" class="form-control form--control" name="shipping_fee" value="{{ getAmount($shippingFee) }}" required>
                                    </div>
                                </div>
                            </div>

                            <div>
                                <button type="submit" class="btn btn--base w-100">@lang('Submit')</button>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </section>
@endsection

