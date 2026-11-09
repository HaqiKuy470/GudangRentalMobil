@props(['name', 'label', 'type' => 'text', 'value' => '', 'required' => false])
<div class="field">
    <label for="{{ $name }}">{{ $label }} @if ($required)
            <span aria-hidden="true">*</span>
        @endif
    </label>
    <input id="{{ $name }}" name="{{ $name }}" type="{{ $type }}"
        value="{{ $type === 'password' ? '' : old($name, $value) }}" @required($required) {{ $attributes }}
        @if ($errors->has($name)) aria-invalid="true" aria-describedby="{{ $name }}-error" @endif>
    @error($name)
        <p class="error" id="{{ $name }}-error">{{ $message }}</p>
    @enderror
</div>
