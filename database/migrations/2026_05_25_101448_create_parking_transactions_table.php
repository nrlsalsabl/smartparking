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
        Schema::create('parking_transactions', function (Blueprint $table) {
            $table->id();

            $table->foreignId('booking_id')
                ->constrained('bookings')
                ->onDelete('cascade');

            $table->timestamp('checkin_at')->nullable();
            $table->timestamp('checkout_at')->nullable();

            $table->integer('duration')->nullable();

            $table->decimal('total_price', 10, 2)
                ->default(0);

            $table->enum('payment_status', [
                'unpaid',
                'paid'
            ])->default('unpaid');

            $table->enum('status', [
                'active',
                'completed'
            ])->default('active');

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('parking_transactions');
    }
};
