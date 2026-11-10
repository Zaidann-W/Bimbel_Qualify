<?php

namespace App\Http\Controllers;

use App\Http\Requests\PendaftaranPembayaranRequest;
use App\Models\Application;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class PendaftaranPembayaranController extends Controller
{
    /**
     * Show the payment page for an approved application.
     */
    public function show(string $kode): View|RedirectResponse
    {
        $application = Application::where('registration_number', $kode)->firstOrFail();

        if ($application->status !== 'diterima') {
            abort(403, 'Pendaftaran belum disetujui admin.');
        }

        if ($application->payment_status === 'lunas') {
            abort(403, 'Pembayaran sudah lunas.');
        }

        return view('pendaftaran.bayar', [
            'application' => $application,
        ]);
    }

    /**
     * Store payment submission (optional bukti transfer).
     */
    public function store(PendaftaranPembayaranRequest $request, string $kode): RedirectResponse
    {
        $application = Application::where('registration_number', $kode)->firstOrFail();

        if ($application->status !== 'diterima') {
            abort(403, 'Pendaftaran belum disetujui admin.');
        }

        if ($application->payment_status === 'lunas') {
            abort(403, 'Pembayaran sudah lunas.');
        }

        $data = [
            'payment_status' => 'menunggu_verifikasi',
        ];

        if ($request->hasFile('bukti_transfer')) {
            $path = $request->file('bukti_transfer')->store('applications/payment-proofs', 'public');
            $data['payment_proof_path'] = $path;
        }

        $application->update($data);

        return redirect()->route('pendaftaran.bayar', $kode)
            ->with('success', 'Bukti pembayaran telah dikirim. Silakan tunggu verifikasi admin.');
    }
}
