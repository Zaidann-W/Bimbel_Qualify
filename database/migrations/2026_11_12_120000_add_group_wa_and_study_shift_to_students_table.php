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
        Schema::table('students', function (Blueprint $table) {
            $table->string('group_wa', 20)->nullable()->after('phone_wa');
            $table->foreignId('study_shift_id')->nullable()->after('group_wa')->constrained('study_shifts')->nullOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('students', function (Blueprint $table) {
            $table->dropForeign(['study_shift_id']);
            $table->dropColumn([
                'group_wa',
                'study_shift_id',
            ]);
        });
    }
};
