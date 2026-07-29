<?php

namespace App\Http\Controllers;

use App\Models\JenisPajak;
use App\Models\Unit;
use Illuminate\Http\Request;

class PengaturanController extends Controller
{
    public function index(Request $request)
    {
        $tab = $request->get('tab', 'jenis-pajak');
        if (! in_array($tab, ['jenis-pajak', 'unit', 'sistem'], true)) {
            $tab = 'jenis-pajak';
        }

        $jenisPajaks = JenisPajak::withCount('arsips')
            ->orderBy('nama_jenis_pajak')
            ->get();

        $units = Unit::withCount('arsips')
            ->orderBy('nama_unit')
            ->get();

        return view('pengaturan.index', compact(
            'tab',
            'jenisPajaks',
            'units',
        ));
    }
}
