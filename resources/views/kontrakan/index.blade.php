@extends('layouts.app')

@section('content')
<div class="container">
    <h2 class="mb-4">Data Kontrakan</h2>

    <!-- Tombol Tambah -->
    <button class="btn btn-primary mb-3" data-bs-toggle="modal" data-bs-target="#modalTambah">
        + Tambah Kontrakan
    </button>

    <!-- 🔍 Form Pencarian -->
    <form method="GET" action="{{ route('kontrakan.index') }}" class="d-flex mb-3">
        <input type="text" name="search" class="form-control me-2"
               placeholder="Cari nomor unit, status, atau keterangan..." value="{{ request('search') }}">
        <button type="submit" class="btn btn-primary">Cari</button>
    </form>

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    <div class="card shadow-sm rounded">
        <div class="card-body">
            <table class="table table-bordered table-striped">
                <thead class="table-pink">
                    <tr>
                        <th>No</th>
                        <th>ID</th>
                        <th>Nomor Unit</th>
                        <th>Harga Sewa</th>
                        <th>Status</th>
                        <th>Keterangan</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($kontrakan as $k)
                    <tr>
                        <td>{{ $loop->iteration + ($kontrakan->currentPage() - 1) * $kontrakan->perPage() }}</td>
                        <td>{{ $k->id_kontrakan }}</td>
                        <td>{{ $k->nomor_unit }}</td>
                        <td>Rp {{ number_format($k->harga_sewa, 0, ',', '.') }}</td>
                        <td>
                            <span class="badge {{ $k->status == 'terisi' ? 'bg-success' : 'bg-secondary' }}">
                                {{ ucfirst($k->status) }}
                            </span>
                        </td>
                        <td>{{ $k->keterangan }}</td>
                        <td>
                            <!-- Tombol Edit -->
                            <button class="btn btn-warning btn-sm" data-bs-toggle="modal" data-bs-target="#modalEdit{{ $k->id_kontrakan }}">Edit</button>

                            <!-- Tombol Hapus -->
                            <button class="btn btn-danger btn-sm" data-bs-toggle="modal" data-bs-target="#modalHapus{{ $k->id_kontrakan }}">Hapus</button>
                        </td>
                    </tr>

                    <!-- Modal Edit -->
                    <div class="modal fade" id="modalEdit{{ $k->id_kontrakan }}" tabindex="-1">
                        <div class="modal-dialog">
                            <form action="{{ route('kontrakan.update', $k->id_kontrakan) }}" method="POST">
                                @csrf @method('PUT')
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <h5 class="modal-title">Edit Kontrakan</h5>
                                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                    </div>
                                    <div class="modal-body">
                                        <div class="mb-3">
                                            <label>Nomor Unit</label>
                                            <input type="text" name="nomor_unit" class="form-control" value="{{ $k->nomor_unit }}" required>
                                        </div>
                                        <div class="mb-3">
                                            <label>Harga Sewa</label>
                                            <input type="number" name="harga_sewa" class="form-control" value="{{ $k->harga_sewa }}" required>
                                        </div>
                                        <div class="mb-3">
                                            <label>Status</label>
                                            <select name="status" class="form-control" required>
                                                <option value="kosong" {{ $k->status=='kosong'?'selected':'' }}>Kosong</option>
                                                <option value="terisi" {{ $k->status=='terisi'?'selected':'' }}>Terisi</option>
                                            </select>
                                        </div>
                                        <div class="mb-3">
                                            <label>Keterangan</label>
                                            <textarea name="keterangan" class="form-control">{{ $k->keterangan }}</textarea>
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

                    <!-- Modal Hapus -->
                    <div class="modal fade" id="modalHapus{{ $k->id_kontrakan }}" tabindex="-1">
                        <div class="modal-dialog">
                            <form action="{{ route('kontrakan.destroy', $k->id_kontrakan) }}" method="POST">
                                @csrf @method('DELETE')
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <h5 class="modal-title">Konfirmasi Hapus</h5>
                                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                    </div>
                                    <div class="modal-body">
                                        Apakah anda yakin ingin menghapus kontrakan <strong>{{ $k->nomor_unit }}</strong>?
                                    </div>
                                    <div class="modal-footer">
                                        <button type="submit" class="btn btn-danger">Ya, Hapus</button>
                                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                    @empty
                    <tr>
                        <td colspan="7" class="text-center text-muted">Tidak ada data kontrakan ditemukan.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>

            <!-- 🔹 Pagination -->
            <div class="mt-3">
                {{ $kontrakan->links() }}
            </div>
        </div>
    </div>
</div>

<!-- Modal Tambah -->
<div class="modal fade" id="modalTambah" tabindex="-1">
    <div class="modal-dialog">
        <form action="{{ route('kontrakan.store') }}" method="POST">
            @csrf
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Tambah Kontrakan</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label>Nomor Unit</label>
                        <input type="text" name="nomor_unit" class="form-control"
                               value="{{ 'U' . str_pad(($kontrakan->max('id_kontrakan') ?? 0) + 1, 3, '0', STR_PAD_LEFT) }}">
                    </div>
                    <div class="mb-3">
                        <label>Harga Sewa</label>
                        <input type="number" name="harga_sewa" class="form-control" value="1500000">
                    </div>
                    <div class="mb-3">
                        <label>Status</label>
                        <select name="status" class="form-control" required>
                            <option value="kosong">Kosong</option>
                            <option value="terisi">Terisi</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label>Keterangan</label>
                        <textarea name="keterangan" class="form-control"></textarea>
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
@endsection
