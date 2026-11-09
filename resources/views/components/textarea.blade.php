@props(['name', 'label', 'value' => '', 'required' => false])
<div class="field">
    <label for="{{ $name }}">{{ $label }} @if ($required)
            *
        @endif
    </label>
    <textarea id="{{ $name }}" name="{{ $name }}" @required($required) {{ $attributes }}>{{ old($name, $value) }}</textarea>
    @error($name)
        <p class="error">{{ $message }}</p>
    @enderror
</div>
