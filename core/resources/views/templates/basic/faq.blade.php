@extends($activeTemplate . 'layouts.frontend')
@section('content')
    @include('Template::sections.faq')
    @if ($sections != null)
        @foreach (json_decode($sections) as $sec)
            @include($activeTemplate . 'sections.' . $sec)
        @endforeach
    @endif
@endsection
