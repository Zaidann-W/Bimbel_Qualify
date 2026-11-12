<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AcademicPeriod;
use App\Models\CashAccount;
use App\Models\CashSession;
use App\Models\Expense;
use App\Models\Invoice;
use App\Models\Payment;
use App\Models\WebsiteSetting;
use Illuminate\Database\Query\Builder as QueryBuilder;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class KeuanganController extends Controller
{
    /**
     * Tampilkan pusat keuangan, arus kas, tagihan SPP, dan transaksi (data dari DB).
     */
    public function index(Request $request): View
    {
        $arah = in_array($request->string('arah')->toString(), ['masuk', 'keluar'], true)
            ? $request->string('arah')->toString()
            : 'semua';
        $kata = trim($request->string('q')->toString());

        $transaksi = $this->daftarTransaksi($arah, $kata);

        // Angka pokok arus kas dari pembayaran berhasil & buku pengeluaran.
        $jumlahMasuk = Payment::where('status', 'berhasil')->count();
        $totalMasuk = (float) Payment::where('status', 'berhasil')->sum('amount');
        $jumlahKeluar = Expense::count();
        $totalKeluar = (float) Expense::sum('amount');
        $saldoBersih = $totalMasuk - $totalKeluar;

        $tabs = [
            ['key' => 'semua', 'label' => 'Semua', 'jumlah' => $jumlahMasuk + $jumlahKeluar],
            ['key' => 'masuk', 'label' => 'Pemasukan', 'jumlah' => $jumlahMasuk],
            ['key' => 'keluar', 'label' => 'Pengeluaran', 'jumlah' => $jumlahKeluar],
        ];

        $tagihan = Invoice::with('student')
            ->whereNotIn('status', ['lunas', 'batal'])
            ->latest('due_date')
            ->limit(5)
            ->get()
            ->map(fn ($invoice) => [
                'nama' => $invoice->student->full_name ?? '-',
                'kelas' => trim((string) ($invoice->student->education_level ?? '').' '.($invoice->student->school_grade ?? '')),
                'nominal' => (float) $invoice->amount,
                'tempo' => $invoice->due_date?->diffForHumans() ?? '-',
            ]);

        $jatuhTempo = Invoice::whereNotIn('status', ['lunas', 'batal'])
            ->whereDate('due_date', '<=', today())
            ->count();

        return view('admin.keuangan', [
            'transaksi' => $transaksi,
            'tabs' => $tabs,
            'arah' => $arah,
            'kata' => $kata,
            'tagihan' => $tagihan,
            'jatuhTempo' => $jatuhTempo,
            'totalMasuk' => $totalMasuk,
            'totalKeluar' => $totalKeluar,
            'saldoBersih' => $saldoBersih,
            'jumlahMasuk' => $jumlahMasuk,
            'jumlahKeluar' => $jumlahKeluar,
            'akunKas' => $this->akunKas(),
            'sesiAktif' => CashSession::aktif()->with('opener')->latest('opened_at')->first(),
            'sesiTerakhir' => CashSession::where('status', 'ditutup')->with(['opener', 'closer'])->latest('closed_at')->first(),
            'tahunAjaran' => $this->tahunAjaran(),
            'namaLembaga' => WebsiteSetting::pluck('value', 'key')['nama_lembaga'] ?? config('app.name'),
        ]);
    }

    /**
     * Buku transaksi: gabungan pembayaran masuk & pengeluaran kas, diurutkan dari terbaru.
     *
     * @param  string  $arah  semua | masuk | keluar
     * @param  string  $kata  kata kunci pencarian
     */
    private function daftarTransaksi(string $arah, string $kata): LengthAwarePaginator
    {
        $masuk = $this->transaksiMasuk($kata);
        $keluar = $this->transaksiKeluar($kata);

        $query = match ($arah) {
            'masuk' => $masuk,
            'keluar' => $keluar,
            default => $masuk->unionAll($keluar),
        };

        $halaman = $query->orderByDesc('waktu')->paginate(10)->withQueryString();

        $halaman->setCollection($halaman->getCollection()->map(fn (object $baris) => [
            'arah' => $baris->arah,
            'waktu' => Carbon::parse($baris->waktu)->locale('id')->translatedFormat('d M Y, H:i').' WIB',
            'keterangan' => $baris->keterangan ?? '-',
            'pihak' => $baris->pihak ?? '-',
            'metode' => $baris->metode ?? '-',
            'nominal' => (float) $baris->nominal,
            'status' => $baris->status ?? '-',
            'nomor' => $baris->nomor ?? '-',
        ]));

        return $halaman;
    }

    /**
     * Baris pemasukan dari pembayaran yang sudah berhasil.
     */
    private function transaksiMasuk(string $kata): QueryBuilder
    {
        $query = DB::table('payments')
            ->join('invoices', 'invoices.id', '=', 'payments.invoice_id')
            ->leftJoin('students', 'students.id', '=', 'invoices.student_id')
            ->where('payments.status', 'berhasil')
            ->select([
                DB::raw("'masuk' as arah"),
                DB::raw('COALESCE(payments.paid_at, payments.created_at) as waktu'),
                'invoices.description as keterangan',
                'students.full_name as pihak',
                'payments.payment_method as metode',
                'payments.amount as nominal',
                'invoices.status as status',
                'invoices.invoice_number as nomor',
            ]);

        if ($kata !== '') {
            $like = '%'.$kata.'%';
            $query->where(function ($where) use ($like) {
                $where->where('invoices.description', 'like', $like)
                    ->orWhere('invoices.invoice_number', 'like', $like)
                    ->orWhere('students.full_name', 'like', $like)
                    ->orWhere('payments.payment_method', 'like', $like);
            });
        }

        return $query;
    }

    /**
     * Baris pengeluaran dari buku kas.
     */
    private function transaksiKeluar(string $kata): QueryBuilder
    {
        $query = DB::table('expenses')
            ->join('expense_categories', 'expense_categories.id', '=', 'expenses.expense_category_id')
            ->join('cash_accounts', 'cash_accounts.id', '=', 'expenses.cash_account_id')
            ->select([
                DB::raw("'keluar' as arah"),
                'expenses.spent_at as waktu',
                'expenses.description as keterangan',
                'expense_categories.name as pihak',
                'cash_accounts.name as metode',
                'expenses.amount as nominal',
                DB::raw("'tercatat' as status"),
                DB::raw('null as nomor'),
            ]);

        if ($kata !== '') {
            $like = '%'.$kata.'%';
            $query->where(function ($where) use ($like) {
                $where->where('expenses.description', 'like', $like)
                    ->orWhere('expense_categories.name', 'like', $like)
                    ->orWhere('cash_accounts.name', 'like', $like);
            });
        }

        return $query;
    }

    /**
     * Daftar akun kas aktif lengkap dengan saldo bukunya.
     *
     * Saldo = saldo awal + pembayaran yang masuk lewat metode akun ini − pengeluaran dari akun ini.
     *
     * @return array<int, array<string, mixed>>
     */
    private function akunKas(): array
    {
        $akun = CashAccount::aktif()->get();
        $metode = $akun->flatMap->paymentMethods()->unique()->values()->all();

        $masukPerMetode = Payment::where('status', 'berhasil')
            ->when($metode !== [], fn ($query) => $query->whereIn('payment_method', $metode))
            ->groupBy('payment_method')
            ->selectRaw('payment_method, SUM(amount) as total')
            ->pluck('total', 'payment_method');

        $keluarPerAkun = Expense::groupBy('cash_account_id')
            ->selectRaw('cash_account_id, SUM(amount) as total')
            ->pluck('total', 'cash_account_id');

        return $akun->map(function (CashAccount $akun) use ($masukPerMetode, $keluarPerAkun) {
            $pemasukan = collect($akun->paymentMethods())
                ->sum(fn (string $metodeAkun) => (float) ($masukPerMetode[$metodeAkun] ?? 0));

            return [
                'nama' => $akun->name,
                'keterangan' => $akun->account_number
                    ? 'Rek. '.$akun->account_number
                    : ($akun->bank_name ?: ucfirst($akun->type)),
                'saldo' => (float) $akun->opening_balance + $pemasukan - (float) ($keluarPerAkun[$akun->id] ?? 0),
            ];
        })->toArray();
    }

    /**
     * Tahun ajaran aktif dari tabel academic_periods.
     */
    private function tahunAjaran(): string
    {
        return AcademicPeriod::active()->value('academic_year')
            ?? AcademicPeriod::latest('starts_on')->value('academic_year')
            ?? now()->year.'/'.(now()->year + 1);
    }
}
