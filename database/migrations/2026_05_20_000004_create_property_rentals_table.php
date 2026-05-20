<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('property_rentals', function (Blueprint $table) {
            $table->id();
            $table->foreignId('property_id')->constrained()->cascadeOnDelete();
            $table->string('tenant_name');
            $table->string('tenant_email')->nullable();
            $table->string('tenant_phone')->nullable();
            $table->date('start_date');
            $table->date('end_date');
            $table->decimal('monthly_rent', 16, 2);
            $table->decimal('deposit', 16, 2)->nullable();
            $table->string('currency', 8)->default('MYR');
            $table->enum('payment_cycle', ['monthly', 'quarterly', 'semi_annually', 'annually'])->default('monthly');
            $table->enum('status', ['active', 'pending', 'completed', 'cancelled'])->default('active');
            $table->text('notes')->nullable();
            $table->timestamps();

            $table->index(['property_id', 'status']);
            $table->index(['start_date', 'end_date']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('property_rentals');
    }
};
