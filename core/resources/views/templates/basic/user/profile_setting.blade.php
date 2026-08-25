@extends($activeTemplate . 'layouts.master')
@section('content')
    <section class="settings-container">
        <div class="row">
            <div class="col-xxxl-3 col-xxl-4">
                @include($activeTemplate . '.partials.user_account_menu')
            </div>
            <div class="col-xxxl-9 col-xxl-8">
                <form class="register" method="post" enctype="multipart/form-data">
                    @csrf
                    <div class="setting-content mb-3">
                        <div class="setting-content__details">
                            <div class="user-profile d-flex flex-wrap flex-column flex-sm-row">
                                <div class="thumb">
                                    <img id="imagePreview"
                                        src="{{ getImage(getFilePath('userProfile') . '/' . $user->image, avatar: true) }}"
                                        data-src="{{ getImage(getFilePath('userProfile') . '/' . $user->image, avatar: true) }}"
                                        class="lazyload" alt="@lang('user')">
                                    <label for="file-input" class="file-input-btn">
                                        <i class="la la-edit"></i>
                                    </label>
                                </div>
                                <div class="user-profile-content ms-3">
                                    <h3 class="title m-0">{{ $user->fullname }}</h3>
                                    <h5 class="title mb-2 text-muted">{{ $user->username }}</h5>
                                    <div class="user-profile__info d-flex flex-sm-column gap-2 gap-sm-1 flex-wrap">
                                        @if ($user->email)
                                            <div class="gap-2 d-flex">
                                                <span class="icon"><i class="las la-envelope"></i></span>
                                                <span class="text">{{ $user->email }}</span>
                                            </div>
                                        @endif

                                        @if ($user->mobileNumber)
                                            <div class="gap-2 d-flex">
                                                <span class="icon"><i class="las la-phone"></i></span>
                                                <span class="text">{{ $user->mobileNumber }}</span>
                                            </div>
                                        @endif

                                        @if ($user->country_name)
                                            <div class="gap-2 d-flex">
                                                <span class="icon"><i class="las la-globe"></i></span>
                                                <span class="text">{{ $user->country_name }}</span>
                                            </div>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="setting-content">
                        <div class="setting-content__details">
                            <input type='file' class="d-none" name="image" id="file-input" accept=".png, .jpg, .jpeg" />
                            <div class="row">
                                <div class="form-group col-sm-6">
                                    <label class="form--label">@lang('First Name')</label>
                                    <input type="text" class="form--control" name="firstname"
                                        value="{{ $user->firstname }}" required>
                                </div>
                                <div class="form-group col-sm-6">
                                    <label class="form--label">@lang('Last Name')</label>
                                    <input type="text" class="form--control" name="lastname"
                                        value="{{ $user->lastname }}" required>
                                </div>
                            </div>
                            <div class="row">
                                <div class="form-group col-sm-6">
                                    <label class="form--label">@lang('State')</label>
                                    <input type="text" class="form--control" name="state" value="{{ $user->state }}">
                                </div>
                                <div class="form-group col-sm-6">
                                    <label class="form--label">@lang('City')</label>
                                    <input type="text" class="form--control" name="city" value="{{ $user->city }}">
                                </div>
                            </div>

                            <div class="row">
                                <div class="form-group col-sm-6">
                                    <label class="form--label">@lang('Zip Code')</label>
                                    <input type="text" class="form--control" name="zip" value="{{ $user->zip }}">
                                </div>
                                <div class="form-group col-sm-6">
                                    <label class="form--label">@lang('Address')</label>
                                    <input type="text" class="form--control" name="address"
                                        value="{{ $user->address }}">
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

@push('style')
    <style>
        .user-profile .thumb img {
            height: 100%;
            width: 100%;
            object-fit: cover;
            border-radius: 50%;
            border: 1px solid hsl(var(--base) / .5);
        }

        .user-profile .thumb {
            height: 150px;
            width: 150px;
            position: relative;
            flex-shrink: 0;
        }
        @media screen and (max-width: 991px) {
            .user-profile .thumb {
                height: 120px;
                width: 120px;
            }
        }
        @media screen and (max-width: 768px) {
            .user-profile .thumb {
                height: 90px;
                width: 90px;
            }
        }

        .user-profile .thumb img {
            height: 100%;
            width: 100%;
            object-fit: cover;
            border-radius: 50%;
            border: 1px solid hsl(var(--base) / .5);
            padding: 5px;
        }

        .file-input-btn {
            position: absolute;
            right: 10px;
            bottom: 0;
            background: hsl(var(--white));
            color: hsl(var(--black));
            border-radius: 50%;
            height: 32px;
            width: 32px;
            display: grid;
            place-content: center;
            border: 1px solid hsl(var(--base) / .5);
            cursor: pointer;
        }
    </style>
@endpush

@push('script')
    <script>
        (function($) {
            'use strict';

            $("#file-input").on('change', function() {
                readURL(this);
            });

            function readURL(input) {
                if (input.files && input.files[0]) {
                    var reader = new FileReader();
                    reader.onload = function(e) {
                        $('#imagePreview').attr('src', e.target.result);
                        $('#imagePreview').hide();
                        $('#imagePreview').fadeIn(650);
                    }
                    reader.readAsDataURL(input.files[0]);
                }
            }
        })(jQuery)
    </script>
@endpush
