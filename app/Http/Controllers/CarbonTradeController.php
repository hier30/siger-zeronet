<?php

namespace App\Http\Controllers;

use App\Models\CarbonData;
use Illuminate\Http\Request;

class CarbonTradeController extends Controller
{
    /**
     * Display the carbon trade table page.
     */
    public function index(Request $request)
    {
        $tahun = $request->get('tahun', 2025);
        $availableYears = CarbonData::select('tahun')
            ->distinct()
            ->orderBy('tahun', 'desc')
            ->pluck('tahun');

        return view('carbon-trade', compact('tahun', 'availableYears'));
    }
}
