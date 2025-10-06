@extends('layouts.app')

@section('content')
<div class="container">
    @if(session('success'))
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        {{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
@endif

@if(session('error'))
    <div class="alert alert-danger alert-dismissible fade show" role="alert">
        {{ session('error') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
@endif

    <h2>Dashboard Penyewa</h2>
<h4>Selamat datang, {{ auth()->user()->name }}!</h6>
    <h4>Kontrakan Tersedia</h4>
    <div class="card mb-4">
        <div class="card-body">
            <table class="table table-bordered table-striped">
                <thead>
                    <tr>
                        <th>No</th>
                        <th>Nomor Unit</th>
                        <th>Harga Sewa</th>
                        <th>Keterangan</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($kontrakanKosong as $k)
                    <tr>
                        <td>{{ $loop->iteration + ($kontrakanKosong->currentPage() - 1) * $kontrakanKosong->perPage() }}</td>
                        <td>{{ $k->nomor_unit }}</td>
                        <td>Rp {{ number_format($k->harga_sewa, 0, ',', '.') }}</td>
                        <td>{{ $k->keterangan }}</td>
                        <td>
    <!-- Tombol ajukan sewa -->
    <button type="button" class="btn btn-success btn-sm" data-bs-toggle="modal" data-bs-target="#ajukanModal{{ $k->id_kontrakan }}">
        Ajukan Sewa
    </button>

    <!-- Modal konfirmasi ajukan sewa -->
    <div class="modal fade" id="ajukanModal{{ $k->id_kontrakan }}" tabindex="-1" aria-labelledby="ajukanModalLabel{{ $k->id_kontrakan }}" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="ajukanModalLabel{{ $k->id_kontrakan }}">Konfirmasi Ajukan Sewa</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Tutup"></button>
                </div>
                <div class="modal-body">
                    Apakah Anda yakin ingin mengajukan sewa untuk kontrakan <strong>{{ $k->nomor_unit }}</strong>?
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <form action="{{ route('sewa.ajukan') }}" method="POST">
                        @csrf
                        <input type="hidden" name="id_kontrakan" value="{{ $k->id_kontrakan }}">
                        <button type="submit" class="btn btn-success">Ya, Ajukan</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</td>

                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="text-center text-muted">Tidak ada kontrakan kosong.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
            {{ $kontrakanKosong->links() }}
        </div>
    </div>

<h4>Status Sewa Saya</h4>
<div class="card mb-4">
    <div class="card-body">
        @if($sewaSaya->isEmpty())
            <p class="text-muted">Belum ada sewa diajukan.</p>
        @else
            <table class="table table-bordered table-striped">
                <thead>
                    <tr>
                        <th>No</th>
                        <th>Nomor Unit</th>
                        <th>Tanggal Mulai</th>
                        <th>Tanggal Selesai</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($sewaSaya as $s)
                        <tr>
                            <td>{{ $loop->iteration }}</td>
                            <td>{{ $s->kontrakan->nomor_unit }}</td>
                            <td>{{ $s->tgl_mulai }}</td>
                            <td>{{ $s->tgl_selesai }}</td>
                            <td>
                                @if($s->status_sewa == 'aktif')
                                    <span class="badge bg-success">Aktif</span>
                                @else
                                    <span class="badge bg-secondary">Selesai</span>
                                @endif
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        @endif
    </div>
</div>

</div>
@endsection
