@extends($activeTemplate . 'layouts.frontend')
@section('content')
    <section class=" h-100 maintenance-page flex-column justify-content-center">
        <div class=" h-100 container">
            <div class=" h-100 row justify-content-center align-items-center">
                <div class="col-lg-7 text-center">
                    <div class="row justify-content-center">
                        <div class="col-sm-6 col-lg-8 col-8">
                            <img class="img-fluid mx-auto mb-5" src="{{ getImage(getFilePath('maintenance') . '/' . $maintenance?->data_values?->image, getFileSize('maintenance')) }}" alt="image">
                        </div>
                    </div>
                    <p class="mx-auto text-center">@php echo $maintenance->data_values->description @endphp</p>
                </div>
            </div>
        </div>
    </section>
@endsection

@push('style')
<style>
    header{
        display:none;
    }
    footer{
        display:none;
    }
    .breadcrumb{
        display:none;
    }
    body{
        background-color:white;
        display: flex;
        align-items: center;
        height: 100vh;
        justify-content: center;
    }
</style>
@endpush
