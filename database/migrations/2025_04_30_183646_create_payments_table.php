<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('payments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('mission_id')->constrained()->onDelete('cascade');
            $table->foreignId('user_id')->nullable()->constrained(); // accountant who processed it
            $table->decimal('allowance_amount', 10, 2)->default(0); // indemnités journalières
            $table->decimal('transport_amount', 10, 2)->default(0); // frais de transport
            $table->decimal('total_amount', 10, 2)->default(0);
            $table->enum('status', ['pending', 'paid'])->default('pending');
            $table->enum('payment_method', ['virement', 'cheque', 'especes'])->nullable();
            $table->string('payment_reference')->nullable(); // numéro de chèque ou virement
            $table->date('payment_date')->nullable();
            $table->text('comments')->nullable();
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('payments');
    }
};