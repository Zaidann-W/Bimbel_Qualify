<?php

use App\Models\Testimonial;
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
        Schema::create('testimonials', function (Blueprint $table) {
            $table->id();
            $table->string('inisial', 10);
            $table->string('avatar', 50); // warna avatar seperti: bg-palegold text-espresso, bg-espresso text-white, etc.
            $table->string('nama', 100);
            $table->string('meta', 200); // contoh: 'Alumni 2023 &bull; FK Unpad'
            $table->integer('rating'); // 1-5
            $table->text('kutipan');
            $table->date('diinput');
            $table->enum('kategori', ['Siswa Sukses', 'Wali Murid']);
            $table->boolean('tayang')->default(true);
            $table->boolean('ekstra')->default(false); // testimoni premium/extra
            $table->timestamps();
        });

        // Insert default testimoni data dari view lama
        Testimonial::insert([
            [
                'inisial' => 'NA',
                'avatar' => 'bg-palegold text-espresso',
                'nama' => 'Nabila Amanda, S.Ked',
                'meta' => 'Alumni 2023 &bull; FK Unpad',
                'rating' => 5,
                'kutipan' => 'Metode bedah soal di QUALIFY sangat terstruktur. Pengajar sangat sabar dan menguasai konsep dasar matematika dan biologi hingga bisa tembus FK di pilihan pertama.',
                'diinput' => '2024-07-12',
                'kategori' => 'Siswa Sukses',
                'tayang' => true,
                'ekstra' => false,
            ],
            [
                'inisial' => 'BW',
                'avatar' => 'bg-espresso text-white',
                'nama' => 'Bambang Wicaksono',
                'meta' => 'Wali Murid &bull; ITB STEI',
                'rating' => 4,
                'kutipan' => 'Sebagai orang tua, laporan berkala hasil tryout dan konseling peminatan jurusan sangat membantu kami membimbing anak. Transparan dan profesional.',
                'diinput' => '2024-08-04',
                'kategori' => 'Wali Murid',
                'tayang' => true,
                'ekstra' => false,
            ],
            [
                'inisial' => 'RF',
                'avatar' => 'bg-gold text-espresso',
                'nama' => 'Rizky Fauzan',
                'meta' => 'Alumni 2024 &bull; Teknik Sipil UI',
                'rating' => 4,
                'kutipan' => 'Suasana belajar kondusif dengan fasilitas ruang konsultasi VIP. Mentor sangat supportif hingga detik-detik sebelum ujian UTBK dimulai.',
                'diinput' => '2024-08-29',
                'kategori' => 'Siswa Sukses',
                'tayang' => true,
                'ekstra' => false,
            ],
            [
                'inisial' => 'DP',
                'avatar' => 'bg-sand text-bronze',
                'nama' => 'Dewi Puspita',
                'meta' => 'Wali Murid &bull; SMA Negeri 3 Bandung',
                'rating' => 5,
                'kutipan' => 'Administrasinya rapi, jadwal fleksibel, dan pembayaran SPP bisa dicicil tanpa ribet. Sangat membantu keluarga kami.',
                'diinput' => '2024-09-18',
                'kategori' => 'Wali Murid',
                'tayang' => true,
                'ekstra' => true,
            ],
            [
                'inisial' => 'AY',
                'avatar' => 'bg-bark-700 text-white',
                'nama' => 'Aditya Yoga',
                'meta' => 'Alumni 2024 &bull; Universitas Airlangga',
                'rating' => 5,
                'kutipan' => 'Try out SNBT di QUALIFY punya pembahasan paling detail. Skor saya naik 120 poin dalam dua bulan terakhir.',
                'diinput' => '2024-10-02',
                'kategori' => 'Siswa Sukses',
                'tayang' => false,
                'ekstra' => true,
            ],
        ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('testimonials');
    }
};
