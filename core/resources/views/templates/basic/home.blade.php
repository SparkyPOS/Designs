@extends($activeTemplate . 'layouts.frontend')
@section('content')
    @include('Template::sections.banner')
    @if (isset($sections->secs) && $sections->secs != null)
        @foreach (json_decode($sections->secs) as $sec)
            @include($activeTemplate . 'sections.' . $sec)
        @endforeach
    @endif
@endsection
