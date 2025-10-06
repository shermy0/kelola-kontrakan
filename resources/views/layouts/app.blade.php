<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Manajemen Kontrakan</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="{{ asset('css/app.css') }}" rel="stylesheet"> <!-- styling custom -->
</head>
<body>
<div class="sidebar">
    <h4 class="p-3">Menu</h4>

    {{-- Menu untuk Admin --}}
    @if(auth()->user()->role === 'admin')
        <a href="{{ route('dashboard.admin') }}">Dashboard Admin</a>
        <a href="{{ route('kontrakan.index') }}">Kontrakan</a>
        <a href="{{ route('penyewa.index') }}">Penyewa</a>
        <a href="{{ route('sewa.index') }}">Sewa</a>
    @endif

    {{-- Menu untuk Pengelola --}}
    @if(auth()->user()->role === 'pengelola')
        <a href="{{ route('dashboard.pengelola') }}">Dashboard Pengelola</a>
        <a href="{{ route('kontrakan.index') }}">Kontrakan</a>
        <a href="{{ route('sewa.index') }}">Sewa</a>
    @endif

    {{-- Logout --}}
    <form action="{{ route('logout') }}" method="POST" class="m-2">
        @csrf
        <button type="submit" class="btn btn-danger w-100">Logout</button>
    </form>
</div>

        <div class="content">
            @yield('content')
        </div>
@stack('scripts')

</body>
<!-- Bootstrap Bundle with Popper -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</html>
