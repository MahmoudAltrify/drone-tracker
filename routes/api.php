<?php

use App\Http\Controllers\v1\AuthController;
use App\Http\Controllers\v1\DroneController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');


Route::prefix('/v1')->group(function (){
    Route::prefix('/drones')->middleware('auth:sanctum')->group(function (){
        Route::get('/', [DroneController::class, 'index']);
        Route::get('/online', [DroneController::class, 'getOnlineDrones']);
        Route::get('/nearby', [DroneController::class, 'getNearbyDrones']);
        Route::get('/{serial}/path', [DroneController::class, 'getDronePathBySerial']);
        Route::get('/dangerous', [DroneController::class, 'getDangerousDrones']);
    });
    Route::controller(AuthController::class)->group(function (){
       Route::post('login', 'login');
        Route::middleware('auth:sanctum')->group(function () {
            Route::get('/me', 'me');
            Route::post('/logout', 'logout');
        });
    });
});
