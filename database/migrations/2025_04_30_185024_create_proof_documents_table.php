<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('proof_documents', function (Blueprint $table) {
            $table->id();
            $table->foreignId('mission_id')->constrained()->onDelete('cascade');
            $table->foreignId('user_id')->nullable()->constrained(); // user who uploaded it
            $table->enum('status', ['pending', 'approved', 'rejected'])->default('pending');
            $table->enum('type', ['transport', 'hotel', 'conference', 'other'])->default('other');
            $table->string('title');
            $table->text('description')->nullable();
            $table->decimal('amount', 10, 2)->nullable();
            $table->string('document_path');
            $table->string('reviewer_id')->nullable(); // accountant who reviewed it
            $table->text('reviewer_comment')->nullable();
            $table->dateTime('reviewed_at')->nullable();
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('proof_documents');
    }
};