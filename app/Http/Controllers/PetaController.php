<?php

namespace App\Http\Controllers;

use App\Models\CarbonData;
use App\Models\Kecamatan;
use Illuminate\Http\Request;

class PetaController extends Controller
{
    /**
     * Display the emission map page.
     */
    public function emisi(Request $request)
    {
        $tahun = $request->get('tahun', 2025);
        $availableYears = CarbonData::select('tahun')
            ->distinct()
            ->orderBy('tahun', 'desc')
            ->pluck('tahun');

        return view('peta-emisi', compact('tahun', 'availableYears'));
    }

    /**
     * Display the absorption map page.
     */
    public function absorpsi(Request $request)
    {
        $tahun = $request->get('tahun', 2025);
        $availableYears = CarbonData::select('tahun')
            ->distinct()
            ->orderBy('tahun', 'desc')
            ->pluck('tahun');

        return view('peta-absorpsi', compact('tahun', 'availableYears'));
    }
}
