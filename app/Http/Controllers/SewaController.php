<?php

namespace App\Http\Controllers;

use App\Models\Sewa;
use App\Models\Penyewa;
use App\Models\Kontrakan;
use Illuminate\Http\Request;

class SewaController extends Controller
{
    public function index()
    {
        $sewa = Sewa::with(['penyewa', 'kontrakan'])->get();

        // Ambil ID penyewa & kontrakan yang masih aktif
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
            'status_sewa' => 'required|in:aktif,selesai',
        ], [
            'tgl_selesai.after_or_equal' => 'Tanggal selesai tidak boleh lebih awal dari tanggal mulai.'
        ]);

        // Cek apakah sudah ada sewa aktif lain
        $kontrakanAktif = Sewa::where('id_kontrakan', $request->id_kontrakan)
            ->where('status_sewa', 'aktif')
            ->exists();

        $penyewaAktif = Sewa::where('id_penyewa', $request->id_penyewa)
            ->where('status_sewa', 'aktif')
            ->exists();

        if ($kontrakanAktif && $request->status_sewa === 'aktif') {
            return redirect()->back()->with('error', 'Kontrakan ini sudah memiliki sewa aktif!');
        }

        if ($penyewaAktif && $request->status_sewa === 'aktif') {
            return redirect()->back()->with('error', 'Penyewa ini sudah memiliki sewa aktif!');
        }

        Sewa::create($request->all());

        if ($request->status_sewa === 'aktif') {
            Kontrakan::find($request->id_kontrakan)->update(['status' => 'terisi']);
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
            'status_sewa' => 'required|in:aktif,selesai',
        ], [
            'tgl_selesai.after_or_equal' => 'Tanggal selesai tidak boleh lebih awal dari tanggal mulai.'
        ]);

        $sewa = Sewa::findOrFail($id);

        // Validasi status aktif ganda
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
                    ->withErrors([
                        'error' => '⚠️ Tidak dapat mengubah menjadi aktif karena penyewa atau kontrakan sudah memiliki sewa aktif lain.'
                    ])
                    ->withInput()
                    ->with('edit_id', $id);
            }
        }

        $sewa->update($request->all());

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
}
