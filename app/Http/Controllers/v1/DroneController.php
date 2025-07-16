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
     * @OA\Get(
     *     path="/v1/drones",
     *     tags={"Drones"},
     *     summary="List all drones or filter by serial",
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(
     *         name="serial",
     *         in="query",
     *         description="Filter by partial or full serial number",
     *         required=false,
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="List of drones",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="code", type="integer", example=200),
     *             @OA\Property(property="message", type="string", example="success"),
     *             @OA\Property(
     *                 property="data",
     *                 type="array",
     *                 @OA\Items(ref="#/components/schemas/DroneResource")
     *             )
     *         )
     *     ),
     *     @OA\Response(response=401, description="Unauthenticated")
     * )
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
     * @OA\Get(
     *     path="/v1/drones/online",
     *     tags={"Drones"},
     *     summary="Get online drones",
     *     security={{"bearerAuth":{}}},
     *     @OA\Response(
     *         response=200,
     *         description="Online drones list",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="code", type="integer", example=200),
     *             @OA\Property(property="message", type="string", example="success"),
     *             @OA\Property(
     *                 property="data",
     *                 type="array",
     *                 @OA\Items(ref="#/components/schemas/DroneResource")
     *             )
     *         )
     *     ),
     *     @OA\Response(response=401, description="Unauthenticated")
     * )
     */
    public function getOnlineDrones(){
        $onlineDrones = Drone::query()->with('danger')
            ->where('is_online', true)
            ->latest('updated_at')
            ->get();
        return $this->successResponse(DroneResource::collection($onlineDrones));
    }

    /**
     * @OA\Get(
     *     path="/v1/drones/nearby",
     *     tags={"Drones"},
     *     summary="Get drones near a point (5km radius)",
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(
     *         name="lat",
     *         in="query",
     *         required=true,
     *         description="Latitude of the point",
     *         @OA\Schema(type="number", format="float")
     *     ),
     *     @OA\Parameter(
     *         name="lng",
     *         in="query",
     *         required=true,
     *         description="Longitude of the point",
     *         @OA\Schema(type="number", format="float")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Nearby drones",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="code", type="integer", example=200),
     *             @OA\Property(property="message", type="string", example="success"),
     *             @OA\Property(
     *                 property="data",
     *                 type="array",
     *                 @OA\Items(ref="#/components/schemas/DroneResource")
     *             )
     *         )
     *     ),
     *     @OA\Response(response=422, description="Validation error"),
     *     @OA\Response(response=401, description="Unauthenticated")
     * )
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
     * @OA\Get(
     *     path="/v1/drones/{serial}/path",
     *     tags={"Drones"},
     *     summary="Get drone flight path as GeoJSON",
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(
     *         name="serial",
     *         in="path",
     *         required=true,
     *         description="Drone serial number",
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="GeoJSON LineString path",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="code", type="integer", example=200),
     *             @OA\Property(property="message", type="string", example="success"),
     *             @OA\Property(
     *                 property="data",
     *                 type="object",
     *                 @OA\Property(property="type", type="string", example="LineString"),
     *                 @OA\Property(
     *                     property="coordinates",
     *                     type="array",
     *                     @OA\Items(
     *                         type="array",
     *                         @OA\Items(type="number", format="float", example=39.5)
     *                     )
     *                 )
     *             )
     *         )
     *     ),
     *     @OA\Response(response=404, description="Drone not found"),
     *     @OA\Response(response=401, description="Unauthenticated")
     * )
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
     * @OA\Get(
     *     path="/v1/drones/dangerous",
     *     tags={"Drones"},
     *     summary="Get dangerous drones",
     *     security={{"bearerAuth":{}}},
     *     @OA\Response(
     *         response=200,
     *         description="Dangerous drones list",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="code", type="integer", example=200),
     *             @OA\Property(property="message", type="string", example="success"),
     *             @OA\Property(
     *                 property="data",
     *                 type="array",
     *                 @OA\Items(ref="#/components/schemas/DroneResource")
     *             )
     *         )
     *     ),
     *     @OA\Response(response=401, description="Unauthenticated")
     * )
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
