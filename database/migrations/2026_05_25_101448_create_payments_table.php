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
        Schema::create('payments', function (Blueprint $table) {
            $table->id();

            $table->foreignId('transaction_id')
                ->constrained('parking_transactions')
                ->onDelete('cascade');

            $table->string('payment_method');

            $table->decimal('amount', 10, 2);

            $table->string('payment_proof')->nullable();

            $table->timestamp('payment_date')->nullable();

            $table->enum('status', [
                'pending',
                'paid',
                'failed'
            ])->default('pending');
            
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('payments');
    }
};
