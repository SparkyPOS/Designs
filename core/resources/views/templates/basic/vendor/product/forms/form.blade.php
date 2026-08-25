@extends($activeTemplate . 'layouts.master')
@section('content')
    <div class="new-product">
        <div class="add-new-product__header">
            <ul class="add-new-product__steps">
                <li class="add-new-product__step {{ $step == Status::STEP_GENERAL ? 'active' : '' }}">
                    <a href="{{ ($isUpdateOrNew == 'update')? route('vendor.products.edit', [$product->slug, Status::STEP_GENERAL]) : "javascript:void(0)" }}">
                        <span class="add-new-product__step-number"></span>
                        <span class="add-new-product__step-text">@lang('General')</span>
                    </a>
                </li>
                <li class="add-new-product__step {{ $step == Status::STEP_DESCRIPTION ? 'active' : '' }}">
                    <a href="{{ ($isUpdateOrNew == 'update')? route('vendor.products.edit', [$product->slug, Status::STEP_DESCRIPTION]) : "javascript:void(0)" }}">
                        <span class="add-new-product__step-number"></span>
                        <span class="add-new-product__step-text">@lang('Description')</span>
                    </a>
                </li>
                <li class="add-new-product__step {{ $step == Status::STEP_SEO ? 'active' : '' }}">
                    <a href="{{ ($isUpdateOrNew == 'update')? route('vendor.products.edit', [$product->slug, Status::STEP_SEO]) : "javascript:void(0)" }}">
                        <span class="add-new-product__step-number"></span>
                        <span class="add-new-product__step-text">@lang('Seo Content')</span>
                    </a>
                </li>
                <li
                    class="add-new-product__step {{ $step == Status::STEP_VARIANTS ? 'active' : '' }}  {{ $product?->product_type == Status::PRODUCT_TYPE_VARIABLE ? '' : 'd-none' }}">
                    <a href="{{ ($isUpdateOrNew == 'update')? route('vendor.products.edit', [$product->slug, Status::STEP_VARIANTS]) : "javascript:void(0)" }}">
                        <span class="add-new-product__step-number"></span>
                        <span class="add-new-product__step-text">@lang('Variants')</span>
                    </a>
                </li>
                <li class="add-new-product__step {{ $step == Status::STEP_MEDIA ? 'active' : '' }}">
                    <a href="{{ ($isUpdateOrNew == 'update')? route('vendor.products.edit', [$product->slug, Status::STEP_MEDIA]) : "javascript:void(0)" }}">
                        <span class="add-new-product__step-number"></span>
                        <span class="add-new-product__step-text">@lang('Gallery Images')</span>
                    </a>
                </li>
            </ul>
        </div>
        <form action="{{ route('vendor.products.store', $product?->id) }}" method="POST" enctype="multipart/form-data">
            @csrf
            <input type="hidden" name="type" value="{{ $step }}">
            <input type="hidden" name="isUpdateOrNew" value="{{ $isUpdateOrNew }}">
            <div class="add-new-product__body">
                <div class="add-new-product__form">
                    @include($activeTemplate . 'vendor.product.forms.' . $step)
                </div>
            </div>
                @if(!($step == "variants" && blank($variants ?? [])))
                    <div class="add-new-product__footer">
                        <a href="{{ productBackRoute($isUpdateOrNew, $step, $product->slug ?? null, $product?->product_type) }}"
                            class="btn btn-outline--dark" @disabled($step == Status::STEP_GENERAL)>
                            @lang('Back')
                        </a>
                        <button type="submit" class="btn btn--base" @disabled(blank($product->productVariants ?? []) && $step == Status::STEP_VARIANTS)>
                            @if ($step == Status::STEP_MEDIA)
                                @lang('Submit')
                            @else
                                @lang('Save & Continue')
                            @endif
                        </button>
                    </div>
                @endif
        </form>
    </div>

    <x-confirmation-modal />
@endsection
