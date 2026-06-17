<?php

namespace Database\Seeders;

use App\Models\CarbonData;
use App\Models\Kecamatan;
use Illuminate\Database\Seeder;

class CarbonData2026Seeder extends Seeder
{
    private const TARGET_YEAR = 2026;
    private const DEFAULT_SOURCE_YEAR = 2025;

    public function run(): void
    {
        $created = 0;
        $skipped = 0;
        $missingSource = 0;

        foreach (Kecamatan::orderBy('kecamatan')->get() as $kecamatan) {
            if ($this->hasTargetData($kecamatan->id)) {
                $skipped++;
                continue;
            }

            $source = $this->sourceCarbonData($kecamatan->id);

            if (! $source) {
                $missingSource++;
                $this->command?->warn("Tidak ada data dasar untuk {$kecamatan->kecamatan}. Data 2026 dilewati.");
                continue;
            }

            $factors = $this->factorsFor($kecamatan->kecamatan);
            $yearGap = max(self::TARGET_YEAR - (int) $source->tahun, 1);

            CarbonData::create([
                'kecamatan_id' => $kecamatan->id,
                'tahun' => self::TARGET_YEAR,
                'emisi_co2' => round((float) $source->emisi_co2 * ($factors['emisi'] ** $yearGap), 2),
                'absorpsi_co2' => round((float) $source->absorpsi_co2 * ($factors['absorpsi'] ** $yearGap), 2),
            ]);

            $created++;
        }

        $this->command?->info("Seeder data carbon 2026 selesai. Dibuat: {$created}, dilewati karena sudah ada: {$skipped}, tanpa data dasar: {$missingSource}.");
    }

    private function hasTargetData(int $kecamatanId): bool
    {
        return CarbonData::where('kecamatan_id', $kecamatanId)
            ->where('tahun', self::TARGET_YEAR)
            ->exists();
    }

    private function sourceCarbonData(int $kecamatanId): ?CarbonData
    {
        return CarbonData::where('kecamatan_id', $kecamatanId)
            ->where('tahun', self::DEFAULT_SOURCE_YEAR)
            ->first()
            ?? CarbonData::where('kecamatan_id', $kecamatanId)
                ->where('tahun', '<', self::TARGET_YEAR)
                ->orderByDesc('tahun')
                ->first();
    }

    private function factorsFor(string $namaKecamatan): array
    {
        $name = strtolower($namaKecamatan);

        $center = [
            'enggal',
            'tanjung karang pusat',
            'tanjung karang timur',
            'kedaton',
            'way halim',
        ];

        $transportIndustry = [
            'panjang',
            'teluk betung selatan',
            'teluk betung timur',
        ];

        $developingResidential = [
            'sukarame',
            'labuhan ratu',
            'rajabasa',
            'langkapura',
        ];

        $greenArea = [
            'kemiling',
            'teluk betung barat',
            'sukabumi',
        ];

        if ($this->matchesAny($name, $center)) {
            return [
                'emisi' => 1.04,
                'absorpsi' => 0.995,
            ];
        }

        if ($this->matchesAny($name, $transportIndustry)) {
            return [
                'emisi' => 1.055,
                'absorpsi' => 0.99,
            ];
        }

        if ($this->matchesAny($name, $developingResidential)) {
            return [
                'emisi' => 1.035,
                'absorpsi' => 1.005,
            ];
        }

        if ($this->matchesAny($name, $greenArea)) {
            return [
                'emisi' => 1.025,
                'absorpsi' => 1.015,
            ];
        }

        return [
            'emisi' => 1.028,
            'absorpsi' => 1.003,
        ];
    }

    private function matchesAny(string $name, array $needles): bool
    {
        foreach ($needles as $needle) {
            if (str_contains($name, $needle)) {
                return true;
            }
        }

        return false;
    }
}
