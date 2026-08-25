@props(['checked' => false, 'class' => NULL, 'id' => NULL])
<label class="form-check form-switch form--switch ps-0 switch">
    <input type="checkbox" {{ $attributes }} @checked($checked) class="form-check-input {{ $class }}" id="{{ $id }}">
</label>

@pushOnce('style')
    <style>
        .form--switch .form-check-input {
           height: 20px;
            width: 40px;
        }

        .form--switch .form-check-input::before {
            width: 16px;
            height: 16px;
            left: 3px;
        }

        .form--switch .form-check-input:checked::before {
            left: calc(100% - 19px);
        }
    </style>
@endPushOnce
