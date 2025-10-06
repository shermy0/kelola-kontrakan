<?php

namespace App\Http\Controllers;

use App\Models\Kontrakan;
use App\Models\Sewa;
use App\Models\Penyewa;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function admin()
    {
        $totalPenyewa = Penyewa::count();
        $totalKontrakan = Kontrakan::count();
        $totalSewaAktif = Sewa::where('status_sewa', 'aktif')->count();
        $totalSewaSelesai = Sewa::where('status_sewa', 'selesai')->count();
        $recentSewa = Sewa::with(['penyewa', 'kontrakan'])
                        ->latest()->take(5)->get();

        return view('dashboard_admin', compact(
            'totalPenyewa', 
            'totalKontrakan', 
            'totalSewaAktif', 
            'totalSewaSelesai', 
            'recentSewa'
        ));
    }

    public function pengelola()
    {
        return view('dashboard_pengelola');
    }

        // 🟢 Dashboard untuk penyewa
public function penyewa()
{
    $userId = auth()->id();

    // Ambil keyword search
    $search = request('search');

    // Query kontrakan kosong, dengan filter search
    $kontrakanKosong = Kontrakan::where('status', 'kosong')
        ->when($search, function($query, $search) {
            $query->where('nomor_unit', 'like', "%{$search}%")
                  ->orWhere('keterangan', 'like', "%{$search}%");
        })
        ->paginate(5)
        ->withQueryString(); // Supaya pagination tetap membawa query search

    // Data sewa penyewa yang login
    $penyewa = Penyewa::where('id_penyewa', $userId)->first();
    $sewaSaya = Sewa::with('kontrakan')
        ->where('id_penyewa', $penyewa->id_penyewa)
        ->orderBy('tgl_mulai', 'desc')
        ->get();

    return view('dashboard_penyewa', compact('kontrakanKosong', 'sewaSaya'));
}


}
