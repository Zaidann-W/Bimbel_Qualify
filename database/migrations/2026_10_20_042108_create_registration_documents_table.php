<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
{
    Schema::create('registration_documents', function (Blueprint $table) {
        $table->id();
        $table->foreignId('registration_id')->constrained()->cascadeOnDelete();
        $table->string('type')->default('REPORT_CARD');
        $table->string('path');                              // lokasi file di storage privat
        $table->string('original_name');
        $table->string('mime_type', 100);
        $table->unsignedInteger('size');                     // dalam byte
        $table->timestamps();
    });
}

public function down(): void
{
    Schema::dropIfExists('registration_documents');
}
};
