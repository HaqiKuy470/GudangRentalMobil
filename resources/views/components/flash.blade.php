@if (session('success'))
    <div class="alert" role="status">
        {{ session('success') }}</div>
@endif
@if ($errors->any())
    <div class="alert error" role="alert"><strong>Periksa kembali isian Anda.</strong>
        <ul>
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif
