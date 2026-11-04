<?php

namespace App\Services;

use App\Models\Guardian;
use App\Models\Invoice;
use App\Models\Payment;
use App\Models\Student;
use Illuminate\Support\Facades\Auth;

class PembayaranService
{
    /**
     * Susun seluruh data halaman pembayaran untuk orang tua/wali.
     *
     * Data diambil dari basis data berdasarkan user yang sedang login (role ortu)
     * → guardian → siswa terkait → tagihan (invoice) dan pembayaran.
     *
     * Bagian payment gateway sengaja tidak diimplementasi di sini; difokuskan
     * hanya pada penampilan data tagihan, riwayat pembayaran, dan filter periode.
     *
     * @param  string|null  $periode  Filter periode (contoh: "april-2025"), null = semua
     * @return array{
     *     guardian: array<string, mixed>|null,
     *     anak: array<int, array<string, mixed>>,
     *     selected_anak: array<string, mixed>|null,
     *     ringkasan: array<string, mixed>,
     *     tagihan: array<int, array<string, mixed>>,
     *     periode_options: array<int, array<string, mixed>>
     * }
     */
    public function pembayaran(?string $periode = null): array
    {
        $user = Auth::user();
        $guardian = null;
        $anak = [];

        try {
            $guardian = Guardian::where('user_id', $user->id)->first();
        } catch (\Exception $e) {
            $guardian = null;
        }

        if ($guardian !== null) {
            $anak = $this->dataAnak($guardian, $periode);
        }

        $selectedAnak = $anak[0] ?? null;
        $ringkasan = $this->ringkasan($anak);
        $tagihan = $this->tagihanSemua($anak);
        $periodeOptions = $this->periodeOptions($anak);

        return [
            'guardian' => $guardian === null ? null : [
                'id' => $guardian->id,
                'name' => $guardian->name,
                'occupation' => $guardian->occupation,
                'phone_wa' => $guardian->phone_wa,
                'email' => $guardian->email,
                'address' => $guardian->address,
            ],
            'anak' => $anak,
            'selected_anak' => $selectedAnak,
            'ringkasan' => $ringkasan,
            'tagihan' => $tagihan,
            'periode_options' => $periodeOptions,
        ];
    }

    /**
     * Dapatkan semua siswa yang terkait dengan guardian beserta tagihannya.
     *
     * @return array<int, array<string, mixed>>
     */
    private function dataAnak(Guardian $guardian, ?string $periode): array
    {
        $students = $guardian->students()->active()->orderBy('full_name')->get();

        return $students->map(fn (Student $siswa) => [
            'id' => $siswa->id,
            'nis' => $siswa->nis,
            'nisn' => $siswa->nisn,
            'nama' => $siswa->full_name,
            'nama_panggilan' => $siswa->nickname ?? $siswa->full_name,
            'inisial' => $siswa->inisial(),
            'sekolah' => $siswa->school_name,
            'kelas' => trim(sprintf('%s %s %s', $siswa->school_grade, $siswa->education_level, $siswa->major)),
            'status' => $siswa->status,
            'email' => $siswa->email,
            'phone_wa' => $siswa->phone_wa,
            'tagihan' => $this->tagihanSiswa($siswa, $periode),
        ])->toArray();
    }

    /**
     * Dapatkan daftar tagihan satu siswa dengan data pembayarannya.
     *
     * @return array<int, array<string, mixed>>
     */
    private function tagihanSiswa(Student $siswa, ?string $periode): array
    {
        $query = Invoice::where('student_id', $siswa->id)
            ->with('payments')
            ->orderByDesc('due_date');

        if ($periode !== null) {
            $query->where('description', 'like', '%'.$periode.'%');
        }

        return $query->get()->map(fn (Invoice $invoice) => $this->formatTagihan($invoice))->toArray();
    }

    /**
     * Ubah satu Invoice menjadi array data tagihan untuk tampilan tabel.
     *
     * @return array<string, mixed>
     */
    private function formatTagihan(Invoice $invoice): array
    {
        $totalBayar = $invoice->payments()->where('status', 'berhasil')->sum('amount');
        $sisa = max(0, (float) $invoice->amount - (float) $totalBayar);
        $lunas = $sisa <= 0;

        $tanggalJatuhTempo = $invoice->due_date?->format('d M Y') ?? '-';
        $tanggalBayar = $invoice->paid_at?->format('d M Y') ?? '-';

        $periodeLabel = $invoice->academicPeriod?->academic_year ?? '-';
        if ($periodeLabel === '-' && $invoice->due_date !== null) {
            $periodeLabel = $invoice->due_date->format('F Y');
        }

        return [
            'id' => $invoice->id,
            'invoice_number' => $invoice->invoice_number,
            'nama' => $invoice->description ?? 'Tagihan '.$invoice->invoice_number,
            'jadwal' => $lunas ? 'Dibayar '.$tanggalBayar : 'Jatuh tempo '.$tanggalJatuhTempo,
            'nominal' => 'Rp '.number_format($invoice->amount, 0, ',', '.'),
            'nominal_raw' => (float) $invoice->amount,
            'status' => $lunas ? 'lunas' : 'belum_lunas',
            'status_label' => $lunas ? 'Lunas' : 'Belum Lunas',
            'tanggal_jatuh_tempo' => $tanggalJatuhTempo,
            'tanggal_bayar' => $tanggalBayar,
            'periode' => $periodeLabel,
            'total_bayar' => 'Rp '.number_format($totalBayar, 0, ',', '.'),
            'sisa' => 'Rp '.number_format($sisa, 0, ',', '.'),
            'aksi' => $lunas ? 'kwitansi' : 'bayar_instan',
            'aksi_label' => $lunas ? 'Kwitansi' : 'Bayar Instan',
        ];
    }

    /**
     * Susun data ringkasan: sisa tagihan dan total sudah terbayar.
     *
     * @return array<string, mixed>
     */
    private function ringkasan(array $anak): array
    {
        $totalTagihan = 0.0;
        $totalTerbayar = 0.0;
        $totalSisa = 0.0;

        foreach ($anak as $a) {
            foreach ($a['tagihan'] as $t) {
                $totalTagihan += (float) $t['nominal_raw'];
                $totalTerbayar += (float) str_replace(['Rp ', '.', ','], ['', '', '.'], $t['total_bayar']);
                $totalSisa += (float) str_replace(['Rp ', '.', ','], ['', '', '.'], $t['sisa']);
            }
        }

        $persenLunas = $totalTagihan === 0 ? 100.0 : round($totalTerbayar / $totalTagihan * 100, 1);

        return [
            'total_tagihan' => 'Rp '.number_format($totalTagihan, 0, ',', '.'),
            'total_terbayar' => 'Rp '.number_format($totalTerbayar, 0, ',', '.'),
            'total_sisa' => 'Rp '.number_format($totalSisa, 0, ',', '.'),
            'sisa_tagihan' => 'Rp '.number_format($totalSisa, 0, ',', '.'),
            'total_tagihan_raw' => $totalTagihan,
            'total_terbayar_raw' => $totalTerbayar,
            'total_sisa_raw' => $totalSisa,
            'persen_lunas' => $persenLunas,
            'status_pelunasan' => number_format($persenLunas, 1, '.', ',').'% Lunas',
        ];
    }

    /**
     * Kumpulkan semua tagihan dari semua anak untuk ditampilkan dalam tabel.
     *
     * @return array<int, array<string, mixed>>
     */
    private function tagihanSemua(array $anak): array
    {
        $kumpulkan = [];

        foreach ($anak as $a) {
            foreach ($a['tagihan'] as $t) {
                $kumpulkan[] = array_merge($t, ['anak' => $a['nama'], 'anak_id' => $a['id']]);
            }
        }

        usort($kumpulkan, fn ($a, $b) => strtotime($b['tanggal_jatuh_tempo'] ?? '1970-01-01') <=> strtotime($a['tanggal_jatuh_tempo'] ?? '1970-01-01'));

        return array_slice($kumpulkan, 0, 50);
    }

    /**
     * Dapatkan opsi filter periode dari tagihan yang ada.
     *
     * @return array<int, array<string, mixed>>
     */
    private function periodeOptions(array $anak): array
    {
        $periode = [];

        foreach ($anak as $a) {
            foreach ($a['tagihan'] as $t) {
                $label = $t['periode'] ?? '-';
                if ($label !== '-' && ! isset($periode[$label])) {
                    $periode[$label] = [
                        'value' => strtolower(str_replace(' ', '-', $label)),
                        'label' => $label,
                    ];
                }
            }
        }

        return array_values($periode);
    }
}
