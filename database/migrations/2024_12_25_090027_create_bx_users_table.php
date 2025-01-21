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
        Schema::create('bx_users', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('bx_id')->unique(); // ID из Битрикс
            $table->string('name');
            $table->string('last_name')->nullable();
            $table->string('email')->nullable();
            $table->string('work_position')->nullable();
            $table->foreignId('department_id')->constrained('departments')->onDelete('cascade');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('bx_users');
    }
};
