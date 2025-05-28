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
        Schema::table('missions', function (Blueprint $table) {
            $table->dateTime('chef_approval_date')->nullable()->after('status');
            $table->text('chef_comments')->nullable()->after('chef_approval_date');
            $table->dateTime('director_approval_date')->nullable()->after('chef_comments');
            $table->text('director_comments')->nullable()->after('director_approval_date');
            $table->text('rejection_reason')->nullable()->after('director_comments');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('missions', function (Blueprint $table) {
            $table->dropColumn([
                'chef_approval_date',
                'chef_comments',
                'director_approval_date',
                'director_comments',
                'rejection_reason'
            ]);
        });
    }
};