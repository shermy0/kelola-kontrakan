<?php

namespace App\Http\Controllers;

use App\Models\Sewa;
use App\Models\Penyewa;
use App\Models\Kontrakan;
use Illuminate\Http\Request;

class SewaController extends Controller
{
    public function index(Request $request)
    {
        $query = Sewa::with(['penyewa', 'kontrakan']);

        // 🔍 Pencarian
        if ($request->has('search')) {
            $search = $request->search;
            $query->whereHas('penyewa', function ($q) use ($search) {
                $q->where('nama_lengkap', 'like', "%{$search}%");
            })->orWhereHas('kontrakan', function ($q) use ($search) {
                $q->where('nomor_unit', 'like', "%{$search}%");
            });
        }

        $sewa = $query->paginate(5)->appends(['search' => $request->search]);
        $penyewaAktif = Sewa::where('status_sewa', 'aktif')->pluck('id_penyewa')->toArray();
        $kontrakanAktif = Sewa::where('status_sewa', 'aktif')->pluck('id_kontrakan')->toArray();
        $penyewa = Penyewa::all();
        $kontrakan = Kontrakan::all();

        return view('sewa.index', compact('sewa', 'penyewa', 'kontrakan', 'penyewaAktif', 'kontrakanAktif'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'id_penyewa' => 'required|exists:penyewa,id_penyewa',
            'id_kontrakan' => 'required|exists:kontrakan,id_kontrakan',
            'tgl_mulai' => 'required|date',
            'tgl_selesai' => 'required|date|after_or_equal:tgl_mulai',
            'status_sewa' => 'required|in:aktif,selesai,menunggu,ditolak',
        ], [
            'tgl_selesai.after_or_equal' => 'Tanggal selesai tidak boleh lebih awal dari tanggal mulai.'
        ]);

        // Cek sewa aktif ganda
        if ($request->status_sewa === 'aktif') {
            $kontrakanAktif = Sewa::where('id_kontrakan', $request->id_kontrakan)
                ->where('status_sewa', 'aktif')
                ->exists();

            $penyewaAktif = Sewa::where('id_penyewa', $request->id_penyewa)
                ->where('status_sewa', 'aktif')
                ->exists();

            if ($kontrakanAktif) {
                return redirect()->back()->with('error', 'Kontrakan ini sudah memiliki sewa aktif!');
            }

            if ($penyewaAktif) {
                return redirect()->back()->with('error', 'Penyewa ini sudah memiliki sewa aktif!');
            }
        }

        $sewa = Sewa::create($request->all());

        // 🔄 Update status kontrakan otomatis
        $kontrakan = Kontrakan::find($request->id_kontrakan);
        if ($request->status_sewa === 'aktif') {
            $kontrakan->update(['status' => 'terisi']);
        } else {
            $kontrakan->update(['status' => 'kosong']);
        }

        return redirect()->route('sewa.index')->with('success', 'Data sewa berhasil ditambahkan.');
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'id_penyewa' => 'required|exists:penyewa,id_penyewa',
            'id_kontrakan' => 'required|exists:kontrakan,id_kontrakan',
            'tgl_mulai' => 'required|date',
            'tgl_selesai' => 'required|date|after_or_equal:tgl_mulai',
            'status_sewa' => 'required|in:aktif,selesai,menunggu,ditolak',
        ], [
            'tgl_selesai.after_or_equal' => 'Tanggal selesai tidak boleh lebih awal dari tanggal mulai.'
        ]);

        $sewa = Sewa::findOrFail($id);

        // Cek jika ingin ubah ke aktif
        if ($request->status_sewa === 'aktif') {
            $cekKontrakan = Sewa::where('id_kontrakan', $request->id_kontrakan)
                ->where('status_sewa', 'aktif')
                ->where('id_sewa', '!=', $id)
                ->exists();

            $cekPenyewa = Sewa::where('id_penyewa', $request->id_penyewa)
                ->where('status_sewa', 'aktif')
                ->where('id_sewa', '!=', $id)
                ->exists();

            if ($cekKontrakan || $cekPenyewa) {
                return redirect()
                    ->back()
                    ->withErrors(['error' => '⚠️ Tidak dapat mengubah menjadi aktif karena penyewa atau kontrakan sudah memiliki sewa aktif lain.'])
                    ->withInput()
                    ->with('edit_id', $id);
            }
        }

        $sewa->update($request->all());

        // 🔄 Update status kontrakan otomatis
        $kontrakan = Kontrakan::find($request->id_kontrakan);
        if ($request->status_sewa === 'aktif') {
            $kontrakan->update(['status' => 'terisi']);
        } else {
            $kontrakan->update(['status' => 'kosong']); // termasuk ditolak, selesai, menunggu
        }

        return redirect()->route('sewa.index')->with('success', 'Data sewa berhasil diperbarui.');
    }

    public function destroy($id)
    {
        $sewa = Sewa::findOrFail($id);
        $kontrakan = $sewa->kontrakan;

        if ($sewa->status_sewa === 'aktif') {
            $kontrakan->update(['status' => 'kosong']);
        }

        $sewa->delete();

        return redirect()->route('sewa.index')->with('success', 'Data sewa berhasil dihapus.');
    }

    public function approve($id)
    {
        $sewa = Sewa::findOrFail($id);

        // Cek sewa aktif ganda
        if (Sewa::where('id_kontrakan', $sewa->id_kontrakan)->where('status_sewa', 'aktif')->exists() ||
            Sewa::where('id_penyewa', $sewa->id_penyewa)->where('status_sewa', 'aktif')->exists()) {
            return redirect()->back()->with('error', 'Kontrakan atau Penyewa sudah memiliki sewa aktif!');
        }

        $sewa->status_sewa = 'aktif';
        $sewa->save();

        // Update status kontrakan
        $kontrakan = $sewa->kontrakan;
        $kontrakan->update(['status' => 'terisi']);

        return redirect()->back()->with('success', 'Sewa berhasil di-approve.');
    }

    public function reject($id)
    {
        $sewa = Sewa::findOrFail($id);
        $sewa->status_sewa = 'ditolak';
        $sewa->save();

        // Update status kontrakan
        $kontrakan = $sewa->kontrakan;
        $kontrakan->update(['status' => 'kosong']);

        return redirect()->back()->with('success', 'Sewa berhasil di-reject.');
    }
}
