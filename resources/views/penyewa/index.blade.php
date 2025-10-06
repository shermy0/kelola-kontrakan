@extends('layouts.app')

@section('content')
<div class="container">
    <h2 class="mb-4">Manajemen Penyewa</h2>

    <!-- Tombol Tambah Penyewa -->
    <button class="btn btn-primary mb-3" data-bs-toggle="modal" data-bs-target="#modalTambah">
        + Tambah Penyewa
    </button>

    <!-- 🔍 Form Pencarian -->
    <form method="GET" action="{{ route('penyewa.index') }}" class="d-flex mb-3">
        <input type="text" name="search" class="form-control me-2"
               placeholder="Cari nama, NIK, atau alamat..." value="{{ request('search') }}">
        <button type="submit" class="btn btn-primary">Cari</button>
    </form>

    <!-- Tabel Penyewa -->
    <div class="card shadow-sm rounded">
        <div class="card-body">
            <table class="table table-bordered table-striped">
                <thead class="table-pink">
                    <tr>
                        <th>No</th>
                        <th>ID</th>
                        <th>Nama Lengkap</th>
                        <th>No Telepon</th>
                        <th>NIK</th>
                        <th>Alamat</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($penyewa as $p)
                    <tr>
                        <td>{{ $loop->iteration + ($penyewa->currentPage() - 1) * $penyewa->perPage() }}</td>
                        <td>{{ $p->id_penyewa }}</td>
                        <td>{{ $p->nama_lengkap }}</td>
                        <td>{{ $p->no_telepon }}</td>
                        <td>{{ $p->nik }}</td>
                        <td>{{ $p->alamat }}</td>
                        <td>
                            <!-- Tombol Edit -->
                            <button class="btn btn-warning btn-sm"
                                data-bs-toggle="modal"
                                data-bs-target="#modalEdit{{ $p->id_penyewa }}">
                                Edit
                            </button>

                            <!-- Tombol Hapus -->
                            <button class="btn btn-danger btn-sm"
                                data-bs-toggle="modal"
                                data-bs-target="#modalHapus{{ $p->id_penyewa }}">
                                Hapus
                            </button>
                        </td>
                    </tr>

                    <!-- Modal Edit -->
                    <div class="modal fade" id="modalEdit{{ $p->id_penyewa }}" tabindex="-1">
                        <div class="modal-dialog">
                            <div class="modal-content">
                                <form action="{{ route('penyewa.update', $p->id_penyewa) }}" method="POST">
                                    @csrf @method('PUT')
                                    <div class="modal-header">
                                        <h5 class="modal-title">Edit Penyewa</h5>
                                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                    </div>
                                    <div class="modal-body">
                                        <div class="mb-3">
                                            <label>Nama Lengkap</label>
                                            <input type="text" name="nama_lengkap" class="form-control"
                                                value="{{ $p->nama_lengkap }}" required>
                                        </div>
                                        <div class="mb-3">
                                            <label>No Telepon</label>
                                            <input type="text" name="no_telepon" class="form-control"
                                                value="{{ $p->no_telepon }}">
                                        </div>
                                        <div class="mb-3">
                                            <label>NIK</label>
                                            <input type="text" name="nik" class="form-control"
                                                value="{{ $p->nik }}">
                                        </div>
                                        <div class="mb-3">
                                            <label>Alamat</label>
                                            <textarea name="alamat" class="form-control">{{ $p->alamat }}</textarea>
                                        </div>
                                    </div>
                                    <div class="modal-footer">
                                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                                        <button type="submit" class="btn btn-success">Simpan</button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>

                    <!-- Modal Hapus -->
                    <div class="modal fade" id="modalHapus{{ $p->id_penyewa }}" tabindex="-1">
                        <div class="modal-dialog">
                            <form action="{{ route('penyewa.destroy', $p->id_penyewa) }}" method="POST">
                                @csrf @method('DELETE')
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <h5 class="modal-title">Konfirmasi Hapus</h5>
                                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                    </div>
                                    <div class="modal-body">
                                        Apakah anda yakin ingin menghapus <strong>{{ $p->nama_lengkap }}</strong>?
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
                        <td colspan="7" class="text-center text-muted">Tidak ada data penyewa ditemukan.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>

            <!-- 🔹 Pagination -->
            <div class="mt-3">
                {{ $penyewa->links() }}
            </div>
        </div>
    </div>
</div>

<!-- Modal Tambah -->
<div class="modal fade" id="modalTambah" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <form action="{{ route('penyewa.store') }}" method="POST">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title">Tambah Penyewa</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label>Nama Lengkap</label>
                        <input type="text" name="nama_lengkap" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label>No Telepon</label>
                        <input type="text" name="no_telepon" class="form-control">
                    </div>
                    <div class="mb-3">
                        <label>NIK</label>
                        <input type="text" name="nik" class="form-control">
                    </div>
                    <div class="mb-3">
                        <label>Alamat</label>
                        <textarea name="alamat" class="form-control"></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-success">Simpan</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
