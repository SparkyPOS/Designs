@extends('admin.layouts.app')

@section('panel')
    <div class="card">
        <div class="card-body border-bottom">
            <div class="alert alert-info mb-0">
                <strong>@lang('3D model requirements'):</strong>
                @lang('Upload a GLB or GLTF model with UV-mapped shirt materials. Models using the bundled T-Shirt mesh names receive front/back artwork decals automatically. The bundled reference model remains active until a product-specific model is uploaded.')
            </div>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive--md table-responsive">
                <table class="table table--light style--two">
                    <thead>
                        <tr>
                            <th>@lang('Product')</th>
                            <th>@lang('Vendor')</th>
                            <th>@lang('Current 3D Model')</th>
                            <th>@lang('Preview')</th>
                            <th>@lang('Upload / Replace')</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($products as $product)
                            <tr>
                                <td>
                                    <div class="user d-flex align-items-center">
                                        <div class="thumb">
                                            <img src="{{ $product->mainImage(true) }}" alt="{{ $product->name }}">
                                        </div>
                                        <div class="ps-2">
                                            <strong>{{ __($product->name) }}</strong><br>
                                            <small class="text-muted">#{{ $product->id }}</small>
                                        </div>
                                    </div>
                                </td>
                                <td>{{ $product?->vendor?->username ?? __('N/A') }}</td>
                                <td>
                                    @if ($product->designer_model)
                                        <span class="badge badge--success">@lang('Custom')</span>
                                        <small class="d-block mt-1 text-muted text-break">{{ $product->designer_model }}</small>
                                    @else
                                        <span class="badge badge--warning">@lang('Bundled fallback')</span>
                                    @endif
                                </td>
                                <td>
                                    <img src="{{ $product->designerPreviewUrl() }}" alt="@lang('Designer preview')" style="width:72px;height:72px;object-fit:cover;border-radius:8px">
                                </td>
                                <td>
                                    <form action="{{ route('admin.designer.assets.update', $product) }}" method="post" enctype="multipart/form-data" class="d-flex flex-column gap-2">
                                        @csrf
                                        <input type="file" name="designer_model" class="form-control" accept=".glb,.gltf">
                                        <input type="file" name="designer_preview" class="form-control" accept=".jpg,.jpeg,.png,.webp">
                                        <details class="border rounded p-2">
                                            <summary class="fw-semibold">@lang('Model & artwork settings')</summary>
                                            <div class="mt-3 ink-admin-settings-wrap">
                                                <x-designer-settings :product="$product" :id-prefix="'admin-designer-' . $product->id" />
                                            </div>
                                        </details>
                                        <button type="submit" class="btn btn-sm btn--primary">
                                            <i class="las la-upload"></i> @lang('Save Assets')
                                        </button>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="100%" class="text-center text-muted">{{ __($emptyMessage) }}</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
        @if ($products->hasPages())
            <div class="card-footer py-4">{{ paginateLinks($products) }}</div>
        @endif
    </div>
@endsection

@push('breadcrumb-plugins')
    <x-search-form placeholder="Search products or vendors" />
@endpush

@push('style-lib')
    <link rel="stylesheet" href="{{ asset('assets/designer/designer.css') }}?v={{ @filemtime(base_path('../assets/designer/designer.css')) ?: time() }}">
@endpush

@push('script')
    <script type="module" src="{{ asset('assets/designer/designer.js') }}?v={{ @filemtime(base_path('../assets/designer/designer.js')) ?: time() }}"></script>
@endpush
