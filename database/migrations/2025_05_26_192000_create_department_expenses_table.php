<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('department_expenses', function (Blueprint $table) {
            $table->id();
            $table->string('department');
            $table->foreignId('mission_id')->constrained()->onDelete('cascade');
            $table->foreignId('payment_id')->nullable()->constrained()->onDelete('set null');
            $table->decimal('amount', 10, 2);
            $table->string('type'); // 'allowance', 'transport', 'additional'
            $table->text('description')->nullable();
            $table->timestamps();

            // Add foreign key for department
            $table->foreign('department')
                  ->references('department')
                  ->on('department_settings')
                  ->onDelete('cascade');
        });
    }

    public function down()
    {
        Schema::dropIfExists('department_expenses');
    }
}; 