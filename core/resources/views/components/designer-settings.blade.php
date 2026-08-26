@php
    $settings = $product->resolvedDesignerSettings();
    $defaults = \App\Models\Product::designerSettingsDefaults();
    $idPrefix = $idPrefix ?? 'designer';
    $groups = [
        [
            'key' => 'model',
            'label' => __('Model position'),
            'description' => __('Fit and orient the model in the viewer.'),
            'fields' => [
                ['model_scale', __('Model size'), 'any', __('Makes the complete model larger or smaller.'), false],
                ['rotation_x', __('Tilt forward / back'), 'any', __('Rotates the model around the horizontal axis.'), true],
                ['rotation_y', __('Turn left / right'), 'any', __('Changes which direction is treated as the front.'), false],
                ['rotation_z', __('Tilt left / right'), 'any', __('Corrects a model that appears tilted.'), true],
                ['offset_x', __('Move left / right'), 'any', __('Moves the complete model horizontally.'), true],
                ['offset_y', __('Move up / down'), 'any', __('Moves the complete model vertically.'), true],
            ],
        ],
        [
            'key' => 'front',
            'label' => __('Front print area'),
            'description' => __('Position the artwork shown on the front.'),
            'fields' => [
                ['front_x', __('Move left / right'), 'any', __('Moves only the front artwork horizontally.'), false],
                ['front_y', __('Move up / down'), 'any', __('Moves only the front artwork vertically.'), false],
                ['front_width', __('Artwork width'), 'any', __('Changes the width of the front artwork.'), false],
                ['front_height', __('Artwork height'), 'any', __('Changes the height of the front artwork.'), false],
            ],
        ],
        [
            'key' => 'back',
            'label' => __('Back print area'),
            'description' => __('Position the artwork shown on the back.'),
            'fields' => [
                ['back_x', __('Move left / right'), 'any', __('Moves only the back artwork horizontally.'), false],
                ['back_y', __('Move up / down'), 'any', __('Moves only the back artwork vertically.'), false],
                ['back_width', __('Artwork width'), 'any', __('Changes the width of the back artwork.'), false],
                ['back_height', __('Artwork height'), 'any', __('Changes the height of the back artwork.'), false],
            ],
        ],
    ];
@endphp

<div class="ink-model-setup" data-designer-settings data-default-settings='@json($defaults)'>
    <div
        class="ink-settings-preview"
        data-model-settings-preview
        data-model-url="{{ $product->designerModelUrl() }}"
        data-preview-url="{{ $product->designerPreviewUrl() }}"
        data-has-custom-model="{{ $product->designer_model ? '1' : '0' }}"
    ></div>

    <div class="ink-settings-fields row g-3 mt-0">
        @foreach ($groups as $group)
            <div class="col-xl-4" data-setting-group="{{ $group['key'] }}">
                <div class="ink-setting-group border rounded p-3 h-100">
                    <div class="mb-3">
                        <h6 class="mb-1">{{ $group['label'] }}</h6>
                        <small class="text-muted">{{ $group['description'] }}</small>
                    </div>
                    <div class="row g-2">
                        @foreach ($group['fields'] as [$key, $label, $step, $help, $advanced])
                            <div class="col-6 {{ $advanced ? 'ink-advanced-setting' : '' }}" data-setting-key="{{ $key }}">
                                <label for="{{ $idPrefix }}-{{ $key }}" class="form-label small mb-1" title="{{ $help }}">
                                    {{ $label }} <span class="ink-setting-help" aria-hidden="true">?</span>
                                </label>
                                <input
                                    id="{{ $idPrefix }}-{{ $key }}"
                                    type="number"
                                    class="form-control form-control-sm"
                                    name="designer_settings[{{ $key }}]"
                                    value="{{ $settings[$key] }}"
                                    step="{{ $step }}"
                                    inputmode="decimal"
                                    aria-describedby="{{ $idPrefix }}-{{ $key }}-help"
                                    required
                                >
                                <span id="{{ $idPrefix }}-{{ $key }}-help" class="visually-hidden">{{ $help }}</span>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        @endforeach
    </div>
</div>
