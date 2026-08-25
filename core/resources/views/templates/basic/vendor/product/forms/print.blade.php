<div class="row g-3">
    <div class="col-md-12">
        <div class="alert alert--warning" role="alert">
            <p class="text--warning">
                <i class="las la-exclamation-triangle"></i>
                @lang('The inch is converted to pixels at a rate of 1 inch = 10 pixels for the canvas. Please upload the image according to this calculation. Make sure the image fully covers the design area. You can also upload a larger image than the design area.')
            </p>
            <p class="text--warning">
                <i class="las la-exclamation-triangle"></i>
                @lang('If the product has color variations, please upload an SVG image. By uploading an SVG image, users will be able to see and design with the product according to each color variation.')
            </p>
            <p class="text--warning">
                <i class="las la-exclamation-triangle"></i>
                @lang('Note: For SVG images, the color effect area must be inside the first <path> element for the color to be changeable.')
            </p>
        </div>
    </div>
    <div class="col-md-12 text-end mb-3">
        <button type="button" class="btn btn--sm btn-outline--base-two addMoreBtn">
            <i class="la la-plus"></i>
            @lang('Add More')
        </button>
    </div>
</div>
<div class="row gy-3 position-sticky" id="printArea">
    @forelse($product->productPrintAreas as $productPrintArea)
        @include($activeTemplate . '.partials.print_area')
    @empty
        @include($activeTemplate . '.partials.print_area')
    @endforelse

</div>
<div class="form-group d-flex gap-2 mt-5 mb-0">
    <label for="productPublish" class="form--label">@lang('Publish product')</label>
    <x-toggle-switch name="published" value="1" :checked="$product?->is_published" id="productPublish" />
</div>

@push('script-lib')
    <script src="{{ asset('assets/global/js/fabric.min.js') }}"></script>
@endpush

@push('style')
    <style>
        .upper-canvas,
        .canvas {
            border-radius: 4px !important;
        }

        .mw-fit {
            max-width: fit-content;
        }
    </style>
@endpush

@push('script')
    <script>
        (function($) {
            'use strict';

            let isSpacePressed = false;
            const DPI = `{{ gs('print_area_dpi') }}`;

            function inchesToPixels(inch) {
                return inch * DPI;
            }

            // Initialize a single print area canvas
            function initPrintAreaCanvas(container, initialLoadImage = false) {
                let canvasEl = container.find('canvas')[0];
                let imageInput = container.closest('.card').find("input[type='file'][name='print_area_image[]']")[0];
                let widthInput = container.closest('.card').find("input[name='print_area_width[]']")[0];
                let heightInput = container.closest('.card').find("input[name='print_area_height[]']")[0];
                let selectedImageAreaInput = container.closest('.card').find("input[name='selected_area[]']")[0];

                let fabricCanvas = new fabric.Canvas(canvasEl);
                let editableShape = null; // can be rect or circle
                let currentZoom = 1;
                let isMoveMode = false;
                let lastPosX, lastPosY;

                container.find('.canvas-container').css({
                    width: '100%',
                    height: '400px'
                });
                container.find('.upper-canvas').css({
                    width: '100%',
                    height: '400px'
                });
                canvasEl.style.width = '100%';
                canvasEl.style.height = '400px';
                fabricCanvas.setWidth(canvasEl.clientWidth);
                fabricCanvas.setHeight(canvasEl.clientHeight);

                const zoomInBtn = container.find('.zoom-in');
                const zoomOutBtn = container.find('.zoom-out');
                const zoomResetBtn = container.find('.zoom-reset');
                const moveModeBtn = container.find('.move-mode-toggle');
                const shapeSelector = container.find('.shape-selector'); // <select> with 'rect' or 'circle'

                // Zoom buttons
                zoomInBtn.on('click', () => setCanvasZoom(currentZoom + 0.1));
                zoomOutBtn.on('click', () => setCanvasZoom(currentZoom - 0.1));
                zoomResetBtn.on('click', () => setCanvasZoom(1));

                function setCanvasZoom(zoom) {
                    currentZoom = Math.min(Math.max(0.1, zoom), 10);
                    fabricCanvas.setZoom(currentZoom);
                    container.find('.zoom-reset').text(Math.round(currentZoom * 100) + '%');
                }

                // Move mode toggle
                moveModeBtn.on('click', function() {
                    isMoveMode = !isMoveMode;
                    fabricCanvas.selection = !isMoveMode;
                    fabricCanvas.forEachObject(obj => obj.selectable = !isMoveMode);
                    fabricCanvas.discardActiveObject();
                    fabricCanvas.requestRenderAll();
                    moveModeBtn.toggleClass('active-move', isMoveMode);
                    $(fabricCanvas.upperCanvasEl).css('cursor', isMoveMode ? 'move' : 'default');
                });

                // Create editable shape
                function createEditableShape(type, widthPx, heightPx, left = 50, top = 50) {
                    if (editableShape) fabricCanvas.remove(editableShape);

                    if (type === 'circle') {
                        editableShape = new fabric.Circle({
                            left,
                            top,
                            radius: widthPx / 2,
                            fill: 'rgba(0,255,0,0.2)',
                            stroke: 'green',
                            strokeWidth: 1,
                            selectable: true,
                            hasControls: true,
                            lockUniScaling: true,
                            objectCaching: false
                        });
                    } else {
                        editableShape = new fabric.Rect({
                            left,
                            top,
                            width: widthPx,
                            height: heightPx,
                            fill: 'rgba(0,255,0,0.2)',
                            stroke: 'green',
                            strokeWidth: 1,
                            selectable: true,
                            hasControls: true,
                            lockUniScaling: true,
                            objectCaching: false
                        });
                    }

                    fabricCanvas.add(editableShape);
                    fabricCanvas.renderAll();
                    updateEditableAreaInput(editableShape, selectedImageAreaInput);
                }

                // Shape selector change
                shapeSelector.on('change', function() {
                    const type = $(this).val();
                    const widthPx = inchesToPixels(parseFloat(widthInput.value) || 2);
                    const heightPx = inchesToPixels(parseFloat(heightInput.value) || 2);
                    createEditableShape(type, widthPx, heightPx);
                });

                // Width / height input
                $(widthInput).on('change', function() {
                    if (!editableShape) return;
                    const wPx = inchesToPixels(parseFloat(this.value));
                    if (editableShape.type === 'circle') {
                        editableShape.set({
                            radius: wPx / 2,
                            scaleX: 1,
                            scaleY: 1
                        });
                    } else {
                        editableShape.set({
                            width: wPx,
                            scaleX: 1
                        });
                    }
                    fabricCanvas.renderAll();
                    syncShapeToInputs(editableShape);
                });

                $(heightInput).on('change', function() {
                    if (!editableShape) return;
                    const hPx = inchesToPixels(parseFloat(this.value));
                    if (editableShape.type === 'circle') {
                        editableShape.set({
                            radius: hPx / 2,
                            scaleX: 1,
                            scaleY: 1
                        });
                    } else {
                        editableShape.set({
                            height: hPx,
                            scaleY: 1
                        });
                    }
                    fabricCanvas.renderAll();
                    syncShapeToInputs(editableShape);
                });

                function syncShapeToInputs(shape) {
                    if (!shape) return;

                    if (shape.type === 'circle') {
                        // Diameter
                        const diameter = Math.round(shape.radius * 2 * shape.scaleX);
                        widthInput.value = (diameter / DPI).toFixed(2);
                        heightInput.value = (diameter / DPI).toFixed(2);
                    } else {
                        widthInput.value = (shape.width * shape.scaleX / DPI).toFixed(2);
                        heightInput.value = (shape.height * shape.scaleY / DPI).toFixed(2);
                    }

                    updateEditableAreaInput(shape, selectedImageAreaInput);
                }

                // When user resizes/moves/rotates shape
                fabricCanvas.on('object:scaling', function(e) {
                    if (e.target === editableShape) {
                        syncShapeToInputs(editableShape);
                    }
                });

                fabricCanvas.on('object:modified', function(e) {
                    if (e.target === editableShape) {
                        syncShapeToInputs(editableShape);
                    }
                });
                // Handle image upload
                function handleImageUpload(file) {
                    if (!file) return;
                    const dataTransfer = new DataTransfer();
                    dataTransfer.items.add(file);
                    imageInput.files = dataTransfer.files;

                    const reader = new FileReader();
                    reader.onload = function(f) {
                        const imageUrl = f.target.result;
                        fabric.Image.fromURL(imageUrl, function(img) {
                            fabricCanvas.clear();
                            img.set({
                                selectable: false
                            });
                            fabricCanvas.setBackgroundImage(img, fabricCanvas.renderAll.bind(fabricCanvas));
                            fitImageToCanvas(fabricCanvas, img, container);

                            const widthPx = inchesToPixels(parseFloat(widthInput.value) || 2);
                            const heightPx = inchesToPixels(parseFloat(heightInput.value) || 2);
                            const type = shapeSelector.val();

                            createEditableShape(type, widthPx, heightPx, (img.width - widthPx) / 2, (img
                                .height - heightPx) / 2);
                        });
                    };
                    reader.readAsDataURL(file);
                }

                imageInput.addEventListener('change', e => handleImageUpload(e.target.files[0]));

                // Drag & drop
                const dropZone = container[0];
                dropZone.addEventListener('dragover', e => {
                    e.preventDefault();
                    dropZone.classList.add('drag-over');
                });
                dropZone.addEventListener('dragleave', e => {
                    e.preventDefault();
                    dropZone.classList.remove('drag-over');
                });
                dropZone.addEventListener('drop', e => {
                    e.preventDefault();
                    dropZone.classList.remove('drag-over');
                    handleImageUpload(e.dataTransfer.files[0]);
                });

                // Object modified
                fabricCanvas.on('object:modified', function(e) {
                    if (e.target === editableShape) updateEditableAreaInput(editableShape,
                        selectedImageAreaInput);
                });

                // Mouse pan / zoom (same as original)
                fabricCanvas.on('mouse:down', function(opt) {
                    const evt = opt.e;
                    if (isSpacePressed || isMoveMode) {
                        fabricCanvas.isDragging = true;
                        fabricCanvas.lastPosX = evt.clientX;
                        fabricCanvas.lastPosY = evt.clientY;
                        fabricCanvas.selection = false;
                    }
                });
                fabricCanvas.on('mouse:move', function(opt) {
                    if (fabricCanvas.isDragging && (isSpacePressed || isMoveMode)) {
                        const e = opt.e;
                        const vpt = fabricCanvas.viewportTransform;
                        vpt[4] += e.clientX - fabricCanvas.lastPosX;
                        vpt[5] += e.clientY - fabricCanvas.lastPosY;
                        fabricCanvas.requestRenderAll();
                        fabricCanvas.lastPosX = e.clientX;
                        fabricCanvas.lastPosY = e.clientY;
                    }
                });
                fabricCanvas.on('mouse:up', function() {
                    if (fabricCanvas.isDragging) {
                        fabricCanvas.isDragging = false;
                        fabricCanvas.selection = !(isSpacePressed || isMoveMode);
                    }
                });
                fabricCanvas.on('mouse:wheel', function(opt) {
                    const delta = opt.e.deltaY;
                    let zoom = fabricCanvas.getZoom();
                    zoom *= 0.999 ** delta;
                    zoom = Math.min(Math.max(0.1, zoom), 10);
                    fabricCanvas.zoomToPoint({
                        x: opt.e.offsetX,
                        y: opt.e.offsetY
                    }, zoom);
                    currentZoom = zoom;
                    container.find('.zoom-reset').text(Math.round(zoom * 100) + '%');
                    opt.e.preventDefault();
                    opt.e.stopPropagation();
                });

                document.addEventListener('keydown', function(e) {
                    if (e.code === 'Space') {
                        isSpacePressed = true;
                        $(fabricCanvas.upperCanvasEl).css('cursor', 'move');
                    }
                });
                document.addEventListener('keyup', function(e) {
                    if (e.code === 'Space') {
                        isSpacePressed = false;
                        $(fabricCanvas.upperCanvasEl).css('cursor', isMoveMode ? 'move' : 'default');
                    }
                });

                // Load existing image and shape
                if (initialLoadImage) {
                    const existingImageUrl = imageInput.dataset.src;
                    const areaData = selectedImageAreaInput.value ? JSON.parse(selectedImageAreaInput.value) : null;

                    if (existingImageUrl) {
                        fabric.Image.fromURL(existingImageUrl, function(img) {
                            fabricCanvas.clear();
                            img.set({
                                selectable: false
                            });
                            fabricCanvas.setBackgroundImage(img, fabricCanvas.renderAll.bind(fabricCanvas));
                            fitImageToCanvas(fabricCanvas, img, container);

                            if (areaData) {
                                if (areaData.type === 'circle') {
                                    editableShape = new fabric.Circle({
                                        left: areaData.left,
                                        top: areaData.top,
                                        radius: areaData.radius,
                                        fill: 'rgba(0,255,0,0.2)',
                                        stroke: 'green',
                                        strokeWidth: 1,
                                        selectable: true
                                    });
                                    shapeSelector.val('circle');
                                    widthInput.value = heightInput.value = (areaData.radius * 2 / DPI).toFixed(
                                        2);
                                } else {
                                    editableShape = new fabric.Rect({
                                        left: areaData.left,
                                        top: areaData.top,
                                        width: areaData.width,
                                        height: areaData.height,
                                        fill: 'rgba(0,255,0,0.2)',
                                        stroke: 'green',
                                        strokeWidth: 1,
                                        selectable: true
                                    });
                                    shapeSelector.val('rect');
                                    widthInput.value = (areaData.width / DPI).toFixed(2);
                                    heightInput.value = (areaData.height / DPI).toFixed(2);
                                }
                                fabricCanvas.add(editableShape);
                                fabricCanvas.renderAll();
                            }
                        });
                    }
                }

                function updateEditableAreaInput(shape, inputField) {
                    if (!shape || !inputField) return;

                    const shapeData = {
                        type: shape.type,
                        left: Math.round(shape.left),
                        top: Math.round(shape.top),
                        angle: Math.round(shape.angle || 0),
                        width: Math.round(shape.width * shape.scaleX),
                        height: Math.round(shape.height * shape.scaleY),
                    };
                    if (shape.type === 'circle') shapeData.radius = Math.round(shape.radius * shape.scaleX);

                    inputField.value = JSON.stringify(shapeData);
                }
            }

            // Fit background image
            function fitImageToCanvas(fabricCanvas, img, container) {
                const canvasWidth = fabricCanvas.getWidth();
                const canvasHeight = fabricCanvas.getHeight();
                const imgWidth = img.width;
                const imgHeight = img.height;

                let scale = 1;
                if (imgWidth > canvasWidth || imgHeight > canvasHeight) scale = Math.min(canvasWidth / imgWidth,
                    canvasHeight / imgHeight);

                fabricCanvas.setZoom(scale);
                fabricCanvas.viewportTransform[4] = (canvasWidth - imgWidth * scale) / 2;
                fabricCanvas.viewportTransform[5] = (canvasHeight - imgHeight * scale) / 2;
                container.find('.zoom-reset').text(Math.round(scale * 100) + '%');
            }

            // Initialize all print areas
            let initialLoadImage = `{{ count($product?->productPrintAreas) }}`;
            $('.printSideAreaItem').each(function() {
                initPrintAreaCanvas($(this), initialLoadImage);
            });




            // When Add More button is clicked
            $('.addMoreBtn').on('click', function() {
                let totalElement = $(document).find("input[name='id[]']").length;

                addNewPrintElement(++totalElement);

                setTimeout(() => {
                    let newItem = $('#printArea .printSideAreaItem').last();
                    initPrintAreaCanvas(newItem);

                    window.scrollTo({
                        top: document.body.scrollHeight,
                        behavior: "smooth"
                    });
                }, 50);
            });

            function updateImageMessage(width, height, container) {
                let message = `@lang('Please upload an image where the design area is properly defined.')`;
                if (height > 0 && width > 0) {
                    message =
                        `@lang('The design area must be exactly') ${inchesToPixels(width).toFixed(2)}×${inchesToPixels(height).toFixed(2)} @lang('pixels, but the image can be larger.')`;
                }
                container.find('.imageUploadMessage').text(message);
            }


            function addNewPrintElement(totalElement) {
                const printAreaElements = `<div class="col-md-12">
                    <input type="hidden" name="id[]" value="">
                    <div class="card custom--card printarea--card">
                        <div class="card-header variant-item d-flex align-items-center justify-content-between flex-wrap gap-2">
                            <h3 class="card-title mb-0">@lang('Print Area')</h3>
                            <div class="d-flex align-items-center gap-3 variant-item-bottom mt-3 mb-2">
                                <button type="button" class=" remove-btn removePrintArea"><i class="la la-times"></i></button>
                                <a class="toggle-btn expandPrintAreaBtn" data-bs-toggle="collapse"
                                    href="#printSideArea${totalElement}" role="button" aria-expanded="true"
                                    aria-controls="printSideArea${totalElement}">
                                    <i class="las la-angle-down"></i>
                                </a>
                            </div>
                        </div>

                        <div class="card-body printSideAreaItem collapse show" id="printSideArea${totalElement}">
                            <div class="row gy-4">
                                <div class="col-md-12">
                                    <div class="row gx-5">
                                        <div class="col-12">
                                            <div class="row">
                                                <div class="col-md-6">
                                                    <div class="form-group">
                                                        <label class="form--label required">@lang('Name')</label>
                                                        <input type="text" class="form--control" name="name[]" required>
                                                    </div>
                                                </div>
                                                <div class="col-md-6">
                                                    <div class="form-group">
                                                        <label class="form--label required" for="regular_price">@lang('Print Area Width')</label>
                                                        <div class="input-group input--group">
                                                            <input type="number" class="form--control form-control" step="any" name="print_area_width[]"
                                                            value="" required>
                                                            <span class="input-group-text">@lang('Inches')</span>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="col-md-6">
                                                    <div class="form-group">
                                                        <label class="form--label required" for="regular_price">@lang('Print Area Height')</label>
                                                        <div class="input-group input--group">
                                                            <input type="number" class="form--control form-control" step="any" name="print_area_height[]"
                                                            value="" required>
                                                            <span class="input-group-text">@lang('Inches')</span>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="col-md-6">
                                                    <div class="form-group">
                                                        <label class="form--label printAreaImage">@lang('Upload Print Area Image')</label>
                                                        <input type="file" class="form--control" name="print_area_image[]" required>
                                                        <small class="text--base-two">
                                                            <i class="la la-info-circle"></i>
                                                            @lang('Supported Files: .png, .jpg, .jpeg.')
                                                            <span class="imageUploadMessage">@lang('Please upload an image where the design area is properly defined.')</span>
                                                        </small>
                                                    </div>
                                                </div>
                                                <div class="col-md-12">
                                                    <div class="form-group">
                                                        <div class="flex-between gap-2 mb-2 ">
                                                            <label class="form--label mb-0 required">@lang('Select Print Area')</label>
                                                            <select class="shape-selector form-select py-2 mw-fit ">
                                                                <option value="rect">@lang('Rectangle')</option>
                                                                <option value="circle">@lang('Circle')</option>
                                                            </select>
                                                        </div>
                                                        <input type="hidden" class="form--control" name="selected_area[]" required>
                                                        <div class="d-flex justify-content-center custom-canvas-container">
                                                            <canvas class="canvas border" width="600" height="400">
                                                                @lang('Upload Print Area Image')
                                                            </canvas>
                                                            <div class="canvas-position" data-bs-toggle="tooltip" data-bs-placement="right"
                                                                title="@lang('Move Mode')">
                                                                <button type="button" class="btn canvas-position__btn move-mode-toggle"
                                                                    data-action="toggleMove">
                                                                    <span class="move-mode-not-active">
                                                                        <svg width="30" height="30" viewBox="0 0 40 40" fill="none"
                                                                            xmlns="http://www.w3.org/2000/svg" style="stroke-width: 0;">
                                                                            <path
                                                                                d="M23.6142 16.6667C24.0475 16.4881 24.5462 16.5868 24.8778 16.9168L27.1966 19.2245C27.6494 19.6751 27.6494 20.4057 27.1966 20.8563C26.7438 21.3069 26.0097 21.3069 25.5569 20.8563L25.2174 20.5183V24.6557H29.3748L29.0352 24.3178C28.5824 23.8672 28.5824 23.1366 29.0352 22.686C29.488 22.2354 30.2221 22.2354 30.6749 22.686L32.9937 24.9937C33.4465 25.4443 33.4465 26.1749 32.9937 26.6255L30.6749 28.9332C30.2221 29.3838 29.488 29.3838 29.0352 28.9332C28.5824 28.4826 28.5824 27.752 29.0352 27.3014L29.3748 26.9634H25.2174V31.1009L25.5569 30.7629C26.0097 30.3123 26.7438 30.3123 27.1966 30.7629C27.6494 31.2135 27.6494 31.9441 27.1966 32.3947L24.8778 34.7024C24.425 35.153 23.6909 35.153 23.2381 34.7024L20.9193 32.3947C20.4665 31.9441 20.4665 31.2135 20.9193 30.7629C21.372 30.3123 22.1061 30.3123 22.5589 30.7629L22.8985 31.1009V26.9634H18.7411L19.0807 27.3014C19.5334 27.752 19.5334 28.4826 19.0807 28.9332C18.6279 29.3838 17.8938 29.3838 17.441 28.9332L15.1222 26.6255C14.7906 26.2955 14.6914 25.7992 14.8708 25.368C15.0503 24.9368 15.473 24.6557 15.942 24.6557H22.8985V17.7327C22.8985 17.266 23.181 16.8453 23.6142 16.6667Z"
                                                                                fill="currentColor"></path>
                                                                            <path fill-rule="evenodd" clip-rule="evenodd"
                                                                                d="M14.2029 5C11.3214 5 8.98547 7.32468 8.98547 10.1923V10.7692C8.98547 11.4065 9.50456 11.9231 10.1449 11.9231C10.7852 11.9231 11.3043 11.4065 11.3043 10.7692V10.1923C11.3043 8.59918 12.602 7.30769 14.2029 7.30769C15.8037 7.30769 17.1014 8.59918 17.1014 10.1923V13.3333H8.98547C7.70481 13.3333 6.66663 14.3665 6.66663 15.641V21.0256C6.66663 22.3001 7.70481 23.3333 8.98547 23.3333H19.4203C20.7009 23.3333 21.7391 22.3001 21.7391 21.0256V15.641C21.7391 14.3665 20.7009 13.3333 19.4203 13.3333V10.1923C19.4203 7.32468 17.0843 5 14.2029 5ZM8.98547 15.641V21.0256H19.4203V15.641H8.98547Z"
                                                                                fill="currentColor"></path>
                                                                        </svg>
                                                                    </span>
                                                                    <span class="move-mode-active">
                                                                        <svg width="30" height="30" viewBox="0 0 40 40" fill="none"
                                                                            xmlns="http://www.w3.org/2000/svg">
                                                                            <path fill-rule="evenodd" clip-rule="evenodd"
                                                                                d="M13.8406 5C10.779 5 8.29715 7.41766 8.29715 10.4L8.29714 13.3333C6.93644 13.3333 5.83337 14.4078 5.83337 15.7333L5.83338 20.9333C5.83338 22.2588 6.93644 23.3333 8.29715 23.3333H19.3841C20.7448 23.3333 21.8479 22.2588 21.8479 20.9333L21.8479 15.7333C21.8479 14.4079 20.7448 13.3333 19.3841 13.3333L19.3841 10.4C19.3841 7.41766 16.9022 5 13.8406 5ZM16.9203 10.4L16.9203 13.3333H10.7609L10.7609 10.4C10.7609 8.74315 12.1397 7.4 13.8406 7.4C15.5415 7.4 16.9203 8.74315 16.9203 10.4Z"
                                                                                fill="currentColor"></path>
                                                                            <path
                                                                                d="M25.1827 16.1515C24.8304 15.8083 24.3005 15.7056 23.8402 15.8914C23.3799 16.0771 23.0798 16.5147 23.0798 17V24.2H15.6884C15.1902 24.2 14.741 24.4924 14.5503 24.9408C14.3597 25.3892 14.4651 25.9053 14.8174 26.2485L17.2811 28.6485C17.7622 29.1172 18.5422 29.1172 19.0233 28.6485C19.5044 28.1799 19.5044 27.4201 19.0233 26.9515L18.6625 26.6H23.0798V30.9029L22.7189 30.5515C22.2379 30.0828 21.4579 30.0828 20.9768 30.5515C20.4957 31.0201 20.4957 31.7799 20.9768 32.2485L23.4406 34.6485C23.9216 35.1172 24.7016 35.1172 25.1827 34.6485L27.6465 32.2485C28.1276 31.7799 28.1276 31.0201 27.6465 30.5515C27.1654 30.0828 26.3854 30.0828 25.9043 30.5515L25.5435 30.9029V26.6H29.9608L29.6 26.9515C29.1189 27.4201 29.1189 28.1799 29.6 28.6485C30.0811 29.1172 30.8611 29.1172 31.3421 28.6485L33.8059 26.2485C34.287 25.7799 34.287 25.0201 33.8059 24.5515L31.3421 22.1515C30.8611 21.6828 30.0811 21.6828 29.6 22.1515C29.1189 22.6201 29.1189 23.3799 29.6 23.8485L29.9608 24.2H25.5435V19.8971L25.9043 20.2486C26.3854 20.7172 27.1654 20.7172 27.6465 20.2486C28.1276 19.7799 28.1276 19.0201 27.6465 18.5515L25.1827 16.1515Z"
                                                                                fill="currentColor"></path>
                                                                        </svg>
                                                                    </span>
                                                                </button>
                                                            </div>
                                                            <div class="canvas-zoom">
                                                                <button type="button" data-action="zoomIn" class="btn canvas-zoom__btn zoom-in">
                                                                    <i class="las la-plus"></i>
                                                                </button>
                                                                <button type="button" class="btn canvas-zoom__btn zoom-reset">
                                                                    100%
                                                                </button>
                                                                <button type="button" data-action="zoomOut" class="btn canvas-zoom__btn zoom-out">
                                                                    <i class="las la-minus"></i>
                                                                </button>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>`;

                $('#printArea').append(printAreaElements);
            }

            $(document).on('click', '.removePrintArea', function() {
                $(this).closest('.col-md-12').remove();
            });

        })(jQuery);
    </script>
@endpush
