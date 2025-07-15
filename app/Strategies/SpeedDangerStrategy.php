<?php

namespace App\Strategies;

use App\Interfaces\IDangerStrategy;

class SpeedDangerStrategy implements IDangerStrategy
{
    public function check(array $data): ?string
    {
        return $data['horizontal_speed'] > 10 ? 'speed' : null;
    }
}
