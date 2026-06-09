<?php

namespace App\Http\Controllers;

use App\Models\CarbonData;
use App\Models\Kecamatan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ApiController extends Controller
{
    /**
     * GET /api/dashboard
     * Returns KPI aggregates and chart data.
     */
    public function dashboard(Request $request)
    {
        $tahun = $request->get('tahun', 2025);

        $kpi = CarbonData::where('tahun', $tahun)
            ->selectRaw('
                COALESCE(SUM(emisi_co2), 0) as total_emisi,
                COALESCE(SUM(absorpsi_co2), 0) as total_absorpsi,
                COALESCE(SUM(emisi_co2), 0) - COALESCE(SUM(absorpsi_co2), 0) as selisih,
                GREATEST(COALESCE(SUM(emisi_co2), 0) - COALESCE(SUM(absorpsi_co2), 0), 0) as kredit_karbon
            ')
            ->first();

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

        $availableYears = CarbonData::select('tahun')
            ->distinct()
            ->orderBy('tahun', 'desc')
            ->pluck('tahun');

        return response()->json([
            'tahun' => (int) $tahun,
            'kpi' => $kpi,
            'chart_data' => $chartData,
            'available_years' => $availableYears,
        ]);
    }

    /**
     * GET /api/peta-emisi
     * Returns GeoJSON FeatureCollection with emission data.
     */
    public function petaEmisi(Request $request)
    {
        $tahun = $request->get('tahun', 2025);

        $list_kecamatan = Kecamatan::select('id', 'kecamatan as nama_kecamatan', \Illuminate\Support\Facades\DB::raw('ST_AsGeoJSON(geom) as geojson'))
            ->with(['carbonData' => function ($query) use ($tahun) {
            $query->where('tahun', $tahun);
        }])->get();

        $features = [];

        foreach ($list_kecamatan as $kecamatan) {
            $carbon = $kecamatan->carbonData->first();
            $geojson = is_string($kecamatan->geojson) ? json_decode($kecamatan->geojson, true) : $kecamatan->geojson;

            if (!$geojson) continue;

            // Handle geojson whether it's a full Feature/FeatureCollection or just geometry
            $geometry = $geojson;
            if (isset($geojson['type']) && $geojson['type'] === 'Feature') {
                $geometry = $geojson['geometry'];
            } elseif (isset($geojson['type']) && $geojson['type'] === 'FeatureCollection') {
                $geometry = $geojson['features'][0]['geometry'] ?? null;
            }

            if (!$geometry) continue;

            $emisi = $carbon ? (float) $carbon->emisi_co2 : 0;
            $absorpsi = $carbon ? (float) $carbon->absorpsi_co2 : 0;
            $net = $emisi - $absorpsi;

            $features[] = [
                'type' => 'Feature',
                'properties' => [
                    'id' => $kecamatan->id,
                    'nama_kecamatan' => $kecamatan->nama_kecamatan,
                    'emisi_co2' => $emisi,
                    'absorpsi_co2' => $absorpsi,
                    'net_carbon' => round($net, 2),
                    'kredit_karbon' => $net > 0 ? round($net, 2) : 0,
                    'status' => $net > 0 ? 'Defisit Karbon' : ($net < 0 ? 'Surplus Karbon' : 'Seimbang'),
                ],
                'geometry' => $geometry,
            ];
        }

        return response()->json([
            'type' => 'FeatureCollection',
            'features' => $features,
        ]);
    }

    /**
     * GET /api/peta-absorpsi
     * Returns GeoJSON FeatureCollection with absorption data.
     */
    public function petaAbsorpsi(Request $request)
    {
        $tahun = $request->get('tahun', 2025);

        $list_kecamatan = Kecamatan::select('id', 'kecamatan as nama_kecamatan', \Illuminate\Support\Facades\DB::raw('ST_AsGeoJSON(geom) as geojson'))
            ->with(['carbonData' => function ($query) use ($tahun) {
            $query->where('tahun', $tahun);
        }])->get();

        $features = [];

        foreach ($list_kecamatan as $kecamatan) {
            $carbon = $kecamatan->carbonData->first();
            $geojson = is_string($kecamatan->geojson) ? json_decode($kecamatan->geojson, true) : $kecamatan->geojson;

            if (!$geojson) continue;

            $geometry = $geojson;
            if (isset($geojson['type']) && $geojson['type'] === 'Feature') {
                $geometry = $geojson['geometry'];
            } elseif (isset($geojson['type']) && $geojson['type'] === 'FeatureCollection') {
                $geometry = $geojson['features'][0]['geometry'] ?? null;
            }

            if (!$geometry) continue;

            $emisi = $carbon ? (float) $carbon->emisi_co2 : 0;
            $absorpsi = $carbon ? (float) $carbon->absorpsi_co2 : 0;
            $net = $emisi - $absorpsi;

            $features[] = [
                'type' => 'Feature',
                'properties' => [
                    'id' => $kecamatan->id,
                    'nama_kecamatan' => $kecamatan->nama_kecamatan,
                    'emisi_co2' => $emisi,
                    'absorpsi_co2' => $absorpsi,
                    'net_carbon' => round($net, 2),
                    'kredit_karbon' => $net > 0 ? round($net, 2) : 0,
                    'status' => $net > 0 ? 'Defisit Karbon' : ($net < 0 ? 'Surplus Karbon' : 'Seimbang'),
                ],
                'geometry' => $geometry,
            ];
        }

        return response()->json([
            'type' => 'FeatureCollection',
            'features' => $features,
        ]);
    }

    /**
     * GET /api/carbon-trade
     * Returns paginated carbon trade data with search and filter.
     */
    public function carbonTrade(Request $request)
    {
        $tahun = $request->get('tahun', 2025);
        $search = $request->get('search', '');
        $sortBy = $request->get('sort_by', 'nama_kecamatan');
        $sortDir = $request->get('sort_dir', 'asc');
        $perPage = $request->get('per_page', 20);

        $allowedSorts = ['nama_kecamatan', 'emisi_co2', 'absorpsi_co2', 'net_carbon'];
        if (!in_array($sortBy, $allowedSorts)) {
            $sortBy = 'nama_kecamatan';
        }
        $sortDir = in_array($sortDir, ['asc', 'desc']) ? $sortDir : 'asc';

        $query = CarbonData::where('carbon_data.tahun', $tahun)
            ->join('kecamatan', 'kecamatan.id', '=', 'carbon_data.kecamatan_id')
            ->select(
                'kecamatan.kecamatan as nama_kecamatan',
                'carbon_data.emisi_co2',
                'carbon_data.absorpsi_co2',
                DB::raw('(carbon_data.emisi_co2 - carbon_data.absorpsi_co2) as net_carbon'),
                DB::raw('GREATEST(carbon_data.emisi_co2 - carbon_data.absorpsi_co2, 0) as kredit_karbon'),
                DB::raw("CASE
                    WHEN (carbon_data.emisi_co2 - carbon_data.absorpsi_co2) > 0 THEN 'Defisit Karbon'
                    WHEN (carbon_data.emisi_co2 - carbon_data.absorpsi_co2) < 0 THEN 'Surplus Karbon'
                    ELSE 'Seimbang'
                END as status")
            );

        if ($search) {
            $query->where('kecamatan.kecamatan', 'ILIKE', "%{$search}%");
        }

        $query->orderBy($sortBy === 'nama_kecamatan' ? 'kecamatan.kecamatan' : $sortBy, $sortDir);

        $data = $query->paginate($perPage);

        $availableYears = CarbonData::select('tahun')
            ->distinct()
            ->orderBy('tahun', 'desc')
            ->pluck('tahun');

        return response()->json([
            'tahun' => (int) $tahun,
            'data' => $data,
            'available_years' => $availableYears,
        ]);
    }
}
