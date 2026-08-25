@extends($activeTemplate . 'layouts.master')
@section('content')
    <div class="card custom--card">
        <div class="card-body p-0">
            <div class="table-responsive--md table-responsive">
                <table class="table table--light style--two">
                    <thead>
                        <tr>
                            <th>@lang('Name')</th>
                            <th>@lang('Image')</th>
                            <th>@lang('Status')</th>
                            <th>@lang('Action')</th>
                        </tr>
                    </thead>

                    <tbody class="list">
                        @forelse($printAreas as $printArea)
                            <tr>
                                <td>{{ __($printArea->name) }}</td>
                                <td>
                                    <img src="{{ getImage(getFilePath('printArea') . '/' . $printArea->image, getFileSize('printArea')) }}" alt="@lang('image')">
                                </td>
                                <td>@php echo $printArea->statusBadge; @endphp</td>
                                <td>
                                    <button type="button" class="btn btn--xs btn-outline--base-two editPrintAreaBtn"
                                        data-name="{{ $printArea->name }}"
                                        data-image="{{ getImage(getFilePath('printArea') . '/' . $printArea->image, getFileSize('printArea')) }}"
                                        data-action="{{ route('vendor.product.print.area.store', $printArea->id) }}">
                                        <i class="la la-pencil"></i>
                                        @lang('Edit')
                                    </button>
                                    @if ($printArea->status == Status::ENABLE)
                                        <button type="button" class="btn btn--xs btn-outline--danger confirmationBtn"
                                            data-action="{{ route('vendor.product.print.area.status', $printArea->id) }}"
                                            data-question="@lang('Are you sure to disable this print area?')">
                                            <i class="las la-eye-slash"></i> @lang('Disable')
                                        </button>
                                    @else
                                        <button type="button" class="btn btn--xs btn-outline--success confirmationBtn"
                                            data-action="{{ route('vendor.product.print.area.status', $printArea->id) }}"
                                            data-question="@lang('Are you sure to enable this print area?')">
                                            <i class="las la-eye"></i> @lang('Enable')
                                        </button>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td class="text-muted text-center" colspan="100%">{{ __($emptyMessage) }}</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        @if ($printAreas->hasPages())
            <div class="card-footer py-4">
                {{ paginateLinks($printAreas) }}
            </div>
        @endif
    </div>

    <div class="modal custom--modal" id="printAreaModal" aria-modal="true" role="dialog">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h3 class="modal-title"></h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form action="" method="POST" enctype="multipart/form-data">
                        @csrf
                        <div class="form-group">
                            <label class="form--label">@lang('Name')</label>
                            <input type="text" class="form--control" name="name" value="" required>
                        </div>

                        <div class="form-group">
                            <label class="form--label">@lang('Image')</label>
                            <x-image-uploader name="image" image="" class="w-100" type="printArea" :required=true />
                        </div>
                        <button type="submit" class="btn btn--base w-100 h-45">@lang('Submit')</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
    <x-confirmation-modal />
@endsection

@push('breadcrumb-plugins')
    <button class="btn btn--base addNewPrintAreaBtn">
        <i class="las la-plus"></i>
        @lang('Add New')
    </button>
@endpush

@push('script')
    <script>
        (function($) {
            "use strict";
            const modal = $('#printAreaModal');
            const placeholderImage = '{{ getImage('/', getFileSize('catalogCategory')) }}';

             $('.addNewPrintAreaBtn').on('click', function() {
                modal.find('form')[0]?.reset();
                modal.find('.modal-title').text("@lang('New Pint Area')");
                modal.find('form .image-upload-input').prop('required', true);
                modal.find('form .image--uploader').siblings('label').addClass('required');
                modal.find('form').find(".image-upload-preview ").css('background-image', 'url(' + placeholderImage + ')');
                modal.find('form').attr('action', "{{ route('vendor.product.print.area.store') }}");
                modal.modal('show');
            });

            $('.editPrintAreaBtn').on('click', function() {
                const data = $(this).data();
                modal.find('.modal-title').text("@lang('Edit Pint Area')");
                modal.find('form .image-upload-input').prop('required', false);
                modal.find('form .image--uploader').siblings('label').removeClass('required');
                modal.find('form').find(".image-upload-preview ").css('background-image', data.image);
                modal.find('form').find("[name='name']").val(data.name);
                modal.find('form').attr('action', data.action);
                modal.modal('show');
            });
        })(jQuery);
    </script>
@endpush
