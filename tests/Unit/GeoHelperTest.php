<?php

namespace Tests\Unit;

use App\Helpers\GeoHelper;
use PHPUnit\Framework\TestCase;

class GeoHelperTest extends TestCase
{
    public function test_it_generates_correct_haversine_sql()
    {
        $lat = 31.9784;
        $lng = 35.8309;
        $radius = 5;

        $sql = GeoHelper::haversineFormula($lat, $lng, $radius);

        $this->assertIsString($sql);
        $this->assertStringContainsString("6371 * acos", $sql);
        $this->assertStringContainsString("radians($lat)", $sql);
        $this->assertStringContainsString("radians($lng)", $sql);
        $this->assertStringContainsString("< $radius", $sql);
    }
}
