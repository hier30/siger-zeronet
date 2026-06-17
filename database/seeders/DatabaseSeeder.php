<?php

namespace Database\Seeders;

use App\Models\CarbonData;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        User::updateOrCreate([
            'email' => 'admin@sigerzeronet.local',
        ], [
            'name' => 'Admin SigerZeroNet',
            'password' => Hash::make('admin12345'),
        ]);

        if (CarbonData::query()->doesntExist()) {
            $this->call([
                CarbonDataSeeder::class,
            ]);
        }
    }
}
