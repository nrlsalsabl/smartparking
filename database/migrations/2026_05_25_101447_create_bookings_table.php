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
        Schema::create('bookings', function (Blueprint $table) {
            $table->id();

            $table->foreignId('user_id')
                ->constrained('users')
                ->onDelete('cascade');

            $table->foreignId('vehicle_id')
                ->constrained('vehicles')
                ->onDelete('cascade');

            $table->foreignId('parking_area_id')
                ->constrained('parking_areas')
                ->onDelete('cascade');

            $table->string('booking_code')->unique();

            $table->timestamp('booking_time');
            $table->timestamp('expired_at');

            $table->enum('status', [
                'pending',
                'active',
                'completed',
                'cancelled'
            ])->default('pending');
            
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('bookings');
    }
};
