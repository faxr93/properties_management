<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('property_valuations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('property_id')->constrained()->cascadeOnDelete();
            $table->date('valuation_date');
            $table->decimal('market_value', 18, 2);
            $table->decimal('land_value', 18, 2)->nullable();
            $table->decimal('building_value', 18, 2)->nullable();
            $table->string('currency', 8)->default('MYR');
            $table->enum('method', ['market', 'income', 'cost', 'comparative'])->default('market');
            $table->string('valuer_name')->nullable();
            $table->string('valuer_license')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();

            $table->index(['property_id', 'valuation_date']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('property_valuations');
    }
};
