<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateReservationsTable extends Migration
{
    public function up()
    {
        Schema::create('reservations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('mission_id')->constrained()->onDelete('cascade');
            $table->foreignId('user_id')->nullable()->constrained(); // accountant who processed it
            $table->enum('status', ['pending', 'in_progress', 'completed', 'cancelled'])->default('pending');
            $table->enum('type', ['flight', 'train', 'hotel', 'other'])->nullable();
            $table->string('reservation_number')->nullable();
            $table->string('provider')->nullable(); // airline, hotel chain, etc
            $table->decimal('cost', 10, 2)->nullable();
            $table->dateTime('reservation_date')->nullable();
            $table->text('notes')->nullable();
            $table->string('attachment')->nullable(); // path to uploaded reservation PDF
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('reservations');
    }
}