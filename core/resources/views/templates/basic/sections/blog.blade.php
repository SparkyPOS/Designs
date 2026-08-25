@php
    $blogContent = getContent('blog.content', true)?->data_values ?? null;
    $blogs = getContent('blog.element', limit: 4, orderById: true);
@endphp

<section class="blog-section my-120">
    <div class="container">
        <div class="section-heading mw-100">
            <h2 class="section-heading__title">{{ __($blogContent->heading ?? '') }}</h2>
        </div>
        <div class="row gy-4 justify-content-center">
            @foreach ($blogs as $blog)
                @include($activeTemplate . 'partials.blog')
            @endforeach
        </div>
    </div>
</section>
