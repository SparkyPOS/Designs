<div class="col-xxl-3 col-lg-4 col-sm-6">
    <div class="blog-item">
        <a class="blog-item__thumb" href="{{ route('blog.details', $blog->slug) }}">
            <img src="{{ frontendImage('blog', 'thumb_' . $blog?->data_values?->image, '775x440') }}" class="fit-image" alt="@lang('Image')">
        </a>
        <div class="blog-item__body">
            <p class="blog-item__date">
                {{ showDateTime($blog->created_at, 'M d, Y') }}
            </p>
            <h5 class="blog-item__title">
                <a href="{{ route('blog.details', $blog->slug) }}" class="blog-item__title-link border-effect">{{ __(strlimit($blog?->data_values?->title, 60)) }}</a>
            </h5>
        </div>
        <div class="blog-item__footer">
            <a href="{{ route('blog.details', $blog->slug) }}" class="btn--simple">
                @lang('Read More')
                <span class="btn--simple__icon">
                    <i class="las la-arrow-right"></i>
                </span>
            </a>
        </div>
    </div>
</div>
