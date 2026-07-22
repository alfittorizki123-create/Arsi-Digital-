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
            'arsip_aktif' => Arsip::where('status', 'aktif')->count(),
            'arsip_inaktif' => Arsip::where('status', 'inaktif')->count(),
            'total_unit' => Unit::count(),
        ];

        $arsipTerbaru = Arsip::with(['jenisPajak', 'unit'])
            ->latest()
            ->take(5)
            ->get();

        return view('dashboard', compact('stats', 'arsipTerbaru'));
    }
}
