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
    @if(auth()->user()->role === 'penyewa')
        <a href="{{ route('dashboard.penyewa') }}">Dashboard Penyewa</a>
    @endif

    {{-- Logout --}}
    <form action="{{ route('logout') }}" method="POST" class="m-2">
        @csrf
{{-- Logout --}}
<button type="button" class="btn btn-danger w-100" data-bs-toggle="modal" data-bs-target="#logoutModal">
    Logout
</button>

<!-- Modal Konfirmasi Logout -->
<div class="modal fade" id="logoutModal" tabindex="-1" aria-labelledby="logoutModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="logoutModalLabel">Konfirmasi Logout</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Tutup"></button>
      </div>
      <div class="modal-body">
        Apakah Anda yakin ingin logout?
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
        <form action="{{ route('logout') }}" method="POST">
            @csrf
            <button type="submit" class="btn btn-danger">Ya, Logout</button>
        </form>
      </div>
    </div>
  </div>
</div>
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
