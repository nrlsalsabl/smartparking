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
        Schema::create('vehicles', function (Blueprint $table) {
            $table->id();

            $table->foreignId('user_id')
                ->constrained('users')
                ->onDelete('cascade');

            $table->foreignId('vehicle_type_id')
                ->constrained('vehicle_types')
                ->onDelete('cascade');

            $table->string('plate_number')->unique();
            $table->string('brand');
            $table->string('color');

            $table->string('vehicle_photo')->nullable();

            $table->enum('status', [
                'active',
                'inactive'
            ])->default('active');
            
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('vehicles');
    }
};
