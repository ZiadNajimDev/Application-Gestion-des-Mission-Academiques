<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->enum('role', ['enseignant', 'directeur', 'chef_departement', 'comptable'])
                  ->default('enseignant')
                  ->after('email');
            
            // Add other fields for users
            $table->string('cin')->nullable();
            $table->string('phone')->nullable();
            $table->string('department')->nullable();
        });
    }

    public function down()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('role');
            $table->dropColumn('cin');
            $table->dropColumn('phone');
            $table->dropColumn('department');
        });
    }
};