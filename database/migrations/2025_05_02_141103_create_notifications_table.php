<?php
// database/migrations/[timestamp]_create_notifications_table.php


use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('notifications', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('title');
            $table->text('message');
            $table->string('type')->default('primary'); // primary, success, warning, danger, info
            $table->string('icon')->default('bell');
            $table->string('link')->nullable();
            $table->boolean('read')->default(false);
            $table->string('related_id')->nullable(); // mission_id, reservation_id, etc.
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('notifications');
    }
};