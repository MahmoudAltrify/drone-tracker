<?php

namespace Tests\Feature;

use App\Models\NoFlyZone;
use App\Strategies\GeofenceDangerStrategy;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class GeofenceDangerStrategyTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function test_it_detects_drone_inside_no_fly_zone()
    {
        $polygon = [
            ['lat' => 31.975, 'lng' => 35.830],
            ['lat' => 31.975, 'lng' => 35.835],
            ['lat' => 31.980, 'lng' => 35.835],
            ['lat' => 31.980, 'lng' => 35.830],
        ];

        NoFlyZone::query()->create([
            'name' => 'Test Zone',
            'polygon' => $polygon,
            'min_lat' => 31.975,
            'max_lat' => 31.980,
            'min_lng' => 35.830,
            'max_lng' => 35.835,
        ]);

        $strategy = new GeofenceDangerStrategy();

        $result = $strategy->check([
            'latitude' => 31.977,
            'longitude' => 35.832,
        ]);

        $this->assertEquals('geofence', $result);
    }

    #[Test]
    public function test_it_returns_null_if_drone_is_outside()
    {
        $strategy = new GeofenceDangerStrategy();

        $result = $strategy->check([
            'latitude' => 32.000,
            'longitude' => 36.000,
        ]);

        $this->assertNull($result);
    }
}
