<?php

namespace App\Http\Controllers;

use App\Models\Penyewa;
use Illuminate\Http\Request;

class PenyewaController extends Controller
{
   public function index(Request $request)
{
    $query = Penyewa::query();

    // ✅ Fitur pencarian
    if ($request->has('search') && $request->search != '') {
        $search = $request->search;
        $query->where('nama_lengkap', 'like', "%{$search}%")
              ->orWhere('no_telepon', 'like', "%{$search}%")
              ->orWhere('nik', 'like', "%{$search}%")
              ->orWhere('alamat', 'like', "%{$search}%");
    }

    // ✅ Pagination (5 data per halaman)
    $penyewa = $query->paginate(5)->appends(['search' => $request->search]);

    return view('penyewa.index', compact('penyewa'));
}

    public function create()
    {
        return view('penyewa.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'nama_lengkap' => 'required|string|max:255',
            'no_telepon'   => 'nullable|string|max:20',
            'nik'          => 'nullable|string|max:20',
            'alamat'       => 'nullable|string',
        ]);

        Penyewa::create($request->all());

        return redirect()->route('penyewa.index')
            ->with('success', 'Penyewa berhasil ditambahkan!');
    }

    public function edit($id)
    {
        $penyewa = Penyewa::findOrFail($id);
        return view('penyewa.edit', compact('penyewa'));
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'nama_lengkap' => 'required|string|max:255',
            'no_telepon'   => 'nullable|string|max:20',
            'nik'          => 'nullable|string|max:20',
            'alamat'       => 'nullable|string',
        ]);

        $penyewa = Penyewa::findOrFail($id);
        $penyewa->update($request->all());

        return redirect()->route('penyewa.index')
            ->with('success', 'Data penyewa berhasil diperbarui!');
    }

    public function destroy($id)
    {
        $penyewa = Penyewa::findOrFail($id);
        $penyewa->delete();

        return redirect()->route('penyewa.index')
            ->with('success', 'Penyewa berhasil dihapus!');
    }
}
