@extends('layouts.app')

@section('content')
<div class="container">
    <h2 class="mb-4">Manajemen Penyewa</h2>

    <!-- Tombol Tambah Penyewa -->
    <button class="btn btn-primary mb-3" data-bs-toggle="modal" data-bs-target="#modalTambah">
        + Tambah Penyewa
    </button>

    <!-- Tabel Penyewa -->
    <div class="card shadow-sm rounded">
        <div class="card-body">
            <table class="table table-bordered table-striped">
                <thead class="table-pink">
                    <tr>
                        <th>ID</th>
                        <th>Nama Lengkap</th>
                        <th>No Telepon</th>
                        <th>NIK</th>
                        <th>Alamat</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($penyewa as $p)
                    <tr>
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
                            <button class="btn btn-danger btn-sm" data-bs-toggle="modal" data-bs-target="#modalHapus{{ $p->id_penyewa }}">Hapus</button>
                            </form>
                        </td>
                    </tr>

                    <!-- Modal Edit Penyewa -->
                    <div class="modal fade" id="modalEdit{{ $p->id_penyewa }}" tabindex="-1">
                        <div class="modal-dialog">
                            <div class="modal-content">
                                <form action="{{ route('penyewa.update', $p->id_penyewa) }}" method="POST">
                                    @csrf
                                    @method('PUT')
                                    <div class="modal-header">
                                        <h5 class="modal-title">Edit Penyewa</h5>
                                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                    </div>
                                    <div class="modal-body">
                                        <div class="mb-3">
                                            <label>Nama Lengkap</label>
                                            <input type="text" name="nama_lengkap" class="form-control" value="{{ $p->nama_lengkap }}" required>
                                        </div>
                                        <div class="mb-3">
                                            <label>No Telepon</label>
                                            <input type="text" name="no_telepon" class="form-control" value="{{ $p->no_telepon }}">
                                        </div>
                                        <div class="mb-3">
                                            <label>NIK</label>
                                            <input type="text" name="nik" class="form-control" value="{{ $p->nik }}">
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
                </tbody>
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
                        Apakah anda yakin ingin menghapus penyewa <strong>{{ $p->nama_lengkap }}</strong>?
                    </div>
                    <div class="modal-footer">
                        <button type="submit" class="btn btn-danger">Ya, Hapus</button>
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    </div>
                </div>
            </form>
        </div>
    </div>
    @endforeach
            </table>
        </div>
    </div>
</div>

<!-- Modal Tambah Penyewa -->
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
