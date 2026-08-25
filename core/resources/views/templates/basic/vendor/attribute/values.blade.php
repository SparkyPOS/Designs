@extends($activeTemplate . '.layouts.master')

@section('content')
    <div class="row gy-4">
        <div class="col-md-4">
            <div class="card custom--card b-radius--10 position-sticky top-30 border">
                <div class="card-header d-flex flex-wrap justify-content-between align-items-center">
                    <h3 class="card-title formTitle mb-0">@lang('Add New Value')</h3>
                    <button type="reset" class="btn btn--sm btn--base resetBtn d-none" form="attributeForm"> <i
                            class="las la-plus"></i>@lang('Add New')</button>
                </div>

                <div class="card-body">
                    <form action="{{ route('vendor.product.attribute.values.store', $attribute->id) }}" method="POST"
                        id="attributeForm" enctype="multipart/form-data">
                        @csrf
                        <input type="hidden" name="value_id" value="">

                        <div class="form-group">
                            <label class="form--label">@lang('Name')</label>
                            <input type="text" class="form--control nameField" name="name" required />
                        </div>
                        <div class="form-group mb-0">
                            <label class="form--label">@lang('Value')</label>
                            @if ($attribute->type == Status::ATTRIBUTE_TYPE_COLOR)
                                <div class="form-group color-variation-row mb-1" data-id="0">
                                    <div class="input-group input--group input--group-color">
                                        <input type="text" name="value"
                                            class="form--control form-control product-color-input" id="product-color-0"
                                            value="e1e7ef">
                                        <span class="input-group-text colorInput pe-5 cursor-pointer" id="colorInput-0"
                                            style="background-color: #e1e7ef">
                                        </span>
                                    </div>
                                </div>
                                <small class="text--base-two"><i class="la la-info-circle"></i> @lang('The color code must be in HEX format')</small>
                            @elseif($attribute->type == Status::ATTRIBUTE_TYPE_IMAGE)
                                <x-frontend-image-uploader name="value" type="attribute" :required=true />
                            @else
                                <input type="text" class="form--control nameField" name="value" required />
                            @endif
                        </div>
                    </form>
                </div>

                <div class="card-footer">
                    <button type="submit" class="btn btn--base w-100" form="attributeForm">@lang('Submit')</button>
                </div>
            </div>

        </div>
        <div class="col-md-8">
            <div class="card custom--card b-radius--10">
                <div class="card-body p-0">
                    <div class="table-responsive--md table-responsive">
                        <table class="table">
                            <thead>
                                <tr>
                                    <th>@lang('Name')</th>
                                    <th class="text-center">@lang('Value')</th>
                                    <th>@lang('Action')</th>
                                </tr>
                            </thead>

                            <tbody>
                                @forelse($attributeValues as $attributeValue)
                                    <tr>
                                        <td>{{ __($attributeValue->name) }}</td>
                                        <td>
                                            @if ($attribute->type == Status::ATTRIBUTE_TYPE_COLOR)
                                                <span class="color-value"
                                                    style="background-color: #{{ $attributeValue->value }}">&nbsp;</span>
                                            @elseif($attribute->type == Status::ATTRIBUTE_TYPE_IMAGE)
                                                <span class="color-value image-popup"
                                                    data-image="{{ getImage(getFilePath('attribute') . '/' . $attributeValue?->value, getFileSize('attribute')) }}">
                                                    <img src="{{ getImage(getFilePath('attribute') . '/' . $attributeValue?->value, getFileSize('attribute')) }}"
                                                        alt="@lang('image')">
                                                </span>
                                            @else
                                                {{ $attributeValue->value }}
                                            @endif
                                        </td>

                                        <td>
                                            <button class="btn btn--xs btn--base editAttribute"
                                                data-item="{{ $attributeValue }}">
                                                <i class="la la-pencil"></i> @lang('Edit')
                                            </button>

                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td class="text-muted text-center" colspan="100%">{{ __($emptyMessage) }}</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            @if ($attributeValues->hasPages())
                {{ paginateLinks($attributeValues) }}
            @endif
        </div>
    </div>

    @include($activeTemplate . 'partials.image_popup_modal')
@endsection

@push('breadcrumb-plugins')
    <form>
        <div class="input-group input--group">
            <input type="search" name="search" class="form-control form--control" value="{{ request()->search }}"
                placeholder="@lang('Search by transactions')">
            <button class="input-group-text btn--sm btn btn--base text-white">
                <i class="las la-search"></i>
            </button>
        </div>
    </form>
    <a href="{{ route('vendor.product.attribute.index') }}" type="button" class="btn btn-outline--base-two text-white">
        <i class="las la-angle-left"></i>
        @lang('Back')
    </a>
@endpush

@push('script-lib')
    <script src="{{ asset('assets/admin/js/spectrum.js') }}"></script>
@endpush

@push('style-lib')
    <link rel="stylesheet" href="{{ asset('assets/admin/css/spectrum.css') }}">
@endpush
@push('style')
    <style>
        .image--uploader {
            width: 160px;
        }

        .color-value {
            border-radius: 50%;
            width: 35px;
            height: 35px;
            border: 1px solid hsl(var(--black)/0.1);
        }

        .input--group-color .input-group-text {
            border-left: 1px solid hsl(var(--black)/0.1) !important;
            margin-left: 0px !important;
        }
    </style>
@endpush

@push('script')
    <script>
        (function($) {
            "use strict";
            const form = $('#attributeForm');
            const defaultImg = "{{ getImage(null, avatar: true, avatarName: 'default.png') }}";

            const addSpectrum = () => {
                $('.colorPicker').spectrum({
                    color: $(this).data('color'),
                    change: function(color) {
                        $(this).parent().siblings('.colorCode').val(color.toHexString().replace(/^#?/,
                            ''));
                    }
                });
            };

            addSpectrum();

            $('.colorCode').on('input', function() {
                $('.colorPicker').spectrum('set', $(this).val());
            });

            const editAttributeClickHandler = function() {
                const data = $(this).data('item');
                $('.resetBtn').removeClass('d-none');
                $('.formTitle').text(`@lang('Update Value')`);
                $('[name=name]').val(data.name);

                @if ($attribute->type == Status::ATTRIBUTE_TYPE_IMAGE)
                    $('[name=value]').prop('required', false);
                    const imagePath = `{{ getFilePath('attribute') }}`;
                    const basePath = `{{ url('') }}`;
                    const image = basePath + '/' + imagePath + "/" + data.value;

                    $('.image-upload-preview').css('background-image', `url("${image}")`);
                @elseif ($attribute->type == Status::ATTRIBUTE_TYPE_COLOR)
                    $('.colorPicker').val(data.value);
                    $('.colorInput').css('background-color', `#${data.value}`);
                @endif

                @if ($attribute->type != Status::ATTRIBUTE_TYPE_IMAGE)
                    $('[name=value]').val(data.value);
                @endif

                $('[name=value_id]').val(data.id);
                addSpectrum();
            }

            form.on('reset', function() {
                $('.formTitle').text(`@lang('Add New Value')`);
                $('.resetBtn').addClass('d-none');
                $('.colorPicker').val('e81f1f');
                $('[name=value_id]').val('');
                @if ($attribute->type == Status::ATTRIBUTE_TYPE_IMAGE)
                    let defaultImg = "{{ getImage(null, getFileSize('attribute')) }}";
                    $('.showImage').attr('src', `${defaultImg}`);
                @endif
                addSpectrum();
            });

            $('.editAttribute').on('click', editAttributeClickHandler);
        })(jQuery);
    </script>
@endpush
