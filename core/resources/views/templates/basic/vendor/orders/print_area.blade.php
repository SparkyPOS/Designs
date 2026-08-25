@extends($activeTemplate . 'layouts.master')

@section('content')
    <div class="order-details mt-2">
        <div class="order-details-top justify-content-between gap-2">
            <span class="d-inline-flex align-items-center gap-2">
                @php echo $order->paymentBadge() @endphp
                @php echo $order->statusBadge() @endphp
            </span>
            <h4 class="order-details-id mb-0 d-flex align-items-center flex-wrap gap-3 mb-3">
                <span class="order-details-id">#{{ $order->order_number }}</span>
            </h4>
        </div>


        <div class="row g-5 pt-3">
            @foreach ($printAreas as $printArea)
                @include($activeTemplate . '.partials.order_print_area')
            @endforeach
        </div>

    </div>
@endsection
@push('breadcrumb-plugins')
    <a href="{{ route('vendor.order.details', $order->order_number) }}" class="btn btn-outline--base-two text-white">
        <i class="las la-angle-left"></i>
        @lang('Back to Orders Details')
    </a>
@endpush

@push('script-lib')
    <script src="{{ asset('assets/global/js/fabric.min.js') }}"></script>
@endpush
@push('script')
    <script>
        (function($) {
            "use strict";

            let isSpacePressed = false;
            const DPI = {{ gs('print_area_dpi') }};
            const pxToInch = px => (px / DPI).toFixed(2);
            const desingAreaBackground = "{{ $colorCode ? '#' . $colorCode : 'transparent' }}";

            fabric.Object.prototype.toObject = (function(originalToObject) {
                return function(additionalProps) {
                    return originalToObject.call(this, ['shapeName', 'originalSrc', 'customInfo'].concat(
                        additionalProps || []));
                };
            })(fabric.Object.prototype.toObject);

            function initDesignCanvas(container) {
                const canvasEl = container.find('canvas')[0];
                const hiddenInput = container.find('input[name="selected_area[]"]')[0];
                const imageURL = canvasEl.dataset.src;
                const editableAreaData = JSON.parse(canvasEl.dataset.selected || 'null');
                const canvas = new fabric.Canvas(canvasEl, {
                    selection: false,
                    hoverCursor: 'pointer',
                });

                canvasEl.fabricCanvas = canvas;
                resizeCanvas(canvas, container);
                $(window).on('resize', resizeCanvas);

                const inspector = container.find('.design-inspector');
                const noSelectionDiv = inspector.find('.no-selection');
                const propsDiv = inspector.find('.props');

                container.find('[data-action="zoomIn"]').on('click', () => zoom(canvas, +0.1, container));
                container.find('[data-action="zoomOut"]').on('click', () => zoom(canvas, -0.1, container));
                container.find('.zoom-reset').on('click', () => zoomReset(canvas, container));
                container.find('.download-full-design').off('click').on('click', () => {
                    downloadFullDesign(canvas, editableAreaData);
                });
                container.find('[data-action="toggleMove"]').on('click', () => toggleMove(canvas, container));
                container.find('[data-action="fitFullScreen"]').on('click', () => fitScreen(canvas, container, true));


                // Pan/Move support
                canvas.on('mouse:down', function(opt) {
                    if (!canvas.isMoveMode) return;
                    const evt = opt.e;
                    canvas.lastPosX = evt.clientX;
                    canvas.lastPosY = evt.clientY;
                    canvas.isDragging = true;
                    canvas.selection = false;
                });

                canvas.on('mouse:move', function(opt) {
                    if (!canvas.isDragging || !canvas.isMoveMode) return;
                    const e = opt.e;
                    const vpt = canvas.viewportTransform;
                    vpt[4] += e.clientX - canvas.lastPosX;
                    vpt[5] += e.clientY - canvas.lastPosY;
                    canvas.requestRenderAll();
                    canvas.lastPosX = e.clientX;
                    canvas.lastPosY = e.clientY;
                });

                canvas.on('mouse:up', function() {
                    canvas.isDragging = false;
                    if (!canvas.isMoveMode) canvas.selection = true;
                });

                function showInfo(obj) {
                    if (!obj) return clearInfo();
                    canvas.setActiveObject(obj);
                    canvas.renderAll();

                    noSelectionDiv.hide();
                    propsDiv.show();

                    const info = obj.customInfo || {};

                    propsDiv.find('.prop-type').text(info.shapeName || obj.shapeName || (obj._originalTextbox ?
                        'Curved Text' : '-'));
                    propsDiv.find('.prop-text').text(info.text || obj.text || '-');

                    const fontSize = info.fontSize || obj.fontSize;
                    propsDiv.find('.prop-font-size').text(
                        fontSize ? `${pxToInch(fontSize)} in (${fontSize}px)` : '-'
                    );

                    propsDiv.find('.prop-font-family').text(info.fontFamily || obj.fontFamily || '-');
                    propsDiv.find('.prop-color').text(info.fill || obj.fill || '-');
                    propsDiv.find('.prop-bold').text((info.fontWeight === 'bold' || obj.fontWeight === 'bold') ? 'Yes' :
                        'No');
                    propsDiv.find('.prop-underline').text(info.underline || obj.underline ? 'Yes' : 'No');
                    propsDiv.find('.prop-stroke-color').text(info.stroke || obj.stroke || '-');
                    propsDiv.find('.prop-stroke-width').text(
                        info.strokeWidth != null ? info.strokeWidth : (obj.strokeWidth != null ? obj.strokeWidth :
                            '-')
                    );

                    if (obj.type === 'circle') {
                        // For circles, get the actual radius accounting for scaling and background scaling
                        const actualRadius = obj.radius * (obj.scaleX || 1);
                        const actualDiameter = actualRadius * 2;

                        // Convert back to original dimensions if background is scaled
                        const bgImg = canvas.backgroundImage;
                        const originalRadius = bgImg ? actualRadius / bgImg.scaleX : actualRadius;
                        const originalDiameter = originalRadius * 2;

                        propsDiv.find('.circle-radius-row').show();
                        propsDiv.find('.prop-radius').text(
                            `${pxToInch(originalRadius)} in (${Math.round(originalRadius)}px)`);
                        propsDiv.find('.prop-width').text(
                            `${pxToInch(originalDiameter)} in (${Math.round(originalDiameter)}px)`);
                        propsDiv.find('.prop-height').text(
                            `${pxToInch(originalDiameter)} in (${Math.round(originalDiameter)}px)`);
                    } else {
                        // For other objects, get actual dimensions accounting for scaling
                        const actualWidth = obj.width * (obj.scaleX || 1);
                        const actualHeight = obj.height * (obj.scaleY || 1);

                        // Convert back to original dimensions if background is scaled
                        const bgImg = canvas.backgroundImage;
                        const originalWidth = bgImg ? actualWidth / bgImg.scaleX : actualWidth;
                        const originalHeight = bgImg ? actualHeight / bgImg.scaleY : actualHeight;

                        propsDiv.find('.circle-radius-row').hide();
                        propsDiv.find('.prop-width').text(
                            `${pxToInch(originalWidth)} in (${Math.round(originalWidth)}px)`);
                        propsDiv.find('.prop-height').text(
                            `${pxToInch(originalHeight)} in (${Math.round(originalHeight)}px)`);
                    }

                    // Calculate position relative to editable area, accounting for background image scaling
                    const bgImg = canvas.backgroundImage;
                    let displayLeft = obj.left;
                    let displayTop = obj.top;

                    if (editableAreaData && bgImg) {
                        let scaledEditableLeft, scaledEditableTop;

                        if (editableAreaData.type === 'circle') {
                            // For circles, the editable area position is the top-left corner of the bounding box
                            // But we need to calculate position relative to the circle center
                            const scaledRadius = editableAreaData.radius * bgImg.scaleX;
                            scaledEditableLeft = bgImg.left + editableAreaData.left * bgImg.scaleX;
                            scaledEditableTop = bgImg.top + editableAreaData.top * bgImg.scaleY;
                        } else {
                            // For rectangles, use top-left corner
                            scaledEditableLeft = bgImg.left + editableAreaData.left * bgImg.scaleX;
                            scaledEditableTop = bgImg.top + editableAreaData.top * bgImg.scaleY;
                        }

                        // Get position relative to scaled editable area
                        const relativeLeft = obj.left - scaledEditableLeft;
                        const relativeTop = obj.top - scaledEditableTop;

                        // Convert to original scale
                        displayLeft = relativeLeft / bgImg.scaleX;
                        displayTop = relativeTop / bgImg.scaleY;
                    } else if (editableAreaData) {
                        // Fallback if no background image scaling
                        displayLeft = obj.left - editableAreaData.left;
                        displayTop = obj.top - editableAreaData.top;
                    }

                    propsDiv.find('.prop-left').text(
                        `${pxToInch(displayLeft)} in (${Math.round(displayLeft)}px)`
                    );
                    propsDiv.find('.prop-top').text(
                        `${pxToInch(displayTop)} in (${Math.round(displayTop)}px)`
                    );

                    if (obj.shapeName === 'Image' && obj.originalSrc) {
                        container.find('.download-original-image').removeClass('d-none').off('click').on('click',
                            function() {
                                downloadOriginalImage(obj);
                            });
                    } else {
                        container.find('.download-original-image').addClass('d-none');
                    }

                    if (obj) {
                        container.find('.download-shape-image').removeClass('d-none').off('click').on('click',
                            function() {
                                downloadObjectImage(obj);
                            });
                    } else {
                        container.find('.download-shape-image').addClass('d-none');
                    }
                }



                function clearInfo() {
                    container.find('.download-shape-image').addClass('d-none');
                    noSelectionDiv.show();
                    propsDiv.hide();
                    canvas.discardActiveObject();
                    canvas.renderAll();
                }

                function drawEditableArea() {
                    if (!editableAreaData) return;

                    const containerEl = container.find('.canvas-container')[0];
                    const canvasWidth = canvas.getWidth();
                    const canvasHeight = canvas.getHeight();

                    const bgImg = canvas.backgroundImage;
                    if (!bgImg) return;

                    // Image scale and offset
                    const imgScale = bgImg.scaleX; // uniform scale
                    const imgLeft = bgImg.left;
                    const imgTop = bgImg.top;

                    // detect svg or normal image
                    const isSvg = imageURL && imageURL.toLowerCase().endsWith('.svg');

                    // fill color depending on type
                    let fillColor = 'transparent';
                    if (!isSvg) {
                        fillColor = desingAreaBackground;
                    }


                    // border color should contrast with fill (if svg) OR background color (if image)
                    const compareColor = (isSvg ? desingAreaBackground : desingAreaBackground);
                    let strokeColor = 'red';
                    if (compareColor && compareColor !== 'transparent') {
                        strokeColor = getContrastingColor(compareColor);
                    }

                    // Adjust editable area to match scaled image
                    let area;

                    if (editableAreaData.type === 'circle') {
                        const radius = editableAreaData.radius * imgScale;
                        const centerX = imgLeft + editableAreaData.left * imgScale + radius;
                        const centerY = imgTop + editableAreaData.top * imgScale + radius;

                        area = new fabric.Circle({
                            left: centerX,
                            top: centerY,
                            originX: 'center',
                            originY: 'center',
                            radius: radius,
                            fill: fillColor,
                            stroke: strokeColor,
                            strokeDashArray: [5, 5],
                            selectable: false,
                            evented: false,
                            objectCaching: false,
                            isEditableBorder: true,
                        });
                    } else {
                        area = new fabric.Rect({
                            left: imgLeft + editableAreaData.left * imgScale,
                            top: imgTop + editableAreaData.top * imgScale,
                            width: editableAreaData.width * imgScale,
                            height: editableAreaData.height * imgScale,
                            fill: fillColor,
                            stroke: strokeColor,
                            strokeDashArray: [5, 5],
                            selectable: false,
                            evented: false,
                            objectCaching: false,
                            isEditableBorder: true,
                        });
                    }

                    canvas.add(area);
                    canvas.sendToBack(area);
                }

                function makeNonEditable(obj) {
                    obj.selectable = false;
                    obj.evented = true;
                    obj.lockMovementX = true;
                    obj.lockMovementY = true;
                    obj.lockScalingX = true;
                    obj.lockScalingY = true;
                    obj.lockRotation = true;
                    obj.lockUniScaling = true;
                    obj.lockSkewingX = true;
                    obj.lockSkewingY = true;
                    obj.hasControls = false;
                    obj.hasBorders = true;
                    obj.editable = false; // if text, cannot edit
                }

                const containerEl = container.find('.canvas-container')[0];
                const canvasWidth = containerEl.clientWidth;
                const canvasHeight = containerEl.clientHeight;
                canvas.setWidth(canvasWidth);
                canvas.setHeight(canvasHeight);

                const savedJson = JSON.parse(hiddenInput.value);
                canvas.loadFromJSON(savedJson, () => {
                    canvas.getObjects().forEach(obj => {
                        makeNonEditable(obj)
                        // restrictObject(obj); // still keep inside editable area if needed
                    });

                    drawEditableArea(); // redraw the border
                    canvas.requestRenderAll();
                });

                canvas.on('mouse:down', function(opt) {
                    const pointer = canvas.getPointer(opt.e, true); // true ensures zoom & pan are respected
                    const objects = canvas.getObjects();
                    for (let i = objects.length - 1; i >= 0; i--) {
                        const obj = objects[i];
                        if (obj.containsPoint(pointer) && !obj.excludeFromExport) { // Skip border
                            showInfo(obj);
                            return;
                        }
                    }
                    clearInfo();
                });

                canvas.on('mouse:wheel', function(opt) {
                    const delta = opt.e.deltaY;
                    let zoom = canvas.getZoom();
                    zoom *= 0.999 ** delta; // Smooth zoom
                    zoom = Math.min(Math.max(0.1, zoom), 10);
                    const pointer = canvas.getPointer(opt.e);

                    // Zoom relative to pointer
                    canvas.zoomToPoint({
                        x: opt.e.offsetX,
                        y: opt.e.offsetY
                    }, zoom);

                    opt.e.preventDefault();
                    opt.e.stopPropagation();

                    // Update zoom percentage display
                    const container = $(canvas.upperCanvasEl).closest('.printSideAreaItem');
                    container.find('.zoom-reset').text(Math.round(zoom * 100) + '%');
                });


                // Pan support with space + mouse
                canvas.on('mouse:down', function(opt) {
                    const evt = opt.e;
                    if (isSpacePressed) {
                        canvas.isDragging = true;
                        canvas.lastPosX = evt.clientX;
                        canvas.lastPosY = evt.clientY;
                        canvas.selection = false;
                        canvas.defaultCursor = 'move';
                    }
                });

                canvas.on('mouse:move', function(opt) {
                    if (canvas.isDragging && isSpacePressed) {
                        const e = opt.e;
                        const vpt = canvas.viewportTransform;
                        vpt[4] += e.clientX - canvas.lastPosX;
                        vpt[5] += e.clientY - canvas.lastPosY;
                        canvas.requestRenderAll();
                        canvas.lastPosX = e.clientX;
                        canvas.lastPosY = e.clientY;
                    }
                });

                canvas.on('mouse:up', function() {
                    if (canvas.isDragging && isSpacePressed) {
                        canvas.isDragging = false;
                        canvas.selection = true;
                        canvas.defaultCursor = 'default';
                    }
                });


                clearInfo();
                setTimeout(() => {
                    fitScreen(canvas, container);
                    zoom(canvas, 0.00001, container)
                }, 100);
            }


            function fitScreen(canvas, container, isClick = false) {
                const editableAreaData = JSON.parse(canvas.lowerCanvasEl.dataset.selected || 'null');
                if (!editableAreaData) return;

                const bg = canvas.backgroundImage;
                if (!bg) return;

                const canvasWidth = canvas.getWidth();
                const canvasHeight = canvas.getHeight();
                const padding = 50;

                // Scaled editable area (include background image offset and scale)
                const areaLeft = bg.left + editableAreaData.left * bg.scaleX;
                const areaTop = bg.top + editableAreaData.top * bg.scaleY;
                const areaWidth = editableAreaData.width * bg.scaleX;
                const areaHeight = editableAreaData.height * bg.scaleY;

                // Zoom to fit editable area
                const zoomX = (canvasWidth - padding * 2) / areaWidth;
                const zoomY = (canvasHeight - padding * 2) / areaHeight;
                let zoom = Math.min(zoomX, zoomY);
                zoom = Math.max(0.1, Math.min(zoom, 5));

                // Center editable area
                const areaCenterX = areaLeft + areaWidth / 2;
                const areaCenterY = areaTop + areaHeight / 2;

                const offsetX = canvasWidth / 2 - areaCenterX * zoom;
                const offsetY = canvasHeight / 2 - areaCenterY * zoom;

                canvas.viewportTransform = [zoom, 0, 0, zoom, offsetX, offsetY];
                canvas.requestRenderAll();

                container.find('.zoom-reset').text(Math.round(zoom * 100) + '%');

                if (isClick) {
                    const z = Math.max(0.1, Math.min(10, canvas.getZoom() + 0.0000001));
                    canvas.setZoom(z);
                }
            }

            function getContrastingColor(hex) {
                if (!hex || hex === 'transparent') return 'red'; // fallback

                hex = hex.replace('#', '');
                if (hex.length === 3) hex = hex.split('').map(c => c + c).join('');

                const r = parseInt(hex.substr(0, 2), 16);
                const g = parseInt(hex.substr(2, 2), 16);
                const b = parseInt(hex.substr(4, 2), 16);

                const luminance = (0.299 * r + 0.587 * g + 0.114 * b) / 255;

                return luminance > 0.5 ? '#000000' : '#FFFFFF';
            }

            function resizeCanvas(canvas, container) {
                const wrapper = container.find('.designer-canvas');
                const width = wrapper.width();
                const height = wrapper.height();

                canvas.setWidth(width);
                canvas.setHeight(height);
                canvas.calcOffset();
                canvas.renderAll();
            }


            function zoom(canvas, step, container) {
                const z = Math.max(.1, Math.min(10, canvas.getZoom() + step));
                canvas.setZoom(z);

                const percentage = Math.round(z * 100) + '%';
                container.find('.zoom-reset').text(percentage);
            }

            function zoomReset(canvas, container) {
                canvas.setZoom(1);
                container.find('.zoom-reset').text('100%');
            }

            $('.printSideAreaItem').each(function() {
                initDesignCanvas($(this));
            });


            function toggleMove(canvas, container) {
                canvas.isMoveMode = !canvas.isMoveMode;

                if (canvas.isMoveMode) {
                    canvas.selection = false;
                    canvas.defaultCursor = 'move';
                    canvas.interactive = false; // Disable object interaction

                    canvas.forEachObject(obj => {
                        obj.selectable = false;
                        obj.evented = false;
                    });

                } else {
                    // Move/pan mode OFF
                    canvas.selection = true;
                    canvas.defaultCursor = 'default';
                    canvas.interactive = true; // Enable object interaction

                    canvas.forEachObject(obj => {
                        obj.selectable = false; // Keep objects non-selectable
                        obj.evented = true; // But allow events for info display
                    });

                    canvas.calcOffset(); // Fix pointer offset recalculation
                    zoom(canvas, +0.00000001, container);
                }

                canvas.discardActiveObject();
                canvas.requestRenderAll();

                const moveModeBtn = $(canvas.upperCanvasEl)
                    .closest('.printSideAreaItem')
                    .find('[data-action="toggleMove"]');
                moveModeBtn.toggleClass('active-move', canvas.isMoveMode);
            }


            function downloadOriginalImage(imgObj) {
                if (!imgObj.originalSrc) {
                    alert(`@lang('No original image data available')`);
                    return;
                }
                const link = document.createElement('a');
                link.href = imgObj.originalSrc; // base64 or URL
                link.download = 'original-image.png'; // file name
                document.body.appendChild(link);
                link.click();
                document.body.removeChild(link);
            }

            function downloadObjectImage(obj) {
                if (!obj) {
                    alert('No object selected');
                    return;
                }

                if (obj.originalSrc) {
                    // Original uploaded image thakle seta download korano jabe
                    const link = document.createElement('a');
                    link.href = obj.originalSrc;
                    link.download = 'original-image-{{ $order->order_number }}.png';
                    document.body.appendChild(link);
                    link.click();
                    document.body.removeChild(link);
                } else {
                    // Onno shape ba text hole clone kore export korbo
                    const tempCanvas = new fabric.Canvas(null, {
                        width: obj.width * (obj.scaleX || 1) + (obj.strokeWidth || 0) * 2,
                        height: obj.height * (obj.scaleY || 1) + (obj.strokeWidth || 0) * 2,
                    });

                    // Clone object
                    obj.clone(clonedObj => {
                        clonedObj.set({
                            left: tempCanvas.width / 2,
                            top: tempCanvas.height / 2,
                            originX: 'center',
                            originY: 'center',
                            selectable: false,
                            evented: false,
                        });
                        tempCanvas.add(clonedObj);
                        tempCanvas.renderAll();

                        const dataURL = tempCanvas.toDataURL({
                            format: 'png',
                            quality: 1,
                        });

                        const link = document.createElement('a');
                        link.href = dataURL;
                        link.download = (obj.shapeName || 'text') + '-{{ $order->order_number }}.png';
                        document.body.appendChild(link);
                        link.click();
                        document.body.removeChild(link);

                        tempCanvas.dispose(); // cleanup
                    });
                }
            }


            function downloadFullDesign(canvas, editableAreaData) {
                canvas.discardActiveObject(); // remove selection box
                canvas.renderAll();

                if (!editableAreaData) {
                    const canvasEl = canvas.lowerCanvasEl;
                    editableAreaData = JSON.parse(canvasEl.dataset.selected || 'null');
                }

                if (!editableAreaData) {
                    alert("No editable area defined.");
                    return;
                }

                const bgImg = canvas.backgroundImage;
                if (!bgImg) {
                    alert("Background image not found.");
                    return;
                }

                // Calculate actual editable area on canvas (with scaling)
                const scaledEditableArea = {
                    left: bgImg.left + editableAreaData.left * bgImg.scaleX,
                    top: bgImg.top + editableAreaData.top * bgImg.scaleY,
                    width: editableAreaData.width * bgImg.scaleX,
                    height: editableAreaData.height * bgImg.scaleY,
                    radius: editableAreaData.radius ? editableAreaData.radius * bgImg.scaleX : null,
                    type: editableAreaData.type || 'rect'
                };

                const allObjects = canvas.getObjects();

                const objectsInEditableArea = allObjects.filter(obj => {
                    if (obj.isEditableBorder || obj.excludeFromExport) return false;

                    const objBounds = obj.getBoundingRect(true);

                    if (scaledEditableArea.type === 'circle') {
                        // Circle center & radius
                        const cx = scaledEditableArea.left + scaledEditableArea.radius;
                        const cy = scaledEditableArea.top + scaledEditableArea.radius;
                        const r = scaledEditableArea.radius;

                        // Object corners
                        const corners = [{
                                x: objBounds.left,
                                y: objBounds.top
                            },
                            {
                                x: objBounds.left + objBounds.width,
                                y: objBounds.top
                            },
                            {
                                x: objBounds.left,
                                y: objBounds.top + objBounds.height
                            },
                            {
                                x: objBounds.left + objBounds.width,
                                y: objBounds.top + objBounds.height
                            },
                        ];

                        return corners.some(c => {
                            const dx = c.x - cx;
                            const dy = c.y - cy;
                            return dx * dx + dy * dy <= r * r;
                        });
                    } else {
                        // Rect overlap
                        return !(objBounds.left + objBounds.width <= scaledEditableArea.left ||
                            objBounds.left >= scaledEditableArea.left + scaledEditableArea.width ||
                            objBounds.top + objBounds.height <= scaledEditableArea.top ||
                            objBounds.top >= scaledEditableArea.top + scaledEditableArea.height);
                    }
                });

                if (objectsInEditableArea.length === 0) {
                    const lenientObjects = allObjects.filter(obj => !obj.isEditableBorder);
                    if (lenientObjects.length > 0) {
                        processExport(lenientObjects, scaledEditableArea, editableAreaData, bgImg);
                        return;
                    } else {
                        alert("No design objects found to export.");
                        return;
                    }
                }

                processExport(objectsInEditableArea, scaledEditableArea, editableAreaData, bgImg);
            }


            function processExport(objectsToExport, scaledEditableArea, editableAreaData, bgImg) {
                // Use the original editable area dimensions at 100% scale
                const exportWidth = editableAreaData.width;
                const exportHeight = editableAreaData.height;

                // Create export canvas with original editable area dimensions
                const exportCanvas = new fabric.Canvas(null, {
                    width: exportWidth,
                    height: exportHeight,
                    backgroundColor: 'transparent'
                });

                // Calculate the scale factor to convert from current canvas to original size
                const scaleFactorX = 1 / bgImg.scaleX;
                const scaleFactorY = 1 / bgImg.scaleY;

                // Process objects for export
                const clonePromises = objectsToExport.map((obj, index) => {
                    return new Promise(resolve => {
                        obj.clone(clone => {
                            // Calculate the object's position relative to the editable area
                            // First, get position relative to scaled editable area
                            const relativeLeft = obj.left - scaledEditableArea.left;
                            const relativeTop = obj.top - scaledEditableArea.top;

                            // Then convert to original scale
                            const newLeft = relativeLeft * scaleFactorX;
                            const newTop = relativeTop * scaleFactorY;

                            // Adjust object scaling to maintain appearance at original size
                            const finalScaleX = (obj.scaleX || 1) * scaleFactorX;
                            const finalScaleY = (obj.scaleY || 1) * scaleFactorY;

                            clone.set({
                                left: newLeft,
                                top: newTop,
                                scaleX: finalScaleX,
                                scaleY: finalScaleY,
                                selectable: false,
                                hasBorders: false,
                                hasControls: false,
                                evented: false
                            });

                            exportCanvas.add(clone);
                            resolve();
                        });
                    });
                });

                Promise.all(clonePromises).then(() => {
                    exportCanvas.renderAll();

                    // Export canvas as PNG with exact original dimensions
                    const dataURL = exportCanvas.toDataURL({
                        format: 'png',
                        quality: 1.0,
                        multiplier: 1, // Keep at 1 for exact pixel dimensions
                        withoutTransform: true, // Important: don't apply any transforms
                        enableRetinaScaling: false // Disable retina scaling
                    });

                    // Create and trigger download
                    const link = document.createElement('a');
                    link.href = dataURL;
                    link.download = `customer-design-order-{{ $order->order_number }}.png`;
                    document.body.appendChild(link);
                    link.click();
                    document.body.removeChild(link);

                    // Clean up
                    exportCanvas.dispose();

                }).catch(error => {
                    alert('Failed to export design. Please try again.');
                });
            }


            $(window).on('keydown', function(e) {
                if (e.code === 'Space') {
                    isSpacePressed = true;
                    e.preventDefault(); // prevent page scroll
                }
            });

            $(window).on('keyup', function(e) {
                if (e.code === 'Space') {
                    isSpacePressed = false;
                }
            });


        })(jQuery);
    </script>
@endpush
