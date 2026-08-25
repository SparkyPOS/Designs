@extends($activeTemplate . 'layouts.frontend')
@section('content')
    <section class="blog-section my-120">
        <div class="container">
            <div class="row gy-4 justify-content-center">
                @forelse ($blogs as $blog)
                    @include($activeTemplate . 'partials.blog')
                @empty
                    @include($activeTemplate . 'partials.empty_card', [
                        'heading' => 'Blog not found!',
                    ])
                @endforelse

                @if ($blogs->hasPages())
                    <div class="pagination-wrapper">
                        {{ paginateLinks($blogs) }}
                    </div>
                @endif
            </div>
        </div>
    </section>

    @if ($sections?->secs != null)
        @foreach (json_decode($sections->secs) as $sec)
            @include($activeTemplate . 'sections.' . $sec)
        @endforeach
    @endif
@endsection
