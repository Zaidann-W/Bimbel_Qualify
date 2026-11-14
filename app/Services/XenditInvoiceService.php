<?php

namespace App\Services;

use App\Models\Application;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

/**
 * Pembacaan status invoice Xendit untuk pendaftaran siswa.
 *
 * Callback (webhook) adalah jalur utama, tetapi callback hanya bisa masuk
 * jika aplikasi punya URL publik. Service ini menjadi jaring pengaman:
 * server menanyakan status invoice langsung ke Xendit sehingga status
 * "lunas" tetap terdeteksi walau callback belum sampai.
 */
class XenditInvoiceService
{
    /**
     * Status invoice Xendit yang berarti pembayaran sudah berhasil.
     */
    public const STATUS_LUNAS = ['PAID', 'SETTLED'];

    /**
     * Ambil status terbaru invoice Xendit.
     *
     * @return array<string, mixed>|null Data invoice, atau null bila request gagal.
     */
    public function ambilStatus(string $invoiceId): ?array
    {
        if (blank($invoiceId)) {
            return null;
        }

        try {
            $response = Http::withBasicAuth((string) config('xendit.secret_key'), '')
                ->connectTimeout(8)
                ->timeout(15)
                ->get(config('xendit.base_url').'/'.config('xendit.api_version').'/invoices/'.$invoiceId);
        } catch (\Throwable $exception) {
            Log::warning('Gagal mengambil status invoice Xendit: '.$exception->getMessage(), [
                'invoice_id' => $invoiceId,
            ]);

            return null;
        }

        if ($response->failed()) {
            Log::warning('Gagal mengambil status invoice Xendit', [
                'invoice_id' => $invoiceId,
                'status' => $response->status(),
            ]);

            return null;
        }

        $data = $response->json();

        return is_array($data) ? $data : null;
    }

    /**
     * Cocokkan invoice Xendit dengan pendaftaran siswa.
     *
     * Pencocokan memakai external id yang tersimpan saat link dibuat, lalu
     * jatuh ke nomor registrasi dari external id, dan terakhir ke id invoice.
     *
     * @param  array<string, mixed>  $payload
     */
    public function cariPendaftaranDariPayload(array $payload): ?Application
    {
        $externalId = $payload['external_id'] ?? null;

        if (is_string($externalId) && $externalId !== '') {
            $application = Application::where('xendit_external_id', $externalId)->first();

            if ($application) {
                return $application;
            }

            $application = $this->cariPendaftaranDariExternalId($externalId);

            if ($application) {
                return $application;
            }
        }

        $invoiceId = $payload['id'] ?? null;

        if (is_string($invoiceId) && $invoiceId !== '') {
            return Application::where('xendit_invoice_id', $invoiceId)->first();
        }

        return null;
    }

    /**
     * Ambil pendaftaran dari pola external id "REG-{nomor registrasi}-{kode}".
     */
    public function cariPendaftaranDariExternalId(string $externalId): ?Application
    {
        if (! preg_match('/^REG-(REG-\d{4}-\d{4})-[A-Za-z0-9]+$/', $externalId, $match)) {
            return null;
        }

        return Application::where('registration_number', $match[1])->first();
    }

    /**
     * Tandai pembayaran pendaftaran sebagai lunas.
     */
    public function tandaiLunas(Application $application, ?string $sumber = null): void
    {
        if ($application->payment_status === 'lunas') {
            return;
        }

        $application->update([
            'payment_status' => 'lunas',
            'payment_verified_at' => now(),
        ]);

        Log::info('Pembayaran pendaftaran ditandai lunas', [
            'registration_number' => $application->registration_number,
            'sumber' => $sumber ?? 'sinkronisasi',
        ]);
    }

    /**
     * Sinkronkan status pembayaran pendaftaran dengan status invoice Xendit.
     *
     * Pengecekan dibatasi satu kali per menit per pendaftaran supaya polling
     * dari peramban tidak membanjiri API Xendit.
     *
     * @param  bool  $paksa  Lewati batas waktu pengecekan.
     * @return bool True bila pendaftaran terverifikasi lunas.
     */
    public function sinkronkanPendaftaran(Application $application, bool $paksa = false): bool
    {
        if ($application->payment_status === 'lunas') {
            return true;
        }

        if (blank($application->xendit_invoice_id)) {
            return false;
        }

        if (! $paksa && ! Cache::add($this->kunciSinkronisasi($application), true, 20)) {
            return false;
        }

        $data = $this->ambilStatus($application->xendit_invoice_id);

        if (! $data) {
            // Gagal menjangkau Xendit: lepaskan kunci agar percobaan berikutnya
            // bisa langsung mengulang.
            if (! $paksa) {
                Cache::forget($this->kunciSinkronisasi($application));
            }

            return false;
        }

        $status = $data['status'] ?? null;

        if (! in_array($status, self::STATUS_LUNAS, true)) {
            return false;
        }

        $this->tandaiLunas($application, 'sinkronisasi invoice Xendit');

        return true;
    }

    /**
     * Kunci cache untuk membatasi frekuensi pengecekan status.
     */
    private function kunciSinkronisasi(Application $application): string
    {
        return 'xendit:sinkronisasi-pendaftaran:'.$application->id;
    }
}
