<div class="design-area">
    <div class="tab-content flex-fill" id="pills-tabContent">
        @foreach ($printAreas as $printArea)
            @php
                $selectedArea = json_decode($printArea->selected_area);
                $customerDesing = $cartPrintAreas ? $cartPrintAreas->where('product_print_area_id', $printArea->id)->first()->selected_area_design ?? null : null;
            @endphp
            <div class="tab-pane h-100  @if ($loop->first) show active @endif" id="id{{ $printArea->id }}" role="tabpanel" aria-labelledby="id{{ $printArea->id }}-tab" tabindex="0">
                <div class="design-area__wrapper position-relative printSideAreaItem">
                    <input type="hidden" name="print_area_id[]" value="{{ $printArea->id }}">
                    <div class="left-layout position-relative">
                        <div class="left-layout__item">
                            <a href="{{ route('product.details', $product->slug) }}" class="btn--sm btn design-option__btn back-btn">
                                <i class="las la-arrow-left"></i>
                            </a>
                        </div>
                        <div class="drawer">
                            <div class="left-layout__item textbox">
                                <button type="button" class="btn--sm btn design-option__btn toggle-swap">
                                    <i class="fa-solid fa-font"></i>
                                    <span class="text">@lang('Text')</span>
                                </button>
                                <div class="sidebar-layer design-tools__item">
                                    <div class="sidebar-layer__header">
                                        <h4 class="sidebar-layer__title">@lang('Add Text')</h4>
                                        <span class="sidebar-layer__close">
                                            <i class="fa-solid fa-times"></i>
                                        </span>
                                    </div>
                                    <div class="sidebar-layer__body">
                                        <button type="button" class="btn btn--sm pill w-100 btn-outline--base-two mb-2" data-action="addText">
                                            <i class="fa-solid fa-t"></i>
                                            @lang('Add Text')
                                        </button>

                                        <div class="form-group">
                                            <label class="form--label">@lang('Font Family'):</label>
                                            <select class="font-family form--control form-select form-select-sm w-100" data-action="fontFamily">
                                                <option value="Arial">@lang('Arial')</option>
                                                <option value="Helvetica">@lang('Helvetica')</option>
                                                <option value="Times New Roman">@lang('Times New Roman')</option>
                                                <option value="Courier New">@lang('Courier New')</option>
                                                <option value="Comic Sans MS">@lang('Comic Sans MS')</option>
                                                <option value="Impact">@lang('Impact')</option>
                                                <option value="Georgia">@lang('Georgia')</option>
                                                <option value="Verdana">@lang('Verdana')</option>
                                            </select>
                                        </div>

                                        <div class="form-group">
                                            <label class="form--label">
                                                @lang('Font Size'):
                                            </label>
                                            <div class="d-flex align-items-center gap-2">
                                                <input type="number" class="w-auto font-size form--control form-control-sm" data-action="fontSize" min="8" value="15">
                                            </div>
                                        </div>

                                        <div class="d-flex gap-2 text-tools">
                                            <button type="button" class="btn" data-action="toggleBold">
                                                @lang('B')
                                            </button>

                                            <button type="button" class="btn" data-action="toggleUnderline">
                                                @lang('U')
                                            </button>

                                            <button type="button" class="btn" data-action="toggleItalic">
                                                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="icon icon-tabler icons-tabler-outline icon-tabler-italic">
                                                    <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                                                    <path d="M11 5l6 0" />
                                                    <path d="M7 19l6 0" />
                                                    <path d="M14 5l-4 14" />
                                                </svg>
                                            </button>

                                            <button type="button" class="btn" data-action="toggleTextCurbe">
                                                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="icon icon-tabler icons-tabler-outline icon-tabler-radius-top-right">
                                                    <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                                                    <path d="M5 5h6a8 8 0 0 1 8 8v6" />
                                                </svg>
                                            </button>

                                            <div class="btn">
                                                <input type="color" class="cursor-pointer color-picker colorInput w-100 h-100" data-action="colorPicker" value="#ff0000">
                                            </div>

                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="left-layout__item shape">
                                <button type="button" class="btn--sm btn design-option__btn toggle-swap">
                                    <i class="fa-solid fa-shapes"></i>
                                    <span class="text">@lang('Shape')</span>
                                </button>

                                <div class="sidebar-layer">
                                    <div class="sidebar-layer__header">
                                        <h4 class="sidebar-layer__title">@lang('Add Shape')</h4>
                                        <span class="sidebar-layer__close">
                                            <i class="fa-solid fa-times"></i>
                                        </span>
                                    </div>
                                    <div class="sidebar-layer__body">
                                        <div class="shape-list__search mb-3">
                                            <div class="text-tools d-flex gap-2 justify-content-between align-items-center mb-3 ms-1 me-3">
                                                <div class="btn p-1 flex-shrink-0 w-100" data-bs-toggle="tooltip" data-bs-placement="right" title="@lang('Choose Color')">
                                                    <input type="color" class="cursor-pointer shape-color-picker colorInput w-100 h-100" data-action="shapeColorPicker" value="#ff0000">
                                                </div>
                                            </div>
                                        </div>

                                        <div class="shape-list">
                                            <button type="button" class="btn btn-shape" data-action="addRect">⬛</button>
                                            <button type="button" class="btn btn-shape" data-action="addCircle">⚪</button>
                                            <button type="button" class="btn btn-shape" data-action="addTriangle">🔺</button>
                                            <button type="button" class="btn btn-shape" data-action="addLine">➖</button>
                                            <button type="button" class="btn btn-shape" data-action="addStar">⭐</button>
                                            <button type="button" class="btn btn-shape" data-action="addPolygon">⬠</button>
                                            <button type="button" class="btn btn-shape" data-action="addHeart">❤️</button>
                                            <button type="button" class="btn btn-shape" data-action="addDiamond">🔶</button>
                                            <button type="button" class="btn btn-shape" data-action="addPentagon">⬟</button>
                                            <button type="button" class="btn btn-shape" data-action="addHexagon">⬢</button>
                                            <button type="button" class="btn btn-shape" data-action="addCloud">☁️</button>
                                            <button type="button" class="btn btn-shape" data-action="addArrow">➡️</button>
                                            <button type="button" class="btn btn-shape" data-action="addSpeechBubble">💬</button>
                                            <button type="button" class="btn btn-shape" data-action="addMoon">🌙</button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="left-layout__item draw">
                                <button type="button" class="btn--sm btn design-option__btn toggle-swap" data-action="draw">
                                    <i class="fa-solid fa-pencil"></i>
                                    <span class="text">@lang('Draw')</span>
                                </button>

                                <div class="sidebar-layer">
                                    <div class="sidebar-layer__header">
                                        <h4 class="sidebar-layer__title">@lang('Drawing Area')</h4>
                                        <span class="sidebar-layer__close">
                                            <i class="fa-solid fa-times"></i>
                                        </span>
                                    </div>

                                    <div class="sidebar-layer__body">
                                        <div class="form-group">
                                            <label class="form--label">@lang('Drawing Width')</label>
                                            <div class="input-group input--group">
                                                <input type="number" step="any" id="drawWidthInput" class="form-control form--control drawWidthInput">
                                                <span class="input-group-text">@lang('Inchhes')</span>
                                            </div>
                                        </div>
                                        <div class="form-group">
                                            <label class="form--label">@lang('Drawing Height')</label>
                                            <div class="input-group input--group">
                                                <input type="number" step="any" id="drawHeightInput" class="form-control form--control drawHeightInput">
                                                <span class="input-group-text">@lang('Inchhes')</span>
                                            </div>
                                        </div>
                                        <button type="button" class="btn btn--base w-100 confirmDrawArea" id="confirmDrawArea">@lang('Draw')</button>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="left-layout__item">
                            <label class="btn--sm btn design-option__btn">
                                <input accept="image/*" type="file" data-action="uploadImageInput" class="d-none customer-image-upload">
                                <i class="fa-solid fa-image"></i>
                                <span class="text">@lang('Image')</span>
                            </label>
                        </div>
                        <div class="left-layout__item question-btn">
                            <button type="button" class="btn--sm btn design-option__btn enableInstructionModal" data-bs-toggle="tooltip" data-bs-placement="top" data-bs-original-title="@lang('Design Instruction')">
                                <i class="fa-solid fa-question"></i>
                            </button>
                        </div>
                    </div>
                    <div class="canvas-layout flex-fill">
                        <div class="canvas-layout__header flex-align">
                            <div class="full-screen">
                                <button type="button" class="btn full-screen__btn" data-action="fullScreen" data-bs-toggle="tooltip" data-bs-placement="right" aria-label="Full Screen" data-bs-original-title="@lang('Full Screen')">
                                    <i class="las la-expand-arrows-alt"></i>
                                </button>
                            </div>
                            <div class="canvas-delete">
                                <button type="button" class="btn canvas-delete__btn" data-action="deleteSelected" data-bs-toggle="tooltip" data-bs-placement="right" aria-label="Delete Selected" data-bs-original-title="@lang('Delete Selected')">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" class="icon icon-tabler icons-tabler-outline icon-tabler-trash">
                                        <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                                        <path d="M4 7l16 0" />
                                        <path d="M10 11l0 6" />
                                        <path d="M14 11l0 6" />
                                        <path d="M5 7l1 12a2 2 0 0 0 2 2h8a2 2 0 0 0 2 -2l1 -12" />
                                        <path d="M9 7v-3a1 1 0 0 1 1 -1h4a1 1 0 0 1 1 1v3" />
                                    </svg>
                                </button>
                            </div>
                            <div class="canvas-delete delete-all">
                                <button type="button" class="btn canvas-delete__btn" data-action="clearDesign" data-bs-toggle="tooltip" data-bs-placement="right" aria-label="Delete All" data-bs-original-title="@lang('Delete All')">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" class="icon icon-tabler icons-tabler-outline icon-tabler-restore">
                                        <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                                        <path d="M3.06 13a9 9 0 1 0 .49 -4.087" />
                                        <path d="M3 4.001v5h5" />
                                        <path d="M12 12m-1 0a1 1 0 1 0 2 0a1 1 0 1 0 -2 0" />
                                    </svg>
                                </button>
                            </div>

                            <button type="button" class="btn--xs  btn btn--base ms-auto addToCart">
                                <span class="canvas-btn-icon d-none">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" class="icon icon-tabler icons-tabler-outline icon-tabler-shopping-bag">
                                        <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                                        <path d="M6.331 8h11.339a2 2 0 0 1 1.977 2.304l-1.255 8.152a3 3 0 0 1 -2.966 2.544h-6.852a3 3 0 0 1 -2.965 -2.544l-1.255 -8.152a2 2 0 0 1 1.977 -2.304z" />
                                        <path d="M9 11v-5a3 3 0 0 1 6 0v5" />
                                    </svg>
                                </span>
                                <span class="canvas-btn-text"> @lang('Add to Cart') </span>
                            </button>

                            <button type="submit" class="btn--xs btn btn--base-two ">
                                <span class="canvas-btn-icon d-none">
                                    @lang('Next')
                                </span>
                                <span class="canvas-btn-text"> @lang('Save & Next') </span>
                            </button>
                        </div>
                        <div class="canvas-layout__body">
                            <input type="hidden" class="form--control" name="selected_area[]" value="{{ $customerDesing }}">

                            <div class="designer-canvas position-relative w-100">
                                <canvas class="canvas w-100" data-selected="{{ $printArea->selected_area }}" data-src="{{ getImage(getFilePath('printArea') . '/' . $printArea->image) }}" width="600" height="400"></canvas>
                            </div>
                        </div>
                    </div>
                    <div class="bottom-layout flex-align">
                        <div class="bottom-layout__inner flex-between gap-2 w-100">
                            <div class="bottom-layout__left flex-align gap-2 flex-nowrap">
                                <div class="full-screen position-static">
                                    <button type="button" class="btn full-screen__btn" data-action="fitScreen" data-bs-toggle="tooltip" data-bs-placement="top" title="@lang('Fit Design')">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" class="icon icon-tabler icons-tabler-outline icon-tabler-maximize">
                                            <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                                            <path d="M4 8v-2a2 2 0 0 1 2 -2h2" />
                                            <path d="M4 16v2a2 2 0 0 0 2 2h2" />
                                            <path d="M16 4h2a2 2 0 0 1 2 2v2" />
                                            <path d="M16 20h2a2 2 0 0 0 2 -2v-2" />
                                        </svg>
                                    </button>
                                </div>
                                <div class="canvas-zoom">
                                    <button type="button" data-action="zoomOut" class="btn canvas-zoom__btn zoom-out">
                                        <i class="las la-minus"></i>
                                    </button>
                                    <button type="button" class="btn canvas-zoom__btn zoom-reset">
                                        100%
                                    </button>
                                    <button type="button" data-action="zoomIn" class="btn canvas-zoom__btn zoom-in">
                                        <i class="las la-plus"></i>
                                    </button>
                                </div>

                                <div class="canvas-position" data-bs-toggle="tooltip" data-bs-placement="right" title="@lang('Move Mode')">
                                    <button type="button" class="btn canvas-position__btn move-mode-toggle" data-action="toggleMove">
                                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" width="24" height="24" color="currentColor" fill="none">
                                            <path d="M20.5 9.5V14.1667C20.5 16.34 20.5 17.4267 20.1689 18.2918C19.6627 19.6148 18.6207 20.6601 17.3019 21.1679C16.4395 21.5 15.3562 21.5 13.1896 21.5C12.0534 21.5 11.4853 21.5 10.9566 21.3834C10.1499 21.2056 9.40001 20.8294 8.77419 20.2888C8.36398 19.9344 8.02311 19.4785 7.34137 18.5667L4.33738 14.5487C3.8758 13.9314 3.88907 13.0789 4.36965 12.4763C4.99772 11.6888 6.16877 11.6237 6.8797 12.3369L8.5011 13.9634V10.5V6C8.5011 5.17157 9.17267 4.5 10.0011 4.5C10.8295 4.5 11.5011 5.17157 11.5011 6M11.5011 6V4C11.5011 3.17157 12.1727 2.5 13.0011 2.5C13.8295 2.5 14.5011 3.17157 14.5011 4V6M11.5011 6V10.5M14.5011 6C14.5011 5.17157 15.1727 4.5 16.0011 4.5C16.8295 4.5 17.5011 5.17157 17.5011 6V8M14.5011 6V10.5M20.5011 10.5V8C20.5011 7.17157 19.8295 6.5 19.0011 6.5C18.1727 6.5 17.5011 7.17157 17.5011 8M17.5011 8V10.5" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" />
                                        </svg>
                                    </button>
                                </div>

                            </div>
                            <div class=" d-flex align-items-center justify-content-center flex-wrap footer-message flex-fill text-sm-center">
                                <p class="p-0 pe-2 me-2 border-end m-0">
                                    @lang('Height'):
                                    <i class="fa-solid fa-arrows-up-down"></i>
                                    <strong>{{ pixelsToInches($selectedArea->height) }} @lang('inch')</strong>
                                </p>
                                <p class="p-0 m-0">
                                    @lang('Width'):
                                    <i class="fa-solid fa-arrows-left-right"></i>
                                    <strong>{{ pixelsToInches($selectedArea->width) }} @lang('inch')</strong>
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        @endforeach
    </div>
    <div class="design-area__footer" id="pills-tab" role="tablist">
        @foreach ($printAreas as $printArea)
            <button class="design-area__footer-item @if ($loop->first) active @endif" id="id{{ $printArea->id }}-tab" data-bs-toggle="pill" data-bs-target="#id{{ $printArea->id }}" type="button" role="tab" aria-controls="id{{ $printArea->id }}" aria-selected="{{ $loop->first ? 'true' : 'false' }}">
                {{ __($printArea->name) }}
            </button>
        @endforeach
    </div>
</div>
