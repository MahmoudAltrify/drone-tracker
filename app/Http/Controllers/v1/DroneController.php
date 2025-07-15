<?php

namespace App\Http\Controllers\v1;

use App\Helpers\GeoHelper;
use App\Http\Controllers\ApiBaseController;
use App\Http\Requests\NearbyDroneRequest;
use App\Http\Resources\DroneResource;
use App\Models\Drone;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class DroneController extends ApiBaseController
{
    /**
     * Get all drones or filter by serial
     * @param Request $request
     * @return JsonResponse
     */
    public function index(Request $request)
    {
        $query = Drone::query();

        if ($request->filled('serial')) {
            $query->where('serial', 'like', '%' . $request->serial . '%');
        }

        $drones = $query->with('danger')->latest()->paginate(20);

        return $this->successResponse(DroneResource::collection($drones));
    }

    /**
     * Get online drones only
     * @return JsonResponse
     */
    public function getOnlineDrones(){
        $onlineDrones = Drone::query()->with('danger')
            ->where('is_online', true)
            ->latest('updated_at')
            ->get();
        return $this->successResponse(DroneResource::collection($onlineDrones));
    }

    /**
     * Get nearby drones
     * @param NearbyDroneRequest $request
     * @return JsonResponse
     */
    public function getNearbyDrones(NearbyDroneRequest $request)
    {
        $validated = $request->validated();
        $lat = $validated['lat'];
        $lng = $validated['lng'];

        $formula = GeoHelper::haversineFormula($lat, $lng);

        $drones = Drone::query()
            ->whereHas('telemetries', function ($q) use ($formula) {
                $q->selectRaw('*')->whereRaw($formula);
            })
            ->with('danger')
            ->get();

        return $this->successResponse(DroneResource::collection($drones));
    }

    /**
     * Get drone path
     * @param string $serial
     * @return JsonResponse
     */
    public function getDronePathBySerial(string $serial)
    {
        $drone = Drone::query()->where('serial', $serial)->first();
        if (!$drone) {
            return $this->errorResponse([], 'Drone not found', 404);
        }
        $points = $drone->telemetries()
            ->orderBy('created_at')
            ->get(['latitude', 'longitude'])
            ->map(fn ($t) => [(float) $t->longitude, (float) $t->latitude])
            ->toArray();
        Log::info('points: ' . json_encode($points));
        return $this->successResponse([
            'type' => 'LineString',
            'coordinates' => $points,
        ]);
    }

    /**
     * get dangerous drones
     * @return JsonResponse
     */
    public function getDangerousDrones()
    {
        $drones = Drone::query()->whereHas('danger')
            ->with('danger')
            ->latest('updated_at')
            ->get();

        return $this->successResponse(DroneResource::collection($drones));
    }
}
