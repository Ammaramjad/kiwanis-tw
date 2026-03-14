<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('communications', function (Blueprint $table) {
            $table->id();
            $table->string('subject');
            $table->text('body');
            $table->enum('channel', ['email', 'line', 'sms', 'internal'])->default('email');
            $table->enum('target_type', ['all', 'district', 'club', 'individual'])->default('all');
            $table->unsignedBigInteger('target_id')->nullable();
            $table->enum('status', ['draft', 'sent', 'failed'])->default('draft');
            $table->unsignedBigInteger('sent_by')->nullable();
            $table->timestamp('sent_at')->nullable();
            $table->json('metadata')->nullable();
            $table->timestamps();
        });
    }
    public function down(): void { Schema::dropIfExists('communications'); }
};
