@extends($activeTemplate . 'layouts.master')
@section('content')
    <div class="card custom--card">
        <div class="card-body p-0">
            <div class="table-responsive--md table-responsive">
                <table class="table">
                    <thead>
                        <tr>
                            <th>@lang('Name')</th>
                            <th class="text-center">@lang('Type')</th>
                            <th>@lang('Values')</th>
                            <th>@lang('Status')</th>
                            <th>@lang('Action')</th>
                        </tr>
                    </thead>

                    <tbody>
                        @forelse($attributes as $attribute)
                            <tr>
                                <td>{{ __($attribute->name) }}</td>
                                <td>
                                    @if ($attribute->type == Status::ATTRIBUTE_TYPE_TEXT)
                                        @lang('Text')
                                    @elseif ($attribute->type == Status::ATTRIBUTE_TYPE_COLOR)
                                        @lang('Color')
                                    @else
                                        @lang('Image')
                                    @endif
                                </td>
                                <td>{{ $attribute->attribute_values_count }}</td>

                                <td>@php echo $attribute->statusBadge; @endphp</td>

                                <td class="text-end">
                                    <div class="d-flex justify-content-end flex-wrap gap-2">
                                        <button type="button" class="btn btn--xs btn-outline--base-two  editAttributeBtn"
                                            data-data="{{ $attribute }}">
                                            <i class="me-1 la la-pencil"></i>
                                            @lang('Edit')
                                        </button>
                                        <a href="{{ route('vendor.product.attribute.values.index', $attribute->id) }}"
                                            class="btn btn--xs btn-outline--dark">
                                            <i class="me-1 las la-desktop"></i>
                                            @lang('Values')
                                        </a>
                                        @if ($attribute->status == Status::ENABLE)
                                            <button type="button" class="btn btn--xs btn-outline--danger confirmationBtn"
                                                data-action="{{ route('vendor.product.attribute.status', $attribute->id) }}"
                                                data-question="@lang('Are you sure to disable this attribute?')">
                                                <i class="me-1 las la-eye-slash"></i> @lang('Disable')
                                            </button>
                                        @else
                                            <button type="button" class="btn btn--xs btn-outline--primary confirmationBtn"
                                                data-action="{{ route('vendor.product.attribute.status', $attribute->id) }}"
                                                data-question="@lang('Are you sure to enable this attribute?')">
                                                <i class="me-1 las la-eye"></i> @lang('Enable')
                                            </button>
                                        @endif
                                    </div>
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
    </div>

    @if ($attributes->hasPages())
        {{ paginateLinks($attributes) }}
    @endif

    <div class="modal custom--modal" id="attributeModal" aria-modal="true" role="dialog">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h3 class="modal-title"></h5>
                    <span type="button" class="close" data-bs-dismiss="modal" aria-label="Close">
                        <i class="las la-times"></i>
                    </span>
                </div>
                <div class="modal-body">
                    <form action="{{ route('vendor.product.attribute.store') }}" method="POST">
                        @csrf
                        <div class="form-group">
                            <label class="form--label">@lang('Name')</label>
                            <input type="text" class="form--control" name="name" value="{{ old('name') }}" required>
                        </div>

                        <div class="form-group">
                            <label class="form--label">@lang('Type')</label>
                            <select name="type" class="form--control select2" data-minimum-results-for-search="-1" required>
                                <option value="" disabled selected>@lang('Select One')</option>
                                <option value="1">@lang('Text')</option>
                                <option value="2">@lang('Color')</option>
                                <option value="3">@lang('Image')</option>
                            </select>
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
    <button class="btn btn--base addNewAttributeBtn">
        <i class="las la-plus"></i>
        @lang('Add New')
    </button>
@endpush

@push('script')
    <script>
        (function($) {
            "use strict";
            const modal = $('#attributeModal');
            $('select[name=country]').on('change', function() {
                let mobileCode = $('select[name=country] :selected').data('mobile_code');
                let countryCode = $('select[name=country] :selected').data('code');
                $('input[name=mobile_code]').val((mobileCode != undefined ? mobileCode : ""));
                $('input[name=country_code]').val((countryCode != undefined) ? countryCode : "");
                $('.mobile-code').text('+' + (mobileCode != undefined ? mobileCode : ""));
            });

            $('.addNewAttributeBtn').on('click', function() {
                modal.find('form')[0]?.reset();
                modal.find('.modal-title').text('@lang('Add New Attribute')');
                modal.find('form').attr('action', "{{ route('vendor.product.attribute.store') }}");
                modal.find('form').find("[name='country']").val("").trigger('change');
                modal.modal('show');
            });

            $('.editAttributeBtn').on('click', function() {
                const data = $(this).data('data');

                modal.find('form')[0]?.reset();
                modal.find('.modal-title').text('@lang('Update Attribute')');
                modal.find('form').attr('action',
                    `{{ route('vendor.product.attribute.store') }}/${ data.id }`);
                modal.find('form').find("[name='type']").val(data.type);
                modal.find('form').find("[name='name']").val(data.name);
                modal.modal('show');
            });
        })(jQuery);
    </script>
@endpush
