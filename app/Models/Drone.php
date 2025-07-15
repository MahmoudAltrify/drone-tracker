<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Drone extends Model
{
    use HasFactory;
    protected $guarded = ['id'];
    protected $casts = [
        'is_online' => 'boolean',
    ];
    public function telemetries(): HasMany
    {
        return $this->hasMany(DroneTelemetry::class);
    }

    public function danger(): HasOne
    {
        return $this->hasOne(DangerousDrone::class);
    }

}
