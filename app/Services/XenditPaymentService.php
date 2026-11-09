<?php

namespace App\Services;

use App\Models\Invoice;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Http;

class XenditPaymentService
{
    /**
     * Buat invoice di Xendit dan simpan referensinya ke tagihan lokal.
     *
     * Secret key Xendit hanya dibaca di sisi server; frontend tidak pernah
     * menyentuh kunci ini.
     *
     * @return array{
     *     invoice_url: string|null,
     *     xendit_invoice_id: string|null,
     *     external_id: string,
     *     expired_at: Carbon|null
     * }
     */
    public function createInvoice(Invoice $invoice): array
    {
        $externalId = $this->generateExternalId($invoice);

        $response = Http::withBasicAuth(config('xendit.secret_key'), '')
            ->timeout(30)
            ->post(config('xendit.base_url').'/'.config('xendit.api_version').'/invoices', [
                'external_id' => $externalId,
                'amount' => (float) $invoice->amount,
                'payer_email' => $invoice->student?->user?->email ?? '',
                'description' => $invoice->description ?? 'Tagihan '.$invoice->invoice_number,
                'invoice_duration' => 86400,
                'callback_url' => config('xendit.callback_url'),
                'redirect_url' => config('xendit.redirect_url'),
            ]);

        if ($response->failed()) {
            throw new \RuntimeException(
                'Gagal membuat invoice Xendit: '.$response->body(),
                $response->status()
            );
        }

        $data = $response->json();

        $invoice->update([
            'xendit_invoice_id' => $data['id'] ?? null,
            'xendit_invoice_url' => $data['invoice_url'] ?? null,
            'external_id' => $externalId,
            'expired_at' => $data['expiry_date'] ?? null,
        ]);

        return [
            'invoice_url' => $data['invoice_url'] ?? null,
            'xendit_invoice_id' => $data['id'] ?? null,
            'external_id' => $externalId,
            'expired_at' => $invoice->fresh()->expired_at,
        ];
    }

    private function generateExternalId(Invoice $invoice): string
    {
        return $invoice->invoice_number.'-'.$invoice->id;
    }
}
