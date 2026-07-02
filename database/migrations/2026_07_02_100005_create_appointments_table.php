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
        Schema::create('appointments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('patient_id')->constrained('users');
            $table->foreignId('doctor_id')->nullable()->constrained('doctors');
            $table->date('date');
            $table->enum('shift', ['morning', 'afternoon']);
            $table->enum('type', ['online', 'offline'])->default('online');
            $table->text('reason');
            // Trạng thái: 0: Chờ duyệt, 1: Đã duyệt, 2: Đang chờ khám, 3: Đang khám, 4: Chờ thanh toán, 5: Hoàn thành, 6: Đã hủy
            $table->tinyInteger('status')->default(0); 
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('appointments');
    }
};
