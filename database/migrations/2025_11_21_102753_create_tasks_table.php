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
        Schema::create('tasks', function (Blueprint $table) {
             $table->id();
        $table->foreignId('user_id')->constrained()->onDelete('cascade'); // ربط المهمة بالمستخدم
        $table->string('title'); // عنوان المهمة
        $table->text('description')->nullable(); // وصف المهمة (اختياري)
        $table->boolean('is_done')->default(false); // حالة الإنجاز
        $table->date('due_date')->nullable(); // تاريخ الاستحقاق
        $table->timestamps(); // created_at و updated_at
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tasks');
    }
};
