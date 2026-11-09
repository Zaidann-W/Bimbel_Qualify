<?php

namespace App\Http\Controllers\Ortu;

use App\Http\Controllers\Controller;
use App\Http\Requests\CreateXenditInvoiceRequest;
use App\Models\Guardian;
use App\Models\Invoice;
use App\Services\XenditPaymentService;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;

class XenditInvoiceController extends Controller
{
    public function __construct(private readonly XenditPaymentService $service) {}

    /**
     * Buat invoice Xendit untuk tagihan yang dipilih wali.
     */
    public function store(CreateXenditInvoiceRequest $request): JsonResponse
    {
        $user = Auth::user();
        $invoice = Invoice::findOrFail($request->validated('invoice_id'));

        // Keamanan: pastikan tagihan milik anak wali yang sedang login.
        if (! $this->isFinancialResponsible($user, $invoice)) {
            return response()->json([
                'success' => false,
                'message' => 'Anda tidak berhak mengakses tagihan ini.',
            ], 403);
        }

        // Keamanan: tagihan harus belum dibayar.
        if ($invoice->status !== 'belum_bayar') {
            return response()->json([
                'success' => false,
                'message' => 'Tagihan sudah dibayar atau tidak dapat ditagih ulang.',
            ], 422);
        }

        // Anti-tampering: nominal dari klien harus sama dengan database.
        if ($request->filled('amount') && (float) $request->validated('amount') !== (float) $invoice->amount) {
            return response()->json([
                'success' => false,
                'message' => 'Nominal pembayaran tidak sesuai dengan tagihan.',
            ], 422);
        }

        $result = $this->service->createInvoice($invoice);

        return response()->json([
            'success' => true,
            'data' => $result,
        ]);
    }

    /**
     * Cek apakah user adalah wali yang bertanggung jawab keuangan atas siswa.
     */
    private function isFinancialResponsible($user, Invoice $invoice): bool
    {
        $guardian = Guardian::where('user_id', $user->id)->first();

        return $guardian !== null
            && $guardian->students()
                ->wherePivot('is_financial_responsible', true)
                ->where('students.id', $invoice->student_id)
                ->exists();
    }
}
