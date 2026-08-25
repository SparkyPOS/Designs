@extends($activeTemplate . 'layouts.master')
@section('content')
    <section class="settings-container">
        <div class="row">
            <div class="col-xxxl-3 col-xxl-4">
                @include($activeTemplate . '.partials.vendor_account_menu')
            </div>
            <div class="col-xxxl-9 col-xxl-8">
                <form method="post">
                    @csrf
                    <div class="setting-content">
                        <div class="setting-content__details">
                            <div class="row">
                                <div class="col-12">
                                    <div class="form--label flex flex-wrap justify-content-between align-items-center mb-3">
                                        <span>
                                            @lang('Design Instruction')
                                            <i class="las la-info-circle" data-bs-toggle="tooltip" data-bs-title="@lang('You can set global design instructions that will automatically apply to all products. If you need specific instructions for an individual product, you can also add product-specific instructions, which will override the global ones.')"></i>
                                        </span>
                                        <button type="button" class="btn btn-outline--dark btn--xs addInstructionBtn">
                                            <i class="la la-plus">@lang('Add More')</i>
                                        </button>
                                    </div>
                                    <div class="form-group">
                                        <input type="text" class="form--control" name="design_instruction[]" value="{{ $designInstructions[0] ?? '' }}" required>
                                    </div>
                                </div>
                                <div id="instructions">
                                    @foreach ($designInstructions ?? [] as $designInstruction)
                                        @continue($loop->first)
                                        <div class="form-group">
                                            <div class="d-flex align-items-center gap-2">
                                                <input type="text" class="form--control" name="design_instruction[]" value="{{ $designInstruction }}" required>
                                                <button type="button" class="btn btn-outline--danger removeInstructionBtn text-nowrap"><i class="la la-times"></i>
                                                    <span class="d-none d-md-inline-block">@lang('Remove')</span>
                                                </button>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                            <div>
                                <button type="submit" class="btn btn--base w-100">@lang('Submit')</button>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </section>
@endsection

@push('script')
    <script>
        (function($) {
            "use strict";

            $('.addInstructionBtn').on('click', function() {
                let html = `<div class="form-group">
                    <div class="d-flex align-items-center gap-2">
                        <input type="text" class="form--control" name="design_instruction[]" value="" required>
                        <button type="button" class="btn btn-outline--danger removeInstructionBtn text-nowrap" >
                            <i class="la la-times"></i>
                            <span class="d-none d-md-inline-block">@lang('Remove')</span>
                        </button>
                    </div>
                </div>`;
                $('#instructions').append(html);
            });

            $(document).on('click', '.removeInstructionBtn', function() {
                $(this).closest('.form-group').remove();
            });

        })(jQuery);
    </script>
@endpush
