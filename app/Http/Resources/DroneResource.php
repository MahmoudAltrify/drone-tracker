<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class DroneResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'serial' => $this->serial,
            'is_online' => $this->is_online,
            'last_seen' => $this->updated_at->toDateTimeString(),
            'is_dangerous' => (bool)$this->danger,
            'danger_reason' => $this->danger->reason ?? null,
        ];
    }
}
