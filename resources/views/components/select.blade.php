@props(['name', 'label', 'required' => false])
<div class="field">
    <label for="{{ $name }}">{{ $label }} @if ($required)
            <span aria-hidden="true">*</span>
        @endif
    </label>
    <select id="{{ $name }}" name="{{ $name }}" @required($required)
        {{ $attributes }}>{{ $slot }}</select>
    @error($name)
        <p class="error">{{ $message }}</p>
    @enderror
</div>
