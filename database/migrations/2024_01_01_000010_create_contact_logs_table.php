<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('contact_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('sender_id')->constrained('members')->onDelete('cascade');
            $table->foreignId('receiver_id')->constrained('members')->onDelete('cascade');
            $table->enum('method', ['call', 'sms', 'line', 'email']);
            $table->json('metadata')->nullable();
            $table->timestamps();
            $table->index(['sender_id', 'created_at']);
            $table->index(['receiver_id', 'created_at']);
        });
    }
    public function down(): void { Schema::dropIfExists('contact_logs'); }
};
