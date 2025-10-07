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
<h4>Selamat datang, {{ auth()->user()->name }}!</h4>

<h4>Kontrakan Tersedia</h4>
<form action="{{ route('dashboard.penyewa') }}" method="GET" class="mb-3">
    <div class="input-group">
        <input type="text" name="search" class="form-control" placeholder="Cari nomor unit atau keterangan..." value="{{ request('search') }}">
        <button class="btn btn-primary" type="submit">Cari</button>
    </div>
</form>

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

                        <!-- Modal ajukan sewa -->
                        <div class="modal fade" id="ajukanModal{{ $k->id_kontrakan }}" tabindex="-1" aria-labelledby="ajukanModalLabel{{ $k->id_kontrakan }}" aria-hidden="true">
                            <div class="modal-dialog">
                                <form action="{{ route('sewa.ajukan') }}" method="POST">
                                    @csrf
                                    <input type="hidden" name="id_kontrakan" value="{{ $k->id_kontrakan }}">
                                    <div class="modal-content">
                                        <div class="modal-header">
                                            <h5 class="modal-title" id="ajukanModalLabel{{ $k->id_kontrakan }}">Ajukan Sewa Kontrakan {{ $k->nomor_unit }}</h5>
                                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Tutup"></button>
                                        </div>
                                        <div class="modal-body">
                                            <div class="mb-3">
                                                <label>Tanggal Mulai</label>
                                                <input type="date" name="tgl_mulai" class="form-control" required>
                                            </div>
                                            <div class="mb-3">
                                                <label>Tanggal Selesai</label>
                                                <input type="date" name="tgl_selesai" class="form-control" required>
                                            </div>
                                        </div>
                                        <div class="modal-footer">
                                            <button type="submit" class="btn btn-success">Ajukan</button>
                                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                                        </div>
                                    </div>
                                </form>
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
                        <th>Aksi</th>
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
                                @elseif($s->status_sewa == 'menunggu')
                                    <span class="badge bg-warning text-dark">Menunggu</span>
                                @elseif($s->status_sewa == 'selesai')
                                    <span class="badge bg-secondary">Selesai</span>
                                @elseif($s->status_sewa == 'ditolak')
                                    <span class="badge bg-danger">Ditolak</span>
                                @endif
                            </td>
                            <td>
                                @if($s->status_sewa == 'menunggu')
                                    <!-- Tombol edit tanggal -->
                                    <button type="button" class="btn btn-warning btn-sm" data-bs-toggle="modal" data-bs-target="#editSewaModal{{ $s->id_sewa }}">
                                        Edit
                                    </button>

                                    <!-- Modal edit tanggal -->
                                    <div class="modal fade" id="editSewaModal{{ $s->id_sewa }}" tabindex="-1" aria-labelledby="editSewaLabel{{ $s->id_sewa }}" aria-hidden="true">
                                        <div class="modal-dialog">
                                            <form action="{{ route('sewa.updatePenyewa', $s->id_sewa) }}" method="POST">
                                                @csrf
                                                @method('PUT')
                                                <div class="modal-content">
                                                    <div class="modal-header">
                                                        <h5 class="modal-title" id="editSewaLabel{{ $s->id_sewa }}">Edit Sewa Kontrakan {{ $s->kontrakan->nomor_unit }}</h5>
                                                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Tutup"></button>
                                                    </div>
                                                    <div class="modal-body">
                                                        <div class="mb-3">
                                                            <label>Tanggal Mulai</label>
                                                            <input type="date" name="tgl_mulai" class="form-control" value="{{ $s->tgl_mulai }}" required>
                                                        </div>
                                                        <div class="mb-3">
                                                            <label>Tanggal Selesai</label>
                                                            <input type="date" name="tgl_selesai" class="form-control" value="{{ $s->tgl_selesai }}" required>
                                                        </div>
                                                    </div>
                                                    <div class="modal-footer">
                                                        <button type="submit" class="btn btn-success">Simpan</button>
                                                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                                                    </div>
                                                </div>
                                            </form>
                                        </div>
                                    </div>

                                    <!-- Tombol batal ajukan -->
                                    <button type="button" class="btn btn-danger btn-sm" data-bs-toggle="modal" data-bs-target="#batalModal{{ $s->id_sewa }}">
                                        Batal
                                    </button>

                                    <!-- Modal konfirmasi batal ajukan -->
                                    <div class="modal fade" id="batalModal{{ $s->id_sewa }}" tabindex="-1" aria-labelledby="batalModalLabel{{ $s->id_sewa }}" aria-hidden="true">
                                        <div class="modal-dialog modal-dialog-centered">
                                            <div class="modal-content">
                                                <div class="modal-header">
                                                    <h5 class="modal-title" id="batalModalLabel{{ $s->id_sewa }}">Konfirmasi Batal Sewa</h5>
                                                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Tutup"></button>
                                                </div>
                                                <div class="modal-body">
                                                    Yakin ingin membatalkan pengajuan sewa untuk kontrakan <strong>{{ $s->kontrakan->nomor_unit }}</strong>?
                                                </div>
                                                <div class="modal-footer">
                                                    <form action="{{ route('sewa.batalPenyewa', $s->id_sewa) }}" method="POST">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit" class="btn btn-danger">Ya, Batal</button>
                                                    </form>
                                                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                @else
                                    <span class="text-muted">Tidak bisa diubah</span>
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
