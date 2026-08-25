<div class="modal fade" id="drawingModal" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="drawingModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-fullscreen">
        <div class="modal-content">
            <div class="modal-header py-1">
                <h3 class="modal-title mb-0" id="drawingModalLabel">@lang('Draw')</h3>
            </div>
            <div class="modal-body py-0">
                <div class="toolbar mt-3">
                    <button class="me-2 me-xxsm-0" id="drawUndoBtn">
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" width="20" height="20" color="currentColor" fill="none">
                            <path d="M12 21C16.9706 21 21 16.9706 21 12C21 7.02944 16.9706 3 12 3C8.66873 3 5.76018 4.80989 4.20404 7.5" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round">
                            </path>
                            <path d="M3 3V4.27816C3 6.47004 3 7.56599 3.70725 8.16512C4.4145 8.76425 5.49553 8.58408 7.6576 8.22373L9 8" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round">
                            </path>
                        </svg>
                    </button>
                    <div class="btn-group">
                        <button id="drawFreeBtn" class="active btn"><svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" class="icon icon-tabler icons-tabler-outline icon-tabler-writing">
                                <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                                <path d="M20 17v-12c0 -1.121 -.879 -2 -2 -2s-2 .879 -2 2v12l2 2l2 -2z" />
                                <path d="M16 7h4" />
                                <path d="M18 19h-13a2 2 0 1 1 0 -4h4a2 2 0 1 0 0 -4h-3" />
                            </svg></button>
                        <button class="btn" id="drawRectBtn"><svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" class="icon icon-tabler icons-tabler-outline icon-tabler-rectangle">
                                <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                                <path d="M3 5m0 2a2 2 0 0 1 2 -2h14a2 2 0 0 1 2 2v10a2 2 0 0 1 -2 2h-14a2 2 0 0 1 -2 -2z" />
                            </svg></button>
                        <button class="btn" id="drawCircleBtn"><svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" class="icon icon-tabler icons-tabler-outline icon-tabler-circle">
                                <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                                <path d="M12 12m-9 0a9 9 0 1 0 18 0a9 9 0 1 0 -18 0" />
                            </svg></button>
                    </div>

                    <div class="btn-group me-2 me-xxsm-0">
                        <button class="btn" id="drawEraserBtn">
                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" class="icon icon-tabler icons-tabler-outline icon-tabler-eraser">
                                <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                                <path d="M19 20h-10.5l-4.21 -4.3a1 1 0 0 1 0 -1.41l10 -10a1 1 0 0 1 1.41 0l5 5a1 1 0 0 1 0 1.41l-9.2 9.3" />
                                <path d="M18 13.3l-6.3 -6.3" />
                            </svg>
                        </button>

                        <button class="btn" id="drawClearBtn">
                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" class="icon icon-tabler icons-tabler-outline icon-tabler-trash">
                                <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                                <path d="M4 7l16 0" />
                                <path d="M10 11l0 6" />
                                <path d="M14 11l0 6" />
                                <path d="M5 7l1 12a2 2 0 0 0 2 2h8a2 2 0 0 0 2 -2l1 -12" />
                                <path d="M9 7v-3a1 1 0 0 1 1 -1h4a1 1 0 0 1 1 1v3" />
                            </svg>
                        </button>
                        <button id="drawPanBtn" class="btn">
                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" width="20" height="20" color="currentColor" fill="none">
                                <path d="M8.0469 3.44865L13.4101 5.54728L13.4101 5.54728C16.5034 6.75771 18.05 7.36293 17.9988 8.32296C17.9475 9.28299 16.3334 9.7232 13.1051 10.6036C12.1439 10.8658 11.6633 10.9969 11.3301 11.3301C10.9969 11.6633 10.8658 12.1439 10.6036 13.1051C9.7232 16.3334 9.28299 17.9475 8.32296 17.9988C7.36293 18.05 6.75771 16.5034 5.54728 13.4101L5.54728 13.4101L3.44865 8.0469C2.18138 4.80831 1.54774 3.18901 2.36837 2.36837C3.18901 1.54774 4.80831 2.18138 8.0469 3.44865Z" stroke="currentColor" stroke-width="1.6" stroke-linejoin="round"></path>
                                <path d="M17.5007 13L17.5007 15.7M17.5007 13C17.05 13 15.9707 14.2526 15.9707 14.2526M17.5007 13C17.9514 13 19.0307 14.2526 19.0307 14.2526M17.5007 22L17.5007 19.3M17.5007 22C17.95 22 19.0307 20.7474 19.0307 20.7474M17.5007 22C17.05 22 15.9707 20.7474 15.9707 20.7474M13 17.5007L15.7 17.5007M13 17.5007C13 17.95 14.2526 19.0307 14.2526 19.0307M13 17.5007C13 17.05 14.2526 15.9707 14.2526 15.9707M22 17.5007L19.3 17.5007M22 17.5007C22 17.05 20.7474 15.9707 20.7474 15.9707M22 17.5007C22 17.95 20.7474 19.0307 20.7474 19.0307" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"></path>
                            </svg>
                        </button>
                    </div>
                    <label for="drawColorPicker" class="colorPicker me-2">

                        <input class="d-none" type="color" id="drawColorPicker" value="#eb0000">
                    </label>

                    <div class="form-gopup">
                        <label>@lang('Size'):</label>
                        <input type="range" id="drawBrushRange" min="1" max="50" value="5">
                    </div>

                    <div class="zoom-controls">
                        <button id="drawFitScreen">
                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" class="icon icon-tabler icons-tabler-outline icon-tabler-maximize">
                                <path stroke="none" d="M0 0h24v24H0z" fill="none"></path>
                                <path d="M4 8v-2a2 2 0 0 1 2 -2h2"></path>
                                <path d="M4 16v2a2 2 0 0 0 2 2h2"></path>
                                <path d="M16 4h2a2 2 0 0 1 2 2v2"></path>
                                <path d="M16 20h2a2 2 0 0 0 2 -2v-2"></path>
                            </svg>
                        </button>
                        <button id="drawZoomout">-</button>
                        <div id="drawZoomPercentage">100%</div>
                        <button id="drawZoomin">+</button>
                    </div>
                </div>

                <canvas id="drawCanvas"></canvas>
                <input type="hidden" id="drawDesignDataInput">

            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn--xs btn-outline--danger" data-bs-dismiss="modal">@lang('Cancel')</button>
                <button type="button" id="confirmDrawBtn" class="btn btn--xs btn--base">@lang('Confirm')</button>
            </div>
        </div>
    </div>
</div>

@push('script-lib')
    <script src="{{ asset('assets/admin/js/spectrum.js') }}"></script>
@endpush

@push('style-lib')
    <link rel="stylesheet" href="{{ asset('assets/admin/css/spectrum.css') }}">
@endpush

@push('script')
    <script>
        (function($) {
            "use strict";

            $('.colorPicker').each(function() {
                let $label = $(this);
                let $input = $label.find('input[type="color"]');

                $label.spectrum({
                    color: $input.val(),
                    change: function(color) {
                        let hex = color.toHexString();
                        $input.val(hex).trigger("change"); // update value & fire change event
                        $label.css('color', hex); // update label color preview
                    }
                });
            });
        })(jQuery);
    </script>
@endpush
@push('style')
    <style>
        .colorPicker {
            color: #eb0000;
            border: none;
            padding: 0;
            border-radius: 4px;
            cursor: pointer;
            height: 40px;
            width: 44px;
            background: currentColor;
        }

        .toolbar {
            background: hsl(var(--base-two-d-900));
            padding: 10px;
            display: flex;
            gap: 10px;
            align-items: center;
            flex-wrap: wrap;
            border-radius: 10px;
        }

        .toolbar button {
            background: #555;
            color: white;
            border: none;
            padding: 8px 12px;
            border-radius: 4px;
            cursor: pointer;
        }

        .toolbar button:hover {
            color: hsl(var(--base));
            background: #555;
        }

        .toolbar button.active,
        .toolbar button:active {
            background: hsl(var(--base)) !important;
            color: #555 !important;
        }

        .toolbar input[type="range"] {
            width: 130px;
            height: 11px;
        }

        .toolbar input[type="color"] {
            width: 40px;
            height: 30px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
        }

        .toolbar label {
            color: white;
            font-size: 14px;
            font-weight: 500;
            display: block;
        }

        .zoom-controls {
            display: flex;
            align-items: center;
            gap: 5px;
            margin-left: auto;
        }

        .zoom-controls button {
            width: 30px;
            height: 30px;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .zoom-controls #drawFitScreen {
            padding: 0 !important;
        }

        #drawZoomPercentage {
            background: #444;
            padding: 3px 10px;
            border-radius: 4px;
            cursor: pointer;
            min-width: 50px;
            text-align: center;
            color: hsl(var(--white))
        }

        #drawCanvas {
            display: block;
            background: white;
            margin: 16px;
            border: 1px solid #ddd;
            cursor: crosshair;
        }

        .footer {
            background: #333;
            padding: 10px;
            color: white;
            font-size: 12px;
        }

        .footer input {
            width: 100%;
            background: #555;
            color: white;
            border: 1px solid #666;
            padding: 5px;
            border-radius: 4px;
        }

        #drawCanvas {
            max-width: calc(100% - (var(--bs-modal-padding) * 2))
        }

        @media screen and (-webkit-min-device-pixel-ratio:0) {
            input[type='range'] {
                overflow: hidden;
                width: 80px;
                -webkit-appearance: none;
                background-color: #555;
            }

            input[type='range']::-webkit-slider-runnable-track {
                height: 12px;
                -webkit-appearance: none;
                color: hsl(var(--base-d-300));
                margin-top: -1px;
            }

            input[type='range']::-webkit-slider-thumb {
                width: 12px;
                -webkit-appearance: none;
                height: 12px;
                cursor: ew-resize;
                background: hsl(var(--base-two));
                box-shadow: -80px 0 0 80px hsl(var(--base));
            }
        }

        /** FF*/
        input[type="range"]::-moz-range-progress {
            background-color: hsl(var(--base));
        }

        input[type="range"]::-moz-range-track {
            background-color: #555;
        }

        /* IE*/
        input[type="range"]::-ms-fill-lower {
            background-color: hsl(var(--base));
        }

        input[type="range"]::-ms-fill-upper {
            background-color: #555;
        }

        @media screen and (max-width:991px) {
            .zoom-controls {
                position: fixed;
                bottom: 89px;
                left: 48px;
                box-shadow: 0 0 10px 0 hsl(0deg 0% 0% / 20%);
                padding: 3px;
                border-radius: 5px;
            }
        }

        @media screen and (max-width:620px) {
            .toolbar button {
                padding: 5px 7px;
            }

            .colorPicker {
                width: 34px;
                height: 34px;
            }
        }

        @media screen and (max-width:620px) {
            .toolbar {
                padding: 8px;
                gap: 8px;
            }

            .me-xxsm-0 {
                margin-right: 0 !important;
            }
        }
    </style>
@endpush

@push('script')
    <script>
        "use strict";

        const DPI = `{{ gs('print_area_dpi') }}`;
        let currentDesingPrintAreaCanvas = null;
        let currentDesingPrintAreaContainer = null;
        let currentDesingPrintAreaEditableAreaData = null;
        let currentDesingPrintAreaRestrictObject = null;

        // ====== Setup ======
        const drawingModal = document.getElementById('drawingModal');
        const mainDrawingCanvas = document.getElementById('drawCanvas');
        mainDrawingCanvas.style.touchAction = "none";

        const mainDrawingCtx = mainDrawingCanvas.getContext('2d', {
            alpha: true
        });
        const dpr = window.devicePixelRatio || 1;

        // Create a separate canvas for storing clean drawings (no zoom, no UI)
        const drawingCanvas = document.createElement('canvas');
        const drawingCtx = drawingCanvas.getContext('2d');

        // const toolbarHeight = () => document.querySelector('.toolbar').offsetHeight;
        const footerHeight = () => document.querySelector('.modal-footer').offsetHeight;
        const modalHeaderHeight = () => document.querySelector('.modal-header').offsetHeight;
        const toolbarHeight = () => {
            const el = document.querySelector('.toolbar');
            if (!el) return 0;

            const style = getComputedStyle(el);

            const marginTop = parseFloat(style.marginTop) || 0;
            const marginBottom = parseFloat(style.marginBottom) || 0;

            // offsetHeight = content + padding + border
            const totalHeight = el.offsetHeight + marginTop + marginBottom;

            return totalHeight;
        };

        function inchesToPixels(inch) {
            return inch * DPI;
        }

        let designArea = {
            width: 500,
            height: 500,
            left: 0,
            top: 0
        };

        let history = [];
        let historyIndex = -1;
        let drawing = false;
        let lastX = 0,
            lastY = 0;
        let startX = 0,
            startY = 0;
        let currentPressure = 0.5;
        let tool = 'free';
        let isEraserMode = false;

        // ====== Pan Variables ======
        let isPanMode = false;
        let panning = false;
        let lastPanX = 0,
            lastPanY = 0;
        let spacePressed = false;

        // ====== Zoom Variables ======
        let drawScale = 1;
        let drawOffsetX = 0,
            drawOffsetY = 0;
        const drawZoomStep = 0.1;
        const drawMinZoom = 0.2;
        const drawMaxZoom = 5;

        // UI elements
        const drawFreeBtn = document.getElementById('drawFreeBtn');
        const drawRectBtn = document.getElementById('drawRectBtn');
        const drawCircleBtn = document.getElementById('drawCircleBtn');
        const drawBrushRange = document.getElementById('drawBrushRange');
        const drawColorPicker = document.getElementById('drawColorPicker');
        const drawEraserBtn = document.getElementById('drawEraserBtn');
        const drawUndoBtn = document.getElementById('drawUndoBtn');
        const drawClearBtn = document.getElementById('drawClearBtn');
        const drawPanBtn = document.getElementById('drawPanBtn');
        const drawDesignDataInput = document.getElementById('drawDesignDataInput');

        // ====== Initialize Drawing Canvas ======
        function initializeDrawingCanvas() {
            drawingCanvas.width = mainDrawingCanvas.width;
            drawingCanvas.height = mainDrawingCanvas.height;
            drawingCtx.scale(dpr, dpr);
        }

        // ====== Design Area Functions ======
        function updateDesignArea() {
            designArea.left = (mainDrawingCanvas.width / dpr - designArea.width) / 2;
            designArea.top = (mainDrawingCanvas.height / dpr - designArea.height) / 2;
        }

        function drawDesignAreaOutline() {
            mainDrawingCtx.save();
            mainDrawingCtx.strokeStyle = 'red';
            mainDrawingCtx.lineWidth = 2 / drawScale;
            mainDrawingCtx.setLineDash([5 / drawScale, 5 / drawScale]);

            const left = designArea.left;
            const top = designArea.top;
            const width = designArea.width;
            const height = designArea.height;

            mainDrawingCtx.strokeRect(left, top, width, height);
            mainDrawingCtx.restore();
        }

        function isInsideDesignArea(x, y) {
            const tolerance = 2;
            return (
                x >= designArea.left - tolerance &&
                x <= designArea.left + designArea.width + tolerance &&
                y >= designArea.top - tolerance &&
                y <= designArea.top + designArea.height + tolerance
            );
        }

        // ====== Display Function ======
        function displayWithCurrentZoom() {
            mainDrawingCtx.setTransform(1, 0, 0, 1, 0, 0);
            mainDrawingCtx.clearRect(0, 0, mainDrawingCanvas.width, mainDrawingCanvas.height);

            // Apply current zoom and pan
            mainDrawingCtx.setTransform(
                drawScale * dpr, 0,
                0, drawScale * dpr,
                drawOffsetX * drawScale * dpr,
                drawOffsetY * drawScale * dpr
            );

            // Draw the clean drawing content
            mainDrawingCtx.drawImage(drawingCanvas, 0, 0, mainDrawingCanvas.width / dpr, mainDrawingCanvas.height / dpr);

            // Add red outline for design area
            drawDesignAreaOutline();
        }

        // ====== Canvas Resize ======
        function drawResizeCanvas() {
            const w = window.innerWidth - 32;
            const h = window.innerHeight - toolbarHeight() - footerHeight() - modalHeaderHeight() - 35;
            mainDrawingCanvas.style.width = w + 'px';
            mainDrawingCanvas.style.height = h + 'px';
            mainDrawingCanvas.width = Math.round(w * dpr);
            mainDrawingCanvas.height = Math.round(h * dpr);
            mainDrawingCtx.setTransform(1, 0, 0, 1, 0, 0);
            mainDrawingCtx.scale(dpr, dpr);

            // Resize drawing canvas too
            const oldDrawingData = drawingCanvas.toDataURL();
            drawingCanvas.width = mainDrawingCanvas.width;
            drawingCanvas.height = mainDrawingCanvas.height;
            drawingCtx.setTransform(1, 0, 0, 1, 0, 0);
            drawingCtx.scale(dpr, dpr);

            // Restore drawing content
            if (oldDrawingData && oldDrawingData !== 'data:,') {
                const img = new Image();
                img.onload = () => {
                    drawingCtx.drawImage(img, 0, 0, mainDrawingCanvas.width / dpr, mainDrawingCanvas.height / dpr);
                    updateDesignArea();
                    displayWithCurrentZoom();
                };
                img.src = oldDrawingData;
            } else {
                updateDesignArea();
                displayWithCurrentZoom();
            }
        }

        // ====== History Handling - CLEAN STORAGE ONLY ======
        function saveDesignData() {
            const dataObj = {
                history,
                historyIndex
            };
            const jsonData = JSON.stringify(dataObj);
            drawDesignDataInput.value = jsonData;
        }

        function loadDesignData() {
            const jsonData = drawDesignDataInput.value;
            if (!jsonData) return false;

            try {
                const dataObj = JSON.parse(jsonData);
                if (dataObj.history && dataObj.history.length > 0) {
                    history.length = 0;
                    dataObj.history.forEach(imgData => history.push(imgData));
                    historyIndex = dataObj.historyIndex || 0;

                    drawSnapshot(history[historyIndex]);
                    return true;
                }
            } catch {}
            return false;
        }

        function pushHistory() {
            // Save only the clean drawing content (no zoom, no red outline)
            if (historyIndex < history.length - 1) history.splice(historyIndex + 1);
            history.push(drawingCanvas.toDataURL());
            if (history.length > 30) history.shift();
            historyIndex = history.length - 1;
            saveDesignData();
        }

        function undo() {
            if (historyIndex <= 0) {
                drawingCtx.clearRect(0, 0, drawingCanvas.width / dpr, drawingCanvas.height / dpr);
                historyIndex = -1;
                displayWithCurrentZoom();
                saveDesignData();
                return;
            }
            historyIndex--;
            drawSnapshot(history[historyIndex]);
            saveDesignData();
        }

        function drawSnapshot(dataUrl) {
            const img = new Image();
            img.onload = () => {
                // Clear the drawing canvas and load the clean drawing
                drawingCtx.clearRect(0, 0, drawingCanvas.width / dpr, drawingCanvas.height / dpr);
                drawingCtx.drawImage(img, 0, 0, mainDrawingCanvas.width / dpr, mainDrawingCanvas.height / dpr);

                // Now display on main canvas with current zoom
                displayWithCurrentZoom();
            };
            img.src = dataUrl;
        }

        function redrawFromSnapshot() {
            if (!history[historyIndex]) {
                // Clear both canvases
                drawingCtx.clearRect(0, 0, drawingCanvas.width / dpr, drawingCanvas.height / dpr);
                displayWithCurrentZoom();
                return;
            }
            drawSnapshot(history[historyIndex]);
        }

        // ====== Tool selection ======
        function setActiveTool(btn, name) {
            if (isEraserMode) {
                isEraserMode = false;
                drawEraserBtn.classList.remove('active');
                updateBrushCursor(parseInt(drawBrushRange.value, 10));
            }
            document.querySelectorAll('.toolbar button').forEach(b => b.classList.remove('active'));
            btn.classList.add('active');
            tool = name;
            mainDrawingCanvas.style.cursor = 'crosshair';

            isPanMode = false;
            if (name == 'free') {
                updateCursor();
            }
        }

        drawFreeBtn.addEventListener('click', () => setActiveTool(drawFreeBtn, 'free'));
        drawRectBtn.addEventListener('click', () => setActiveTool(drawRectBtn, 'rect'));
        drawCircleBtn.addEventListener('click', () => setActiveTool(drawCircleBtn, 'circle'));

        // ====== Pointer Position ======
        function getPointerPos(e) {
            const rect = mainDrawingCanvas.getBoundingClientRect();

            // Get raw canvas coordinates
            const canvasX = e.clientX - rect.left;
            const canvasY = e.clientY - rect.top;

            // Convert to canvas coordinate system accounting for CSS scaling
            const scaleX = mainDrawingCanvas.width / rect.width;
            const scaleY = mainDrawingCanvas.height / rect.height;

            const scaledX = (canvasX * scaleX) / dpr;
            const scaledY = (canvasY * scaleY) / dpr;

            // Apply inverse transform to get world coordinates
            const x = (scaledX - drawOffsetX * drawScale) / drawScale;
            const y = (scaledY - drawOffsetY * drawScale) / drawScale;

            return {
                x,
                y
            };
        }

        function getTouchPos(touch) {
            const rect = mainDrawingCanvas.getBoundingClientRect();

            // Get raw canvas coordinates
            const canvasX = touch.clientX - rect.left;
            const canvasY = touch.clientY - rect.top;

            // Convert to canvas coordinate system accounting for CSS scaling
            const scaleX = mainDrawingCanvas.width / rect.width;
            const scaleY = mainDrawingCanvas.height / rect.height;

            const scaledX = (canvasX * scaleX) / dpr;
            const scaledY = (canvasY * scaleY) / dpr;

            // Apply inverse transform to get world coordinates
            const x = (scaledX - drawOffsetX * drawScale) / drawScale;
            const y = (scaledY - drawOffsetY * drawScale) / drawScale;

            return {
                x,
                y
            };
        }

        // ====== Shape Drawing Helpers ======
        function drawRect(x, y, w, h) {
            mainDrawingCtx.strokeRect(x, y, w, h);
            drawingCtx.strokeRect(x, y, w, h);
        }

        function drawCircle(x, y, dx, dy) {
            const r = Math.sqrt(dx * dx + dy * dy);

            mainDrawingCtx.beginPath();
            mainDrawingCtx.arc(x, y, r, 0, Math.PI * 2);
            mainDrawingCtx.stroke();
            mainDrawingCtx.closePath();

            drawingCtx.beginPath();
            drawingCtx.arc(x, y, r, 0, Math.PI * 2);
            drawingCtx.stroke();
            drawingCtx.closePath();
        }

        function clampPoint(x, y) {
            const clampedX = Math.min(Math.max(x, designArea.left), designArea.left + designArea.width);
            const clampedY = Math.min(Math.max(y, designArea.top), designArea.top + designArea.height);
            return [clampedX, clampedY];
        }

        function clampCirclePointer(x, y) {
            const dx = x - startX;
            const dy = y - startY;
            let r = Math.sqrt(dx * dx + dy * dy);

            const maxRight = designArea.left + designArea.width;
            const maxBottom = designArea.top + designArea.height;
            r = Math.min(r,
                startX - designArea.left,
                maxRight - startX,
                startY - designArea.top,
                maxBottom - startY
            );

            const angle = Math.atan2(dy, dx);
            return [startX + r * Math.cos(angle), startY + r * Math.sin(angle)];
        }

        // ====== Cursor ======
        function updateBrushCursor(size, tool = "free") {
            if (tool === 'free') {
                // SVG data as a string
                const svgData = `
            <svg fill="#000000" width="${size}px" height="${size}px" viewBox="0 0 32 32" xmlns="http://www.w3.org/2000/svg">
                <path d="M0 32l12-4 20-20-8-8-20 20zM4 28l2.016-5.984 4 4zM8 20l12-12 4 4-12 12z"></path>
            </svg>`;
                const svgBlob = new Blob([svgData], {
                    type: "image/svg+xml;charset=utf-8"
                });
                const url = URL.createObjectURL(svgBlob);

                const img = new Image();
                img.onload = function() {
                    const cursorCanvas = document.createElement('canvas');
                    cursorCanvas.width = size;
                    cursorCanvas.height = size;
                    const ctx = cursorCanvas.getContext('2d');
                    ctx.clearRect(0, 0, size, size);
                    ctx.drawImage(img, 0, 0, size, size);

                    // Position cursor at pencil tip (bottom-left) instead of center
                    // For pencil icon, the tip is at approximately 15% from left and 85% from top
                    const hotspotX = Math.round(size * 0.15);
                    const hotspotY = Math.round(size * 0.85);

                    mainDrawingCanvas.style.cursor =
                        `url(${cursorCanvas.toDataURL()}) ${hotspotX} ${hotspotY}, crosshair`;
                    URL.revokeObjectURL(url); // Clean up
                };
                img.src = url;
            } else {
                // Default circular cursor
                const cursorCanvas = document.createElement('canvas');
                cursorCanvas.width = size;
                cursorCanvas.height = size;
                const ctxCursor = cursorCanvas.getContext('2d');
                ctxCursor.beginPath();
                ctxCursor.arc(size / 2, size / 2, size / 2 - 1, 0, Math.PI * 2);
                ctxCursor.strokeStyle = 'black';
                ctxCursor.lineWidth = 1;
                ctxCursor.stroke();

                mainDrawingCanvas.style.cursor = `url(${cursorCanvas.toDataURL()}) ${size / 2} ${size / 2}, crosshair`;
            }
        }



        function updateEraseCursor(size) {
            const cursorSize = size * 2;
            const cursorCanvas = document.createElement('canvas');
            const ctxCursor = cursorCanvas.getContext('2d');
            cursorCanvas.width = cursorSize;
            cursorCanvas.height = cursorSize;
            ctxCursor.clearRect(0, 0, cursorSize, cursorSize);

            ctxCursor.beginPath();
            ctxCursor.arc(cursorSize / 2, cursorSize / 2, cursorSize / 2 - 1, 0, Math.PI * 2);
            ctxCursor.strokeStyle = 'rgba(255, 0, 0, 0.7)';
            ctxCursor.lineWidth = 1;
            ctxCursor.stroke();

            ctxCursor.beginPath();
            ctxCursor.arc(cursorSize / 2, cursorSize / 2, cursorSize / 2 - 3, 0, Math.PI * 2);
            ctxCursor.fillStyle = 'rgba(255, 255, 255, 0.3)';
            ctxCursor.fill();

            mainDrawingCanvas.style.cursor =
                `url(${cursorCanvas.toDataURL()}) ${cursorSize / 2} ${cursorSize / 2}, crosshair`;
        }

        // ====== Drawing Functions ======
        function drawShape(toolName, x, y) {
            const size = parseFloat(drawBrushRange.value);
            const color = drawColorPicker.value;
            const opacity = 1;

            // Set up both contexts the same way
            [mainDrawingCtx, drawingCtx].forEach(context => {
                context.globalAlpha = toolName === 'free' ? opacity : 1;
                context.lineWidth = size;
                context.strokeStyle = color;

                if (isEraserMode) {
                    context.globalCompositeOperation = 'destination-out';
                    context.lineWidth = size * 2;
                } else {
                    context.globalCompositeOperation = 'source-over';
                }
            });

            if (toolName === 'free') {
                // Draw on both canvases
                mainDrawingCtx.globalAlpha = opacity;
                mainDrawingCtx.lineWidth = Math.max(1, size * (1 + (currentPressure - 0.5)));
                mainDrawingCtx.lineTo(x, y);
                mainDrawingCtx.stroke();

                drawingCtx.globalAlpha = opacity;
                drawingCtx.lineWidth = Math.max(1, size * (1 + (currentPressure - 0.5)));
                drawingCtx.lineTo(x, y);
                drawingCtx.stroke();

                lastX = x;
                lastY = y;
            } else {
                // For shapes, redraw main canvas from drawing canvas + preview
                displayWithCurrentZoom();

                if (toolName === 'rect') {
                    const [clampedX, clampedY] = clampPoint(x, y);
                    mainDrawingCtx.strokeRect(startX, startY, clampedX - startX, clampedY - startY);
                } else if (toolName === 'circle') {
                    const [clampedX, clampedY] = clampCirclePointer(x, y);
                    const dx = clampedX - startX;
                    const dy = clampedY - startY;
                    const r = Math.sqrt(dx * dx + dy * dy);
                    mainDrawingCtx.beginPath();
                    mainDrawingCtx.arc(startX, startY, r, 0, Math.PI * 2);
                    mainDrawingCtx.stroke();
                    mainDrawingCtx.closePath();
                }
            }

            // Reset composite operation for both
            mainDrawingCtx.globalCompositeOperation = 'source-over';
            drawingCtx.globalCompositeOperation = 'source-over';
        }

        function finalizeShape(toolName, x, y) {
            const size = parseFloat(drawBrushRange.value);
            const color = drawColorPicker.value;
            const opacity = 1;

            // Set up both contexts
            [mainDrawingCtx, drawingCtx].forEach(context => {
                context.globalAlpha = opacity;
                context.lineWidth = size;
                context.strokeStyle = color;

                if (isEraserMode) {
                    context.globalCompositeOperation = 'destination-out';
                    context.lineWidth = size * 2;
                } else {
                    context.globalCompositeOperation = 'source-over';
                }
            });

            if (toolName === 'rect') {
                const [clampedX, clampedY] = clampPoint(x, y);
                drawingCtx.strokeRect(startX, startY, clampedX - startX, clampedY - startY);
                pushHistory();
            } else if (toolName === 'circle') {
                const [clampedX, clampedY] = clampCirclePointer(x, y);
                const dx = clampedX - startX;
                const dy = clampedY - startY;
                const r = Math.sqrt(dx * dx + dy * dy);

                drawingCtx.beginPath();
                drawingCtx.arc(startX, startY, r, 0, Math.PI * 2);
                drawingCtx.stroke();
                drawingCtx.closePath();

                pushHistory();
            }

            // Reset and redisplay
            mainDrawingCtx.globalCompositeOperation = 'source-over';
            drawingCtx.globalCompositeOperation = 'source-over';
            displayWithCurrentZoom();
        }

        // ====== Pan Functions ======
        function updateCursor() {

            // Don't set custom cursors on touch devices as they don't show
            const isTouchDevice = 'ontouchstart' in window || navigator.maxTouchPoints > 0;

            if (isPanMode || spacePressed) {
                mainDrawingCanvas.style.cursor = panning ? 'grabbing' : 'grab';
            } else if (isEraserMode && !isTouchDevice) {
                updateEraseCursor(parseInt(drawBrushRange.value, 10));
            } else if (!isTouchDevice) {
                updateBrushCursor(parseInt(drawBrushRange.value, 10));
            } else {
                // For touch devices, use default cursor
                mainDrawingCanvas.style.cursor = 'default';
            }
        }

        mainDrawingCanvas.addEventListener('gesturestart', (e) => {
            e.preventDefault();
        });

        mainDrawingCanvas.addEventListener('gesturechange', (e) => {
            e.preventDefault();
        });

        mainDrawingCanvas.addEventListener('gestureend', (e) => {
            e.preventDefault();
        });

        // Prevent context menu on long press
        mainDrawingCanvas.addEventListener('contextmenu', (e) => {
            e.preventDefault();
        });

        // ====== Drawing Events ======
        mainDrawingCanvas.addEventListener('pointerdown', (e) => {
            e.preventDefault();

            // Prevent scrolling on touch devices
            if (e.pointerType === 'touch') {
                document.body.style.overflow = 'hidden';
            }

            mainDrawingCanvas.setPointerCapture(e.pointerId);

            // Check if pan mode is active OR space key is pressed
            if (isPanMode || spacePressed) {
                panning = true;
                lastPanX = e.clientX;
                lastPanY = e.clientY;
                updateCursor();
                return;
            }

            // Get corrected pointer position
            const p = getPointerPos(e);

            if (!isInsideDesignArea(p.x, p.y)) {
                drawing = false;
                return;
            }

            drawing = true;
            lastX = p.x;
            lastY = p.y;
            startX = p.x;
            startY = p.y;
            currentPressure = e.pressure === 0 ? 0.5 : e.pressure;

            if (tool === 'free') {
                pushHistory();
                // Set up both contexts for free drawing
                mainDrawingCtx.lineJoin = 'round';
                mainDrawingCtx.lineCap = 'round';
                mainDrawingCtx.beginPath();
                mainDrawingCtx.moveTo(lastX, lastY);

                drawingCtx.lineJoin = 'round';
                drawingCtx.lineCap = 'round';
                drawingCtx.beginPath();
                drawingCtx.moveTo(lastX, lastY);
            }
        }, {
            passive: false
        });

        mainDrawingCanvas.addEventListener('pointermove', (e) => {
            e.preventDefault();

            if (panning && (isPanMode || spacePressed)) {
                const deltaX = e.clientX - lastPanX;
                const deltaY = e.clientY - lastPanY;

                drawOffsetX += deltaX / dpr;
                drawOffsetY += deltaY / dpr;

                lastPanX = e.clientX;
                lastPanY = e.clientY;

                displayWithCurrentZoom();
                return;
            }

            if (!drawing) return;

            // Get corrected pointer position
            const p = getPointerPos(e);
            currentPressure = e.pressure === 0 ? 0.5 : e.pressure;

            if (tool === 'circle') {
                const [clampedX, clampedY] = clampCirclePointer(p.x, p.y);
                drawShape(tool, clampedX, clampedY);
            } else {
                if (!isInsideDesignArea(p.x, p.y) && tool !== 'free') {
                    return;
                }

                // For free drawing, allow some tolerance at edges
                if (tool === 'free' && !isInsideDesignArea(p.x, p.y)) {
                    // Clamp to design area for free drawing
                    const clampedX = Math.max(designArea.left, Math.min(designArea.left + designArea.width, p.x));
                    const clampedY = Math.max(designArea.top, Math.min(designArea.top + designArea.height, p.y));
                    drawShape(tool, clampedX, clampedY);
                } else {
                    drawShape(tool, p.x, p.y);
                }
            }

        }, {
            passive: false
        });

        mainDrawingCanvas.addEventListener('pointerup', (e) => {
            e.preventDefault();

            // Re-enable scrolling on touch devices
            if (e.pointerType === 'touch') {
                document.body.style.overflow = '';
            }

            if (panning) {
                panning = false;
                updateCursor();
                mainDrawingCanvas.releasePointerCapture(e.pointerId);
                return;
            }

            if (!drawing) {
                mainDrawingCanvas.releasePointerCapture(e.pointerId);
                return;
            }

            drawing = false;

            // Get corrected pointer position
            const p = getPointerPos(e);

            if (tool === 'free') {
                // For free drawing, we already saved history on pointerdown
                displayWithCurrentZoom();
            } else if (isInsideDesignArea(p.x, p.y)) {
                finalizeShape(tool, p.x, p.y);
            }

            mainDrawingCanvas.releasePointerCapture(e.pointerId);

            // Save design data after drawing is complete
            saveDesignData();
        });

        mainDrawingCanvas.addEventListener('pointercancel', () => {
            drawing = false;
            panning = false;

            // Re-enable scrolling on touch devices
            if (e.pointerType === 'touch') {
                document.body.style.overflow = '';
            }

            updateCursor();

        });

        // ====== Controls ======
        drawUndoBtn.addEventListener('click', undo);

        drawClearBtn.addEventListener('click', () => {
            drawingCtx.clearRect(0, 0, drawingCanvas.width / dpr, drawingCanvas.height / dpr);
            pushHistory();
            displayWithCurrentZoom();
            saveDesignData(); // Update input after clearing
            isPanMode = false;
        });


        // ====== Pan Button ======
        drawPanBtn.addEventListener('click', () => {
            isPanMode = !isPanMode;
            drawPanBtn.classList.toggle('active');
            updateCursor();
            drawScale = Math.max(drawMinZoom, drawScale - 0.0000001);
            drawApplyZoom();
        });

        // ====== Eraser Toggle ======
        drawBrushRange.addEventListener('input', (e) => {
            const size = parseInt(e.target.value, 10);
            if (isPanMode || spacePressed) {
                // Don't update cursor while panning
                return;
            }
            if (isEraserMode) {
                updateEraseCursor(size);
            } else {
                updateBrushCursor(size);
            }
        });

        drawEraserBtn.addEventListener('click', () => {
            isEraserMode = !isEraserMode;
            drawEraserBtn.classList.toggle('active');
            isPanMode = false;

            if (isEraserMode) {
                // Switch to free tool when eraser is activated
                tool = 'free';
                // Update UI to show free tool is active
                document.querySelectorAll('.toolbar button').forEach(b => b.classList.remove('active'));
                drawFreeBtn.classList.add('active');
                drawEraserBtn.classList.add('active'); // Keep eraser button active too
            }

            updateCursor();
        });

        // ====== Zoom Functions ======
        function drawUpdateZoomDisplay() {
            document.getElementById('drawZoomPercentage').textContent = Math.round(drawScale * 100) + '%';
        }

        function drawApplyZoom() {
            displayWithCurrentZoom();
            drawUpdateZoomDisplay();
        }

        // Zoom In
        document.getElementById('drawZoomin').addEventListener('click', () => {
            drawScale = Math.min(drawMaxZoom, drawScale + drawZoomStep);
            drawApplyZoom();
        });

        document.getElementById('drawFitScreen').addEventListener('click', () => {
            centerDrawingCanvas();
        });

        // Zoom Out
        document.getElementById('drawZoomout').addEventListener('click', () => {
            drawScale = Math.max(drawMinZoom, drawScale - drawZoomStep);
            drawApplyZoom();
        });

        // Reset Zoom
        document.getElementById('drawZoomPercentage').addEventListener('click', () => {
            drawScale = 1;
            drawOffsetX = drawOffsetY = 0;
            drawApplyZoom();
        });

        // Mouse Wheel Zoom
        mainDrawingCanvas.addEventListener('wheel', (e) => {
            e.preventDefault();

            const rect = mainDrawingCanvas.getBoundingClientRect();
            const mouseX = e.clientX - rect.left;
            const mouseY = e.clientY - rect.top;

            // Convert mouse position to canvas coordinates
            const canvasMouseX = (mouseX * mainDrawingCanvas.width / rect.width) / dpr;
            const canvasMouseY = (mouseY * mainDrawingCanvas.height / rect.height) / dpr;

            // Get world coordinates before zoom
            const worldX = (canvasMouseX - drawOffsetX) / drawScale;
            const worldY = (canvasMouseY - drawOffsetY) / drawScale;

            const zoomDirection = e.deltaY < 0 ? 1 : -1;
            const oldScale = drawScale;
            drawScale = Math.min(drawMaxZoom, Math.max(drawMinZoom, drawScale + zoomDirection * drawZoomStep));

            // Adjust offset to keep the same world point under the mouse
            drawOffsetX = canvasMouseX - worldX * drawScale;
            drawOffsetY = canvasMouseY - worldY * drawScale;

            drawApplyZoom();
        });


        // ====== Keyboard Events ======
        drawingModal.addEventListener('keydown', (e) => {
            if (e.code === 'Space' && !spacePressed) {
                e.preventDefault();
                spacePressed = true;
                updateCursor();
            }

            if ((e.ctrlKey || e.metaKey) && e.key === 'z') {
                e.preventDefault();
                undo();
            }
            if ((e.ctrlKey || e.metaKey) && e.key === 's') {
                e.preventDefault();
            }
        });

        drawingModal.addEventListener('keyup', (e) => {
            if (e.code === 'Space') {
                spacePressed = false;
                isPanMode = false;
                drawPanBtn.classList.remove('active');
                panning = false;
                updateCursor();
            }
        });

        // ====== Initialize ======
        updateBrushCursor(parseInt(drawBrushRange.value, 10));


        if (drawingModal) {
            const resizeObserver = new ResizeObserver(() => {
                drawResizeCanvas(); // call your canvas resize function
            });

            resizeObserver.observe(drawingModal);
        }
        initializeDrawingCanvas();

        // Initialize History and Canvas on Load
        (function init() {
            setTimeout(() => {
                if (!loadDesignData()) {
                    pushHistory();
                }
                centerDrawingCanvas();
                displayWithCurrentZoom();
            }, 50);
        })();


        // open drwaing modal
        $(document).on("click", ".confirmDrawArea", function () {
            let container = $(this).closest(".sidebar-layer__body");
            let newHeight = parseFloat(container.find(".drawHeightInput").val());
            let newWidth = parseFloat(container.find(".drawWidthInput").val());

            if (newHeight > 0 && newWidth > 0) {
                // Update design area
                designArea.width = inchesToPixels(newWidth);
                designArea.height = inchesToPixels(newHeight);

                // Resize and re-initialize drawing canvas
                drawingCanvas.width = mainDrawingCanvas.width;
                drawingCanvas.height = mainDrawingCanvas.height;
                drawingCtx.setTransform(1, 0, 0, 1, 0, 0);
                drawingCtx.scale(dpr, dpr);
                drawingCtx.clearRect(0, 0, drawingCanvas.width / dpr, drawingCanvas.height / dpr);

                // Update the design area position (center it)
                updateDesignArea();

                // Push empty history for the new canvas
                history.length = 0;
                historyIndex = -1;
                pushHistory();

                // Refresh the display
                // displayWithCurrentZoom();
                centerDrawingCanvas();

                // Close modal
                $(this).closest(".modal").modal("hide");
                $('#drawingModal').modal('show');
            } else {
                alert("Please enter valid width and height!");
            }
        });




        //  Capture design from freehand drawing canvas
        const exportCanvas = document.createElement('canvas');
        exportCanvas.width = designArea.width * dpr;
        exportCanvas.height = designArea.height * dpr;
        const exportCtx = exportCanvas.getContext('2d');

        exportCtx.drawImage(
            drawingCanvas, // your freehand drawing canvas
            designArea.left * dpr,
            designArea.top * dpr,
            designArea.width * dpr,
            designArea.height * dpr,
            0, 0,
            designArea.width * dpr,
            designArea.height * dpr
        );

        const dataURL = exportCanvas.toDataURL('image/png');

        function addFreehandDrawingToFabric() {
            if (!currentDesingPrintAreaCanvas || !currentDesingPrintAreaContainer || !
                currentDesingPrintAreaEditableAreaData) {
                return;
            }

            const editableAreaData = currentDesingPrintAreaEditableAreaData;
            const restrictObject = currentDesingPrintAreaRestrictObject;
            const container = currentDesingPrintAreaContainer;
            const canvas = currentDesingPrintAreaCanvas;

            const padding = 10;
            const width = editableAreaData.width - padding * 2;
            const height = editableAreaData.height - padding * 2;

            const left = editableAreaData.left + padding;
            const top = editableAreaData.top + padding;

            const bgImg = canvas.backgroundImage;
            const imgScale = bgImg ? bgImg.scaleX : 1;
            const imgLeft = bgImg ? bgImg.left : 0;
            const imgTop = bgImg ? bgImg.top : 0;

            // Adjust left/top for background image scale and offset
            const finalLeft = imgLeft + left * imgScale;
            const finalTop = imgTop + top * imgScale;
            const finalWidth = width * imgScale;
            const finalHeight = height * imgScale;

            // Create export canvas for the freehand drawing
            const exportCanvas = document.createElement('canvas');
            exportCanvas.width = designArea.width * dpr;
            exportCanvas.height = designArea.height * dpr;
            const exportCtx = exportCanvas.getContext('2d');

            exportCtx.drawImage(
                drawingCanvas,
                designArea.left * dpr,
                designArea.top * dpr,
                designArea.width * dpr,
                designArea.height * dpr,
                0, 0,
                designArea.width * dpr,
                designArea.height * dpr
            );

            const dataURL = exportCanvas.toDataURL('image/png');

            fabric.Image.fromURL(dataURL, (fabricImg) => {
                fabricImg.set({
                    left: finalLeft,
                    top: finalTop,
                    scaleX: finalWidth / fabricImg.width,
                    scaleY: finalHeight / fabricImg.height,
                    selectable: true,
                    hasControls: true,
                    lockScalingFlip: true,
                    lockRotation: true,
                    shapeName: "Drawing"
                });

                restrictObject(fabricImg);
                canvas.add(fabricImg).setActiveObject(fabricImg);
                canvas.requestRenderAll();

                // save to hidden input (if exists)
                const hiddenInput = container.find('input[name="selected_area[]"]')[0];
                if (hiddenInput) {
                    hiddenInput.value = JSON.stringify(
                        canvas.toJSON(['selectable', 'angle', 'shapeName'])
                    );
                }
            });
        }


        function centerDrawingCanvas() {
            // Calculate center position for the canvas viewport
            const canvasWidth = mainDrawingCanvas.width / dpr;
            const canvasHeight = mainDrawingCanvas.height / dpr;

            // Get the center point of the design area in world coordinates
            const designCenterX = designArea.left + designArea.width / 2;
            const designCenterY = designArea.top + designArea.height / 2;

            // Calculate viewport center
            const viewportCenterX = canvasWidth / 2;
            const viewportCenterY = canvasHeight / 2;

            // Set initial zoom to fit the design area with padding
            const padding = 100;
            const zoomX = (canvasWidth - padding * 2) / designArea.width;
            const zoomY = (canvasHeight - padding * 2) / designArea.height;
            drawScale = Math.min(zoomX, zoomY, drawMaxZoom);
            drawScale = Math.max(drawScale, drawMinZoom);

            // Calculate offset to center the design area at the current zoom
            drawOffsetX = (viewportCenterX / drawScale) - designCenterX;
            drawOffsetY = (viewportCenterY / drawScale) - designCenterY;

            // Update display
            displayWithCurrentZoom();
            drawUpdateZoomDisplay();
        }


        // Usage: when user confirms the drawing
        $('#confirmDrawBtn').on('click', () => {

            addFreehandDrawingToFabric();
            $('#drawingModal').modal('hide'); // Close modal
        });
    </script>
@endpush
