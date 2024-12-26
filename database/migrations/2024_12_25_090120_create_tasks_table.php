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
            $table->unsignedBigInteger('bx_id')->unique();
            $table->string('title');
            $table->text('description')->nullable();
            $table->unsignedTinyInteger('priority');
            $table->unsignedBigInteger('created_by');
            $table->timestamp('created_date');
            $table->unsignedBigInteger('responsible_id');
            $table->unsignedBigInteger('closed_by')->nullable();
            $table->timestamp('deadline')->nullable();
            $table->unsignedTinyInteger('status');
            $table->unsignedTinyInteger('sub_status');
            $table->timestamps();

            $table->foreign('created_by')->references('id')->on('bx_users');
            $table->foreign('responsible_id')->references('id')->on('bx_users');
            $table->foreign('closed_by')->references('id')->on('bx_users');
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
