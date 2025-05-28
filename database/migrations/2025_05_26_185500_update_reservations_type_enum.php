<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        // First, modify the column to be a string temporarily
        Schema::table('reservations', function (Blueprint $table) {
            $table->string('type')->change();
        });

        // Then, convert it back to enum with new values
        Schema::table('reservations', function (Blueprint $table) {
            $table->enum('type', ['flight', 'train', 'bus', 'car', 'hotel', 'other'])->change();
        });
    }

    public function down()
    {
        // Revert back to original enum values
        Schema::table('reservations', function (Blueprint $table) {
            $table->string('type')->change();
        });

        Schema::table('reservations', function (Blueprint $table) {
            $table->enum('type', ['flight', 'train', 'hotel', 'other'])->change();
        });
    }
}; 