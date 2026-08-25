@extends('Template::layouts.design')

@php
    $designerPrintAreas = $printAreas->map(function ($printArea) use ($cartPrintAreas) {
        $savedDesign = null;
        if ($cartPrintAreas) {
            $savedDesign = optional($cartPrintAreas->where('product_print_area_id', $printArea->id)->first())->selected_area_design;
        }

        return [
            'id' => $printArea->id,
            'name' => __($printArea->name),
            'imageUrl' => getImage(getFilePath('printArea') . '/' . $printArea->image),
            'selectedArea' => json_decode($printArea->selected_area, true),
            'savedDesign' => $savedDesign ? json_decode($savedDesign, true) : null,
            'width' => $printArea->width ?? null,
            'height' => $printArea->height ?? null,
        ];
    })->values();

    $designerConfig = [
        'productName' => __($product->name),
        'modelUrl' => $product->designerModelUrl(),
        'previewUrl' => $product->designerPreviewUrl(),
        'modelSettings' => $product->resolvedDesignerSettings(),
        'color' => $colorCode ? '#' . ltrim($colorCode, '#') : '#ffffff',
        'printAreas' => $designerPrintAreas,
        'backUrl' => route('product.details', $product->slug),
        'addToCartUrl' => route('product.add.to.cart'),
        'csrf' => csrf_token(),
    ];
@endphp

@section('content')
    <form action="{{ route('product.buy.now') }}" method="post" id="printArea">
        @csrf
        <input type="hidden" name="product_id" value="{{ $product->id }}">
        <input type="hidden" name="variant_id" value="{{ $productVariant?->id }}">
        <input type="hidden" name="quantity" value="{{ $cartProduct->quantity ?? 1 }}">

        @foreach ($printAreas as $printArea)
            @php
                $savedDesign = $cartPrintAreas
                    ? optional($cartPrintAreas->where('product_print_area_id', $printArea->id)->first())->selected_area_design
                    : null;
            @endphp
            <input type="hidden" name="print_area_id[]" value="{{ $printArea->id }}">
            <input type="hidden" name="selected_area[]" data-design-value="{{ $printArea->id }}" value="{{ $savedDesign }}">
        @endforeach

        <div id="ink-product-designer"></div>
    </form>

    <div class="modal custom--modal" id="instructionModal" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h3 class="modal-title">@lang('Design Instructions')</h3>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="@lang('Close')"></button>
                </div>
                <div class="modal-body">
                    <p>@lang('Keep every text, image, shape, and drawing inside the blue dashed print boundary. Artwork outside that boundary will not be printed.')</p>
                    <p>@lang('Use the mouse wheel or zoom controls for precision. Drag the 3D shirt to rotate it, and use the print-area tabs to edit each configured side.')</p>
                    <p>@lang('Uploaded artwork should be high resolution. Transparent PNG files usually provide the best print result.')</p>
                    @php
                        $designInstructions = $product->design_instruction ?? ($product?->vendor?->design_instruction ?? []);
                    @endphp
                    @if (!blank($designInstructions))
                        <h5 class="mt-3">@lang('Instructions From Vendor')</h5>
                        @foreach ($designInstructions as $designInstruction)
                            <p class="mb-1">{{ __($designInstruction) }}</p>
                        @endforeach
                    @endif
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn--dark btn--sm" data-bs-dismiss="modal">@lang('Close')</button>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('style-lib')
    <link rel="stylesheet" href="{{ asset('assets/designer/designer.css') }}?v={{ @filemtime(base_path('../assets/designer/designer.css')) ?: time() }}">
@endpush

@push('script-lib')
    <script src="{{ asset('assets/global/js/fabric.min.js') }}"></script>
@endpush

@push('script')
    <script>
        window.InkDesignerConfig = @json($designerConfig);
    </script>
    <script type="module" src="{{ asset('assets/designer/designer.js') }}?v={{ @filemtime(base_path('../assets/designer/designer.js')) ?: time() }}"></script>
@endpush
