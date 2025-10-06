@extends('layouts.app')

@section('content')
<div class="container">
    <h2 class="mb-4">Manajemen Sewa</h2>

    <!-- Tombol Tambah Sewa -->
    <button class="btn btn-primary mb-3" data-bs-toggle="modal" data-bs-target="#modalTambah">
        + Tambah Sewa
    </button>

    <!-- ✅ Validasi dari Controller -->
    @if($errors->any())
        <div class="alert alert-danger">
            <strong>Terjadi kesalahan:</strong>
            <ul class="mb-0">
                @foreach($errors->all() as $err)
                    <li>{{ $err }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    <!-- Tabel Sewa -->
    <div class="card shadow-sm rounded">
        <div class="card-body">
            <table class="table table-bordered table-striped align-middle">
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
                            <button class="btn btn-warning btn-sm"
                                data-bs-toggle="modal"
                                data-bs-target="#modalEdit{{ $s->id_sewa }}">
                                Edit
                            </button>
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
                                <form action="{{ route('sewa.update', $s->id_sewa) }}" method="POST" class="form-edit">
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
                                            @error('tgl_mulai')
                                                <div class="text-danger small mt-1">{{ $message }}</div>
                                            @enderror
                                        </div>

                                        <div class="mb-3">
                                            <label>Tanggal Selesai</label>
                                            <input type="date" name="tgl_selesai" class="form-control" value="{{ $s->tgl_selesai }}" required>
                                            @error('tgl_selesai')
                                                <div class="text-danger small mt-1">{{ $message }}</div>
                                            @enderror
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
            <form action="{{ route('sewa.store') }}" method="POST" class="form-tambah">
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
                                <option value="{{ $p->id_penyewa }}" 
                                    {{ in_array($p->id_penyewa, $penyewaAktif) ? 'disabled' : '' }}>
                                    {{ $p->nama_lengkap }}
                                    {{ in_array($p->id_penyewa, $penyewaAktif) ? '(Sedang aktif)' : '' }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="mb-3">
                        <label>Kontrakan</label>
                        <select name="id_kontrakan" class="form-control" required>
                            @foreach($kontrakan as $k)
                                <option value="{{ $k->id_kontrakan }}" 
                                    {{ in_array($k->id_kontrakan, $kontrakanAktif) ? 'disabled' : '' }}>
                                    Unit {{ $k->nomor_unit }}
                                    {{ in_array($k->id_kontrakan, $kontrakanAktif) ? '(Sudah disewa)' : '' }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="mb-3">
                        <label>Tanggal Mulai</label>
                        <input type="date" name="tgl_mulai" class="form-control" required>
                    </div>

                    <div class="mb-3">
                        <label>Tanggal Selesai</label>
                        <input type="date" name="tgl_selesai" class="form-control" required>
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

<!-- ✅ VALIDASI JAVASCRIPT -->
<script>
document.addEventListener('DOMContentLoaded', function() {
    const kontrakanAktif = @json($kontrakanAktif);
    const penyewaAktif = @json($penyewaAktif);

    // Validasi form Edit & Tambah
    document.querySelectorAll('.form-edit, .form-tambah').forEach(form => {
        form.addEventListener('submit', function(e) {
            let valid = true;
            let messages = [];

            const mulai = form.querySelector('input[name="tgl_mulai"]').value;
            const selesai = form.querySelector('input[name="tgl_selesai"]').value;
            const idPenyewa = form.querySelector('select[name="id_penyewa"]').value;
            const idKontrakan = form.querySelector('select[name="id_kontrakan"]').value;
            const status = form.querySelector('select[name="status_sewa"]').value;

            form.querySelectorAll('.alert-error').forEach(a => a.remove());

            if (selesai && mulai && new Date(selesai) < new Date(mulai)) {
                valid = false;
                messages.push('⚠️ Tanggal selesai tidak boleh lebih awal dari tanggal mulai.');
            }

            if (status === 'aktif') {
                if (kontrakanAktif.includes(parseInt(idKontrakan))) {
                    valid = false;
                    messages.push('🏠 Kontrakan ini sudah memiliki sewa aktif lain!');
                }
                if (penyewaAktif.includes(parseInt(idPenyewa))) {
                    valid = false;
                    messages.push('👤 Penyewa ini sudah memiliki sewa aktif lain!');
                }
            }

            if (!valid) {
                e.preventDefault();
                const alertBox = document.createElement('div');
                alertBox.className = 'alert alert-danger alert-error mt-2';
                alertBox.innerHTML = messages.join('<br>');
                form.querySelector('.modal-body').prepend(alertBox);
            }
        });
    });
});
</script>
@endsection
