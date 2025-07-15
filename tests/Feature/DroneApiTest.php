<?php

namespace Tests\Feature;

use App\Models\DangerousDrone;
use App\Models\Drone;
use App\Models\DroneTelemetry;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Log;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class DroneApiTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function test_it_returns_all_drones()
    {
        Drone::factory()->count(5)->create();

        $response = $this->getJson('/api/v1/drones');

        Log::info('res'. json_encode($response));
        $response->assertStatus(200)
            ->assertJson([
                'success' => true,
                'code' => 200,
                'message' => 'success',
            ])
            ->assertJsonStructure([
                'success',
                'code',
                'message',
                'data' => [
                    '*' => [
                        'id',
                        'serial',
                        'is_online',
                        'last_seen',
                        'is_dangerous',
                        'danger_reason'
                    ]
                ]
            ]);
    }
    #[Test]
    public function test_it_filters_drones_by_partial_serial()
    {
        Drone::factory()->create(['serial' => 'ALPHA-12345']);
        Drone::factory()->create(['serial' => 'BETA-99999']);

        $response = $this->getJson('/api/v1/drones?serial=ALPHA');

        $response->assertStatus(200)
            ->assertJsonFragment(['serial' => 'ALPHA-12345'])
            ->assertJsonMissing(['serial' => 'BETA-99999']);
    }
    #[Test]
    public function test_it_returns_online_drones_only()
    {
        Drone::factory()->create(['serial' => 'ONLINE-1', 'is_online' => true]);
        Drone::factory()->create(['serial' => 'OFFLINE-1', 'is_online' => false]);

        $response = $this->getJson('/api/v1/drones/online');

        $response->assertStatus(200)
            ->assertJsonFragment(['serial' => 'ONLINE-1'])
            ->assertJsonMissing(['serial' => 'OFFLINE-1'])
            ->assertJsonStructure([
                'success',
                'code',
                'message',
                'data' => [
                    ['id', 'serial', 'is_online', 'last_seen', 'is_dangerous', 'danger_reason']
                ]
            ]);
    }
    #[Test]
    public function test_it_returns_drones_within_5km()
    {
        $droneNear = Drone::factory()->create(['serial' => 'NEAR-1']);
        DroneTelemetry::query()->create([
            'drone_id' => $droneNear->id,
            'latitude' => 31.9784,
            'longitude' => 35.8309,
            'height' => 10,
            'horizontal_speed' => 2,
            'raw_payload' => json_encode([]),
        ]);

        $droneFar = Drone::factory()->create(['serial' => 'FAR-1']);
        DroneTelemetry::query()->create([
            'drone_id' => $droneFar->id,
            'latitude' => 31.00,
            'longitude' => 35.00,
            'height' => 10,
            'horizontal_speed' => 2,
            'raw_payload' => json_encode([]),
        ]);

        $response = $this->getJson('/api/v1/drones/nearby?lat=31.9784&lng=35.8309');

        $response->assertStatus(200)
            ->assertJsonFragment(['serial' => 'NEAR-1'])
            ->assertJsonMissing(['serial' => 'FAR-1']);
    }
    #[Test]
    public function test_it_returns_flight_path_as_geojson()
    {
        $drone = Drone::factory()->create(['serial' => 'FLIGHT-001']);

        DroneTelemetry::query()->create([
            'drone_id' => $drone->id,
            'latitude' => 31.9784,
            'longitude' => 35.8309,
            'height' => 10,
            'horizontal_speed' => 2,
            'raw_payload' => json_encode([]),
        ]);

        DroneTelemetry::query()->create([
            'drone_id' => $drone->id,
            'latitude' => 31.9786,
            'longitude' => 35.8311,
            'height' => 20,
            'horizontal_speed' => 3,
            'raw_payload' => json_encode([]),
        ]);

        $response = $this->getJson('/api/v1/drones/FLIGHT-001/path');

        Log::info('response: ' . json_encode($response));
        $response->assertStatus(200)
            ->assertJsonFragment(['type' => 'LineString'])
            ->assertJsonFragment(['coordinates' => [
                [35.8309, 31.9784],
                [35.8311, 31.9786],
            ]]);
    }
    public function test_it_returns_all_dangerous_drones()
    {
        Drone::factory()->create(['serial' => 'SAFE-DRONE']);
        $dangerousDrone = Drone::factory()->create(['serial' => 'DANGER-DRONE']);

        DangerousDrone::query()->create([
            'drone_id' => $dangerousDrone->id,
            'reason' => 'altitude',
            'detected_at' => now(),
        ]);

        $response = $this->getJson('/api/v1/drones/dangerous');

        $response->assertStatus(200)
            ->assertJsonFragment(['serial' => 'DANGER-DRONE'])
            ->assertJsonMissing(['serial' => 'SAFE-DRONE']);
    }
}
