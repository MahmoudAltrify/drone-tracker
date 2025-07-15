<?php

namespace Tests\Unit;

use App\Strategies\SpeedDangerStrategy;
use PHPUnit\Framework\TestCase;

class SpeedDangerStrategyTest extends TestCase
{
    public function test_it_returns_speed_when_horizontal_speed_above_10()
    {
        $strategy = new SpeedDangerStrategy();
        $payload = ['horizontal_speed' => 15];

        $this->assertEquals('speed', $strategy->check($payload));
    }

    public function test_it_returns_null_when_speed_is_10_or_less()
    {
        $strategy = new SpeedDangerStrategy();

        $this->assertNull($strategy->check(['horizontal_speed' => 10]));
        $this->assertNull($strategy->check(['horizontal_speed' => 5]));
    }

    public function test_it_handles_missing_speed_gracefully()
    {
        $strategy = new SpeedDangerStrategy();

        $this->assertNull($strategy->check([]));
    }
}
