<?php

namespace App\Http\Controllers;
/**
 * @OA\Info(
 *     title="Drone Monitoring API",
 *     version="1.0.0",
 *     description="This API allows real-time drone tracking, classification, and authentication."
 * )
 *
 * @OA\Server(
 *     url=L5_SWAGGER_CONST_HOST,
 *     description="Localhost API Server"
 * )
 *
 * @OA\SecurityScheme(
 *     securityScheme="bearerAuth",
 *     type="http",
 *     scheme="bearer",
 *     bearerFormat="JWT",
 *     description="Use the login token in Authorization header as Bearer {token}"
 * )
 *  * @OA\Schema(
 *     schema="DroneResource",
 *     type="object",
 *     @OA\Property(property="id", type="integer", example=1),
 *     @OA\Property(property="serial", type="string", example="DJI-001"),
 *     @OA\Property(property="is_online", type="boolean", example=true),
 *     @OA\Property(property="last_seen", type="string", example="2025-07-15 12:00:00"),
 *     @OA\Property(property="is_dangerous", type="boolean", example=true),
 *     @OA\Property(property="danger_reason", type="string", example="altitude")
 * )
 */


class SwaggerController extends Controller
{
    //
}
