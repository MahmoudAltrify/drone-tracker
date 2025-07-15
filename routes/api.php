<?php

use App\Http\Controllers\v1\DroneController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');


Route::prefix('/v1')->group(function (){
    Route::prefix('/drones')->group(function (){
        Route::get('/', [DroneController::class, 'index']);
        Route::get('/online', [DroneController::class, 'getOnlineDrones']);
        Route::get('/nearby', [DroneController::class, 'getNearbyDrones']);
        Route::get('/{serial}/path', [DroneController::class, 'getDronePathBySerial']);
        Route::get('/dangerous', [DroneController::class, 'getDangerousDrones']);
    });
});
