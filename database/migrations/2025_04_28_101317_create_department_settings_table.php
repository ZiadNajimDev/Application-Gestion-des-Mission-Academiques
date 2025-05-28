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
        Schema::create('department_settings', function (Blueprint $table) {
            $table->id();
            $table->string('department')->unique();
            $table->decimal('budget', 10, 2)->default(0);
            $table->text('description')->nullable();
            $table->boolean('director_validation')->default(true);
            $table->boolean('budget_check')->default(true);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('department_settings');
    }
};