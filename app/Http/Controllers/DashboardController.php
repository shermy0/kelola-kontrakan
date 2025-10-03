<?php

namespace App\Http\Controllers;

use App\Models\Kontrakan;
use App\Models\Penyewa;
use App\Models\Sewa;

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
}
