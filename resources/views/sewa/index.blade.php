@extends('layouts.app')

@section('content')
<div class="container">
    <h2 class="mb-4">Manajemen Sewa</h2>

    <!-- Tombol Tambah Sewa -->
    <button class="btn btn-primary mb-3" data-bs-toggle="modal" data-bs-target="#modalTambah">
        + Tambah Sewa
    </button>

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    <!-- Tabel Sewa -->
    <div class="card shadow-sm rounded">
        <div class="card-body">
            <table class="table table-bordered table-striped">
                <thead class="table-pink">
                    <tr>
                        <th>ID</th>
                        <th>Penyewa</th>
                        <th>Kontrakan</th>
                        <th>Tanggal Mulai</th>
                        <th>Tanggal Selesai</th>
                        <th>Status</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($sewa as $s)
                    <tr>
                        <td>{{ $s->id_sewa }}</td>
                        <td>{{ $s->penyewa->nama_lengkap }}</td>
                        <td>Unit {{ $s->kontrakan->nomor_unit }}</td>
                        <td>{{ $s->tgl_mulai }}</td>
                        <td>{{ $s->tgl_selesai ?? '-' }}</td>
                        <td>
                            <span class="badge {{ $s->status_sewa == 'aktif' ? 'bg-success' : 'bg-secondary' }}">
                                {{ ucfirst($s->status_sewa) }}
                            </span>
                        </td>
                        <td>
                            <!-- Tombol Edit -->
                            <button class="btn btn-warning btn-sm"
                                data-bs-toggle="modal"
                                data-bs-target="#modalEdit{{ $s->id_sewa }}">
                                Edit
                            </button>

                        <!-- Tombol Hapus -->
                            <button class="btn btn-danger btn-sm"
                                data-bs-toggle="modal"
                                data-bs-target="#modalHapus{{ $s->id_sewa }}">
                                Hapus
                            </button>
                        </td>
                    </tr>

                    <!-- Modal Edit -->
                    <div class="modal fade" id="modalEdit{{ $s->id_sewa }}" tabindex="-1">
                        <div class="modal-dialog">
                            <div class="modal-content">
                                <form action="{{ route('sewa.update', $s->id_sewa) }}" method="POST">
                                    @csrf
                                    @method('PUT')
                                    <div class="modal-header">
                                        <h5 class="modal-title">Edit Sewa</h5>
                                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                    </div>
                                    <div class="modal-body">
                                        <div class="mb-3">
                                            <label>Penyewa</label>
                                            <select name="id_penyewa" class="form-control" required>
                                                @foreach($penyewa as $p)
                                                    <option value="{{ $p->id_penyewa }}" 
                                                        {{ $s->id_penyewa == $p->id_penyewa ? 'selected' : '' }}>
                                                        {{ $p->nama_lengkap }}
                                                    </option>
                                                @endforeach
                                            </select>
                                        </div>
                                        <div class="mb-3">
                                            <label>Kontrakan</label>
                                            <select name="id_kontrakan" class="form-control" required>
                                                @foreach($kontrakan as $k)
                                                    <option value="{{ $k->id_kontrakan }}" 
                                                        {{ $s->id_kontrakan == $k->id_kontrakan ? 'selected' : '' }}>
                                                        Unit {{ $k->nomor_unit }}
                                                    </option>
                                                @endforeach
                                            </select>
                                        </div>
                                        <div class="mb-3">
                                            <label>Tanggal Mulai</label>
                                            <input type="date" name="tgl_mulai" class="form-control" value="{{ $s->tgl_mulai }}" required>
                                        </div>
                                        <div class="mb-3">
                                            <label>Tanggal Selesai</label>
                                            <input type="date" name="tgl_selesai" class="form-control" value="{{ $s->tgl_selesai }}">
                                        </div>
                                        <div class="mb-3">
                                            <label>Status</label>
                                            <select name="status_sewa" class="form-control" required>
                                                <option value="aktif" {{ $s->status_sewa == 'aktif' ? 'selected' : '' }}>Aktif</option>
                                                <option value="selesai" {{ $s->status_sewa == 'selesai' ? 'selected' : '' }}>Selesai</option>
                                            </select>
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
<div class="modal fade" id="modalHapus{{ $s->id_sewa }}" tabindex="-1">
    <div class="modal-dialog">
        <form action="{{ route('sewa.destroy', $s->id_sewa) }}" method="POST">
            @csrf
            @method('DELETE')
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Konfirmasi Hapus</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    Apakah anda yakin ingin menghapus sewa untuk 
                    <strong>{{ $s->penyewa->nama_lengkap }}</strong> 
                    di unit <strong>{{ $s->kontrakan->nomor_unit }}</strong>?
                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn btn-danger">Ya, Hapus</button>
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                </div>
            </div>
        </form>
    </div>
</div>
                <!-- Modal Hapus -->
                <div class="modal fade" id="modalHapus{{ $s->id_sewa }}" tabindex="-1">
                    <div class="modal-dialog">
                        <form action="{{ route('sewa.destroy', $s->id_sewa) }}" method="POST">
                            @csrf
                            @method('DELETE')
                            <div class="modal-content">
                                <div class="modal-header bg-danger text-white">
                                    <h5 class="modal-title">Konfirmasi Hapus</h5>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                </div>
                                <div class="modal-body">
                                    Apakah anda yakin ingin menghapus sewa untuk 
                                    <strong>{{ $s->penyewa->nama_lengkap }}</strong> 
                                    di unit <strong>{{ $s->kontrakan->nomor_unit }}</strong>?
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
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Modal Tambah -->
<div class="modal fade" id="modalTambah" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <form action="{{ route('sewa.store') }}" method="POST">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title">Tambah Sewa</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label>Penyewa</label>
                        <select name="id_penyewa" class="form-control" required>
                            @foreach($penyewa as $p)
                                <option value="{{ $p->id_penyewa }}">{{ $p->nama_lengkap }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="mb-3">
                        <label>Kontrakan</label>
                        <select name="id_kontrakan" class="form-control" required>
                            @foreach($kontrakan as $k)
                                <option value="{{ $k->id_kontrakan }}">Unit {{ $k->nomor_unit }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="mb-3">
                        <label>Tanggal Mulai</label>
                        <input type="date" name="tgl_mulai" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label>Tanggal Selesai</label>
                        <input type="date" name="tgl_selesai" class="form-control">
                    </div>
                    <div class="mb-3">
                        <label>Status</label>
                        <select name="status_sewa" class="form-control" required>
                            <option value="aktif">Aktif</option>
                            <option value="selesai">Selesai</option>
                        </select>
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
