import React, { Component, Suspense, useCallback, useEffect, useMemo, useRef, useState } from 'react';
import { createRoot } from 'react-dom/client';
import { Canvas } from '@react-three/fiber';
import { Center, Decal, Environment, Html, OrbitControls, useGLTF, useTexture } from '@react-three/drei';
import * as THREE from 'three';
import './designer.css';

const fabric = window.fabric;
const DESIGN_CANVAS_WIDTH = 600;
const DESIGN_CANVAS_HEIGHT = 400;
const BLANK_TEXTURE = 'data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAAAEAAAABCAYAAAAfFcSJAAAADUlEQVQIHWP4z8DwHwAFgAI/ScLqWQAAAABJRU5ErkJggg==';
const SETTINGS_PREVIEW_TEXTURE = `data:image/svg+xml;charset=utf-8,${encodeURIComponent(`
    <svg xmlns="http://www.w3.org/2000/svg" width="600" height="600" viewBox="0 0 600 600">
        <g transform="translate(600 0) scale(-1 1)">
            <rect x="35" y="35" width="530" height="530" rx="24" fill="none" stroke="#2563eb" stroke-width="18" stroke-dasharray="28 18"/>
            <circle cx="300" cy="250" r="82" fill="#ef4444" opacity=".92"/>
            <path d="M300 145L325 220L405 220L340 267L365 345L300 298L235 345L260 267L195 220L275 220Z" fill="#fff"/>
            <text x="300" y="430" text-anchor="middle" font-family="Arial,sans-serif" font-size="58" font-weight="700" fill="#111827">ARTWORK</text>
        </g>
    </svg>
`)}`;
const SETTINGS_PREVIEW_ENGRAVE_TEXTURE = `data:image/svg+xml;charset=utf-8,${encodeURIComponent(`
    <svg xmlns="http://www.w3.org/2000/svg" width="600" height="600" viewBox="0 0 600 600">
        <g transform="translate(600 0) scale(-1 1)" fill="#000000">
            <rect x="35" y="35" width="530" height="530" rx="24" fill="none" stroke="#000000" stroke-width="18" stroke-dasharray="28 18"/>
            <path d="M300 130L337 232L445 235L359 301L389 405L300 345L211 405L241 301L155 235L263 232Z"/>
            <text x="300" y="500" text-anchor="middle" font-family="Arial,sans-serif" font-size="58" font-weight="700">ENGRAVE</text>
        </g>
    </svg>
`)}`;
const SERIALIZE_PROPS = ['shapeName', 'originalSrc', 'customInfo', 'designerId'];

function ensureDesignerId(object) {
    if (!object.designerId) object.designerId = `object-${Date.now()}-${Math.random().toString(36).slice(2, 8)}`;
    return object.designerId;
}

function objectLabel(object) {
    if (['i-text', 'text', 'textbox'].includes(object.type)) return `Text · ${(object.text || 'Untitled').slice(0, 20)}`;
    if (object.type === 'image') return 'Uploaded image';
    const name = object.shapeName || object.type || 'Object';
    return name.charAt(0).toUpperCase() + name.slice(1);
}

function notify(type, message) {
    if (typeof window.notify === 'function') window.notify(type, message);
    else window.alert(message);
}

function readableError(value) {
    if (!value) return 'Something went wrong. Please try again.';
    if (typeof value === 'string') return value;
    if (Array.isArray(value)) return value.map(readableError).join(' ');
    return Object.values(value).flat().map(readableError).join(' ');
}

function getArea(canvas, selectedArea) {
    const bg = canvas.backgroundImage;
    if (!bg || !selectedArea) return null;
    const imageLeft = bg.originX === 'center' ? bg.left - bg.getScaledWidth() / 2 : bg.left;
    const imageTop = bg.originY === 'center' ? bg.top - bg.getScaledHeight() / 2 : bg.top;
    return {
        left: imageLeft + selectedArea.left * bg.scaleX,
        top: imageTop + selectedArea.top * bg.scaleY,
        width: selectedArea.width * bg.scaleX,
        height: selectedArea.height * bg.scaleY,
    };
}

function centerObject(canvas, object, area) {
    object.set({
        left: area ? area.left + area.width / 2 : canvas.getWidth() / 2,
        top: area ? area.top + area.height / 2 : canvas.getHeight() / 2,
        originX: 'center',
        originY: 'center',
    });
    if (area) {
        const maxWidth = area.width * 0.72;
        const maxHeight = area.height * 0.72;
        const scale = Math.min(maxWidth / Math.max(object.width, 1), maxHeight / Math.max(object.height, 1), 1);
        object.scale(scale);
    }
    object.setCoords();
}

function clampObject(canvas, object, selectedArea) {
    const area = getArea(canvas, selectedArea);
    if (!area || object.__designerHelper) return;
    object.setCoords();
    let bounds = object.getBoundingRect(true, true);
    if (bounds.width > area.width || bounds.height > area.height) {
        const factor = Math.min(area.width / bounds.width, area.height / bounds.height) * 0.98;
        object.scaleX *= factor;
        object.scaleY *= factor;
        object.setCoords();
        bounds = object.getBoundingRect(true, true);
    }
    if (bounds.left < area.left) object.left += area.left - bounds.left;
    if (bounds.top < area.top) object.top += area.top - bounds.top;
    if (bounds.left + bounds.width > area.left + area.width) object.left -= bounds.left + bounds.width - area.left - area.width;
    if (bounds.top + bounds.height > area.top + area.height) object.top -= bounds.top + bounds.height - area.top - area.height;
    object.setCoords();
}

function pathShape(type, color) {
    const paths = {
        star: 'M 50 0 L 61 35 L 98 35 L 68 57 L 79 92 L 50 71 L 21 92 L 32 57 L 2 35 L 39 35 z',
        heart: 'M 50 88 C 20 66 0 48 0 25 C 0 5 25 -5 50 18 C 75 -5 100 5 100 25 C 100 48 80 66 50 88 z',
        diamond: 'M 50 0 L 100 50 L 50 100 L 0 50 z',
        pentagon: 'M 50 0 L 100 38 L 81 100 L 19 100 L 0 38 z',
        hexagon: 'M 25 0 L 75 0 L 100 50 L 75 100 L 25 100 L 0 50 z',
        cloud: 'M 23 78 C 4 78 -4 56 9 44 C 8 21 34 12 49 27 C 66 7 96 19 93 43 C 111 51 101 79 82 78 z',
        arrow: 'M 0 35 L 60 35 L 60 10 L 100 50 L 60 90 L 60 65 L 0 65 z',
        bubble: 'M 5 5 L 95 5 L 95 70 L 58 70 L 38 94 L 42 70 L 5 70 z',
        moon: 'M 77 4 C 39 13 35 68 73 87 C 38 103 0 78 0 43 C 0 9 39 -12 77 4 z',
    };
    return new fabric.Path(paths[type], { fill: color, stroke: color, strokeWidth: 1, shapeName: type });
}

function makeShape(type, color) {
    const common = { fill: color, stroke: color, strokeWidth: 1, shapeName: type };
    if (type === 'rect') return new fabric.Rect({ ...common, width: 100, height: 76, rx: 4, ry: 4 });
    if (type === 'circle') return new fabric.Circle({ ...common, radius: 48 });
    if (type === 'triangle') return new fabric.Triangle({ ...common, width: 105, height: 95 });
    if (type === 'line') return new fabric.Line([0, 0, 120, 0], { ...common, fill: null, strokeWidth: 6 });
    if (type === 'polygon') {
        return new fabric.Polygon([{ x: 50, y: 0 }, { x: 100, y: 38 }, { x: 81, y: 100 }, { x: 19, y: 100 }, { x: 0, y: 38 }], common);
    }
    return pathShape(type, color);
}

function applyEngraveStyle(object) {
    if (!object || object.__designerHelper) return object;
    if (object.type === 'image') {
        const Grayscale = fabric?.Image?.filters?.Grayscale;
        if (Grayscale) {
            object.filters = [new Grayscale({ mode: 'luminosity' })];
            object.applyFilters();
        }
    } else {
        if (object.fill !== null && object.fill !== undefined && object.fill !== 'transparent') object.set('fill', '#000000');
        if (object.stroke !== null && object.stroke !== undefined && object.stroke !== 'transparent') object.set('stroke', '#000000');
        if (object.shadow) object.set('shadow', null);
    }
    object.getObjects?.().forEach(applyEngraveStyle);
    return object;
}

function applyEngraveCanvas(canvas) {
    canvas?.getObjects().filter((object) => !object.__designerHelper).forEach(applyEngraveStyle);
}

function applyGarmentColor(image, color) {
    if (!image) return;
    const BlendColor = fabric?.Image?.filters?.BlendColor;
    image.filters = BlendColor && color ? [new BlendColor({ color, mode: 'multiply', alpha: 1 })] : [];
    image.applyFilters?.();
}

function CanvasStage({ area, active, register, onTexture, onState, engrave, garmentColor }) {
    const elementRef = useRef(null);
    const stageRef = useRef(null);
    const canvasRef = useRef(null);
    const borderRef = useRef(null);
    const selectedAreaRef = useRef(area.selectedArea);
    const historyRef = useRef([]);
    const historyIndexRef = useRef(-1);
    const restoringRef = useRef(false);
    const textureTimerRef = useRef(null);
    const garmentColorRef = useRef(garmentColor);

    const syncState = useCallback(() => {
        const canvas = canvasRef.current;
        if (!canvas) return;
        const activeObject = canvas.getActiveObject();
        onState(area.id, {
            activeId: activeObject?.designerId || null,
            zoom: Math.round(canvas.getZoom() * 100),
            layers: canvas.getObjects()
                .filter((object) => !object.__designerHelper)
                .slice()
                .reverse()
                .map((object) => ({ id: ensureDesignerId(object), label: objectLabel(object) })),
        });
    }, [area.id, onState]);

    const serialize = useCallback(() => {
        const canvas = canvasRef.current;
        if (!canvas) return JSON.stringify({ version: fabric.version, objects: [] });
        if (engrave) applyEngraveCanvas(canvas);
        const json = canvas.toJSON(SERIALIZE_PROPS);
        json.objects = canvas.getObjects()
            .filter((object) => !object.__designerHelper)
            .map((object) => {
                ensureDesignerId(object);
                return object.toObject(SERIALIZE_PROPS);
            });
        delete json.backgroundImage;
        delete json.backgroundColor;
        const value = JSON.stringify(json);
        const input = document.querySelector(`[data-design-value="${area.id}"]`);
        if (input) input.value = value;
        return value;
    }, [area.id, engrave]);

    const updateTexture = useCallback(() => {
        const canvas = canvasRef.current;
        if (!canvas) return;
        window.clearTimeout(textureTimerRef.current);
        textureTimerRef.current = window.setTimeout(() => {
            const editable = getArea(canvas, selectedAreaRef.current);
            if (!editable) return;
            if (engrave) applyEngraveCanvas(canvas);
            const border = borderRef.current;
            const previousOpacity = canvas.backgroundImage?.opacity ?? 1;
            if (canvas.backgroundImage) canvas.backgroundImage.set('opacity', 0);
            if (border) border.set('visible', false);
            canvas.renderAll();
            const hasObjects = canvas.getObjects().some((object) => !object.__designerHelper);
            const texture = hasObjects ? canvas.toDataURL({
                format: 'png',
                left: editable.left,
                top: editable.top,
                width: editable.width,
                height: editable.height,
                multiplier: 2,
            }) : BLANK_TEXTURE;
            if (canvas.backgroundImage) canvas.backgroundImage.set('opacity', previousOpacity);
            if (border) border.set('visible', true);
            canvas.renderAll();
            if (!hasObjects) {
                onTexture(area.id, texture);
                return;
            }
            const textureImage = new Image();
            textureImage.onload = () => {
                const mirrored = document.createElement('canvas');
                mirrored.width = textureImage.width;
                mirrored.height = textureImage.height;
                const context = mirrored.getContext('2d');
                context.translate(mirrored.width, 0);
                context.scale(-1, 1);
                context.drawImage(textureImage, 0, 0);
                onTexture(area.id, mirrored.toDataURL('image/png'));
            };
            textureImage.src = texture;
        }, 100);
    }, [area.id, engrave, onTexture]);

    const captureHistory = useCallback(() => {
        if (restoringRef.current) return;
        const json = serialize();
        const history = historyRef.current.slice(0, historyIndexRef.current + 1);
        if (history.at(-1) !== json) history.push(json);
        if (history.length > 40) history.shift();
        historyRef.current = history;
        historyIndexRef.current = history.length - 1;
        syncState();
        updateTexture();
    }, [serialize, syncState, updateTexture]);

    const loadDesign = useCallback((json, capture = false) => {
        const canvas = canvasRef.current;
        if (!canvas) return;
        restoringRef.current = true;
        const background = canvas.backgroundImage;
        const border = borderRef.current;
        canvas.getObjects().forEach((object) => canvas.remove(object));
        const parsed = typeof json === 'string' ? JSON.parse(json || '{}') : (json || {});
        fabric.util.enlivenObjects(parsed.objects || [], (objects) => {
            objects.forEach((object) => {
                object.__designerHelper = false;
                if (engrave) applyEngraveStyle(object);
                ensureDesignerId(object);
                canvas.add(object);
                clampObject(canvas, object, selectedAreaRef.current);
            });
            if (border) canvas.add(border);
            canvas.setBackgroundImage(background, canvas.renderAll.bind(canvas));
            restoringRef.current = false;
            serialize();
            syncState();
            updateTexture();
            if (capture) captureHistory();
        });
    }, [captureHistory, engrave, serialize, syncState, updateTexture]);

    useEffect(() => {
        if (!fabric || !elementRef.current) return undefined;
        const canvas = new fabric.Canvas(elementRef.current, {
            width: DESIGN_CANVAS_WIDTH,
            height: DESIGN_CANVAS_HEIGHT,
            preserveObjectStacking: true,
            selection: true,
        });
        canvasRef.current = canvas;
        canvas.skipOffscreen = true;

        const changed = () => captureHistory();
        canvas.on('object:added', changed);
        canvas.on('object:modified', changed);
        canvas.on('object:removed', changed);
        canvas.on('path:created', (event) => {
            if (engrave) applyEngraveStyle(event.path);
            clampObject(canvas, event.path, selectedAreaRef.current);
            changed();
        });
        canvas.on('object:moving', (event) => clampObject(canvas, event.target, selectedAreaRef.current));
        canvas.on('object:scaling', (event) => clampObject(canvas, event.target, selectedAreaRef.current));
        canvas.on('object:rotating', (event) => clampObject(canvas, event.target, selectedAreaRef.current));
        canvas.on('selection:created', syncState);
        canvas.on('selection:updated', syncState);
        canvas.on('selection:cleared', syncState);
        canvas.on('mouse:wheel', (event) => {
            event.e.preventDefault();
            event.e.stopPropagation();
            const zoom = Math.max(0.35, Math.min(4, canvas.getZoom() * Math.pow(0.999, event.e.deltaY)));
            canvas.zoomToPoint({ x: event.e.offsetX, y: event.e.offsetY }, zoom);
            syncState();
        });

        fabric.Image.fromURL(area.imageUrl, (image) => {
            const scale = Math.min(canvas.getWidth() / image.width, canvas.getHeight() / image.height);
            image.set({
                originX: 'center',
                originY: 'center',
                left: canvas.getWidth() / 2,
                top: canvas.getHeight() / 2,
                scaleX: scale,
                scaleY: scale,
                selectable: false,
                evented: false,
            });
            applyGarmentColor(image, garmentColorRef.current);
            canvas.setBackgroundImage(image, () => {
                const editable = getArea(canvas, selectedAreaRef.current);
                if (editable) {
                    const border = new fabric.Rect({
                        ...editable,
                        fill: 'rgba(255,255,255,.01)',
                        stroke: '#2563eb',
                        strokeWidth: 2,
                        strokeDashArray: [8, 6],
                        selectable: false,
                        evented: false,
                        excludeFromExport: true,
                    });
                    border.__designerHelper = true;
                    borderRef.current = border;
                }
                loadDesign(area.savedDesign || { version: fabric.version, objects: [] }, true);
            });
        }, { crossOrigin: 'anonymous' });

        const addObject = (object) => {
            if (engrave) applyEngraveStyle(object);
            ensureDesignerId(object);
            centerObject(canvas, object, getArea(canvas, selectedAreaRef.current));
            canvas.add(object);
            canvas.setActiveObject(object);
            canvas.requestRenderAll();
        };

        const api = {
            serialize,
            hasDesign: () => canvas.getObjects().some((object) => !object.__designerHelper),
            addText: () => addObject(new fabric.IText('Your text', { fontFamily: 'Arial', fontSize: 34, fill: engrave ? '#000000' : '#111827', shapeName: 'text' })),
            addShape: (type, color) => addObject(makeShape(type, engrave ? '#000000' : color)),
            addImage: (dataUrl) => fabric.Image.fromURL(dataUrl, (image) => {
                image.set({ originalSrc: dataUrl, shapeName: 'image' });
                if (engrave) applyEngraveStyle(image);
                addObject(image);
            }),
            updateText: (property, value) => {
                const object = canvas.getActiveObject();
                if (!object || !['i-text', 'text', 'textbox'].includes(object.type)) return notify('error', 'Select a text object first.');
                object.set(property, engrave && property === 'fill' ? '#000000' : value);
                object.setCoords();
                clampObject(canvas, object, selectedAreaRef.current);
                canvas.requestRenderAll();
                captureHistory();
            },
            toggleText: (property, onValue, offValue) => {
                const object = canvas.getActiveObject();
                if (!object || !['i-text', 'text', 'textbox'].includes(object.type)) return notify('error', 'Select a text object first.');
                object.set(property, object.get(property) === onValue ? offValue : onValue);
                canvas.requestRenderAll();
                captureHistory();
            },
            curveText: () => {
                const object = canvas.getActiveObject();
                if (!object || !['i-text', 'text', 'textbox'].includes(object.type)) return notify('error', 'Select a text object first.');
                object.set('path', object.path ? null : new fabric.Path('M 0 70 Q 140 0 280 70', { visible: false }));
                object.setCoords();
                canvas.requestRenderAll();
                captureHistory();
            },
            setDrawing: (enabled, color, width) => {
                canvas.isDrawingMode = enabled;
                canvas.freeDrawingBrush.color = engrave ? '#000000' : color;
                canvas.freeDrawingBrush.width = Number(width);
                canvas.selection = !enabled;
            },
            deleteSelected: () => {
                const activeObjects = canvas.getActiveObjects().filter((object) => !object.__designerHelper);
                canvas.discardActiveObject();
                activeObjects.forEach((object) => canvas.remove(object));
                canvas.requestRenderAll();
            },
            clear: () => {
                canvas.getObjects().filter((object) => !object.__designerHelper).forEach((object) => canvas.remove(object));
                canvas.discardActiveObject();
                canvas.requestRenderAll();
            },
            undo: () => {
                if (historyIndexRef.current <= 0) return;
                historyIndexRef.current -= 1;
                loadDesign(historyRef.current[historyIndexRef.current]);
            },
            redo: () => {
                if (historyIndexRef.current >= historyRef.current.length - 1) return;
                historyIndexRef.current += 1;
                loadDesign(historyRef.current[historyIndexRef.current]);
            },
            zoom: (factor) => {
                const next = Math.max(0.35, Math.min(4, canvas.getZoom() * factor));
                canvas.zoomToPoint({ x: canvas.getWidth() / 2, y: canvas.getHeight() / 2 }, next);
                syncState();
            },
            fit: () => {
                const editable = getArea(canvas, selectedAreaRef.current);
                if (!editable) return;
                const zoom = Math.min((canvas.getWidth() - 70) / editable.width, (canvas.getHeight() - 70) / editable.height);
                canvas.setViewportTransform([zoom, 0, 0, zoom, canvas.getWidth() / 2 - (editable.left + editable.width / 2) * zoom, canvas.getHeight() / 2 - (editable.top + editable.height / 2) * zoom]);
                canvas.requestRenderAll();
                syncState();
            },
            resetZoom: () => {
                canvas.setViewportTransform([1, 0, 0, 1, 0, 0]);
                canvas.requestRenderAll();
                syncState();
            },
            selectLayer: (id) => {
                const object = canvas.getObjects().find((item) => item.designerId === id);
                if (!object) return;
                canvas.setActiveObject(object);
                canvas.requestRenderAll();
                syncState();
            },
            duplicate: () => {
                const object = canvas.getActiveObject();
                if (!object || object.__designerHelper) return notify('error', 'Select an object to duplicate.');
                object.clone((clone) => {
                    clone.set({ left: object.left + 14, top: object.top + 14, evented: true });
                    if (engrave) applyEngraveStyle(clone);
                    clone.designerId = null;
                    ensureDesignerId(clone);
                    clampObject(canvas, clone, selectedAreaRef.current);
                    canvas.add(clone);
                    canvas.setActiveObject(clone);
                    canvas.requestRenderAll();
                }, SERIALIZE_PROPS);
            },
            moveLayer: (direction) => {
                const object = canvas.getActiveObject();
                if (!object || object.__designerHelper) return notify('error', 'Select an object first.');
                if (direction === 'forward') canvas.bringForward(object);
                else canvas.sendBackwards(object);
                if (borderRef.current) canvas.bringToFront(borderRef.current);
                canvas.requestRenderAll();
                captureHistory();
            },
            centerSelected: () => {
                const object = canvas.getActiveObject();
                const editable = getArea(canvas, selectedAreaRef.current);
                if (!object || !editable) return notify('error', 'Select an object first.');
                object.set({ left: editable.left + editable.width / 2, top: editable.top + editable.height / 2, originX: 'center', originY: 'center' });
                object.setCoords();
                canvas.requestRenderAll();
                captureHistory();
            },
            nudge: (x, y) => {
                const object = canvas.getActiveObject();
                if (!object || object.__designerHelper) return;
                object.set({ left: object.left + x, top: object.top + y });
                clampObject(canvas, object, selectedAreaRef.current);
                canvas.requestRenderAll();
                captureHistory();
            },
            isEditingText: () => Boolean(canvas.getActiveObject()?.isEditing),
            enforceEngrave: () => {
                applyEngraveCanvas(canvas);
                canvas.requestRenderAll();
                captureHistory();
            },
        };
        register(area.id, api);

        return () => {
            window.clearTimeout(textureTimerRef.current);
            register(area.id, null);
            canvas.dispose();
        };
    }, []);

    useEffect(() => {
        garmentColorRef.current = garmentColor;
        const canvas = canvasRef.current;
        if (!canvas?.backgroundImage) return;
        applyGarmentColor(canvas.backgroundImage, garmentColor);
        canvas.requestRenderAll();
    }, [garmentColor]);

    useEffect(() => {
        const canvas = canvasRef.current;
        const stage = stageRef.current;
        const host = stage?.parentElement;
        if (!canvas || !stage || !host) return undefined;

        let frameId = 0;
        let previousSize = '';
        const resizeDisplay = () => {
            window.cancelAnimationFrame(frameId);
            frameId = window.requestAnimationFrame(() => {
                const styles = window.getComputedStyle(host);
                const horizontalPadding = parseFloat(styles.paddingLeft || '0') + parseFloat(styles.paddingRight || '0');
                const availableWidth = Math.max(1, host.clientWidth - horizontalPadding);
                const displayScale = Math.min(1, availableWidth / DESIGN_CANVAS_WIDTH);
                const displayWidth = Math.round(DESIGN_CANVAS_WIDTH * displayScale);
                const displayHeight = Math.round(DESIGN_CANVAS_HEIGHT * displayScale);
                const nextSize = `${displayWidth}x${displayHeight}`;

                if (nextSize !== previousSize) {
                    canvas.setDimensions({
                        width: `${displayWidth}px`,
                        height: `${displayHeight}px`,
                    }, { cssOnly: true });
                    stage.style.width = `${displayWidth}px`;
                    stage.style.height = `${displayHeight}px`;
                    previousSize = nextSize;
                }

                canvas.calcOffset();
                canvas.requestRenderAll();
            });
        };

        const observer = typeof ResizeObserver === 'function' ? new ResizeObserver(resizeDisplay) : null;
        observer?.observe(host);
        window.addEventListener('resize', resizeDisplay);
        resizeDisplay();

        return () => {
            observer?.disconnect();
            window.removeEventListener('resize', resizeDisplay);
            window.cancelAnimationFrame(frameId);
        };
    }, [active]);

    return (
        <div ref={stageRef} className={`ink-canvas-stage ${active ? 'is-active' : ''}`} aria-hidden={!active}>
            <canvas ref={elementRef} width={DESIGN_CANVAS_WIDTH} height={DESIGN_CANVAS_HEIGHT} />
        </div>
    );
}

function textureFrom(url) {
    return useTexture(url || BLANK_TEXTURE);
}

function ReferenceShirt({ modelUrl, color, frontTexture, backTexture, onView, activeSide, settings }) {
    const { nodes, materials, scene } = useGLTF(modelUrl);
    const front = textureFrom(frontTexture);
    const back = textureFrom(backTexture);
    front.colorSpace = THREE.SRGBColorSpace;
    back.colorSpace = THREE.SRGBColorSpace;
    front.flipY = false;
    back.flipY = false;

    const setting = (key, fallback) => {
        const value = Number(settings?.[key]);
        return Number.isFinite(value) ? value : fallback;
    };
    const movement = (key) => setting(key, 0) * 0.1;
    const modelRotation = [
        THREE.MathUtils.degToRad(setting('rotation_x', 0)),
        THREE.MathUtils.degToRad(setting('rotation_y', 0)) + (activeSide === 'back' ? Math.PI : 0),
        THREE.MathUtils.degToRad(setting('rotation_z', 0)),
    ];
    const modelPosition = [setting('offset_x', 0), setting('offset_y', 0), 0];
    const modelScale = setting('model_scale', 1);
    const modelScaleVector = [modelScale, modelScale, modelScale];

    const exactModel = nodes['T-Shirt_1'] && nodes['T-Shirt_2'] && nodes['T-Shirt_3'] && nodes['T-Shirt001'];
    const genericFront = useMemo(() => {
        if (exactModel) return front;
        const texture = front.clone();
        texture.wrapS = THREE.RepeatWrapping;
        texture.wrapT = THREE.RepeatWrapping;
        texture.repeat.set(-1, -1);
        texture.offset.set(1, 1);
        texture.needsUpdate = true;
        return texture;
    }, [exactModel, front]);
    const genericBack = useMemo(() => {
        const texture = back.clone();
        texture.wrapS = THREE.RepeatWrapping;
        texture.wrapT = THREE.RepeatWrapping;
        texture.repeat.set(-1, -1);
        texture.offset.set(1, 1);
        texture.needsUpdate = true;
        return texture;
    }, [back]);
    const shirtMaterial = useMemo(() => {
        if (!exactModel) return null;
        const material = (materials.background || nodes['T-Shirt001'].material).clone();
        material.color = new THREE.Color(color || '#ffffff');
        return material;
    }, [exactModel, materials, nodes, color]);

    const genericModel = useMemo(() => {
        if (exactModel) return null;
        const clone = scene.clone(true);
        clone.traverse((child) => {
            if (!child.isMesh) return;
            const materialsToColor = (Array.isArray(child.material) ? child.material : [child.material]).map((material) => {
                const clonedMaterial = material.clone();
                if (clonedMaterial.color) clonedMaterial.color = new THREE.Color(color || '#ffffff');
                return clonedMaterial;
            });
            child.material = Array.isArray(child.material) ? materialsToColor : materialsToColor[0];
        });

        // Uploaded GLBs can use very different authoring units and origins.
        // Normalize them to the viewer so large, tiny, or offset models remain visible.
        const bounds = new THREE.Box3().setFromObject(clone);
        const size = bounds.getSize(new THREE.Vector3());
        const center = bounds.getCenter(new THREE.Vector3());
        const largestDimension = Math.max(size.x, size.y, size.z, 0.001);
        const normalizedScale = 4.8 / largestDimension;
        clone.scale.setScalar(normalizedScale);
        clone.position.set(
            -center.x * normalizedScale,
            -center.y * normalizedScale,
            -center.z * normalizedScale
        );
        clone.updateMatrixWorld(true);

        // Bake the largest visible mesh into normalized world coordinates. It
        // becomes an invisible decal target so artwork also works on uploaded
        // GLBs whose mesh names differ from the bundled reference shirt.
        let decalGeometry = null;
        let largestVolume = 0;
        clone.traverse((child) => {
            if (!child.isMesh || !child.geometry?.attributes?.position) return;
            const geometry = child.geometry.clone();
            geometry.applyMatrix4(child.matrixWorld);
            geometry.computeBoundingBox();
            const meshSize = geometry.boundingBox.getSize(new THREE.Vector3());
            const volume = meshSize.x * meshSize.y * meshSize.z;
            if (volume > largestVolume) {
                decalGeometry?.dispose();
                decalGeometry = geometry;
                largestVolume = volume;
            } else {
                geometry.dispose();
            }
        });

        if (!decalGeometry) return { scene: clone, decalGeometry: null };

        const decalBounds = decalGeometry.boundingBox;
        const decalSize = decalBounds.getSize(new THREE.Vector3());
        const decalCenter = decalBounds.getCenter(new THREE.Vector3());
        const decalScale = [
            Math.min(decalSize.x * 0.34, 1.6),
            Math.min(decalSize.y * 0.48, 1.5),
            Math.max(decalSize.z * 0.55, 0.5),
        ];
        return {
            scene: clone,
            decalGeometry,
            frontPosition: [decalCenter.x, decalCenter.y, decalBounds.max.z + 0.01],
            backPosition: [decalCenter.x, decalCenter.y, decalBounds.min.z - 0.02],
            bounds: {
                min: [decalBounds.min.x, decalBounds.min.y],
                max: [decalBounds.max.x, decalBounds.max.y],
            },
            decalScale,
            backDecalScale: [decalScale[0] * 0.82, decalScale[1] * 0.82, decalScale[2]],
        };
    }, [exactModel, scene, color]);

    useEffect(() => () => genericModel?.decalGeometry?.dispose(), [genericModel]);
    useEffect(() => () => {
        if (!exactModel) {
            genericFront.dispose();
        }
        genericBack.dispose();
    }, [exactModel, genericFront, genericBack]);

    if (!exactModel) {
        if (!genericModel?.decalGeometry) {
            return <group dispose={null} rotation={modelRotation} position={modelPosition} scale={modelScaleVector}><primitive object={genericModel.scene} /></group>;
        }
        const keepVisible = (base, offset, min, max, artworkSize) => {
            const availableHalf = Math.max((max - min) / 2, 0);
            const artworkHalf = Math.min(Math.abs(artworkSize) / 2, availableHalf);
            const lower = min + artworkHalf;
            const upper = max - artworkHalf;
            return THREE.MathUtils.clamp(base + offset, lower, upper);
        };
        const frontScale = [
            genericModel.decalScale[0] * setting('front_width', 1),
            genericModel.decalScale[1] * setting('front_height', 1),
            genericModel.decalScale[2],
        ];
        const backScale = [
            genericModel.backDecalScale[0] * setting('back_width', 1),
            genericModel.backDecalScale[1] * setting('back_height', 1),
            1,
        ];
        const frontPosition = [
            keepVisible(genericModel.frontPosition[0], movement('front_x'), genericModel.bounds.min[0], genericModel.bounds.max[0], frontScale[0]),
            keepVisible(genericModel.frontPosition[1], movement('front_y'), genericModel.bounds.min[1], genericModel.bounds.max[1], frontScale[1]),
            genericModel.frontPosition[2],
        ];
        const backPosition = [
            keepVisible(genericModel.backPosition[0], -movement('back_x'), genericModel.bounds.min[0], genericModel.bounds.max[0], backScale[0]),
            keepVisible(genericModel.backPosition[1], movement('back_y'), genericModel.bounds.min[1], genericModel.bounds.max[1], backScale[1]),
            genericModel.backPosition[2],
        ];
        return (
            <group dispose={null} rotation={modelRotation} position={modelPosition} scale={modelScaleVector}>
                <primitive object={genericModel.scene} />
                {genericModel.decalGeometry && <>
                    <mesh geometry={genericModel.decalGeometry} onClick={() => onView('front')}>
                        <meshBasicMaterial transparent opacity={0} depthWrite={false} colorWrite={false} />
                        <Decal position={frontPosition} rotation={[0, 0, 0]} scale={frontScale}>
                            <meshStandardMaterial map={genericFront} toneMapped={false} transparent depthTest polygonOffset polygonOffsetFactor={-4} />
                        </Decal>
                    </mesh>
                    <mesh
                        position={backPosition}
                        rotation={[0, Math.PI, 0]}
                        scale={backScale}
                        renderOrder={10}
                        onClick={() => onView('back')}
                    >
                        <planeGeometry args={[1, 1]} />
                        <meshBasicMaterial map={genericBack} toneMapped={false} transparent depthTest={false} depthWrite={false} />
                    </mesh>
                </>}
            </group>
        );
    }

    return (
        <group rotation={modelRotation} position={modelPosition} scale={modelScaleVector}>
            <Center position={[0, 0.1, 0]}>
                <group dispose={null}>
                    <group rotation={[Math.PI / 2, 0, 0]}>
                        <mesh scale={7.5} position={[0, 0, 2]} geometry={nodes['T-Shirt_1'].geometry} material={materials.Shirt} castShadow receiveShadow />
                        <mesh scale={7.5} position={[0, 0, 2]} geometry={nodes['T-Shirt_2'].geometry} onClick={() => onView('front')}>
                            <meshBasicMaterial transparent opacity={0} depthWrite={false} colorWrite={false} />
                            <Decal
                                position={[THREE.MathUtils.clamp(movement('front_x'), -0.7, 0.7), 0.2, -0.31 - THREE.MathUtils.clamp(movement('front_y'), -0.7, 0.7)]}
                                rotation={[-Math.PI / 2 - 0.05, 0, Math.PI]}
                                scale={[0.52 * setting('front_width', 1), 0.7 * setting('front_height', 1), 0.5]}
                            >
                                <meshStandardMaterial map={front} toneMapped={false} transparent depthTest polygonOffset polygonOffsetFactor={-4} />
                            </Decal>
                        </mesh>
                        <mesh scale={7.5} position={[0, 0, 2]} geometry={nodes['T-Shirt_3'].geometry} onClick={() => onView('back')}>
                            <meshBasicMaterial transparent opacity={0} depthWrite={false} colorWrite={false} />
                            <Decal
                                position={[-THREE.MathUtils.clamp(movement('back_x'), -0.7, 0.7), -0.2, -0.27 - THREE.MathUtils.clamp(movement('back_y'), -0.7, 0.7)]}
                                rotation={[Math.PI / 2 - 0.2, 0, Math.PI]}
                                scale={[0.52 * setting('back_width', 1), 0.7 * setting('back_height', 1), 0.5]}
                            >
                                <meshStandardMaterial map={genericBack} toneMapped={false} transparent depthTest polygonOffset polygonOffsetFactor={-4} />
                            </Decal>
                        </mesh>
                        <mesh scale={7.5} position={[0, 0, 2]} geometry={nodes['T-Shirt_4'].geometry} material={materials['left hand']} castShadow receiveShadow />
                        <mesh scale={7.5} position={[0, 0, 2]} geometry={nodes['T-Shirt_5'].geometry} material={materials['right hand']} castShadow receiveShadow />
                    </group>
                    <group rotation={[Math.PI / 2, 0, 0]}>
                        <mesh scale={7.5} position={[0, 0, 2]} geometry={nodes['T-Shirt001'].geometry} material={shirtMaterial} castShadow receiveShadow />
                    </group>
                </group>
            </Center>
        </group>
    );
}

class ViewerBoundary extends Component {
    constructor(props) {
        super(props);
        this.state = { failed: false, message: '' };
    }
    static getDerivedStateFromError() { return { failed: true }; }
    componentDidCatch(error) { this.setState({ message: error?.message || '3D preview unavailable' }); }
    render() {
        return this.state.failed
            ? <div className="ink-model-error">{this.props.fallback}<small>{this.state.message}</small></div>
            : this.props.children;
    }
}

function ModelViewer({ config, textures, areas, activeId, setActiveId, garmentColor }) {
    const frontArea = areas.find((area) => !/back/i.test(area.name)) || areas[0];
    const backArea = areas.find((area) => /back/i.test(area.name)) || areas[1];
    const activeSide = backArea && activeId === backArea.id ? 'back' : 'front';
    const switchSide = (side) => {
        const target = side === 'back' ? backArea : frontArea;
        if (target) setActiveId(target.id);
    };
    const preview = <img className="ink-model-fallback" src={config.previewUrl} alt="Product 3D preview" />;

    return (
        <section className="ink-model-panel">
            <div className="ink-panel-heading">
                <div><span className="ink-step">01</span><h2>Preview in 3D</h2></div>
                <span className="ink-live"><i /> Live preview</span>
            </div>
            <div className="ink-model-canvas">
                <ViewerBoundary fallback={preview}>
                    <Canvas camera={{ position: [0, 0, 8.5], fov: 40 }} shadows dpr={[1, 1.8]}>
                        <ambientLight intensity={0.65} />
                        <directionalLight position={[4, 6, 5]} intensity={1.2} castShadow />
                        <Suspense fallback={<Html center><div className="ink-loader">Loading 3D model…</div></Html>}>
                            <ReferenceShirt modelUrl={config.modelUrl} color={garmentColor || config.color} frontTexture={textures[frontArea?.id]} backTexture={textures[backArea?.id]} onView={switchSide} activeSide={activeSide} settings={config.modelSettings} />
                            <Environment preset="studio" />
                        </Suspense>
                        <OrbitControls enablePan={false} minDistance={5} maxDistance={12} minPolarAngle={Math.PI / 3} maxPolarAngle={Math.PI / 1.65} />
                    </Canvas>
                </ViewerBoundary>
            </div>
            <div className="ink-model-help">Drag to rotate · Scroll to zoom · Artwork updates automatically</div>
            <div className="ink-view-buttons">
                {areas.map((area) => <button type="button" key={area.id} className={activeId === area.id ? 'active' : ''} onClick={() => setActiveId(area.id)}>{area.name}</button>)}
            </div>
        </section>
    );
}

function readSettingsFromForm(form) {
    if (!form) return {};
    const settings = {};
    form.querySelectorAll('input[name^="designer_settings["]').forEach((input) => {
        const key = input.name.match(/^designer_settings\[([^\]]+)]$/)?.[1];
        if (key) settings[key] = Number(input.value);
    });
    return settings;
}

function PreviewReady({ onReady }) {
    useEffect(() => onReady(), [onReady]);
    return null;
}

function sameSettings(left, right) {
    const keys = new Set([...Object.keys(left || {}), ...Object.keys(right || {})]);
    return [...keys].every((key) => Math.abs(Number(left?.[key]) - Number(right?.[key])) < 0.0001);
}

function ModelSettingsPreview({ element }) {
    const form = element.closest('form');
    const settingsRoot = element.closest('[data-designer-settings]');
    const initialSettings = useRef(readSettingsFromForm(form));
    const initialDesignForm = useRef(settingsRoot?.dataset.initialDesignerForm || 'dtg');
    const [settings, setSettings] = useState(initialSettings.current);
    const [designForm, setDesignForm] = useState(initialDesignForm.current);
    const [modelUrl, setModelUrl] = useState(element.dataset.modelUrl);
    const [activeSide, setActiveSide] = useState('front');
    const [localFileName, setLocalFileName] = useState('');
    const [modelLoaded, setModelLoaded] = useState(false);
    const [showAdvanced, setShowAdvanced] = useState(false);
    const objectUrlRef = useRef(null);
    const submittingRef = useRef(false);
    const defaults = useMemo(() => {
        try {
            return JSON.parse(settingsRoot?.dataset.defaultSettings || '{}');
        } catch {
            return {};
        }
    }, [settingsRoot]);
    const isDirty = localFileName !== '' || designForm !== initialDesignForm.current || !sameSettings(settings, initialSettings.current);

    const updateFormSettings = useCallback((nextSettings) => {
        Object.entries(nextSettings).forEach(([key, value]) => {
            const input = form?.querySelector(`input[name="designer_settings[${key}]"]`);
            if (!input) return;
            input.value = value;
            input.dispatchEvent(new Event('input', { bubbles: true }));
            input.dispatchEvent(new Event('change', { bubbles: true }));
        });
    }, [form]);

    const markReady = useCallback(() => setModelLoaded(true), []);

    useEffect(() => {
        const inputs = [...(form?.querySelectorAll('input[name^="designer_settings["]') || [])];
        const sync = () => setSettings(readSettingsFromForm(form));
        inputs.forEach((input) => {
            input.addEventListener('input', sync);
            input.addEventListener('change', sync);
        });

        const modelInput = form.querySelector('input[name="designer_model"]');
        const loadSelectedModel = () => {
            const file = modelInput?.files?.[0];
            if (objectUrlRef.current) URL.revokeObjectURL(objectUrlRef.current);
            objectUrlRef.current = file ? URL.createObjectURL(file) : null;
            setModelLoaded(false);
            setModelUrl(objectUrlRef.current || element.dataset.modelUrl);
            setLocalFileName(file?.name || '');
        };
        modelInput?.addEventListener('change', loadSelectedModel);
        const designFormSelect = settingsRoot?.querySelector('[data-designer-form-select]');
        const syncDesignForm = () => {
            const nextForm = designFormSelect?.value || 'dtg';
            setDesignForm(nextForm);
            const help = settingsRoot?.querySelector('[data-designer-form-help]');
            if (help) help.textContent = nextForm === 'engrave'
                ? 'Engrave makes all text, shapes, and drawing black and converts uploaded artwork to monochrome.'
                : 'DTG keeps the full-color design tools currently available to customers.';
        };
        designFormSelect?.addEventListener('change', syncDesignForm);
        const handleSubmit = () => { submittingRef.current = true; };
        form?.addEventListener('submit', handleSubmit);

        return () => {
            inputs.forEach((input) => {
                input.removeEventListener('input', sync);
                input.removeEventListener('change', sync);
            });
            modelInput?.removeEventListener('change', loadSelectedModel);
            designFormSelect?.removeEventListener('change', syncDesignForm);
            form?.removeEventListener('submit', handleSubmit);
            if (objectUrlRef.current) URL.revokeObjectURL(objectUrlRef.current);
        };
    }, [element, form]);

    useEffect(() => {
        settingsRoot?.classList.toggle('ink-show-advanced', showAdvanced);
        return () => settingsRoot?.classList.remove('ink-show-advanced');
    }, [settingsRoot, showAdvanced]);

    useEffect(() => {
        if (!isDirty) return undefined;
        const warnBeforeLeaving = (event) => {
            if (submittingRef.current) return;
            event.preventDefault();
            event.returnValue = '';
        };
        window.addEventListener('beforeunload', warnBeforeLeaving);
        return () => window.removeEventListener('beforeunload', warnBeforeLeaving);
    }, [isDirty]);

    const autoFit = () => updateFormSettings({
        model_scale: defaults.model_scale ?? 1,
        rotation_x: defaults.rotation_x ?? 0,
        rotation_y: defaults.rotation_y ?? 0,
        rotation_z: defaults.rotation_z ?? 0,
        offset_x: defaults.offset_x ?? 0,
        offset_y: defaults.offset_y ?? 0,
    });
    const resetSide = () => updateFormSettings({
        [`${activeSide}_x`]: defaults[`${activeSide}_x`] ?? 0,
        [`${activeSide}_y`]: defaults[`${activeSide}_y`] ?? 0,
        [`${activeSide}_width`]: defaults[`${activeSide}_width`] ?? 1,
        [`${activeSide}_height`]: defaults[`${activeSide}_height`] ?? 1,
    });
    const revertUnsaved = () => {
        updateFormSettings(initialSettings.current);
        const modelInput = form?.querySelector('input[name="designer_model"]');
        if (modelInput && localFileName) {
            modelInput.value = '';
            modelInput.dispatchEvent(new Event('change', { bubbles: true }));
        }
        const designFormSelect = settingsRoot?.querySelector('[data-designer-form-select]');
        if (designFormSelect) {
            designFormSelect.value = initialDesignForm.current;
            designFormSelect.dispatchEvent(new Event('change', { bubbles: true }));
        }
    };

    const frontReady = Number(settings.front_width) > 0 && Number(settings.front_height) > 0;
    const backReady = Number(settings.back_width) > 0 && Number(settings.back_height) > 0;
    const modelLabel = localFileName || (element.dataset.hasCustomModel === '1' ? 'Custom model' : 'Reference model');
    const previewTexture = designForm === 'engrave' ? SETTINGS_PREVIEW_ENGRAVE_TEXTURE : SETTINGS_PREVIEW_TEXTURE;

    const fallback = <img className="ink-model-fallback" src={element.dataset.previewUrl} alt="3D model preview fallback" />;
    return (
        <div className="ink-settings-preview-card">
            <div className="ink-settings-preview-heading">
                <div><strong>Set up your 3D model</strong><small>Changes appear immediately in the preview</small></div>
                <span>{modelLabel}</span>
            </div>
            <div className="ink-setup-status" aria-label="Model setup status">
                <span className={modelLoaded ? 'ready' : ''}><i />{modelLoaded ? 'Model loaded' : 'Loading model'}</span>
                <span className={frontReady ? 'ready' : ''}><i />Front configured</span>
                <span className={backReady ? 'ready' : ''}><i />Back configured</span>
                <span className="ready"><i />{designForm === 'engrave' ? 'Engrave · monochrome' : 'DTG · full color'}</span>
                <span className={isDirty ? 'changed' : 'ready'}><i />{isDirty ? 'Unsaved changes' : 'Settings saved'}</span>
            </div>
            <div className="ink-settings-preview-canvas">
                <ViewerBoundary key={modelUrl} fallback={fallback}>
                    <Canvas camera={{ position: [0, 0, 8.5], fov: 40 }} dpr={[1, 1.5]}>
                        <ambientLight intensity={0.8} />
                        <directionalLight position={[4, 6, 5]} intensity={1.35} />
                        <Suspense fallback={<Html center><div className="ink-loader">Loading model…</div></Html>}>
                            <ReferenceShirt
                                key={modelUrl}
                                modelUrl={modelUrl}
                                color="#ffffff"
                                frontTexture={previewTexture}
                                backTexture={previewTexture}
                                activeSide={activeSide}
                                settings={settings}
                                onView={setActiveSide}
                            />
                            <PreviewReady onReady={markReady} />
                        </Suspense>
                        <OrbitControls enablePan={false} minDistance={4.5} maxDistance={13} />
                    </Canvas>
                </ViewerBoundary>
            </div>
            <div className="ink-settings-preview-actions">
                <button type="button" className={activeSide === 'front' ? 'active' : ''} onClick={() => setActiveSide('front')}>Front</button>
                <button type="button" className={activeSide === 'back' ? 'active' : ''} onClick={() => setActiveSide('back')}>Back</button>
                <small>Drag to rotate · Scroll to zoom</small>
            </div>
            <div className="ink-setup-toolbar">
                <div>
                    <button type="button" onClick={autoFit} title="Center the model and restore its recommended size">Auto-fit model</button>
                    <button type="button" onClick={resetSide}>Reset {activeSide}</button>
                    <button type="button" onClick={revertUnsaved} disabled={!isDirty}>Revert unsaved</button>
                </div>
                <button type="button" className="ink-mode-toggle" aria-pressed={showAdvanced} onClick={() => setShowAdvanced((value) => !value)}>
                    {showAdvanced ? 'Hide advanced settings' : 'Show advanced settings'}
                </button>
            </div>
            {isDirty && <div className="ink-unsaved-note" role="status">Remember to save the product to keep these model settings.</div>}
        </div>
    );
}

const SHAPES = [
    ['rect', '■'], ['circle', '●'], ['triangle', '▲'], ['line', '━'], ['star', '★'], ['polygon', '⬟'], ['heart', '♥'],
    ['diamond', '◆'], ['pentagon', '⬟'], ['hexagon', '⬢'], ['cloud', '☁'], ['arrow', '➜'], ['bubble', '▰'], ['moon', '◐'],
];

function Tools({ api, tool, setTool, editorState, engrave }) {
    const [textColor, setTextColor] = useState('#111827');
    const [shapeColor, setShapeColor] = useState('#ef4444');
    const [drawColor, setDrawColor] = useState('#111827');
    const [brushWidth, setBrushWidth] = useState(5);

    useEffect(() => {
        if (!api) return undefined;
        if (engrave) {
            setTextColor('#000000');
            setShapeColor('#000000');
            setDrawColor('#000000');
            api.enforceEngrave();
        }
        api.setDrawing(tool === 'draw', engrave ? '#000000' : drawColor, brushWidth);
        return () => api.setDrawing(false, engrave ? '#000000' : drawColor, brushWidth);
    }, [api, engrave, tool]);

    const choose = (next) => {
        if (tool === 'draw' && next !== 'draw') api?.setDrawing(false, drawColor, brushWidth);
        setTool(next);
        if (next === 'draw') api?.setDrawing(true, engrave ? '#000000' : drawColor, brushWidth);
    };

    return (
        <aside className="ink-tools">
            {engrave && <div className="ink-engrave-notice"><i />Engrave mode · Black and monochrome artwork only</div>}
            <div className="ink-tool-tabs">
                <button type="button" className={tool === 'text' ? 'active' : ''} onClick={() => choose('text')}><span>T</span>Text</button>
                <button type="button" className={tool === 'image' ? 'active' : ''} onClick={() => choose('image')}><span>▧</span>Image</button>
                <button type="button" className={tool === 'shape' ? 'active' : ''} onClick={() => choose('shape')}><span>◇</span>Shape</button>
                <button type="button" className={tool === 'draw' ? 'active' : ''} onClick={() => choose('draw')}><span>✎</span>Draw</button>
            </div>
            <div className="ink-tool-body">
                {tool === 'text' && <>
                    <button type="button" className="ink-primary-tool" onClick={() => api?.addText()}>+ Add text</button>
                    <label>Font family<select onChange={(e) => api?.updateText('fontFamily', e.target.value)}><option>Arial</option><option>Helvetica</option><option>Times New Roman</option><option>Courier New</option><option>Comic Sans MS</option><option>Impact</option><option>Georgia</option><option>Verdana</option></select></label>
                    <label>Font size<input type="number" min="8" defaultValue="34" onChange={(e) => api?.updateText('fontSize', Number(e.target.value))} /></label>
                    <div className="ink-inline-tools">
                        <button type="button" onClick={() => api?.toggleText('fontWeight', 'bold', 'normal')}><b>B</b></button>
                        <button type="button" onClick={() => api?.toggleText('fontStyle', 'italic', 'normal')}><i>I</i></button>
                        <button type="button" onClick={() => api?.toggleText('underline', true, false)}><u>U</u></button>
                        <button type="button" title="Curve text" onClick={() => api?.curveText()}>⌒</button>
                        {engrave
                            ? <span className="ink-fixed-black" title="Engrave color is fixed to black"><i />Black only</span>
                            : <input aria-label="Text color" type="color" value={textColor} onChange={(e) => { setTextColor(e.target.value); api?.updateText('fill', e.target.value); }} />}
                    </div>
                </>}
                {tool === 'image' && <>
                    <label className="ink-upload"><span>＋</span><b>Upload artwork</b><small>PNG, JPG, SVG or WebP</small><input type="file" accept="image/*" onChange={(event) => {
                        const file = event.target.files?.[0];
                        if (!file) return;
                        if (file.size > 15 * 1024 * 1024) {
                            event.target.value = '';
                            return notify('error', 'Artwork files must be 15 MB or smaller.');
                        }
                        const reader = new FileReader();
                        reader.onload = () => api?.addImage(reader.result);
                        reader.readAsDataURL(file);
                        event.target.value = '';
                    }} /></label>
                    <p className="ink-hint">{engrave ? 'Uploaded artwork is automatically converted to monochrome.' : 'High-resolution transparent PNG artwork gives the best print result.'}</p>
                </>}
                {tool === 'shape' && <>
                    {engrave
                        ? <label>Shape color<span className="ink-fixed-black"><i />Black only</span></label>
                        : <label>Shape color<input type="color" value={shapeColor} onChange={(e) => setShapeColor(e.target.value)} /></label>}
                    <div className="ink-shape-grid">{SHAPES.map(([name, glyph]) => <button title={name} type="button" key={name} onClick={() => api?.addShape(name, engrave ? '#000000' : shapeColor)}>{glyph}</button>)}</div>
                </>}
                {tool === 'draw' && <>
                    <div className="ink-draw-status"><i /> Free drawing is active</div>
                    {engrave
                        ? <label>Brush color<span className="ink-fixed-black"><i />Black only</span></label>
                        : <label>Brush color<input type="color" value={drawColor} onChange={(e) => { setDrawColor(e.target.value); api?.setDrawing(true, e.target.value, brushWidth); }} /></label>}
                    <label>Brush width<input type="range" min="1" max="30" value={brushWidth} onChange={(e) => { setBrushWidth(e.target.value); api?.setDrawing(true, drawColor, e.target.value); }} /><span>{brushWidth}px</span></label>
                    <button type="button" className="ink-secondary-tool" onClick={() => choose('text')}>Finish drawing</button>
                </>}
            </div>
            <div className="ink-layers">
                <div className="ink-layers-heading">
                    <strong>Layers</strong>
                    <span>{editorState.layers?.length || 0} objects</span>
                </div>
                <div className="ink-layer-actions">
                    <button type="button" title="Duplicate selected (Ctrl/⌘ + D)" aria-label="Duplicate selected layer" onClick={() => api?.duplicate()}>⧉</button>
                    <button type="button" title="Move selected forward" aria-label="Move selected layer forward" onClick={() => api?.moveLayer('forward')}>↑</button>
                    <button type="button" title="Move selected backward" aria-label="Move selected layer backward" onClick={() => api?.moveLayer('backward')}>↓</button>
                    <button type="button" title="Center selected in print area" aria-label="Center selected layer in print area" onClick={() => api?.centerSelected()}>◎</button>
                </div>
                <div className="ink-layer-list">
                    {editorState.layers?.length ? editorState.layers.map((layer, index) => (
                        <button type="button" key={layer.id} className={editorState.activeId === layer.id ? 'active' : ''} onClick={() => api?.selectLayer(layer.id)}>
                            <span>{editorState.layers.length - index}</span>{layer.label}
                        </button>
                    )) : <p>Add text, artwork, a shape, or a drawing to begin.</p>}
                </div>
                <small className="ink-shortcuts">Delete removes · Arrow keys nudge · Ctrl/⌘ Z undo</small>
            </div>
        </aside>
    );
}

function DesignerApp({ config }) {
    const areas = config.printAreas;
    const engrave = config.designForm === 'engrave';
    const initialGarmentColor = /^#[0-9a-f]{6}$/i.test(config.color || '') ? config.color : '#ffffff';
    const [activeId, setActiveId] = useState(areas[0]?.id);
    const [tool, setTool] = useState('text');
    const [garmentColor, setGarmentColor] = useState(initialGarmentColor);
    const [textures, setTextures] = useState({});
    const [editorStates, setEditorStates] = useState({});
    const [, setReadyVersion] = useState(0);
    const [busy, setBusy] = useState(false);
    const apiRef = useRef({});
    const rootRef = useRef(null);
    const activeApi = apiRef.current[activeId];
    const activeArea = areas.find((area) => area.id === activeId);
    const activeEditorState = editorStates[activeId] || { activeId: null, zoom: 100, layers: [] };

    const register = useCallback((id, api) => {
        if (api) apiRef.current[id] = api;
        else delete apiRef.current[id];
        setReadyVersion((version) => version + 1);
    }, []);
    const updateTexture = useCallback((id, texture) => setTextures((current) => current[id] === texture ? current : { ...current, [id]: texture }), []);
    const updateEditorState = useCallback((id, state) => setEditorStates((current) => ({ ...current, [id]: state })), []);
    const serializeAll = () => Object.values(apiRef.current).forEach((api) => api.serialize());
    const hasDesign = () => Object.values(apiRef.current).some((api) => api.hasDesign());

    const saveNext = () => {
        serializeAll();
        if (!hasDesign()) return notify('error', 'Please add your design before submitting. Let your creativity shine!');
        document.getElementById('printArea').requestSubmit();
    };
    const addToCart = async () => {
        serializeAll();
        if (!hasDesign()) return notify('error', 'Please add your design before adding this product to cart.');
        setBusy(true);
        try {
            const form = document.getElementById('printArea');
            const response = await fetch(config.addToCartUrl, {
                method: 'POST',
                headers: { 'X-CSRF-TOKEN': config.csrf, 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' },
                body: new FormData(form),
            });
            const data = await response.json();
            if (!response.ok || data.status === 'error') throw new Error(readableError(data.message || data.errors));
            notify('success', readableError(data.message));
            const count = document.getElementById('totalCartItems');
            if (count && data.data?.totalProduct !== undefined) count.textContent = data.data.totalProduct;
        } catch (error) {
            notify('error', error.message);
        } finally {
            setBusy(false);
        }
    };

    useEffect(() => {
        const form = document.getElementById('printArea');
        const stopEnter = (event) => { if (event.key === 'Enter') event.preventDefault(); };
        const submit = (event) => {
            serializeAll();
            if (!hasDesign()) {
                event.preventDefault();
                notify('error', 'Please add your design before submitting. Let your creativity shine!');
            }
        };
        form.addEventListener('keydown', stopEnter);
        form.addEventListener('submit', submit);
        return () => { form.removeEventListener('keydown', stopEnter); form.removeEventListener('submit', submit); };
    }, []);

    useEffect(() => {
        const handleShortcut = (event) => {
            const target = event.target;
            if (target instanceof HTMLInputElement || target instanceof HTMLTextAreaElement || target instanceof HTMLSelectElement) return;
            const api = apiRef.current[activeId];
            if (!api || api.isEditingText()) return;
            const command = event.metaKey || event.ctrlKey;
            if (command && event.key.toLowerCase() === 'z') {
                event.preventDefault();
                event.shiftKey ? api.redo() : api.undo();
            } else if (command && event.key.toLowerCase() === 'y') {
                event.preventDefault();
                api.redo();
            } else if (command && event.key.toLowerCase() === 'd') {
                event.preventDefault();
                api.duplicate();
            } else if (event.key === 'Delete' || event.key === 'Backspace') {
                event.preventDefault();
                api.deleteSelected();
            } else if (['ArrowLeft', 'ArrowRight', 'ArrowUp', 'ArrowDown'].includes(event.key)) {
                event.preventDefault();
                const distance = event.shiftKey ? 10 : 1;
                api.nudge(event.key === 'ArrowLeft' ? -distance : event.key === 'ArrowRight' ? distance : 0, event.key === 'ArrowUp' ? -distance : event.key === 'ArrowDown' ? distance : 0);
            }
        };
        window.addEventListener('keydown', handleShortcut);
        return () => window.removeEventListener('keydown', handleShortcut);
    }, [activeId]);

    if (!fabric) return <div className="alert alert-danger">The design editor could not load. Please refresh the page.</div>;
    if (!areas.length) return <div className="alert alert-warning">No print area is configured for this product.</div>;

    return (
        <div className={`ink-designer ${engrave ? 'is-engrave' : ''}`} ref={rootRef}>
            <header className="ink-designer-header">
                <a href={config.backUrl} className="ink-back" aria-label="Back to product">←</a>
                <div><span>{engrave ? 'Engrave designer' : 'Product designer'}</span><h1>{config.productName}</h1></div>
                <div className="ink-header-actions">
                    <button type="button" onClick={() => window.jQuery?.('#instructionModal').modal('show')}>? Help</button>
                    <button type="button" onClick={() => rootRef.current?.requestFullscreen?.()}>⛶ Full screen</button>
                </div>
            </header>
            <main className="ink-workspace">
                <ModelViewer config={config} textures={textures} areas={areas} activeId={activeId} setActiveId={setActiveId} garmentColor={garmentColor} />
                <section className="ink-editor-panel">
                    <div className="ink-panel-heading">
                        <div><span className="ink-step">02</span><h2>Create your design</h2></div>
                        <span className={`ink-side-label ${engrave ? 'ink-engrave-badge' : ''}`}>{engrave ? 'Engrave · Black only' : `Editing: ${activeArea?.name} · ${activeEditorState.layers.length} objects`}</span>
                    </div>
                    <div className="ink-editor-grid">
                        <Tools api={activeApi} tool={tool} setTool={setTool} editorState={activeEditorState} engrave={engrave} />
                        <div className="ink-canvas-column">
                            <div className="ink-canvas-actions">
                                <div>
                                    <button type="button" title="Undo" aria-label="Undo" onClick={() => activeApi?.undo()}>↶</button>
                                    <button type="button" title="Redo" aria-label="Redo" onClick={() => activeApi?.redo()}>↷</button>
                                </div>
                                <div>
                                    <button type="button" title="Zoom out" aria-label="Zoom out" onClick={() => activeApi?.zoom(.85)}>−</button>
                                    <button type="button" title="Fit design area" aria-label="Fit design area" onClick={() => activeApi?.fit()}>Fit</button>
                                    <button type="button" title="Reset zoom" aria-label={`Reset zoom, currently ${activeEditorState.zoom}%`} onClick={() => activeApi?.resetZoom()}>{activeEditorState.zoom}%</button>
                                    <button type="button" title="Zoom in" aria-label="Zoom in" onClick={() => activeApi?.zoom(1.15)}>＋</button>
                                </div>
                                <div className="ink-garment-color" title="Change the garment color used by the drawing and 3D previews">
                                    <span>Garment</span>
                                    {['#ffffff', '#111827', '#2563eb', '#dc2626'].map((color) => (
                                        <button
                                            type="button"
                                            key={color}
                                            className={garmentColor.toLowerCase() === color ? 'active' : ''}
                                            style={{ '--swatch-color': color }}
                                            aria-label={`Preview garment in ${color}`}
                                            onClick={() => setGarmentColor(color)}
                                        />
                                    ))}
                                    <label aria-label="Choose a custom garment preview color">
                                        <input type="color" value={garmentColor} onChange={(event) => setGarmentColor(event.target.value)} />
                                    </label>
                                </div>
                                <div>
                                    <button type="button" title="Duplicate selected" aria-label="Duplicate selected object" onClick={() => activeApi?.duplicate()}>⧉</button>
                                    <button type="button" title="Center selected" aria-label="Center selected object" onClick={() => activeApi?.centerSelected()}>◎</button>
                                    <button type="button" title="Delete selected" aria-label="Delete selected object" onClick={() => activeApi?.deleteSelected()}>⌫</button>
                                    <button type="button" title="Clear design" aria-label="Clear all artwork from this print area" onClick={() => { if (window.confirm('Clear all artwork from this print area?')) activeApi?.clear(); }}>Clear</button>
                                </div>
                            </div>
                            <div className="ink-canvas-wrap">
                                {areas.map((area) => <CanvasStage key={area.id} area={area} active={activeId === area.id} register={register} onTexture={updateTexture} onState={updateEditorState} engrave={engrave} garmentColor={garmentColor} />)}
                            </div>
                            <div className="ink-canvas-meta">
                                <span>Blue dashed line = printable boundary</span>
                                {(activeArea?.width || activeArea?.height) && <strong>{activeArea.width || '—'} × {activeArea.height || '—'} in</strong>}
                            </div>
                            <div className="ink-print-tabs">{areas.map((area) => <button type="button" key={area.id} className={activeId === area.id ? 'active' : ''} onClick={() => setActiveId(area.id)}>{area.name}</button>)}</div>
                        </div>
                    </div>
                </section>
            </main>
            <footer className="ink-designer-footer">
                <div><span>{engrave ? 'Engrave-ready workflow' : 'Print-ready workflow'}</span><small>{engrave ? 'All artwork is saved in black or monochrome for engraving.' : 'Your design is saved with the product and remains editable in the cart.'}</small></div>
                <div>
                    <button type="button" className="ink-cart-button" disabled={busy} onClick={addToCart}>{busy ? 'Saving…' : 'Add to cart'}</button>
                    <button type="button" className="ink-next-button" onClick={saveNext}>Save & next →</button>
                </div>
            </footer>
        </div>
    );
}

const mount = document.getElementById('ink-product-designer');
if (mount && window.InkDesignerConfig) createRoot(mount).render(<DesignerApp config={window.InkDesignerConfig} />);

document.querySelectorAll('[data-model-settings-preview]').forEach((element) => {
    let mounted = false;
    const mountPreview = () => {
        if (mounted) return;
        mounted = true;
        createRoot(element).render(<ModelSettingsPreview element={element} />);
    };
    const details = element.closest('details');
    if (!details || details.open) mountPreview();
    else details.addEventListener('toggle', () => details.open && mountPreview(), { once: true });
});
