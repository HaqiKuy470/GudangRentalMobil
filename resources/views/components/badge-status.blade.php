@props(['status'])
@php($value = $status instanceof \BackedEnum ? $status->value : (string) $status)
@php(
    $label =
        $status instanceof \BackedEnum
            ? $status->label()
            : match ($value) {
                'tersedia' => 'Tersedia',
                'disewa' => 'Sedang Disewa',
                'perawatan' => 'Perawatan',
                'tidak-tersedia' => 'Tidak tersedia',
                default => $value
            }
)
<span class="badge {{ strtolower(str_replace(' ', '-', $value)) }}">{{ $label }}</span>
