<?php

namespace App\Http\Controllers;

use App\Models\Arsip;
use App\Models\Unit;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index()
    {
        $arsipCounts = DB::table('arsips')
            ->selectRaw('COUNT(*) as total, SUM(CASE WHEN status = "aktif" THEN 1 ELSE 0 END) as aktif, SUM(CASE WHEN status = "inaktif" THEN 1 ELSE 0 END) as inaktif')
            ->first();

        $stats = [
            'total_arsip' => (int) ($arsipCounts->total ?? 0),
            'arsip_aktif' => (int) ($arsipCounts->aktif ?? 0),
            'arsip_inaktif' => (int) ($arsipCounts->inaktif ?? 0),
            'total_unit' => Unit::count(),
        ];

        $arsipTerbaru = Arsip::with('unit')
            ->latest()
            ->take(5)
            ->get();

        return view('dashboard', compact('stats', 'arsipTerbaru'));
    }
}