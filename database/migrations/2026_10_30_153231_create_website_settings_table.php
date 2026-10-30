<?php

use App\Models\WebsiteSetting;
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
        Schema::create('website_settings', function (Blueprint $table) {
            $table->id();
            $table->string('key', 50)->unique();
            $table->text('value')->nullable();
            $table->string('description')->nullable();
            $table->timestamps();
        });

        // Insert default website settings
        WebsiteSetting::insert([
            ['key' => 'nama_lembaga', 'value' => 'BIMBEL QUALIFY', 'description' => 'Nama Lembaga Pendidikan'],
            ['key' => 'slogan', 'value' => 'Bimbingan Belajar Menuju Kampus Impian & PTN Favorit', 'description' => 'Slogan / Tagline Publik'],
            ['key' => 'whatsapp', 'value' => '+62 812-4455-8891', 'description' => 'Nomor WhatsApp CS Publik'],
            ['key' => 'email', 'value' => 'info@bimbelqualify.com', 'description' => 'Email Informasi Publik'],
            ['key' => 'alamat', 'value' => 'Jl. Dayang Sumbi No. 14, Coblong, Kota Bandung', 'description' => 'Alamat Kampus Pusat (Headquarter)'],
        ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('website_settings');
    }
};
