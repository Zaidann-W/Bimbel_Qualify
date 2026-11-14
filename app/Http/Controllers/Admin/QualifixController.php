<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\QualifixTicket;
use App\Models\StaffProfile;
use App\Services\QualifixService;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\RedirectResponse;

class QualifixController extends Controller
{
    public function __construct(private readonly QualifixService $qualifixService) {}

    /**
     * Tampilkan daftar tiket Klinik PR (QualiFix) yang diambil dari database.
     */
    public function index(Request $request): View
    {
        $filters = [
            'q' => trim((string) $request->query('q', '')),
            'status' => $request->query('status', 'semua'), // semua | menunggu | dibahas | selesai
        ];

        $dbStatus = [
            'menunggu' => 'menunggu_tentor',
            'dibahas' => 'sedang_dibahas',
            'selesai' => 'selesai',
        ];

        $tiket = QualifixTicket::query()
            ->with(['student', 'subject', 'classGroup', 'staff'])
            ->when($filters['q'] !== '', function ($query) use ($filters) {
                $keyword = '%'.$filters['q'].'%';
                $query->where(function ($inner) use ($keyword) {
                    $inner->where('ticket_number', 'like', $keyword)
                        ->orWhere('title', 'like', $keyword)
                        ->orWhereHas('student', fn ($s) => $s->where('full_name', 'like', $keyword))
                        ->orWhereHas('subject', fn ($s) => $s->where('name', 'like', $keyword));
                });
            })
            ->when($filters['status'] !== 'semua', fn ($query) => $query->where('status', $dbStatus[$filters['status']] ?? $filters['status']))
            ->orderByDesc('submitted_at')
            ->orderByDesc('ticket_number')
            ->paginate(10)
            ->withQueryString();

        $dbToView = array_flip($dbStatus);

        $tiket->getCollection()->each(function ($ticket) use ($dbToView): void {
            $ticket->view_status = $dbToView[$ticket->status] ?? $ticket->status;
            $ticket->mapel = $ticket->subject?->name.' ('.$ticket->classGroup?->code.')';
            $ticket->tentor_nama = $ticket->staff->first()?->full_name_with_title ?? null;
        });

        // Angka pokok antrean Klinik PR.
        $kpis = [
            'total' => QualifixTicket::count(),
            'menunggu' => QualifixTicket::where('status', 'menunggu_tentor')->count(),
            'selesai' => QualifixTicket::where('status', 'selesai')->count(),
        ];

        // Ketersediaan tentor pendamping Klinik PR.
        $tentor = StaffProfile::active()
            ->with(['qualificTickets' => fn ($query) => $query->where('status', '!=', 'selesai')])
            ->orderBy('full_name_with_title')
            ->get()
            ->map(fn ($staff) => [
                'id' => $staff->id,
                'nama' => $staff->full_name_with_title,
                'bidang' => $staff->job_title ?? '-',
                'kondisi' => $staff->qualificTickets->isNotEmpty() ? 'membimbing' : 'tersedia',
            ])
            ->values()
            ->all();

        return view('admin.qualifix', [
            'tiket' => $tiket,
            'tentor' => $tentor,
            'filters' => $filters,
            'kpis' => $kpis,
            'per_halaman' => 10,
        ]);
    }

    /**
     * Tetapkan / ganti tentor pendamping untuk sebuah tiket.
     */
    public function assign(QualifixTicket $ticket, Request $request): RedirectResponse
    {
        $request->validate([
            'staff_id' => ['required', 'exists:staff_profiles,id'],
        ]);

        $this->qualifixService->reassign($ticket, (int) $request->input('staff_id'));

        return redirect()->route('admin.qualifix')
            ->with('sukses', 'Tentor pendamping untuk tiket #'.$ticket->ticket_number.' telah diperbarui.');
    }

    /**
     * Perbarui status tiket (admin).
     */
    public function updateStatus(QualifixTicket $ticket, Request $request): RedirectResponse
    {
        $request->validate([
            'status' => ['required', 'in:menunggu_tentor,sedang_dibahas,selesai'],
        ]);

        $this->qualifixService->updateStatus($ticket, $request->input('status'));

        return redirect()->route('admin.qualifix')
            ->with('sukses', 'Status tiket #'.$ticket->ticket_number.' diperbarui.');
    }
}
