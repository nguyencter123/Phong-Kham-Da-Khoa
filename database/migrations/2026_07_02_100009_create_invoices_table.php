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
        Schema::create('invoices', function (Blueprint $table) {
            $table->id();
            $table->foreignId('appointment_id')->constrained('appointments')->onDelete('cascade');
            $table->string('invoice_number')->unique();
            $table->decimal('consultation_fee', 10, 2);
            $table->decimal('medicine_fee', 10, 2)->default(0.00);
            $table->decimal('total_amount', 10, 2);
            $table->enum('payment_method', ['cash', 'vietqr', 'vnpay'])->nullable();
            $table->enum('payment_status', ['unpaid', 'paid'])->default('unpaid');
            $table->string('transaction_id')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('invoices');
    }
};
