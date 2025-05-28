
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('missions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->enum('type', ['nationale', 'internationale']);
            $table->enum('transport_type', ['voiture', 'transport_public', 'train', 'avion']);
            $table->date('start_date');
            $table->date('end_date');
            $table->string('destination_city');
            $table->string('destination_institution');
            $table->string('title');
            $table->text('objective');
            $table->string('supervisor_name')->nullable();
            $table->string('additional_documents')->nullable();
            $table->enum('status', ['soumise', 'validee_chef', 'validee_directeur', 'billet_reserve', 'terminee', 'rejetee'])->default('soumise');
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('missions');
    }
};