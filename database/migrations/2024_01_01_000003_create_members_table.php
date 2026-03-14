<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('members', function (Blueprint $table) {
            $table->id();
            $table->string('member_id')->unique();
            $table->foreignId('user_id')->nullable()->constrained()->onDelete('set null');
            $table->foreignId('club_id')->nullable()->constrained()->onDelete('set null');
            $table->foreignId('district_id')->nullable()->constrained()->onDelete('set null');
            $table->string('name_zh');
            $table->string('name_en')->nullable();
            $table->string('profile_photo')->nullable();
            $table->string('phone')->nullable();
            $table->string('email')->nullable();
            $table->string('line_id')->nullable();
            $table->text('address')->nullable();
            $table->string('city')->nullable();
            $table->string('profession')->nullable();
            $table->string('company')->nullable();
            $table->enum('membership_type', ['regular', 'honorary', 'associate', 'corporate'])->default('regular');
            $table->date('join_date')->nullable();
            $table->boolean('is_active')->default(true);
            $table->text('bio')->nullable();
            $table->string('emergency_contact_name')->nullable();
            $table->string('emergency_contact_phone')->nullable();
            $table->timestamps();
            $table->softDeletes();
            $table->index(['club_id', 'district_id']);
            $table->index('is_active');
        });
    }
    public function down(): void { Schema::dropIfExists('members'); }
};
