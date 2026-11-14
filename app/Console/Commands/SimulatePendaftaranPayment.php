<?php

namespace App\Console\Commands;

use App\Models\Application;
use App\Services\XenditInvoiceService;
use Illuminate\Console\Command;

class SimulatePendaftaranPayment extends Command
{
    /**
     * @var string
     */
    protected $signature = 'pendaftaran:simulate-paid
                            {kode : Nomor registrasi pendaftaran, contoh REG-2026-0003}
                            {--sync : Ambil status sebenarnya dari Xendit alih-alih menandai lunas}';

    /**
     * @var string
     */
    protected $description = 'Tandai pembayaran pendaftaran sebagai lunas untuk keperluan pengujian lokal';

    /**
     * Jalankan perintah.
     */
    public function handle(): int
    {
        $kode = strtoupper((string) $this->argument('kode'));

        $application = Application::where('registration_number', $kode)->first();

        if (! $application) {
            $this->error("Pendaftaran {$kode} tidak ditemukan.");

            return self::FAILURE;
        }

        if ($this->option('sync')) {
            $terverifikasi = app(XenditInvoiceService::class)->sinkronkanPendaftaran($application, true);

            if (! $terverifikasi) {
                $this->warn('Status invoice Xendit belum menunjukkan pembayaran berhasil.');

                return self::FAILURE;
            }
        } else {
            app(XenditInvoiceService::class)->tandaiLunas($application, 'simulasi lokal');
        }

        $application->refresh();

        $this->info("Pendaftaran {$kode} kini berstatus: {$application->payment_status}.");
        $this->line('Buka: '.route('pendaftaran.buatAkun', $kode));

        return self::SUCCESS;
    }
}
