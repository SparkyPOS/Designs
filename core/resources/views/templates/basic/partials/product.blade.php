@forelse($products as $product)
    @if (request()->routeIs('product.details'))
        <div class="sold-together__items">
    @else
        <div class="col-lg-3 col-sm-6 ">
    @endif
    <div class="product-card">
        <div class="product-card__thumb position-relative">
            <div class="product-ratting">
                <i class="fa-solid fa-star"></i>
                {{ $product->averageRating() }}
            </div>
            <a class="product-card__thumb-img" href="{{ route('product.details', $product->slug) }}">
                <img src="{{ $product->mainImage(true) }}" alt="@lang('Product Image')">
            </a>
        </div>
        <div class="product-card__content">
            <div class="product-card__title">
                <a class="h3 product-card__title-text" href="{{ route('product.details', $product->slug) }}">
                    {{ __($product->name) }}
                </a>
                <span class="product-card__title-by">
                    @lang('By')
                    <a href="{{ route('product.vendor', $product?->vendor?->username) }}">{{ $product?->vendor?->company_name }}</a>
                </span>
            </div>
            <div class="d-flex justify-content-between">
                <a class="product-card__price" href="{{ route('product.details', $product->slug) }}">
                    <h3 class="product-card__price-text">
                        @if ($product->regular_price != $product->sale_price)
                            <del class="text--danger">{{ showAmount($product->regular_price) }}</del>
                        @endif
                        <span>{{ showAmount($product->sale_price) }}</span>
                    </h3>
                </a>
                <div>

                </div>
            </div>
        </div>
    </div>
    </div>
@empty
    <div class="col-12">
        <div class="empty-list text-center">
            <img src="{{ getImage('assets/images/empty.png') }}" alt="@lang('image')">
            <h3 class="mt-3">@lang('Product Not Found!')</h3>
        </div>
    </div>
@endforelse

@if (!request()->routeIs('product.details') && !request()->routeIs('vendor.home'))
    <div class="col-md-12">
        @if ($products->hasPages())
            {{ paginateLinks($products) }}
        @endif
    </div>
@endif
