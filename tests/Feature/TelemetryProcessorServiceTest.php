<?php

namespace Tests\Feature;

use App\Models\DangerousDrone;
use App\Models\Drone;
use App\Services\TelemetryProcessorService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Log;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class TelemetryProcessorServiceTest extends TestCase
{
    use RefreshDatabase;

    protected TelemetryProcessorService $processor;

    protected function setUp(): void
    {
        parent::setUp();
        $this->processor = new TelemetryProcessorService();
    }

    #[Test]
    public function test_it_flags_dangerous_drone_by_altitude()
    {
        $payload = [
            'latitude' => 31.97,
            'longitude' => 35.83,
            'height' => 600,
            'horizontal_speed' => 2,
        ];

        $this->processor->process('device/ALT-001/osd', $payload);

        $drone = Drone::query()->where('serial', 'ALT-001')->first();
        $this->assertNotNull($drone);
        $this->assertDatabaseHas('dangerous_drones', [
            'drone_id' => $drone->id,
            'reason' => 'altitude',
        ]);
    }

    #[Test]
    public function test_it_flags_dangerous_drone_by_speed()
    {
        $payload = [
            'latitude' => 31.97,
            'longitude' => 35.83,
            'height' => 100,
            'horizontal_speed' => 15,
        ];

        $this->processor->process('device/SPEED-001/osd', $payload);

        $drone = Drone::query()->where('serial', 'SPEED-001')->first();
        $this->assertNotNull($drone);
        $this->assertDatabaseHas('dangerous_drones', [
            'drone_id' => $drone->id,
            'reason' => 'speed',
        ]);
    }

    #[Test]
    public function test_it_stores_telemetry_and_marks_drone_online()
    {
        $payload = [
            'latitude' => 31.1,
            'longitude' => 35.2,
            'height' => 50,
            'horizontal_speed' => 3,
        ];

        $this->processor->process('device/TEST-001/osd', $payload);

        $drone = Drone::query()->where('serial', 'TEST-001')->first();
        $this->assertNotNull($drone);
        Log::info('is online: '. $drone->is_online);
        $this->assertTrue($drone->is_online);
        $this->assertEquals(1, $drone->telemetries()->count());
    }

    #[Test]
    public function test_it_ignores_invalid_topic()
    {
        $this->processor->process('invalid/topic/structure', [
            'latitude' => 0,
            'longitude' => 0,
            'height' => 100,
            'horizontal_speed' => 2,
        ]);

        $this->assertEquals(0, Drone::query()->count());
        $this->assertEquals(0, DangerousDrone::query()->count());
    }
}
