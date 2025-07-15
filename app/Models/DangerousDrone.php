<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DangerousDrone extends Model
{
    protected $guarded = ['id'];
    public function drone(): BelongsTo
    {
        return $this->belongsTo(Drone::class);
    }
}
