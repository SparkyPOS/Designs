@extends('admin.layouts.app')

@section('panel')
    <div class="row">
        <div class="col-12">
            <div class="row gy-4 justify-content-center">

                <div class="col-xl-3 col-sm-6">
                    <x-widget style="2" cover_cursor="1" color="success" icon="la la-shopping-cart" link="{{ route('admin.report.transaction', $vendor->id) }}" value="{{ showAmount($vendor->balance) }}" title="Balance" icon_style="solid" overlay_icon="0" />
                </div>
                <div class="col-xl-3 col-sm-6">
                    <x-widget style="2" cover_cursor="1" color="primary" icon="la la-shopping-cart" link="{{ route('admin.order.index') }}?search={{ $vendor->username }}" value="{{ $widget['totalOrders'] }}" title="Total Orders" icon_style="solid" overlay_icon="0" />
                </div>

                <div class="col-xl-3 col-sm-6">
                    <x-widget style="2" cover_cursor="1" color="danger" icon="la la-times-circle" link="{{ route('admin.order.pending') }}?search={{ $vendor->username }}" value="{{ $widget['canceledOrders'] }}" title="Canceled Orders" icon_style="solid" overlay_icon="0" />
                </div>

                <div class="col-xl-3 col-sm-6">
                    <x-widget style="2" cover_cursor="1" color="warning" icon="la la-pause-circle" link="{{ route('admin.order.pending') }}?search={{ $vendor->username }}" value="{{ $widget['pendingOrders'] }}" title="Pending Orders" icon_style="solid" overlay_icon="0" />
                </div>

                <div class="col-xl-3 col-sm-6">
                    <x-widget style="2" cover_cursor="1" color="info" icon="la la-play" link="{{ route('admin.order.pending') }}?search={{ $vendor->username }}" value="{{ $widget['processingOrders'] }}" title="On Processing Orders" icon_style="solid" overlay_icon="0" />
                </div>

                <div class="col-xl-3 col-sm-6">
                    <x-widget style="2" cover_cursor="1" color="green" icon="la la-truck" link="{{ route('admin.order.pending') }}?search={{ $vendor->username }}" value="{{ $widget['dispatchedOrders'] }}" title="Dispatched Orders" icon_style="solid" overlay_icon="0" />
                </div>

                <div class="col-xl-3 col-sm-6">
                    <x-widget style="2" cover_cursor="1" color="success" icon="la la-check-circle" link="{{ route('admin.order.delivered') }}?search={{ $vendor->username }}" value="{{ $widget['deliveredOrders'] }}" title="Delivered Orders" icon_style="solid" overlay_icon="0" />
                </div>

                <div class="col-xl-3 col-sm-6">
                    <x-widget style="2" color="success" icon="la la-shopping-bag" value="{{ showAmount($widget['totalOrderAmount']) }}" title="Total Shopping" icon_style="solid" overlay_icon="0" />
                </div>

                <div class="col-xl-3 col-sm-6">
                    <x-widget style="2" link="{{ route('admin.order.index') }}?search={{ $vendor->username }}" cover_cursor="1" color="success" icon="la la-wallet" value="{{ showAmount($widget['totalOrderCommission']) }}" title="Order Commissions" icon_style="solid" overlay_icon="0" />
                </div>

                <div class="col-xl-3 col-sm-6">
                    <x-widget style="2" cover_cursor="1" color="warning" icon="la la-wallet" link="{{ route('admin.withdraw.data.pending') }}?search={{ $vendor->username }}" value="{{ $widget['pendingWithdraws'] }}" title="Pending Withdraws" icon_style="solid" overlay_icon="0" />
                </div>

                <div class="col-xl-3 col-sm-6">
                    <x-widget style="2" cover_cursor="1" color="danger" icon="la la-wallet" link="{{ route('admin.withdraw.data.rejected') }}?search={{ $vendor->username }}" value="{{ $widget['rejectedWithdraws'] }}" title="Rejected Withdraws" icon_style="solid" overlay_icon="0" />
                </div>

                <div class="col-xl-3 col-sm-6">
                    <x-widget style="2" cover_cursor="1" color="dark" icon="la la-money-bill" link="{{ route('admin.withdraw.data.approved') }}?search={{ $vendor->username }}" value="{{ showAmount($widget['totalWithdraw']) }}" title="Total Withdraws" icon_style="solid" overlay_icon="0" />
                </div>

                <div class="col-xl-3 col-sm-6">
                    <x-widget style="2" cover_cursor="1" color="warning" icon="la la-ticket" link="{{ route('admin.ticket.pending') }}?search={{ $vendor->username }}" value="{{ $widget['pendingTickets'] }}" title="Pending Tickets" icon_style="solid" overlay_icon="0" />
                </div>

                <div class="col-xl-3 col-sm-6">
                    <x-widget style="2" cover_cursor="1" color="success" icon="la la-ticket" link="{{ route('admin.ticket.closed') }}?search={{ $vendor->username }}" value="{{ $widget['closedTickets'] }}" title="Closed Tickets" icon_style="solid" overlay_icon="0" />
                </div>

                <div class="col-xl-3 col-sm-6">
                    <x-widget style="2" cover_cursor="1" color="info" icon="la la-ticket" link="{{ route('admin.ticket.index') }}?search={{ $vendor->username }}" value="{{ $widget['totalTickets'] }}" title="Total Tickets" icon_style="solid" overlay_icon="0" />
                </div>
                <div class="col-xl-3 col-sm-6">
                    <x-widget style="2" cover_cursor="1" color="primary" icon="la la-exchange-alt" link="{{ route('admin.report.transaction', $vendor->id) }}" value="{{ $widget['totalTransaction'] }}" title="Total Transactions" icon_style="solid" overlay_icon="0" />
                </div>
                <!-- dashboard-w1 end -->
            </div>

            <div class="d-flex flex-wrap gap-3 mt-4">
                <div class="flex-fill">
                    <button data-bs-toggle="modal" data-bs-target="#addSubModal" class="btn btn--success btn--shadow w-100 btn-lg bal-btn" data-act="add">
                        <i class="las la-plus-circle"></i> @lang('Balance')
                    </button>
                </div>

                <div class="flex-fill">
                    <button data-bs-toggle="modal" data-bs-target="#addSubModal" class="btn btn--danger btn--shadow w-100 btn-lg bal-btn" data-act="sub">
                        <i class="las la-minus-circle"></i> @lang('Balance')
                    </button>
                </div>

                <div class="flex-fill">
                    <a href="{{route('admin.report.vendor.login.history')}}?search={{ $vendor->username }}" class="btn btn--primary btn--shadow w-100 btn-lg">
                        <i class="las la-list-alt"></i>@lang('Logins')
                    </a>
                </div>

                <div class="flex-fill">
                    <a href="{{ route('admin.vendors.notification.log',$vendor->id) }}" class="btn btn--secondary btn--shadow w-100 btn-lg">
                        <i class="las la-bell"></i>@lang('Notifications')
                    </a>
                </div>

                @if($vendor->kyc_data)
                <div class="flex-fill">
                    <a href="{{ route('admin.vendors.kyc.details', $vendor->id) }}" target="_blank" class="btn btn--dark btn--shadow w-100 btn-lg">
                        <i class="las la-user-check"></i>@lang('KYC Data')
                    </a>
                </div>
                @endif

                <div class="flex-fill">
                    @if($vendor->status == Status::USER_ACTIVE)
                    <button type="button" class="btn btn--warning btn--shadow w-100 btn-lg userStatus" data-bs-toggle="modal" data-bs-target="#userStatusModal">
                        <i class="las la-ban"></i>@lang('Ban Vendor')
                    </button>
                    @else
                    <button type="button" class="btn btn--success btn--shadow w-100 btn-lg userStatus" data-bs-toggle="modal" data-bs-target="#userStatusModal">
                        <i class="las la-undo"></i>@lang('Unban Vendor')
                    </button>
                    @endif
                </div>
            </div>


            <div class="card mt-30">
                <div class="card-header">
                    <h5 class="card-title mb-0">@lang('Information of') {{$vendor->fullname}}</h5>
                </div>
                <div class="card-body">
                    <form action="{{route('admin.vendors.update',[$vendor->id])}}" method="POST"
                          enctype="multipart/form-data">
                        @csrf

                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>@lang('First Name')</label>
                                    <input class="form-control" type="text" name="firstname" required value="{{$vendor->firstname}}">
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="form-control-label">@lang('Last Name')</label>
                                    <input class="form-control" type="text" name="lastname" required value="{{$vendor->lastname}}">
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>@lang('Email')</label>
                                    <input class="form-control" type="email" name="email" value="{{$vendor->email}}" required>
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>@lang('Mobile Number')</label>
                                    <div class="input-group ">
                                        <span class="input-group-text mobile-code">+{{ $vendor->dial_code }}</span>
                                        <input type="number" name="mobile" value="{{ $vendor->mobile }}" id="mobile" class="form-control checkUser" required>
                                    </div>
                                </div>
                            </div>

                            <div class="col-md-12">
                                <div class="form-group ">
                                    <label>@lang('Address')</label>
                                    <input class="form-control" type="text" name="address" value="{{$vendor->address}}">
                                </div>
                            </div>

                            <div class="col-xl-4 col-md-6">
                                <div class="form-group">
                                    <label>@lang('Company Name')</label>
                                    <input class="form-control" type="text" readonly value="{{$vendor->company_name}}">
                                </div>
                            </div>
                            <div class="col-xl-4 col-md-6">
                                <div class="form-group">
                                    <label>@lang('Delivery Days')</label>
                                    <input class="form-control" type="text" readonly value="{{$vendor->delivery_days}}">
                                </div>
                            </div>
                            <div class="col-xl-4 col-md-6">
                                <div class="form-group">
                                    <label>@lang('Shipping Fee')</label>
                                    <div class="input-group ">
                                        <span class="input-group-text mobile-code">{{ gs('cur_sym') }}</span>
                                        <input type="number" value="{{ getAmount($vendor->shipping_fee) }}" id="mobile" class="form-control" readonly>
                                    </div>
                                </div>
                            </div>
                            <div class="col-xl-3 col-md-6">
                                <div class="form-group">
                                    <label>@lang('City')</label>
                                    <input class="form-control" type="text" name="city" value="{{$vendor->city}}">
                                </div>
                            </div>

                            <div class="col-xl-3 col-md-6">
                                <div class="form-group ">
                                    <label>@lang('State')</label>
                                    <input class="form-control" type="text" name="state" value="{{$vendor->state}}">
                                </div>
                            </div>

                            <div class="col-xl-3 col-md-6">
                                <div class="form-group ">
                                    <label>@lang('Zip/Postal')</label>
                                    <input class="form-control" type="text" name="zip" value="{{$vendor->zip}}">
                                </div>
                            </div>

                            <div class="col-xl-3 col-md-6">
                                <div class="form-group ">
                                    <label>@lang('Country') <span class="text--danger">*</span></label>
                                    <select name="country" class="form-control select2">
                                        @foreach($countries as $key => $country)
                                            <option data-mobile_code="{{ $country->dial_code }}" value="{{ $key }}" @selected($vendor->country_code == $key)>{{ __($country->country) }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>


                            <div class="col-xl-3 col-md-6 col-12">
                                <div class="form-group">
                                    <label>@lang('Email Verification')</label>
                                    <input type="checkbox" data-width="100%" data-onstyle="-success" data-offstyle="-danger"
                                           data-bs-toggle="toggle" data-on="@lang('Verified')" data-off="@lang('Unverified')" name="ev"
                                           @if($vendor->ev) checked @endif>
                                </div>
                            </div>

                            <div class="col-xl-3 col-md-6 col-12">
                                <div class="form-group">
                                    <label>@lang('Mobile Verification')</label>
                                    <input type="checkbox" data-width="100%" data-onstyle="-success" data-offstyle="-danger"
                                           data-bs-toggle="toggle" data-on="@lang('Verified')" data-off="@lang('Unverified')" name="sv"
                                           @if($vendor->sv) checked @endif>
                                </div>
                            </div>
                            <div class="col-xl-3 col-12">
                                <div class="form-group">
                                    <label>@lang('2FA Verification') </label>
                                    <input type="checkbox" data-width="100%" data-height="50" data-onstyle="-success" data-offstyle="-danger" data-bs-toggle="toggle" data-on="@lang('Enable')" data-off="@lang('Disable')" name="ts" @if($vendor->ts) checked @endif>
                                </div>
                            </div>
                            <div class="col-xl-3 col-12">
                                <div class="form-group">
                                    <label>@lang('KYC') </label>
                                    <input type="checkbox" data-width="100%" data-height="50" data-onstyle="-success" data-offstyle="-danger" data-bs-toggle="toggle" data-on="@lang('Verified')" data-off="@lang('Unverified')" name="kv" @if($vendor->kv == Status::KYC_VERIFIED) checked @endif>
                                </div>
                            </div>
                            <div class="col-md-12">
                                <button type="submit" class="btn btn--primary w-100 h-45">@lang('Submit')
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>



    {{-- Add Sub Balance MODAL --}}
    <div id="addSubModal" class="modal fade" tabindex="-1" role="dialog">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title"><span class="type"></span> <span>@lang('Balance')</span></h5>
                    <button type="button" class="close" data-bs-dismiss="modal" aria-label="Close">
                        <i class="las la-times"></i>
                    </button>
                </div>
                <form action="{{route('admin.vendors.add.sub.balance',$vendor->id)}}" class="balanceAddSub disableSubmission" method="POST">
                    @csrf
                    <input type="hidden" name="act">
                    <div class="modal-body">
                        <div class="form-group">
                            <label>@lang('Amount')</label>
                            <div class="input-group">
                                <input type="number" step="any" name="amount" class="form-control" placeholder="@lang('Please provide positive amount')" required>
                                <div class="input-group-text">{{ __(gs('cur_text')) }}</div>
                            </div>
                        </div>
                        <div class="form-group">
                            <label>@lang('Remark')</label>
                            <textarea class="form-control" placeholder="@lang('Remark')" name="remark" rows="4" required></textarea>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="submit" class="btn btn--primary h-45 w-100">@lang('Submit')</button>
                    </div>
                </form>
            </div>
        </div>
    </div>


    <div id="userStatusModal" class="modal fade" tabindex="-1" role="dialog">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">
                        @if($vendor->status == Status::USER_ACTIVE) @lang('Ban Vendor') @else @lang('Unban Vendor') @endif
                    </h5>
                    <button type="button" class="close" data-bs-dismiss="modal" aria-label="Close">
                        <i class="las la-times"></i>
                    </button>
                </div>
                <form action="{{route('admin.vendors.status',$vendor->id)}}" method="POST">
                    @csrf
                    <div class="modal-body">
                        @if($vendor->status == Status::USER_ACTIVE)
                        <h6 class="mb-2">@lang('If you ban this vendor he/she won\'t able to access his/her dashboard.')</h6>
                        <div class="form-group">
                            <label>@lang('Reason')</label>
                            <textarea class="form-control" name="reason" rows="4" required></textarea>
                        </div>
                        @else
                        <p><span>@lang('Ban reason was'):</span></p>
                        <p>{{ $vendor->ban_reason }}</p>
                        <h4 class="text-center mt-3">@lang('Are you sure to unban this vendor?')</h4>
                        @endif
                    </div>
                    <div class="modal-footer">
                        @if($vendor->status == Status::USER_ACTIVE)
                        <button type="submit" class="btn btn--primary h-45 w-100">@lang('Submit')</button>
                        @else
                        <button type="button" class="btn btn--dark" data-bs-dismiss="modal">@lang('No')</button>
                        <button type="submit" class="btn btn--primary">@lang('Yes')</button>
                        @endif
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection

@push('breadcrumb-plugins')
    <a href="{{route('admin.vendors.login',$vendor->id)}}" target="_blank" class="btn btn-sm btn-outline--primary" ><i class="las la-sign-in-alt"></i>@lang('Login as Vendor')</a>
@endpush

@push('script')
<script>
    (function($){
    "use strict"


        $('.bal-btn').on('click',function(){

            $('.balanceAddSub')[0].reset();

            var act = $(this).data('act');
            $('#addSubModal').find('input[name=act]').val(act);
            if (act == 'add') {
                $('.type').text('Add');
            }else{
                $('.type').text('Subtract');
            }
        });

        let mobileElement = $('.mobile-code');
        $('select[name=country]').on('change',function(){
            mobileElement.text(`+${$('select[name=country] :selected').data('mobile_code')}`);
        });

    })(jQuery);
</script>
@endpush
