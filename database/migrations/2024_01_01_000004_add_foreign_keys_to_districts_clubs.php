<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::table('districts', function (Blueprint $table) {
            $table->foreign('chairperson_id')->references('id')->on('members')->onDelete('set null');
        });
        Schema::table('clubs', function (Blueprint $table) {
            $table->foreign('president_id')->references('id')->on('members')->onDelete('set null');
            $table->foreign('secretary_id')->references('id')->on('members')->onDelete('set null');
            $table->foreign('treasurer_id')->references('id')->on('members')->onDelete('set null');
        });
    }
    public function down(): void {
        Schema::table('districts', function (Blueprint $table) {
            $table->dropForeign(['chairperson_id']);
        });
        Schema::table('clubs', function (Blueprint $table) {
            $table->dropForeign(['president_id']);
            $table->dropForeign(['secretary_id']);
            $table->dropForeign(['treasurer_id']);
        });
    }
};
