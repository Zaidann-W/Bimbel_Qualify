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
    Schema::create('materials', function (Blueprint $table) {
        $table->id();
        $table->string('title');
        $table->foreignId('subject_id')->constrained()->restrictOnDelete();
        $table->foreignId('file_id')->constrained('files')->restrictOnDelete();
        $table->foreignId('uploaded_by')->nullable()->constrained('users')->nullOnDelete();
        $table->enum('status', ['draf', 'terbit'])->default('draf')->index();
        $table->boolean('allow_download')->default(false);
        $table->timestamp('published_at')->nullable();
        $table->timestamps();
        $table->softDeletes();
    });

    Schema::create('material_class_groups', function (Blueprint $table) {
        $table->id();
        $table->foreignId('material_id')->constrained()->cascadeOnDelete();
        $table->foreignId('class_group_id')->constrained()->cascadeOnDelete();
        $table->timestamps();
        $table->unique(['material_id', 'class_group_id']);
    });

    Schema::create('qualifix_tickets', function (Blueprint $table) {
        $table->id();
        $table->string('ticket_number', 30)->unique(); // contoh: KPR-0824
        $table->foreignId('student_id')->constrained()->cascadeOnDelete();
        $table->foreignId('subject_id')->constrained()->restrictOnDelete();
        $table->foreignId('class_group_id')->nullable()->constrained()->nullOnDelete();
        $table->string('title');
        $table->text('question');
        $table->string('status', 30)->default('menunggu_tentor')->index(); // menunggu_tentor, sedang_dibahas, selesai
        $table->timestamp('submitted_at')->nullable()->useCurrent();
        $table->timestamp('resolved_at')->nullable();
        $table->timestamps();
    });

    Schema::create('qualifix_assignments', function (Blueprint $table) {
        $table->id();
        $table->foreignId('ticket_id')->constrained('qualifix_tickets')->cascadeOnDelete();
        $table->foreignId('staff_id')->constrained('staff_profiles')->restrictOnDelete();
        $table->foreignId('assigned_by')->nullable()->constrained('users')->nullOnDelete();
        $table->timestamp('assigned_at')->nullable()->useCurrent();
        $table->timestamps();
    });

    Schema::create('qualifix_answers', function (Blueprint $table) {
        $table->id();
        $table->foreignId('ticket_id')->constrained('qualifix_tickets')->cascadeOnDelete();
        $table->foreignId('staff_id')->constrained('staff_profiles')->restrictOnDelete();
        $table->text('body');
        $table->string('status', 20)->default('draf'); // draf / terkirim
        $table->timestamp('sent_at')->nullable();
        $table->timestamps();
    });

    Schema::create('qualifix_attachments', function (Blueprint $table) {
        $table->id();
        $table->foreignId('ticket_id')->constrained('qualifix_tickets')->cascadeOnDelete();
        $table->foreignId('answer_id')->nullable()->constrained('qualifix_answers')->cascadeOnDelete(); // null = lampiran pertanyaan
        $table->foreignId('file_id')->constrained('files')->restrictOnDelete();
        $table->timestamps();
    });

    Schema::create('notification_logs', function (Blueprint $table) {
        $table->id();
        $table->string('recipient_phone', 20);
        $table->string('type', 50); // struk_bayar, ketidakhadiran, tagihan
        $table->unsignedBigInteger('related_id')->nullable();
        $table->string('status', 20)->default('antre'); // antre / terkirim / gagal
        $table->timestamp('sent_at')->nullable();
        $table->timestamps();
        $table->index(['status', 'type']);
    });
}

public function down(): void
{
    Schema::dropIfExists('notification_logs');
    Schema::dropIfExists('qualifix_attachments');
    Schema::dropIfExists('qualifix_answers');
    Schema::dropIfExists('qualifix_assignments');
    Schema::dropIfExists('qualifix_tickets');
    Schema::dropIfExists('material_class_groups');
    Schema::dropIfExists('materials');
}
};
