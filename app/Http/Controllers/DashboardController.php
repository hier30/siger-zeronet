<?php

namespace App\Http\Controllers;

use App\Models\CarbonData;
use App\Models\Kecamatan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    /**
     * Display the dashboard page.
     */
    public function index(Request $request)
    {
        $tahun = $request->get('tahun', 2025);
        $availableYears = CarbonData::select('tahun')
            ->distinct()
            ->orderBy('tahun', 'desc')
            ->pluck('tahun');

        // KPI aggregates
        $kpi = CarbonData::where('tahun', $tahun)
            ->selectRaw('
                COALESCE(SUM(emisi_co2), 0) as total_emisi,
                COALESCE(SUM(absorpsi_co2), 0) as total_absorpsi,
                COALESCE(SUM(emisi_co2), 0) - COALESCE(SUM(absorpsi_co2), 0) as selisih,
                GREATEST(COALESCE(SUM(emisi_co2), 0) - COALESCE(SUM(absorpsi_co2), 0), 0) as kredit_karbon
            ')
            ->first();

        // Per-kecamatan data for charts
        $chartData = CarbonData::where('carbon_data.tahun', $tahun)
            ->join('kecamatan', 'kecamatan.id', '=', 'carbon_data.kecamatan_id')
            ->select(
                'kecamatan.kecamatan as nama_kecamatan',
                'carbon_data.emisi_co2',
                'carbon_data.absorpsi_co2',
                DB::raw('(carbon_data.emisi_co2 - carbon_data.absorpsi_co2) as net_carbon')
            )
            ->orderBy('kecamatan.kecamatan')
            ->get();

        return view('dashboard', compact('kpi', 'chartData', 'tahun', 'availableYears'));
    }
}
