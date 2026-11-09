@props(['variant' => 'primary', 'href' => null, 'icon' => null])
@if ($href)<a href="{{ $href }}"
        {{ $attributes->class(['btn', 'btn-'.$variant]) }}>
        @if ($icon)
            <x-icon :name="$icon" />
        @endif{{ $slot }}
    </a>
@else<button {{ $attributes->class(['btn', 'btn-'.$variant])->merge(['type' => 'submit']) }}>
        @if ($icon)
            <x-icon :name="$icon" />
        @endif{{ $slot }}
    </button>@endif
