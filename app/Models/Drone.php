<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Drone extends Model
{
    public function telemetries(): HasMany
    {
        return $this->hasMany(DroneTelemetry::class);
    }

    public function danger(): HasOne
    {
        return $this->hasOne(DangerousDrone::class);
    }

}
