<?php

namespace App\Http\Controllers;

use App\Models\Sewa;
use App\Models\Kontrakan;
use App\Models\Penyewa;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AjukanSewaController extends Controller  // <-- pastikan extend Controller
{
    public function __construct()
    {
        $this->middleware(['auth', 'role:penyewa']);
    }

    public function store(Request $request)
    {
        $penyewa = Penyewa::where('id_penyewa', Auth::id())->first();
        if (!$penyewa) {
            return redirect()->back()->with('error', 'Data penyewa tidak ditemukan.');
        }

        $request->validate([
            'id_kontrakan' => 'required|exists:kontrakan,id_kontrakan',
        ]);

        $kontrakanAktif = Sewa::where('id_kontrakan', $request->id_kontrakan)
            ->where('status_sewa', 'aktif')
            ->exists();

        $penyewaAktif = Sewa::where('id_penyewa', $penyewa->id_penyewa)
            ->where('status_sewa', 'aktif')
            ->exists();

        if ($kontrakanAktif) {
            return redirect()->back()->with('error', 'Kontrakan ini sudah memiliki sewa aktif.');
        }

        if ($penyewaAktif) {
            return redirect()->back()->with('error', 'Anda sudah memiliki sewa aktif.');
        }

        $sewa = Sewa::create([
            'id_penyewa'   => $penyewa->id_penyewa,
            'id_kontrakan' => $request->id_kontrakan,
            'tgl_mulai'    => now()->format('Y-m-d'),
            'tgl_selesai'  => now()->addMonth()->format('Y-m-d'),
            'status_sewa'  => 'menunggu',
        ]);

        $kontrakan = Kontrakan::find($request->id_kontrakan);
        $kontrakan->update(['status' => 'terisi']);

        return redirect()->back()->with('success', "Sewa untuk kontrakan {$kontrakan->nomor_unit} berhasil diajukan.");
    }

    public function ajukan(Request $request)
{
    $request->validate([
        'id_kontrakan' => 'required|exists:kontrakan,id_kontrakan',
        'tgl_mulai' => 'required|date',
        'tgl_selesai' => 'required|date|after_or_equal:tgl_mulai',
    ], [
        'tgl_selesai.after_or_equal' => 'Tanggal selesai tidak boleh lebih awal dari tanggal mulai.'
    ]);

    $userId = auth()->id();

    $penyewa = Penyewa::where('id_penyewa', $userId)->first();

    // Pastikan kontrakan masih kosong
    $kontrakan = Kontrakan::findOrFail($request->id_kontrakan);
    if ($kontrakan->status != 'kosong') {
        return redirect()->back()->with('error', 'Kontrakan sudah tidak tersedia.');
    }

    Sewa::create([
        'id_penyewa' => $penyewa->id_penyewa,
        'id_kontrakan' => $request->id_kontrakan,
        'tgl_mulai' => $request->tgl_mulai,
        'tgl_selesai' => $request->tgl_selesai,
        'status_sewa' => 'menunggu', // otomatis menunggu approval
    ]);

    return redirect()->back()->with('success', 'Sewa berhasil diajukan, menunggu persetujuan.');
}

// Edit tanggal sewa (oleh penyewa)
public function updatePenyewa(Request $request, $id)
{
    $sewa = Sewa::where('id_sewa', $id)
                 ->where('id_penyewa', auth()->id())
                 ->firstOrFail();

    if($sewa->status_sewa != 'menunggu') {
        return redirect()->back()->with('error', 'Tidak dapat mengubah sewa yang sudah disetujui atau selesai.');
    }

    $request->validate([
        'tgl_mulai' => 'required|date',
        'tgl_selesai' => 'required|date|after_or_equal:tgl_mulai',
    ], [
        'tgl_selesai.after_or_equal' => 'Tanggal selesai tidak boleh lebih awal dari tanggal mulai.'
    ]);

    $sewa->update([
        'tgl_mulai' => $request->tgl_mulai,
        'tgl_selesai' => $request->tgl_selesai,
    ]);

    return redirect()->back()->with('success', 'Tanggal sewa berhasil diperbarui.');
}

// Batalkan ajukan sewa (oleh penyewa)
public function batalPenyewa($id)
{
    $sewa = Sewa::where('id_sewa', $id)
                 ->where('id_penyewa', auth()->id())
                 ->firstOrFail();

    if($sewa->status_sewa != 'menunggu') {
        return redirect()->back()->with('error', 'Hanya sewa menunggu yang bisa dibatalkan.');
    }

    // Update kontrakan menjadi kosong lagi
    $kontrakan = $sewa->kontrakan;
    $kontrakan->update(['status' => 'kosong']);

    $sewa->delete();

    return redirect()->back()->with('success', 'Pengajuan sewa berhasil dibatalkan.');
}


}
