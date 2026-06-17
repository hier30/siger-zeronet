<?php

namespace App\Http\Controllers;

use App\Models\CarbonData;
use App\Models\Kecamatan;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class CarbonDataController extends Controller
{
    public function index(Request $request): View
    {
        $tahun = $request->input('tahun');
        $search = $request->input('search');

        $carbonData = CarbonData::query()
            ->with('kecamatan')
            ->join('kecamatan', 'kecamatan.id', '=', 'carbon_data.kecamatan_id')
            ->select('carbon_data.*')
            ->when($tahun, function ($query) use ($tahun) {
                $query->where('carbon_data.tahun', $tahun);
            })
            ->when($search, function ($query) use ($search) {
                $query->whereRaw('LOWER(kecamatan.kecamatan) LIKE ?', ['%'.strtolower($search).'%']);
            })
            ->orderByDesc('carbon_data.tahun')
            ->orderBy('kecamatan.kecamatan')
            ->paginate(10)
            ->withQueryString();

        $availableYears = CarbonData::select('tahun')
            ->distinct()
            ->orderBy('tahun', 'desc')
            ->pluck('tahun');

        return view('data-carbon.index', compact('carbonData', 'availableYears', 'tahun', 'search'));
    }

    public function create(Request $request): View
    {
        $carbonData = new CarbonData([
            'tahun' => now()->year,
        ]);
        $kecamatan = $this->kecamatanOptions();
        $returnQuery = $this->returnQueryFromRequest($request);

        return view('data-carbon.create', compact('carbonData', 'kecamatan', 'returnQuery'));
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $this->validateCarbonData($request);

        CarbonData::create($validated);

        return redirect()
            ->route('data-carbon.index', $this->returnQueryFromRequest($request))
            ->with('success', 'Data carbon berhasil ditambahkan.');
    }

    public function edit(Request $request, CarbonData $carbonData): View
    {
        $kecamatan = $this->kecamatanOptions();
        $returnQuery = $this->returnQueryFromRequest($request);

        return view('data-carbon.edit', compact('carbonData', 'kecamatan', 'returnQuery'));
    }

    public function update(Request $request, CarbonData $carbonData): RedirectResponse
    {
        $validated = $this->validateCarbonData($request, $carbonData);

        $carbonData->update($validated);

        return redirect()
            ->route('data-carbon.index', $this->returnQueryFromRequest($request))
            ->with('success', 'Data carbon berhasil diperbarui.');
    }

    public function destroy(Request $request, CarbonData $carbonData): RedirectResponse
    {
        $carbonData->delete();

        return redirect()
            ->route('data-carbon.index', $this->returnQueryFromRequest($request))
            ->with('success', 'Data carbon berhasil dihapus.');
    }

    private function validateCarbonData(Request $request, ?CarbonData $carbonData = null): array
    {
        return $request->validate([
            'kecamatan_id' => ['required', 'integer', 'exists:kecamatan,id'],
            'emisi_co2' => ['required', 'numeric'],
            'absorpsi_co2' => ['required', 'numeric'],
            'tahun' => [
                'required',
                'integer',
                Rule::unique('carbon_data')
                    ->where(fn ($query) => $query->where('kecamatan_id', $request->input('kecamatan_id')))
                    ->ignore($carbonData?->id),
            ],
        ], [
            'tahun.unique' => 'Data untuk kecamatan dan tahun tersebut sudah ada.',
        ]);
    }

    private function kecamatanOptions()
    {
        return Kecamatan::orderBy('kecamatan')->get();
    }

    private function returnQueryFromRequest(Request $request): array
    {
        $returnQuery = [];

        foreach (['tahun', 'search'] as $key) {
            $value = $request->input("return_{$key}", $request->query($key));

            if ($value !== null && $value !== '') {
                $returnQuery[$key] = $value;
            }
        }

        return $returnQuery;
    }
}
