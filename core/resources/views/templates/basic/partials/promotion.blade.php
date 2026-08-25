@php
    $content = getContent('promotion.content', true)?->data_values ?? null;
@endphp

<div class="header-top d-lg-block d-none">
    <div class="container">
        <div class="row">
            <div class="col-12">
                <p class="header-top__text">
                    {{ __($content->title) }}
                </p>
            </div>
        </div>
    </div>
</div>
