@extends($activeTemplate . 'layouts.frontend')
@section('content')
    <div class="search-section mt-30 mb-3">
        <form class="container">
            <div class="db__search">
                <label for="search">
                    <i class="la la-search la-lg"></i>
                </label>
                <input type="search" name="search" value="{{ request()->search }}" id="search"
                    placeholder="@lang('Search catalog')">
            </div>
        </form>
    </div>
    <div class="product-breadcrumb">
        <div class="container">
            <div class="product-breadcrumb__wrapper">
                <a href="{{ route('product.all.catalogs') }}" class="product-breadcrumb__link"> @lang('All Catalog') </a>
            </div>
        </div>
    </div>

    <section class="catalog-list-section my-60">
        <div class="container">
            <div class="row">
                <div class="col-12">
                    <div class="catalog-heading">
                        <h2 class="catalog-heading__title">{{ __($catalogContent->heading ?? null) }}</h2>
                        <p class="catalog-heading__desc">
                            {{ __($catalogContent->subheading ?? null) }}
                        </p>
                    </div>
                </div>
            </div>
            <div class="row g-lg-4 g-3 justify-content-center">
                @forelse ($categories as $category)
                    <div class="col-xl-3 col-md-4 col-sm-6 col-xsm-6">
                        <a   href="{{ route('product.catalogs', $category->slug) }}" class="catalog-card">
                            <div class="catalog-card__img">
                                <img src="{{ getImage(getFilePath('catalogCategory') . '/' . $category->image, getFileSize('catalogCategory')) }}"
                                    alt="category-thumb">
                            </div>
                            <div class="catalog-card__content">
                                <h3 class="catalog-card__title">
                                    <span class="catalog-card__link">
                                        <span>{{ __($category->name) }}</span>
                                    </span>
                                </h3>
                            </div>
                        </a>
                    </div>
                @empty
                    <div class="col-12">
                        <div class="empty-list text-center">
                            <img src="{{ getImage('assets/images/empty.png') }}" alt="@lang('Catalog')">
                            <h3 class="mt-3">@lang('Catalog Not Found!')</h3>
                        </div>
                    </div>
                @endforelse
                <div class="col-md-12">
                    @if ($categories->hasPages())
                        {{ paginateLinks($categories) }}
                    @endif
                </div>
            </div>
        </div>
    </section>
@endsection
