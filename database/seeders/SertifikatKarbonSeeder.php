<?php

namespace Database\Seeders;

use App\Models\SertifikatKarbon;
use Illuminate\Database\Seeder;

class SertifikatKarbonSeeder extends Seeder
{
    public function run(): void
    {
        $sertifikats = [
            [
                'nama_pemilik' => 'Kelompok Tani Hijau Kemiling',
                'wilayah' => 'Kemiling',
                'luas_lahan' => 12.50,
                'jumlah_serapan' => 184.75,
                'satuan' => 'ton CO2e',
                'tahun' => 2026,
                'status' => 'Aktif',
                'keterangan' => 'Program konservasi vegetasi kawasan perbukitan dan ruang hijau.',
            ],
            [
                'nama_pemilik' => 'Koperasi Mangrove Pesisir Panjang',
                'wilayah' => 'Panjang',
                'luas_lahan' => 8.20,
                'jumlah_serapan' => 132.40,
                'satuan' => 'ton CO2e',
                'tahun' => 2026,
                'status' => 'Menunggu Verifikasi',
                'keterangan' => 'Pemulihan vegetasi pesisir dan area buffer dekat kawasan pelabuhan.',
            ],
            [
                'nama_pemilik' => 'Komunitas Urban Farming Way Halim',
                'wilayah' => 'Way Halim',
                'luas_lahan' => 3.75,
                'jumlah_serapan' => 46.30,
                'satuan' => 'ton CO2e',
                'tahun' => 2025,
                'status' => 'Tidak Aktif',
                'keterangan' => 'Sertifikat lama yang belum diperpanjang untuk periode terbaru.',
            ],
        ];

        foreach ($sertifikats as $sertifikat) {
            SertifikatKarbon::updateOrCreate(
                [
                    'nama_pemilik' => $sertifikat['nama_pemilik'],
                    'wilayah' => $sertifikat['wilayah'],
                    'tahun' => $sertifikat['tahun'],
                ],
                $sertifikat
            );
        }

        $this->command?->info('Seeder sertifikat karbon selesai. Total data contoh: '.count($sertifikats).'.');
    }
}
