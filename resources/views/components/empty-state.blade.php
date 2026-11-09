@props([
    'title' => 'Belum ada data',
    'description' => 'Data akan tampil di sini setelah ditambahkan.',
    'icon' => 'car',
])
<div class="empty">
    <div class="icon-box"><x-icon :name="$icon" /></div>
    <h3>{{ $title }}</h3>
    <p>{{ $description }}</p>{{ $slot }}
</div>
