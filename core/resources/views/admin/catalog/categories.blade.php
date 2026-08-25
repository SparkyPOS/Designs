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
                                    <th>@lang('Category')</th>
                                    <th>@lang('Total Catalogs')</th>
                                    <th>@lang('Feature')</th>
                                    <th>@lang('Status')</th>
                                    <th>@lang('Action')</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($categories as $category)
                                    <tr>
                                        <td>
                                            <div class="user d-flex ">
                                                <div class="thumb">
                                                    <img src="{{ getImage(getFilePath('catalogCategory') . '/' . $category->image) }}"
                                                    alt="@lang('Catalog Category')" class="plugin_bg" />
                                                </div>
                                                <div class="ps-2">
                                                    {{ __($category->name) }}
                                                </div>
                                            </div>
                                        </td>
                                        <td>{{ $category->catalogs_count }}</td>
                                        <td>@php echo $category->featureBadge; @endphp</td>
                                        <td>@php echo $category->statusBadge; @endphp</td>
                                        <td>
                                            <div class="button--group">
                                                <a class="btn btn-sm btn-outline--info" href="{{ route('admin.catalog.category.seo', $category->id) }}">
                                                    <i class="la la-cog"></i>
                                                    @lang('SEO Setting')
                                                </a>
                                                 <button
                                                    data-action="{{ route('admin.catalog.category.store', $category->id) }}"
                                                    data-image="{{ getImage(getFilePath('catalogCategory') . '/' . $category->image) }}"
                                                    data-name="{{ $category->name }}"
                                                    data-slug="{{ $category->slug }}"
                                                    data-feature="{{ $category->feature }}"
                                                    data-id="{{ $category->id }}"
                                                    class="editBtn btn btn-outline--primary btn-sm">
                                                    <i class="la la-pencil"></i>
                                                    @lang('Edit')
                                                </button>

                                                <button
                                                    title="{{ ($category->status == Status::ENABLE) ? 'Click to disable' : 'Click to enable' }}"
                                                    class="btn btn-outline--{{ ($category->status == Status::ENABLE) ? 'danger' : 'success' }} btn-sm confirmationBtn"
                                                    data-action="{{ route('admin.catalog.category.status', $category->id) }}"
                                                    data-question="{{ ($category->status == Status::ENABLE) ? __('Are you sure to disable this category?') :  __('Are you sure to enable this category?')}}">
                                                    @if ($category->status == Status::ENABLE)
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

                @if ($categories->hasPages())
                    <div class="card-footer py-4">
                        {{ paginateLinks($categories) }}
                    </div>
                @endif
            </div>
        </div>
    </div>

    <div class="modal fade" id="categoryModal" tabindex="-1" role="dialog" aria-labelledby="categoryModalLabel"
        aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="categoryModalLabel">@lang('Add Catalog Category')</h5>
                    <button type="button" class="close" data-bs-dismiss="modal" aria-label="Close">
                        <i class="las la-times"></i>
                    </button>
                </div>
                <form method="post" enctype="multipart/form-data">
                    @csrf
                    <div class="modal-body">
                        <div class="form-group">
                            <div class="d-flex justify-content-between">
                                <label> @lang('Name')</label>
                                <a href="javascript:void(0)" class="buildSlug"><i class="las la-link"></i>@lang('Make Slug')</a>
                            </div>
                            <input type="text" class="form-control" value="{{ old('name') }}" name="name" required />
                        </div>
                        <div class="form-group">
                            <div class="d-flex justify-content-between">
                                <label> @lang('Slug')</label>
                                <div class="slug-verification d-none"></div>
                            </div>
                            <input type="text" class="form-control" value="{{ old('slug') }}" name="slug" required />
                        </div>
                        <div class="form-group">
                            <label>@lang('Feature')</label>
                            <select name="feature" class="form-control" required>
                                <option value="">@lang('Select one')</option>
                                <option value="{{ Status::GENERAL }}">@lang('General')</option>
                                <option value="{{ Status::FEATURED }}">@lang('Featured')</option>
                                <option value="{{ Status::MEGA_MENU }}">@lang('Mega Menu')</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label>@lang('Image')</label>
                            <x-image-uploader class="w-100" type="catalogCategory" :required=true />
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
            let categoryId = "";

            $('.addBtn').on('click', function() {
                categoryId = "";
                $('#categoryModal').find('form')[0]?.reset();
                $('.slug-verification').addClass('d-none');
                $('#categoryModal').find('.modal-title').text("@lang('New Catalog Category')");
                $('#categoryModal').find('form .image-upload-input').prop('required', true);
                $('#categoryModal').find('form [type=submit]').removeClass('disabled');
                $('#categoryModal').find('form .image--uploader').siblings('label').addClass('required');
                $('#categoryModal').find('form').find(".image-upload-preview ").css('background-image', 'url(' + placeholderImage + ')');
                $('#categoryModal').find('form').attr('action', "{{ route('admin.catalog.category.store') }}");
                $('#categoryModal').modal('show');
            });

            $('.editBtn').on('click', function() {
                const data = $(this).data();
                categoryId = data.id;
                $('.slug-verification').addClass('d-none');
                $('#categoryModal').find('.modal-title').text("@lang('Edit Catalog Category')");
                $('#categoryModal').find('form .image-upload-input').prop('required', false);
                $('#categoryModal').find('form .image--uploader').siblings('label').removeClass('required');
                $('#categoryModal').find('form').attr('action', data.action);
                $('#categoryModal').find('form [type=submit]').removeClass('disabled');
                $('#categoryModal').find('form').find("[name='name']").val(data.name);
                $('#categoryModal').find('form').find("[name='slug']").val(data.slug);
                $('#categoryModal').find('form').find("[name='feature']").val(data.feature);
                $('#categoryModal').find('form').find(".image-upload-preview ").css('background-image', 'url( ' + data.image + ')');
                $('#categoryModal').modal('show');
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

                if (slug) {
                    $('.slug-verification').removeClass('d-none');
                    $('.slug-verification').html(`
                        <small class="text--info"><i class="las la-spinner la-spin"></i> @lang('Checking')</small>
                    `);
                    $.get("{{ route('admin.catalog.category.check.slug') }}", {
                        slug: slug,
                        id: categoryId
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
