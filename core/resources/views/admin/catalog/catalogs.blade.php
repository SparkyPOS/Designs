@extends('admin.layouts.app')
@section('panel')
    <div class="row">
        <div class="col-lg-12">
            <div class="card">
                <div class="card-body p-0">
                    <div class="table-responsive--md  table-responsive">
                        <table class="table table--light style--two">
                            <thead>
                                <tr>
                                    <th>@lang('Catalog')</th>
                                    <th>@lang('Category')</th>
                                    <th>@lang('Created At')</th>
                                    <th>@lang('Status')</th>
                                    <th>@lang('Action')</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($catalogs as $catalog)
                                    <tr>
                                        <td>
                                            <div class="user d-flex">
                                                <div class="thumb">
                                                    <img src="{{ getImage(getFilePath('catalog') . '/' . $catalog->image) }}" alt="@lang('Catalog')" class="plugin_bg" />
                                                </div>
                                                <div class="ps-2">
                                                    {{ __($catalog->name) }}
                                                </div>
                                            </div>
                                        </td>
                                        <td>{{ __($catalog?->catalogCategory?->name ?? NULL) }}</td>
                                        <td>{{ showDateTime($catalog->created_at) }}</td>
                                        <td>@php echo $catalog->statusBadge; @endphp</td>
                                        <td>
                                            <div class="button--group">
                                                <a class="btn btn-sm btn-outline--info" href="{{ route('admin.catalog.seo', $catalog->id) }}">
                                                    <i class="la la-cog"></i>
                                                    @lang('SEO Setting')
                                                </a>
                                                <button data-action="{{ route('admin.catalog.store', $catalog->id) }}"
                                                    data-image="{{ getImage(getFilePath('catalog') . '/' . $catalog->image) }}"
                                                    data-category-id="{{ $catalog->catalog_category_id }}"
                                                    data-name="{{ $catalog->name }}"
                                                    data-slug="{{ $catalog->slug }}"
                                                    data-id="{{ $catalog->id }}"
                                                    class="editBtn btn btn-outline--primary btn-sm">
                                                    <i class="la la-pencil"></i>
                                                    @lang('Edit')
                                                </button>
                                                <button
                                                    title="{{ $catalog->status == 1 ? __('Click to disable') : __('Click to enable') }}"
                                                    class="btn btn-outline--{{ $catalog->status == 1 ? 'danger' : 'success' }} btn-sm confirmationBtn"
                                                    data-action="{{ route('admin.catalog.status', $catalog->id) }}"
                                                    data-question="{{ __('Are you sure to change status?') }}">
                                                    @if ($catalog->status == 1)
                                                        <i class="las la-eye-slash"></i>
                                                        @lang('Disable')
                                                    @else
                                                        <i class="las la-eye"></i>
                                                        @lang('Enable')
                                                    @endif
                                                </button>
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td class="text-muted text-center" colspan="100%">{{ __($emptyMessage) }}</td>
                                    </tr>
                                @endforelse

                            </tbody>
                        </table><!-- table end -->
                    </div>
                </div>

                @if ($catalogs->hasPages())
                    <div class="card-footer py-4">
                        {{ paginateLinks($catalogs) }}
                    </div>
                @endif
            </div>
        </div>
    </div>

    <div class="modal fade" id="catalogModal" tabindex="-1" role="dialog" aria-labelledby="catalogModalLabel"
        aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="catalogModalLabel">@lang('Add New Catalog')</h5>
                    <button type="button" class="close" data-bs-dismiss="modal" aria-label="Close">
                        <i class="las la-times"></i>
                    </button>
                </div>
                <form method="post" enctype="multipart/form-data">
                    @csrf
                    <div class="modal-body">
                        <div class="form-group">
                            <label>@lang('Catalog Category')</label>
                            <select class="form-control select2" name="catalog_category_id" required>
                                <option value="">@lang('Select One')</option>
                                @foreach ($catalogCategories as $catalogCategory)
                                    <option value="{{ $catalogCategory->id }}"> {{ __($catalogCategory->name) }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="form-group">
                            <div class="d-flex justify-content-between">
                                <label> @lang('Name')</label>
                                <a href="javascript:void(0)" class="buildSlug"><i class="las la-link"></i>@lang('Make Slug')</a>
                            </div>
                            <input type="text" class="form-control" value="{{ old('message') }}" name="name" required />
                        </div>
                        <div class="form-group">
                            <div class="d-flex justify-content-between">
                                <label> @lang('Slug')</label>
                                <div class="slug-verification d-none"></div>
                            </div>
                            <input type="text" class="form-control" value="{{ old('slug') }}" name="slug" required />
                        </div>
                        <div class="form-group">
                            <label>@lang('Image')</label>
                            <x-image-uploader class="w-100" type="catalog" :required=true />
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="submit" class="btn btn--primary w-100 h-45">@lang('Submit')</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <x-confirmation-modal />
@endsection

@push('breadcrumb-plugins')
    <x-search-form placeholder="Search" />
    <button class="btn btn--primary addBtn">
        <i class="las la-plus"></i>
        @lang('Add New')
    </button>
@endpush

@push('script')
    <script>
        (function($) {
            'use strict';

            const placeholderImage = '{{ getImage('/', getFileSize('catalogCategory')) }}';
            let catalogIdId = "";

            $('.addBtn').on('click', function() {
                catalogIdId = "";
                $('#catalogModal').find('form')[0]?.reset();
                $('.slug-verification').addClass('d-none');
                $('#catalogModal').find('.modal-title').text("@lang('New Catalog')");
                $('#catalogModal').find('form .image-upload-input').prop('required', true);
                $('#catalogModal').find('form .image--uploader').siblings('label').addClass('required');
                $('#catalogModal').find('form').find(".image-upload-preview ").css('background-image', 'url(' + placeholderImage + ')');
                $('#catalogModal').find('form').attr('action', "{{ route('admin.catalog.store') }}");
                $('#catalogModal').find('form').find("[name='catalog_category_id']").val("").trigger('change');
                $('#catalogModal').modal('show');
            });

            $('.editBtn').on('click', function() {
                const data = $(this).data();
                catalogIdId = data.id;
                $('.slug-verification').addClass('d-none');
                $('#catalogModal').find('.modal-title').text("@lang('Edit Catalog')");
                $('#catalogModal').find('form .image-upload-input').prop('required', false);
                $('#catalogModal').find('form .image--uploader').siblings('label').removeClass('required');
                $('#catalogModal').find('form').attr('action', data.action);
                $('#catalogModal').find('form').find("[name='catalog_category_id']").val(data.categoryId).trigger('change');
                $('#catalogModal').find('form').find("[name='name']").val(data.name);
                $('#catalogModal').find('form').find("[name='slug']").val(data.slug);
                $('#catalogModal').find('form').find(".image-upload-preview ").css('background-image', 'url( ' + data.image + ')')
                $('#catalogModal').modal('show');
            });

            $('.buildSlug').on('click', function() {
                let closestForm = $(this).closest('form');
                let title = closestForm.find('[name=name]').val();
                closestForm.find('[name=slug]').val(title);
                closestForm.find('[name=slug]').trigger('input');
            });

            $('[name=slug]').on('input', function() {
                let closestForm = $(this).closest('form');
                closestForm.find('[type=submit]').addClass('disabled')
                let slug = $(this).val();
                slug = slug.toLowerCase().replace(/ /g, '-').replace(/[^\w-]+/g, '');
                $(this).val(slug);
                let catalogCategoryId = $('#catalogModal').find('form').find("[name='catalog_category_id']").val();

                if (slug) {
                    $('.slug-verification').removeClass('d-none');
                    $('.slug-verification').html(`
                        <small class="text--info"><i class="las la-spinner la-spin"></i> @lang('Checking')</small>
                    `);
                    $.get("{{ route('admin.catalog.check.slug') }}", {
                        slug: slug,
                        id: catalogIdId,
                        catalog_category_id: catalogCategoryId
                    }, function(response) {
                        if (!response.exists) {
                            $('.slug-verification').html(`
                                <small class="text--success"><i class="las la-check"></i> @lang('Available')</small>
                            `);
                            closestForm.find('[type=submit]').removeClass('disabled')
                        }
                        if (response.exists) {
                            $('.slug-verification').html(`
                                <small class="text--danger"><i class="las la-times"></i> @lang('Slug already exists')</small>
                            `);
                        }
                    });
                } else {
                    $('.slug-verification').addClass('d-none');
                }
            });
        })(jQuery);
    </script>
@endpush
