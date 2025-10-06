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
            'status_sewa'  => 'aktif',
        ]);

        $kontrakan = Kontrakan::find($request->id_kontrakan);
        $kontrakan->update(['status' => 'terisi']);

        return redirect()->back()->with('success', "Sewa untuk kontrakan {$kontrakan->nomor_unit} berhasil diajukan.");
    }
}
