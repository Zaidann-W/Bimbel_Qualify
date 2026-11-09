<?php

namespace App\Http\Controllers;

use App\Models\Invoice;
use App\Models\Payment;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class XenditWebhookController extends Controller
{
    /**
     * Terima callback/pemberitahuan status pembayaran dari Xendit.
     *
     * Endpoint ini tidak dilindungi oleh sesi auth (Xendit bukan browser).
     * Keasliannya diverifikasi melalui header X-Callback-Token yang cocok
     * dengan XENDIT_CALLBACK_TOKEN di server.
     */
    public function handle(Request $request): JsonResponse
    {
        $token = $request->header('X-Callback-Token');

        if (! $token || ! hash_equals((string) config('xendit.callback_token'), $token)) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized',
            ], 401);
        }

        $payload = $request->all();
        $externalId = $payload['external_id'] ?? null;

        if (! $externalId) {
            return response()->json([
                'success' => false,
                'message' => 'external_id wajib tersedia.',
            ], 400);
        }

        $invoice = Invoice::byExternalId($externalId)->first();

        if (! $invoice) {
            Log::warning('Xendit webhook: invoice tidak ditemukan', [
                'external_id' => $externalId,
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Invoice tidak ditemukan, diabaikan.',
            ], 200);
        }

        // Idempotensi: jangan memproses callback yang sama dua kali.
        if (Payment::where('xendit_external_id', $externalId)->exists()) {
            return response()->json([
                'success' => true,
                'message' => 'Sudah diproses sebelumnya.',
            ], 200);
        }

        $status = $payload['status'] ?? null;
        $transactionId = $payload['id'] ?? $payload['transaction_id'] ?? null;

        match ($status) {
            'PAID' => $invoice->markPaidViaXendit($transactionId, 'PAID', $payload),
            'EXPIRED' => $invoice->markExpiredViaXendit($transactionId, $payload),
            'FAILED' => $invoice->markFailedViaXendit($transactionId, $payload),
            default => Log::info('Xendit webhook: status tidak dikenali', [
                'external_id' => $externalId,
                'status' => $status,
            ]),
        };

        return response()->json([
            'success' => true,
            'message' => 'Webhook diproses.',
        ], 200);
    }
}
