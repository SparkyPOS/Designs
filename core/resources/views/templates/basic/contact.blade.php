@extends($activeTemplate . 'layouts.frontend')
@section('content')
    <section class="contact-banner my-60">
        <div class="container">
            <div class="contact-banner__inner">

                <div class="contact-banner__icon">
                    <i class="fa-solid fa-message"></i>
                </div>
                <h1 class="contact-banner__heading mb-2">
                    {{ __($contactContent?->data_values?->heading) }}
                </h1>
                <div class="contact-banner__desc">
                    {{ __($contactContent?->data_values?->subheading) }}
                </div>
                <div class="contact-banner__feature">
                    @foreach ($contactElements as $contactElement)
                        <div class="contact-banner__feature-item">
                            <span class="contact-banner__feature-icon">
                                @php
                                    echo $contactElement?->data_values?->icon;
                                @endphp
                            </span>
                            <p class="contact-banner__feature-text">{{ __($contactElement?->data_values?->title) }}</p>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    </section>
    <div class="contact-info  my-60">
        <div class="container">
            <div class="row  gy-4">
                <div class="col-lg-6">
                    <div class="contact-info__list">
                        <div class="contact-info__item">
                            <div class="desc">
                                <p class="title">@lang('Email Address')</p>
                                <a href="mailto:{{ $contactContent->data_values->email_address ?? null }}" class="link">{{ $contactContent->data_values->email_address ?? null }}</a>
                            </div>
                            <span class="icon">
                                <i class="fa-solid fa-arrow-right"></i>
                            </span>
                        </div>
                        <div class="contact-info__item">
                            <div class="desc">
                                <p class="title">@lang('Phone Number')</p>
                                <a href="tel:{{ $contactContent->data_values->contact_number ?? null }}" class="link">{{ $contactContent->data_values->contact_number ?? null }}</a>
                            </div>
                            <span class="icon">
                                <i class="fa-solid fa-arrow-right"></i>
                            </span>
                        </div>
                        <div class="contact-info__item">
                            <div class="desc">
                                <p class="title">@lang('Location')</p>
                                <a href="{{ $contactContent->data_values->map_link ?? 'javascript:void(0)' }}" target="_blank" class="link">{{ __($contactContent->data_values->contact_details ?? null) }}</a>
                            </div>
                            <span class="icon">
                                <i class="fa-solid fa-arrow-right"></i>
                            </span>
                        </div>
                    </div>
                    <div class="contact-info__social">
                        @foreach ($socialElements as $socialElement)
                            <a href="{{ $socialElement->data_values->url ?? '' }}" target="_blank" class="contact-info__social-item">
                                @php echo $socialElement->data_values->social_icon ?? ""; @endphp
                            </a>
                        @endforeach
                    </div>
                </div>
                <div class="col-lg-6">
                    <div class="contact-form">
                        <form method="post" class="row verify-gcaptcha">
                            @csrf
                            <div class="col-12">
                                <div class="form-group">
                                    <label for="name" class="form--label">@lang('Name')</label>
                                    <input type="text" name="name" class="form--control" id="name" value="{{ old('name', $user?->fullname) }}" @if ($user && $user->profile_complete) readonly @endif required>
                                </div>
                            </div>
                            <div class="col-12">
                                <div class="form-group">
                                    <label for="email" class="form--label">@lang('Email')</label>
                                    <input name="email" type="email" class="form--control" id="email" value="{{ old('email', $user?->email) }}" @if ($user) readonly @endif required>
                                </div>
                            </div>
                            <div class="col-12">
                                <div class="form-group">
                                    <label for="subject" class="form--label">@lang('Subject')</label>
                                    <input name="subject" type="text" class="form--control" id="subject" value="{{ old('subject') }}" required>
                                </div>
                            </div>
                            <div class="col-12">
                                <div class="form-group">
                                    <label for="message" class="form--label">@lang('Message')</label>
                                    <textarea name="message" class="form--control" id="message" required>{{ old('message') }}</textarea>
                                </div>
                            </div>
                            <div class="col-12">
                                <x-captcha />
                            </div>
                            <div class="col-12">
                                <div class="form-group mb-0">
                                    <button type="submit" class="btn btn--base w-100">@lang('Submit')</button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @if (isset($sections->secs) && $sections->secs != null)
        @foreach (json_decode($sections->secs) as $sec)
            @include($activeTemplate . 'sections.' . $sec)
        @endforeach
    @endif
@endsection
