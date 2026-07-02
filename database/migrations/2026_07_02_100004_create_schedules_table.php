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
        Schema::create('schedules', function (Blueprint $table) {
            $table->id();
            $table->foreignId('doctor_id')->constrained('doctors')->onDelete('cascade');
            $table->tinyInteger('day_of_week'); // 0: Chủ nhật, 1: Thứ 2... 6: Thứ 7
            $table->enum('shift', ['morning', 'afternoon']); // Đặt theo ca
            $table->integer('max_patients_per_slot')->default(20); // Giới hạn ca
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('schedules');
    }
};
