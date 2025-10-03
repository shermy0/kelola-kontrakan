@extends('layouts.app')

@section('content')
<div class="container mt-4 fade-in">
    <h2>Dashboard Admin</h2>
    <h6>Selamat datang, {{ auth()->user()->name }}!</h6>

    <div class="row mt-4">
    <div class="col-md-3">
        <div class="card text-bg-primary mb-3">
            <div class="card-body-dashboard">
                <h5 class="card-title">Total Penyewa</h5>
                <p class="card-text fs-3">{{ $totalPenyewa }}</p>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card text-bg-success mb-3">
            <div class="card-body-dashboard">
                <h5 class="card-title">Total Kontrakan</h5>
                <p class="card-text fs-3">{{ $totalKontrakan }}</p>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card text-bg-danger mb-3">
            <div class="card-body-dashboard">
                <h5 class="card-title">Sewa Aktif</h5>
                <p class="card-text fs-3">{{ $totalSewaAktif }}</p>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card text-bg-danger mb-3">
            <div class="card-body-dashboard">
                <h5 class="card-title">Sewa Selesai</h5>
                <p class="card-text fs-3">{{ $totalSewaSelesai }}</p>
            </div>
        </div>
    </div>
</div>

<!-- Tabel Sewa Terbaru -->
<br>
        <h5>Sewa Terbaru</h5>
    <div class="card-body p-0">
        <table class="table table-striped mb-0">
            <thead>
                <tr>
                    <th>Penyewa</th>
                    <th>Kontrakan</th>
                    <th>Tgl Mulai</th>
                    <th>Status</th>
                </tr>
            </thead>
            <tbody>
                @forelse($recentSewa as $sewa)
                <tr>
                    <td>{{ $sewa->penyewa->nama_lengkap }}</td>
                    <td>{{ $sewa->kontrakan->nomor_unit }}</td>
                    <td>{{ $sewa->tgl_mulai }}</td>
                    <td>
                        <span class="badge bg-{{ $sewa->status_sewa == 'aktif' ? 'success' : 'secondary' }}">
                            {{ ucfirst($sewa->status_sewa) }}
                        </span>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="4" class="text-center">Belum ada data sewa</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

</div>
@endsection
