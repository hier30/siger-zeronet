<?php

namespace Database\Seeders;

use App\Models\CarbonData;
use App\Models\Kecamatan;
use Illuminate\Database\Seeder;

class CarbonDataSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * Generate realistic carbon data for all kecamatan across 3 years.
     */
    public function run(): void
    {
        $list_kecamatan = Kecamatan::all();

        if ($list_kecamatan->isEmpty()) {
            $this->command->warn('No kecamatan found in database. Skipping CarbonDataSeeder.');
            return;
        }

        $years = [2023, 2024, 2025];

        // Realistic variance profiles for each kecamatan
        // Urban-heavy kecamatan have higher emission, green areas have higher absorption
        foreach ($list_kecamatan as $kecamatan) {
            // Generate a consistent profile for this kecamatan
            $urbanFactor = mt_rand(30, 100) / 100; // 0.3 to 1.0
            $greenFactor = mt_rand(20, 90) / 100;  // 0.2 to 0.9

            foreach ($years as $index => $tahun) {
                // Slight yearly growth in emissions (urbanization trend)
                $yearMultiplier = 1 + ($index * 0.05);

                // Emisi: 500 - 5000 ton CO2
                $baseEmisi = 500 + ($urbanFactor * 4500);
                $emisi = round($baseEmisi * $yearMultiplier + mt_rand(-200, 200), 2);
                $emisi = max(500, min(5000, $emisi));

                // Absorpsi: 300 - 4500 ton CO2
                $baseAbsorpsi = 300 + ($greenFactor * 4200);
                // Slight yearly decrease in absorption (deforestation trend)
                $absorpsiMultiplier = 1 - ($index * 0.02);
                $absorpsi = round($baseAbsorpsi * $absorpsiMultiplier + mt_rand(-150, 150), 2);
                $absorpsi = max(300, min(4500, $absorpsi));

                CarbonData::updateOrCreate(
                    [
                        'kecamatan_id' => $kecamatan->id,
                        'tahun' => $tahun,
                    ],
                    [
                        'emisi_co2' => $emisi,
                        'absorpsi_co2' => $absorpsi,
                    ]
                );
            }
        }

        $this->command->info('Carbon data seeded for ' . $list_kecamatan->count() . ' kecamatan across ' . count($years) . ' years.');
    }
}
