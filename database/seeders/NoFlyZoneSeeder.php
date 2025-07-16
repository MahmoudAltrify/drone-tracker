<?php

namespace Database\Seeders;

use App\Models\NoFlyZone;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class NoFlyZoneSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        {
            $polygon = [
                ['lat' => 31.975, 'lng' => 35.830],
                ['lat' => 31.975, 'lng' => 35.835],
                ['lat' => 31.980, 'lng' => 35.835],
                ['lat' => 31.980, 'lng' => 35.830],
            ];

            $latitudes = array_column($polygon, 'lat');
            $longitudes = array_column($polygon, 'lng');

            NoFlyZone::query()->create([
                'name' => 'Airport',
                'polygon' => $polygon,
                'min_lat' => min($latitudes),
                'max_lat' => max($latitudes),
                'min_lng' => min($longitudes),
                'max_lng' => max($longitudes),
            ]);
        }
    }
}
