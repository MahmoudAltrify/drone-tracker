<?php

namespace App\Helpers;

class GeoHelper
{
    public static function haversineFormula(float $lat, float $lng, float $radius = 5): string
    {
        return "(6371 * acos(cos(radians($lat)) * cos(radians(latitude)) * cos(radians(longitude) - radians($lng)) + sin(radians($lat)) * sin(radians(latitude)))) < $radius";
    }
}
