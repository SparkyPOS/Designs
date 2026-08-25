@extends($activeTemplate . 'layouts.frontend')
@section('content')
    <section class="policy-wrapper py-60">
        <div class="container">
            <div class="row">
                <div class="col-md-12">
                    <h2 class="mb-4">{{ __($pageTitle) }}</h2>
                    @php echo $policy->data_values->details; @endphp
                </div>
            </div>
        </div>
    </section>
@endsection
