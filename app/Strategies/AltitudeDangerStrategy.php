<?php

namespace App\Strategies;

use App\Interfaces\IDangerStrategy;

class AltitudeDangerStrategy implements IDangerStrategy
{
    public function check(array $data): ?string
    {
        return $data['height'] > 500 ? 'altitude' : null;
    }
}
