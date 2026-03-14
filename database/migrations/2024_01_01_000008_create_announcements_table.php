<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('announcements', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->string('title_en')->nullable();
            $table->longText('content');
            $table->longText('content_en')->nullable();
            $table->foreignId('club_id')->nullable()->constrained()->onDelete('set null');
            $table->foreignId('district_id')->nullable()->constrained()->onDelete('set null');
            $table->enum('target', ['all', 'district', 'club', 'public'])->default('all');
            $table->dateTime('publish_date')->nullable();
            $table->dateTime('expiration_date')->nullable();
            $table->boolean('is_pinned')->default(false);
            $table->enum('status', ['draft', 'published', 'archived'])->default('draft');
            $table->json('attachments')->nullable();
            $table->unsignedBigInteger('created_by')->nullable();
            $table->timestamps();
            $table->softDeletes();
            $table->index(['status', 'publish_date']);
        });
    }
    public function down(): void { Schema::dropIfExists('announcements'); }
};
