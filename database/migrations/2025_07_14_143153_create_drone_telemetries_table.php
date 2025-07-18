<?php

use App\Models\Drone;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('drone_telemetries', function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(Drone::class)->constrained()->onDelete('cascade');
            $table->decimal('latitude', 10, 7);
            $table->decimal('longitude', 10, 7);
            $table->float('height')->comment('in meters');
            $table->float('horizontal_speed')->comment('in m/s');
            $table->float('vertical_speed')->nullable();
            $table->float('elevation')->nullable();
            $table->json('raw_payload');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('drone_telemetries');
    }
};
