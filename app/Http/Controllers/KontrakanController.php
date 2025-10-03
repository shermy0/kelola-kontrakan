<?php

namespace App\Http\Controllers;

use App\Models\Kontrakan;
use Illuminate\Http\Request;

class KontrakanController extends Controller
{
    public function index()
    {
        $kontrakan = Kontrakan::all();
        return view('kontrakan.index', compact('kontrakan'));
    }

    public function create()
    {
        return view('kontrakan.create');
    }

    public function store(Request $request)
    {
        // Ambil nomor unit terakhir
        $lastUnit = Kontrakan::orderBy('id_kontrakan', 'desc')->first();
        $nextNumber = $lastUnit ? ((int) filter_var($lastUnit->nomor_unit, FILTER_SANITIZE_NUMBER_INT)) + 1 : 1;

        // Format nomor unit otomatis misal: U001
        $autoNomorUnit = 'U' . str_pad($nextNumber, 3, '0', STR_PAD_LEFT);

        // Default harga
        $defaultHarga = 1500000;

        // Nomor unit tetap bisa dioverride dari input user
        $request->merge([
            'nomor_unit' => $request->nomor_unit ?: $autoNomorUnit,
            'harga_sewa' => $request->harga_sewa ?: $defaultHarga,
        ]);

        $request->validate([
            'nomor_unit' => 'required|unique:kontrakan',
            'harga_sewa' => 'required|numeric',
        ]);

        Kontrakan::create($request->all());

        return redirect()->route('kontrakan.index')->with('success', 'Data kontrakan berhasil ditambahkan');
    }

    public function edit($id)
    {
        $kontrakan = Kontrakan::findOrFail($id);
        return view('kontrakan.edit', compact('kontrakan'));
    }

    public function update(Request $request, $id)
    {
        $kontrakan = Kontrakan::findOrFail($id);
        $kontrakan->update($request->all());
        return redirect()->route('kontrakan.index')->with('success', 'Data kontrakan berhasil diperbarui');
    }

    public function destroy($id)
    {
        $kontrakan = Kontrakan::findOrFail($id);
        $kontrakan->delete();
        return redirect()->route('kontrakan.index')->with('success', 'Data kontrakan berhasil dihapus');
    }
}
