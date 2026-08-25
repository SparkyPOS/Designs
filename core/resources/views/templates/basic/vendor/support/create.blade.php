@extends($activeTemplate . 'layouts.master')
@section('content')
    <div class="card custom--card">
        <div class="card-header">
            <h3>{{ __($pageTitle) }}</h3>
        </div>

        <div class="card-body">
            <form action="{{ route('vendor.ticket.store') }}" class="disableSubmission" method="post"
                enctype="multipart/form-data">
                @csrf
                <div class="row">
                    <div class="form-group col-md-6">
                        <label class="form--label">@lang('Subject')</label>
                        <input type="text" name="subject" value="{{ old('subject') }}" class="form--control" required>
                    </div>
                    <div class="form-group col-md-6">
                        <label class="form--label">@lang('Priority')</label>
                        <select name="priority" class="form--control select2" data-minimum-results-for-search="-1" required>
                            <option value="3">@lang('High')</option>
                            <option value="2">@lang('Medium')</option>
                            <option value="1">@lang('Low')</option>
                        </select>
                    </div>
                    <div class="col-12 form-group">
                        <label class="form--label">@lang('Message')</label>
                        <textarea name="message" id="inputMessage" rows="6" class="form--control" required>{{ old('message') }}</textarea>
                    </div>

                    <div class="col-md-12">
                        <div class="flex-between gap-2">
                            <button type="button" class="btn btn--dark btn--xs addAttachment"> <i class="fas fa-plus"></i>
                                @lang('Add Attachment') </button>

                            <button class="btn btn--base" type="submit"><i class="las la-paper-plane"></i>
                                @lang('Reply Message')
                            </button>
                        </div>

                        <small class="my-2 fs-16 d-block"><span
                                class="text--base-two fs-14">@lang('Max 5 files can be uploaded | Maximum upload size is ' . convertToReadableSize(ini_get('upload_max_filesize')) . ' | Allowed File Extensions: .jpg, .jpeg, .png, .pdf, .doc, .docx')</span></small>
                        <div class="row fileUploadsContainer"></div>
                    </div>
                </div>
            </form>
        </div>
    </div>
@endsection

@push('style')
    <style>
        .input-group-text:focus {
            box-shadow: none !important;
        }
    </style>
@endpush

@push('breadcrumb-plugins')
    <a href="{{ route('vendor.ticket.index') }}" class="btn btn--sm btn--base">
        @lang('My Support Ticket')
    </a>
@endpush

@push('script')
    <script>
        (function($) {
            "use strict";
            var fileAdded = 0;
            $('.addAttachment').on('click', function() {
                fileAdded++;
                if (fileAdded == 5) {
                    $(this).attr('disabled', true)
                }
                $(".fileUploadsContainer").append(`
                    <div class="col-lg-4 col-md-12 removeFileInput">
                        <div class="form-group">
                            <div class="input-group input--group">
                                <input type="file" name="attachments[]" class="form-control form--control" accept=".jpeg,.jpg,.png,.pdf,.doc,.docx" required>
                                <button type="button" class="input-group-text removeFile bg--danger border--danger text-white m-1"><i class="fas fa-times"></i></button>
                            </div>
                        </div>
                    </div>
                `)
            });
            $(document).on('click', '.removeFile', function() {
                $('.addAttachment').removeAttr('disabled', true)
                fileAdded--;
                $(this).closest('.removeFileInput').remove();
            });
        })(jQuery);
    </script>
@endpush
