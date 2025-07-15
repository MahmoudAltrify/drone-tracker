<?php

namespace Tests\Unit;

use App\Strategies\AltitudeDangerStrategy;
use PHPUnit\Framework\TestCase;

class AltitudeDangerStrategyTest extends TestCase
{
    public function test_it_returns_altitude_when_height_above_500()
    {
        $strategy = new AltitudeDangerStrategy();
        $payload = ['height' => 600];

        $this->assertEquals('altitude', $strategy->check($payload));
    }

    public function test_it_returns_null_when_height_is_500_or_less()
    {
        $strategy = new AltitudeDangerStrategy();

        $this->assertNull($strategy->check(['height' => 500]));
        $this->assertNull($strategy->check(['height' => 300]));
    }

    public function test_it_handles_missing_height_gracefully()
    {
        $strategy = new AltitudeDangerStrategy();

        $this->assertNull($strategy->check([]));
    }
}
