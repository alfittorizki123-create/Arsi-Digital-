<?php

namespace App\Http\Controllers;

use App\Models\Arsip;
use App\Models\Unit;

class DashboardController extends Controller
{
    public function index()
    {
        $stats = [
            'total_arsip' => Arsip::count(),
            'total_berkas' => (int) Arsip::sum('jumlah'),
            'arsip_aktif' => Arsip::where('status', 'aktif')->count(),
            'arsip_inaktif' => Arsip::where('status', 'inaktif')->count(),
            'total_unit' => Unit::count(),
        ];

        $arsipTerbaru = Arsip::with(['unit'])
            ->latest()
            ->take(5)
            ->get();

        return view('dashboard', compact('stats', 'arsipTerbaru'));
    }
}