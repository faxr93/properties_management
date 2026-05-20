<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('properties', function (Blueprint $table) {
            $table->id();
            $table->string('reference_no')->unique();
            $table->string('name');
            $table->enum('type', [
                'residential',
                'commercial',
                'industrial',
                'agricultural',
                'mixed_use',
                'land',
            ])->default('residential');
            $table->enum('status', ['available', 'occupied', 'under_review', 'inactive'])->default('available');
            $table->text('address');
            $table->string('city')->nullable();
            $table->string('state')->nullable();
            $table->string('postal_code')->nullable();
            $table->string('country')->default('Malaysia');
            $table->decimal('land_area', 14, 2)->nullable()->comment('Land area in m²');
            $table->decimal('building_area', 14, 2)->nullable()->comment('Building area in m²');
            $table->unsignedInteger('bedrooms')->nullable();
            $table->unsignedInteger('bathrooms')->nullable();
            $table->unsignedInteger('year_built')->nullable();
            $table->text('description')->nullable();
            // GIS columns – stored as JSONB GeoJSON objects (SRID 4326 / WGS84 by convention).
            $table->jsonb('location')->nullable()->comment('GeoJSON Point: { type: "Point", coordinates: [lng, lat] }');
            $table->jsonb('boundary')->nullable()->comment('GeoJSON Polygon: { type: "Polygon", coordinates: [[[lng, lat], ...]] }');
            $table->timestamps();
            $table->softDeletes();

            $table->index(['type', 'status']);
            $table->index('city');
        });

        // GIN index on the JSONB boundary column for fast containment / key lookups.
        DB::statement('CREATE INDEX properties_boundary_gin ON properties USING GIN (boundary jsonb_path_ops)');
    }

    public function down(): void
    {
        Schema::dropIfExists('properties');
    }
};
