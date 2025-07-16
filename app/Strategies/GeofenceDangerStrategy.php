<?php

namespace App\Strategies;

use App\Helpers\GeoHelper;
use App\Interfaces\IDangerStrategy;
use App\Models\NoFlyZone;

class GeofenceDangerStrategy implements IDangerStrategy
{
    public function check(array $data): ?string
    {
        if (!isset($data['latitude'], $data['longitude'])) {
            return null;
        }

        $lat = $data['latitude'];
        $lng = $data['longitude'];

        $zones = NoFlyZone::query()->where('min_lat', '<=', $lat)
            ->where('max_lat', '>=', $lat)
            ->where('min_lng', '<=', $lng)
            ->where('max_lng', '>=', $lng)
            ->get();

        foreach ($zones as $zone) {
            if (GeoHelper::pointInPolygon($lat, $lng, $zone->polygon)) {
                return 'geofence';
            }
        }

        return null;
    }
}
