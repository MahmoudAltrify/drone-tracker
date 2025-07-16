<?php

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
        Schema::create('no_fly_zones', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->json('polygon')->comment('[lat, lng] pairs');
            $table->decimal('min_lat', 10, 6)->index();
            $table->decimal('max_lat', 10, 6)->index();
            $table->decimal('min_lng', 10, 6)->index();
            $table->decimal('max_lng', 10, 6)->index();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('no_fly_zones');
    }
};
