@extends($activeTemplate . 'layouts.master')

@section('content')
    <div class="order-details">
        <div class="order-details-top justify-content-between gap-2">
            <div>
                <span class="d-inline-flex align-items-center gap-2">
                    @php echo $order->paymentBadge() @endphp
                    @php echo $order->statusBadge() @endphp
                </span>
            </div>
            <div>
                <h4 class="order-details-id mb-1 d-flex align-items-center flex-wrap gap-3">
                    <span class="order-details-id">#{{ $order->order_number }}
                        </h5>
            </div>
        </div>

        <div class="row mt-3">
            @foreach ($printAreas as $printArea)
                @include($activeTemplate . '.partials.order_print_area')
            @endforeach
        </div>

    </div>
@endsection
@push('breadcrumb-plugins')
    <a href="{{ route('user.order.details', $order->order_number) }}" class="btn btn-outline--base-two text-white">
        <i class="las la-angle-left"></i> @lang('Back to Orders Details')
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
            const desingAreaBackground = "{{ $colorCode ? '#' . $colorCode : 'transparent' }}";

            function initDesignCanvas(container) {
                const canvasEl = container.find('canvas')[0];
                const hiddenInput = container.find('input[name="selected_area[]"]')[0];
                const imageURL = canvasEl.dataset.src;
                const editableAreaData = JSON.parse(canvasEl.dataset.selected || 'null');
                const canvas = new fabric.Canvas(canvasEl);

                canvasEl.fabricCanvas = canvas;
                resizeCanvas(canvas, container);
                $(window).on('resize', resizeCanvas);

                function restrictObject(obj) {
                    if (!editableAreaData) return;

                    function keepInside(obj) {
                        const bound = obj.getBoundingRect(true, true);
                        const maxRight = editableAreaData.left + editableAreaData.width;
                        const maxBottom = editableAreaData.top + editableAreaData.height;

                        if (bound.left < editableAreaData.left) obj.left += (editableAreaData.left - bound.left);
                        if (bound.top < editableAreaData.top) obj.top += (editableAreaData.top - bound.top);
                        if (bound.left + bound.width > maxRight) obj.left -= (bound.left + bound.width - maxRight);
                        if (bound.top + bound.height > maxBottom) obj.top -= (bound.top + bound.height - maxBottom);
                    }

                    obj.on('moving', () => keepInside(obj));
                    obj.on('scaling', () => keepInside(obj));
                    obj.on('rotating', () => keepInside(obj));
                }

                function drawEditableArea() {
                    if (!editableAreaData) return;
                    const bgImg = canvas.backgroundImage;
                    if (!bgImg) return;

                    const imgScale = bgImg.scaleX;
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



                const containerEl = container.find('.canvas-container')[0];
                const canvasWidth = containerEl.clientWidth;
                const canvasHeight = containerEl.clientHeight;
                canvas.setWidth(canvasWidth);
                canvas.setHeight(canvasHeight);

                const savedJson = JSON.parse(hiddenInput.value);
                canvas.loadFromJSON(savedJson, () => {
                    canvas.getObjects().forEach(obj => {
                        // Keep object selectable
                        obj.selectable = true;

                        // Disable all editing
                        obj.hasControls = false; // hide resize/rotate controls
                        obj.lockMovementX = true; // cannot move horizontally
                        obj.lockMovementY = true; // cannot move vertically
                        obj.lockScalingX = true; // cannot scale
                        obj.lockScalingY = true;
                        obj.lockRotation = true; // cannot rotate
                        obj.lockUniScaling = true;
                        obj.editable = false; // if text, cannot edit
                        restrictObject(obj); // still keep inside editable area if needed
                    });

                    drawEditableArea(); // redraw the border
                    canvas.requestRenderAll();
                });


                // Action bindings
                container.find('[data-action]').each((i, btn) => {
                    const action = btn.dataset.action;
                    btn.addEventListener('click', () => {
                        const handler = window[action];
                        if (typeof handler === 'function') handler(canvas, editableAreaData);

                    });
                });

                container.find('[data-action="zoomIn"]').on('click', () => zoom(canvas, +0.1, container));
                container.find('[data-action="zoomOut"]').on('click', () => zoom(canvas, -0.1, container));
                container.find('[data-action="toggleMove"]').on('click', () => toggleMove(canvas));
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

                setTimeout(() => {
                    fitScreen(canvas, container);
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

            function toggleMove(canvas) {
                canvas.isMoveMode = !canvas.isMoveMode;
                canvas.selection = !canvas.isMoveMode;

                canvas.forEachObject(obj => {
                    if (obj.type !== 'image' && obj.evented !== false) {
                        obj.selectable = !canvas.isMoveMode;
                    }
                });

                canvas.discardActiveObject();
                canvas.requestRenderAll();

                const moveModeBtn = $(canvas.upperCanvasEl)
                    .closest('.printSideAreaItem')
                    .find('[data-action="toggleMove"]');
                moveModeBtn.toggleClass('active-move', canvas.isMoveMode);
                $(canvas.upperCanvasEl).css('cursor', canvas.isMoveMode ? 'move' : 'default');
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

            $('.printSideAreaItem').each(function() {
                initDesignCanvas($(this));
            });
        })(jQuery);
    </script>
@endpush
