<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('mission_proofs', function (Blueprint $table) {
            $table->foreignId('reviewer_id')->nullable()->constrained('users');
            $table->text('reviewer_comment')->nullable();
            $table->timestamp('reviewed_at')->nullable();
        });
    }

    public function down()
    {
        Schema::table('mission_proofs', function (Blueprint $table) {
            $table->dropForeign(['reviewer_id']);
            $table->dropColumn(['reviewer_id', 'reviewer_comment', 'reviewed_at']);
        });
    }
};