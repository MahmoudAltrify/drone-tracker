<?php

namespace App\Services;

use App\Models\DangerousDrone;
use App\Models\Drone;
use App\Strategies\AltitudeDangerStrategy;
use App\Strategies\SpeedDangerStrategy;
use Illuminate\Support\Carbon;

class TelemetryProcessorService
{
    protected array $strategies;

    public function __construct()
    {
        $this->strategies = [
            new AltitudeDangerStrategy(),
            new SpeedDangerStrategy(),
        ];
    }

    public function process(string $topic, array $payload): void
    {
        preg_match('/device\/(.+)\/osd/', $topic, $matches);
        $serial = $matches[1] ?? null;

        if (!$serial) {
            logger()->warning('No Serial Found', $payload);
            return;
        }

        $drone = Drone::query()->firstOrCreate(['serial' => $serial]);
        $drone->update(['is_online' => true]);

        $drone->telemetries()->create([
            'latitude' => $payload['latitude'],
            'longitude' => $payload['longitude'],
            'height' => $payload['height'],
            'horizontal_speed' => $payload['horizontal_speed'],
            'vertical_speed' => $payload['vertical_speed'] ?? 0,
            'elevation' => $payload['elevation'] ?? 0,
            'raw_payload' => json_encode($payload),
        ]);

        foreach ($this->strategies as $strategy) {
            if ($reason = $strategy->check($payload)) {
                DangerousDrone::query()->updateOrCreate(
                    ['drone_id' => $drone->id],
                    ['reason' => $reason, 'detected_at' => Carbon::now()]
                );
                break;
            }
        }
    }
}
