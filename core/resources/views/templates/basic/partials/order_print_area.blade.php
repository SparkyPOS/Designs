@php
    $selectedArea = json_decode($printArea->productPrintArea->selected_area ?? null);
@endphp
<div class="col-xxl-6">
    <div class="card custom--card printarea--card">
        <div class="card-header variant-item d-flex align-items-center justify-content-between flex-wrap gap-2">
            <h3 class="card-title mb-0">{{ __($printArea->productPrintArea->name ?? null) }}</h3>
            <div class="d-flex align-items-center gap-3">
                <a class="toggle-btn  expandPrintAreaBtn" data-bs-toggle="collapse"
                    href="#printSideArea{{ $loop->index }}" role="button" aria-expanded="true"
                    aria-controls="printSideArea{{ $loop->index }}">
                    <i class="las la-angle-down"></i>
                </a>
            </div>
        </div>
        <div class="card-body printSideAreaItem collapse show" id="printSideArea{{ $loop->index }}">
            <div class="row gy-4">
                <div class="col-md-12">
                    <div class="row gx-5">
                        <div class="col-12">
                            <div class="form-group">
                                <input type="hidden" class="form--control" name="selected_area[]"
                                    value="{{ $printArea->selected_area_design }}" required>
                                <div class="d-flex justify-content-center">
                                    <div class="designer-canvas position-relative w-100">
                                        <canvas class="canvas border"
                                            data-selected="{{ $printArea->productPrintArea->selected_area ?? null }}"
                                            data-src="{{ getImage(getFilePath('printArea') . '/' . $printArea->productPrintArea->image ?? null) }}"
                                            width="600" height="400">
                                        </canvas>
                                        <div class="canvas-position" data-bs-toggle="tooltip" data-bs-placement="right"
                                            title="@lang('Move Mode')">
                                            <button type="button"
                                                class="btn canvas-position__btn move-mode-toggle"
                                                data-action="toggleMove">
                                                <span class="move-mode-not-active">
                                                    <svg width="30" height="30" viewBox="0 0 40 40"
                                                        fill="none" xmlns="http://www.w3.org/2000/svg"
                                                        style="stroke-width: 0;">
                                                        <path
                                                            d="M23.6142 16.6667C24.0475 16.4881 24.5462 16.5868 24.8778 16.9168L27.1966 19.2245C27.6494 19.6751 27.6494 20.4057 27.1966 20.8563C26.7438 21.3069 26.0097 21.3069 25.5569 20.8563L25.2174 20.5183V24.6557H29.3748L29.0352 24.3178C28.5824 23.8672 28.5824 23.1366 29.0352 22.686C29.488 22.2354 30.2221 22.2354 30.6749 22.686L32.9937 24.9937C33.4465 25.4443 33.4465 26.1749 32.9937 26.6255L30.6749 28.9332C30.2221 29.3838 29.488 29.3838 29.0352 28.9332C28.5824 28.4826 28.5824 27.752 29.0352 27.3014L29.3748 26.9634H25.2174V31.1009L25.5569 30.7629C26.0097 30.3123 26.7438 30.3123 27.1966 30.7629C27.6494 31.2135 27.6494 31.9441 27.1966 32.3947L24.8778 34.7024C24.425 35.153 23.6909 35.153 23.2381 34.7024L20.9193 32.3947C20.4665 31.9441 20.4665 31.2135 20.9193 30.7629C21.372 30.3123 22.1061 30.3123 22.5589 30.7629L22.8985 31.1009V26.9634H18.7411L19.0807 27.3014C19.5334 27.752 19.5334 28.4826 19.0807 28.9332C18.6279 29.3838 17.8938 29.3838 17.441 28.9332L15.1222 26.6255C14.7906 26.2955 14.6914 25.7992 14.8708 25.368C15.0503 24.9368 15.473 24.6557 15.942 24.6557H22.8985V17.7327C22.8985 17.266 23.181 16.8453 23.6142 16.6667Z"
                                                            fill="currentColor"></path>
                                                        <path fill-rule="evenodd" clip-rule="evenodd"
                                                            d="M14.2029 5C11.3214 5 8.98547 7.32468 8.98547 10.1923V10.7692C8.98547 11.4065 9.50456 11.9231 10.1449 11.9231C10.7852 11.9231 11.3043 11.4065 11.3043 10.7692V10.1923C11.3043 8.59918 12.602 7.30769 14.2029 7.30769C15.8037 7.30769 17.1014 8.59918 17.1014 10.1923V13.3333H8.98547C7.70481 13.3333 6.66663 14.3665 6.66663 15.641V21.0256C6.66663 22.3001 7.70481 23.3333 8.98547 23.3333H19.4203C20.7009 23.3333 21.7391 22.3001 21.7391 21.0256V15.641C21.7391 14.3665 20.7009 13.3333 19.4203 13.3333V10.1923C19.4203 7.32468 17.0843 5 14.2029 5ZM8.98547 15.641V21.0256H19.4203V15.641H8.98547Z"
                                                            fill="currentColor"></path>
                                                    </svg>
                                                </span>
                                                <span class="move-mode-active">
                                                    <svg width="30" height="30" viewBox="0 0 40 40"
                                                        fill="none" xmlns="http://www.w3.org/2000/svg">
                                                        <path fill-rule="evenodd" clip-rule="evenodd"
                                                            d="M13.8406 5C10.779 5 8.29715 7.41766 8.29715 10.4L8.29714 13.3333C6.93644 13.3333 5.83337 14.4078 5.83337 15.7333L5.83338 20.9333C5.83338 22.2588 6.93644 23.3333 8.29715 23.3333H19.3841C20.7448 23.3333 21.8479 22.2588 21.8479 20.9333L21.8479 15.7333C21.8479 14.4079 20.7448 13.3333 19.3841 13.3333L19.3841 10.4C19.3841 7.41766 16.9022 5 13.8406 5ZM16.9203 10.4L16.9203 13.3333H10.7609L10.7609 10.4C10.7609 8.74315 12.1397 7.4 13.8406 7.4C15.5415 7.4 16.9203 8.74315 16.9203 10.4Z"
                                                            fill="currentColor"></path>
                                                        <path
                                                            d="M25.1827 16.1515C24.8304 15.8083 24.3005 15.7056 23.8402 15.8914C23.3799 16.0771 23.0798 16.5147 23.0798 17V24.2H15.6884C15.1902 24.2 14.741 24.4924 14.5503 24.9408C14.3597 25.3892 14.4651 25.9053 14.8174 26.2485L17.2811 28.6485C17.7622 29.1172 18.5422 29.1172 19.0233 28.6485C19.5044 28.1799 19.5044 27.4201 19.0233 26.9515L18.6625 26.6H23.0798V30.9029L22.7189 30.5515C22.2379 30.0828 21.4579 30.0828 20.9768 30.5515C20.4957 31.0201 20.4957 31.7799 20.9768 32.2485L23.4406 34.6485C23.9216 35.1172 24.7016 35.1172 25.1827 34.6485L27.6465 32.2485C28.1276 31.7799 28.1276 31.0201 27.6465 30.5515C27.1654 30.0828 26.3854 30.0828 25.9043 30.5515L25.5435 30.9029V26.6H29.9608L29.6 26.9515C29.1189 27.4201 29.1189 28.1799 29.6 28.6485C30.0811 29.1172 30.8611 29.1172 31.3421 28.6485L33.8059 26.2485C34.287 25.7799 34.287 25.0201 33.8059 24.5515L31.3421 22.1515C30.8611 21.6828 30.0811 21.6828 29.6 22.1515C29.1189 22.6201 29.1189 23.3799 29.6 23.8485L29.9608 24.2H25.5435V19.8971L25.9043 20.2486C26.3854 20.7172 27.1654 20.7172 27.6465 20.2486C28.1276 19.7799 28.1276 19.0201 27.6465 18.5515L25.1827 16.1515Z"
                                                            fill="currentColor"></path>
                                                    </svg>
                                                </span>
                                            </button>
                                        </div>
                                        <div class="canvas-zoom">
                                            <button type="button" data-action="zoomIn"
                                                class="btn canvas-zoom__btn zoom-in">
                                                <i class="las la-plus"></i>
                                            </button>
                                            <button type="button" class="btn canvas-zoom__btn zoom-reset">
                                                100%
                                            </button>
                                            <button type="button" data-action="zoomOut"
                                                class="btn canvas-zoom__btn zoom-out">
                                                <i class="las la-minus"></i>
                                            </button>
                                        </div>
                                        <div class="canvas-fit">
                                            <button type="button" data-action="fitFullScreen"
                                                class="btn canvas-fit__btn" data-bs-toggle="tooltip"
                                                data-bs-placement="top" title="@lang('Fit Design')">
                                                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24"
                                                    viewBox="0 0 24 24" fill="none" class="injected-svg"
                                                    data-src="https://cdn.hugeicons.com/icons/square-arrow-expand-01-stroke-sharp.svg?v=2.0"
                                                    xmlns:xlink="http://www.w3.org/1999/xlink" role="img"
                                                    color="#000000">
                                                    <path
                                                        d="M13.0072 11.0047L16.7596 7.25221M17.006 11.0047V7.00586H13.0072M11.0048 13.0071L7.31146 16.7004M7.00598 13.0071V17.0059H11.0048"
                                                        stroke="#000000" stroke-width="1"></path>
                                                    <path d="M21 3V21H3V3H21Z" stroke="#000000" stroke-width="1"
                                                        stroke-linejoin="round"></path>
                                                </svg>
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        @if (request()->routeIs('vendor.*'))
                            <div class="col-md-12">
                                <button class="download-full-design btn btn-outline--base-two w-100 btn--sm mb-3">
                                    <i class="fa-regular fa-floppy-disk me-2"></i>
                                    @lang('Download Full Design')
                                </button>
                                <div class="info-address-list design-inspector">
                                    <ul class="props">
                                        <li>
                                            <strong class="title">@lang('Type/Shape') </strong>
                                            <span>
                                                <span class="divide-colon">:</span>
                                                <span class="prop-type"></span>
                                            </span>
                                        </li>
                                        <li><strong class="title">@lang('Text')</strong>
                                            <span class="divide-colon">:</span>
                                            <span class="prop-text"></span>
                                        </li>
                                        <li><strong class="title">@lang('Font Size')</strong>
                                            <span class="divide-colon">:</span>
                                            <span class="prop-font-size"></span>
                                        </li>
                                        <li><strong class="title">@lang('Font Family')</strong>
                                            <span class="divide-colon">:</span>
                                            <span class="prop-font-family"></span>
                                        </li>
                                        <li><strong class="title">@lang('Color')</strong>
                                            <span class="divide-colon">:</span>
                                            <span class="prop-color"></span>
                                        </li>
                                        <li><strong class="title">@lang('Bold')</strong>
                                            <span class="divide-colon">:</span>
                                            <span class="prop-bold"></span>
                                        </li>
                                        <li><strong class="title">@lang('Underline')</strong>
                                            <span class="divide-colon">:</span>
                                            <span class="prop-underline"></span>
                                        </li>
                                        <li><strong class="title">@lang('Stroke Color')</strong>
                                            <span class="divide-colon">:</span>
                                            <span class="prop-stroke-color"></span>
                                        </li>
                                        <li><strong class="title">@lang('Stroke Width')</strong>
                                            <span class="divide-colon">:</span>
                                            <span class="prop-stroke-width"></span>
                                        </li>
                                        <li><strong class="title">@lang('Width (in)')</strong>
                                            <span class="divide-colon">:</span>
                                            <span class="prop-width"></span>
                                        </li>
                                        <li><strong class="title">@lang('Height (in)')</strong>
                                            <span class="divide-colon">:</span>
                                            <span class="prop-height"></span>
                                        </li>
                                        <li><strong class="title">@lang('Left (in)')</strong>
                                            <span class="divide-colon">:</span>
                                            <span class="prop-left"></span>
                                        </li>
                                        <li><strong class="title">@lang('Top (in)')</strong>
                                            <span class="divide-colon">:</span>
                                            <span class="prop-top"></span>
                                        </li>
                                        <li class="circle-radius-row" style="display: none">
                                            <strong class="title">@lang('Radius (in)')</strong>
                                            <span class="divide-colon">:</span>
                                            <span class="prop-radius"></span>
                                        </li>
                                    </ul>

                                    <!-- Buttons (outside the list) -->
                                    <div class="flex-align gap-2 mt-3">
                                        <button
                                            class="download-shape-image d-none btn--sm btn btn-outline--dark">@lang('Download Selected Shape')</button>
                                        <button
                                            class="download-original-image d-none btn--sm btn btn--secondary">@lang('Download Original Image')</button>
                                    </div>
                                </div>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@push('style')
    <style>
        .info-address-list li .title {
            min-width: 90px;
        }

        .info-address-list li {
            gap: 8px;
            ;
        }
    </style>
@endpush
