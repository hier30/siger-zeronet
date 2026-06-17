<?php

namespace App\Http\Controllers;

use App\Models\SertifikatKarbon;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class SertifikatKarbonController extends Controller
{
    private const STATUSES = [
        'Aktif',
        'Menunggu Verifikasi',
        'Tidak Aktif',
    ];

    public function index(Request $request): View
    {
        $search = $request->input('search');
        $tahun = $request->input('tahun');
        $status = $request->input('status');

        $sertifikats = SertifikatKarbon::query()
            ->when($search, function ($query) use ($search) {
                $query->where(function ($query) use ($search) {
                    $query->whereRaw('LOWER(nama_pemilik) LIKE ?', ['%'.strtolower($search).'%'])
                        ->orWhereRaw('LOWER(wilayah) LIKE ?', ['%'.strtolower($search).'%']);
                });
            })
            ->when($tahun, function ($query) use ($tahun) {
                $query->where('tahun', $tahun);
            })
            ->when($status, function ($query) use ($status) {
                $query->where('status', $status);
            })
            ->orderByDesc('tahun')
            ->orderBy('nama_pemilik')
            ->paginate(10)
            ->withQueryString();

        $availableYears = SertifikatKarbon::select('tahun')
            ->distinct()
            ->orderBy('tahun', 'desc')
            ->pluck('tahun');
        $statuses = self::STATUSES;

        return view('sertifikat-karbon.index', compact('sertifikats', 'availableYears', 'statuses', 'search', 'tahun', 'status'));
    }

    public function create(Request $request): View
    {
        $sertifikatKarbon = new SertifikatKarbon([
            'satuan' => 'ton CO2e',
            'tahun' => now()->year,
            'status' => 'Menunggu Verifikasi',
        ]);
        $statuses = self::STATUSES;
        $returnQuery = $this->returnQueryFromRequest($request);

        return view('sertifikat-karbon.create', compact('sertifikatKarbon', 'statuses', 'returnQuery'));
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $this->validateSertifikat($request);
        $validated['satuan'] = $validated['satuan'] ?: 'ton CO2e';

        SertifikatKarbon::create($validated);

        return redirect()
            ->route('sertifikat-karbon.index', $this->returnQueryFromRequest($request))
            ->with('success', 'Sertifikat karbon berhasil ditambahkan.');
    }

    public function edit(Request $request, SertifikatKarbon $sertifikatKarbon): View
    {
        $statuses = self::STATUSES;
        $returnQuery = $this->returnQueryFromRequest($request);

        return view('sertifikat-karbon.edit', compact('sertifikatKarbon', 'statuses', 'returnQuery'));
    }

    public function update(Request $request, SertifikatKarbon $sertifikatKarbon): RedirectResponse
    {
        $validated = $this->validateSertifikat($request);
        $validated['satuan'] = $validated['satuan'] ?: 'ton CO2e';

        $sertifikatKarbon->update($validated);

        return redirect()
            ->route('sertifikat-karbon.index', $this->returnQueryFromRequest($request))
            ->with('success', 'Sertifikat karbon berhasil diperbarui.');
    }

    public function destroy(Request $request, SertifikatKarbon $sertifikatKarbon): RedirectResponse
    {
        $sertifikatKarbon->delete();

        return redirect()
            ->route('sertifikat-karbon.index', $this->returnQueryFromRequest($request))
            ->with('success', 'Sertifikat karbon berhasil dihapus.');
    }

    private function validateSertifikat(Request $request): array
    {
        return $request->validate([
            'nama_pemilik' => ['required', 'string', 'max:255'],
            'wilayah' => ['required', 'string', 'max:255'],
            'luas_lahan' => ['required', 'numeric'],
            'jumlah_serapan' => ['required', 'numeric'],
            'satuan' => ['nullable', 'string', 'max:50'],
            'tahun' => ['required', 'integer'],
            'status' => ['required', Rule::in(self::STATUSES)],
            'keterangan' => ['nullable', 'string'],
        ]);
    }

    private function returnQueryFromRequest(Request $request): array
    {
        $returnQuery = [];

        foreach (['tahun', 'status', 'search'] as $key) {
            $value = $request->input("return_{$key}", $request->query($key));

            if ($value !== null && $value !== '') {
                $returnQuery[$key] = $value;
            }
        }

        return $returnQuery;
    }
}
