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
        $penyewa = Penyewa::all();
        $kontrakan = Kontrakan::all();

        return view('sewa.index', compact('sewa', 'penyewa', 'kontrakan'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'id_penyewa' => 'required|exists:penyewa,id_penyewa',
            'id_kontrakan' => 'required|exists:kontrakan,id_kontrakan',
            'tgl_mulai' => 'required|date',
            'tgl_selesai' => 'nullable|date|after_or_equal:tgl_mulai',
            'status_sewa' => 'required|in:aktif,selesai',
        ]);

        // Cek apakah kontrakan sudah ada sewa aktif
        $kontrakanAktif = \App\Models\Sewa::where('id_kontrakan', $request->id_kontrakan)
                            ->where('status_sewa', 'aktif')
                            ->exists();

        if ($kontrakanAktif) {
            return redirect()->back()->with('error', 'Kontrakan ini sudah terisi!');
        }

        // Kalau lolos, buat sewa
        $sewa = Sewa::create($request->all());

        // Update status kontrakan ke "terisi"
        $kontrakan = \App\Models\Kontrakan::find($request->id_kontrakan);
        $kontrakan->update(['status' => 'terisi']);

        return redirect()->route('sewa.index')->with('success', 'Data sewa berhasil ditambahkan');
    }


    public function update(Request $request, $id)
    {
        $request->validate([
            'id_penyewa' => 'required|exists:penyewa,id_penyewa',
            'id_kontrakan' => 'required|exists:kontrakan,id_kontrakan',
            'tgl_mulai' => 'required|date',
            'tgl_selesai' => 'nullable|date|after_or_equal:tgl_mulai',
            'status_sewa' => 'required|in:aktif,selesai',
        ]);

        $sewa = Sewa::findOrFail($id);
        $sewa->update($request->all());

        // Kalau sewa selesai, kontrakan jadi kosong
        if ($request->status_sewa === 'selesai') {
            $sewa->kontrakan->update(['status' => 'kosong']);
        }

        return redirect()->route('sewa.index')->with('success', 'Data sewa berhasil diperbarui');
    }

    public function destroy($id)
    {
        $sewa = Sewa::findOrFail($id);
        $sewa->delete();

        return redirect()->route('sewa.index')->with('success', 'Data sewa berhasil dihapus');
    }
}
