@extends('layouts.app')

@section('content')
<div class="container">
    <h2 class="mb-4">Manajemen Sewa</h2>

    <!-- Tombol Tambah Sewa -->
    <button class="btn btn-primary mb-3" data-bs-toggle="modal" data-bs-target="#modalTambah">
        + Tambah Sewa
    </button>

    <!-- ✅ Alerts -->
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
    @if(session('error'))
        <div class="alert alert-danger">{{ session('error') }}</div>
    @endif
    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    <!-- 🔍 Form Pencarian -->
    <form method="GET" action="{{ route('sewa.index') }}" class="d-flex mb-3">
        <input type="text" name="search" class="form-control me-2" placeholder="Cari penyewa atau unit..."
               value="{{ request('search') }}">
        <button type="submit" class="btn btn-primary">Cari</button>
    </form>

    <!-- 🧾 Tabel Data -->
    <div class="card shadow-sm rounded">
        <div class="card-body">
            <table class="table table-bordered table-striped align-middle">
                <thead class="table-pink">
                    <tr>
                        <th>No</th>
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
                    @forelse($sewa as $s)
                    <tr>
                        <td>{{ $loop->iteration }}</td>
                        <td>{{ $s->id_sewa }}</td>
                        <td>{{ $s->penyewa->nama_lengkap }}</td>
                        <td>Unit {{ $s->kontrakan->nomor_unit }}</td>
                        <td>{{ $s->tgl_mulai }}</td>
                        <td>{{ $s->tgl_selesai ?? '-' }}</td>
                        <td>
                            @php
                                $statusClass = match($s->status_sewa) {
                                    'aktif' => 'bg-success',
                                    'selesai' => 'bg-secondary',
                                    'menunggu' => 'bg-warning text-dark',
                                    'ditolak' => 'bg-danger',
                                    default => 'bg-secondary'
                                };
                            @endphp
                            <span class="badge {{ $statusClass }}">
                                {{ ucfirst($s->status_sewa) }}
                            </span>
                        </td>
                        <td>
                            @if($s->status_sewa == 'menunggu')
                                <form action="{{ route('sewa.approve', $s->id_sewa) }}" method="POST" style="display:inline;">
                                    @csrf
                                    <button type="submit" class="btn btn-success btn-sm">Approve</button>
                                </form>
                                <form action="{{ route('sewa.reject', $s->id_sewa) }}" method="POST" style="display:inline;">
                                    @csrf
                                    <button type="submit" class="btn btn-danger btn-sm">Reject</button>
                                </form>
                            @else
                                <button class="btn btn-warning btn-sm" data-bs-toggle="modal" data-bs-target="#modalEdit{{ $s->id_sewa }}">Edit</button>
                                <button class="btn btn-danger btn-sm" data-bs-toggle="modal" data-bs-target="#modalHapus{{ $s->id_sewa }}">Hapus</button>
                            @endif
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
                                                    <option value="{{ $p->id_penyewa }}" {{ $s->id_penyewa == $p->id_penyewa ? 'selected' : '' }}>
                                                        {{ $p->nama_lengkap }}
                                                    </option>
                                                @endforeach
                                            </select>
                                        </div>
                                        <div class="mb-3">
                                            <label>Kontrakan</label>
                                            <select name="id_kontrakan" class="form-control" required>
                                                @foreach($kontrakan as $k)
                                                    <option value="{{ $k->id_kontrakan }}" {{ $s->id_kontrakan == $k->id_kontrakan ? 'selected' : '' }}>
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
                                            <input type="date" name="tgl_selesai" class="form-control" value="{{ $s->tgl_selesai }}" required>
                                        </div>
                                        <div class="mb-3">
                                            <label>Status</label>
                                            <select name="status_sewa" class="form-control" required>
                                                <option value="aktif" {{ $s->status_sewa == 'aktif' ? 'selected' : '' }}>Aktif</option>
                                                <option value="selesai" {{ $s->status_sewa == 'selesai' ? 'selected' : '' }}>Selesai</option>
                                                <option value="menunggu" {{ $s->status_sewa == 'menunggu' ? 'selected' : '' }}>Menunggu</option>
                                                <option value="ditolak" {{ $s->status_sewa == 'ditolak' ? 'selected' : '' }}>Ditolak</option>
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

                    @empty
                    <tr>
                        <td colspan="8" class="text-center text-muted">Tidak ada data ditemukan.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>

            <!-- 📄 Pagination -->
            <div class="d-flex justify-content-center mt-3">
                {{ $sewa->links() }}
            </div>
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
                                <option value="{{ $p->id_penyewa }}" {{ in_array($p->id_penyewa, $penyewaAktif) ? 'disabled' : '' }}>
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
                                <option value="{{ $k->id_kontrakan }}" {{ in_array($k->id_kontrakan, $kontrakanAktif) ? 'disabled' : '' }}>
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
                            <option value="menunggu">Menunggu</option>
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

<!-- ✅ Validasi JS -->
<script>
document.addEventListener('DOMContentLoaded', function() {
    const kontrakanAktif = @json($kontrakanAktif);
    const penyewaAktif = @json($penyewaAktif);

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
