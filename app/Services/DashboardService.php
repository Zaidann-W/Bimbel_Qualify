<?php

namespace App\Services;

use App\Models\AcademicPeriod;
use App\Models\AdmissionBatch;
use App\Models\Application;
use App\Models\ClassGroup;
use App\Models\ClassSession;
use App\Models\Enrollment;
use App\Models\Invoice;
use App\Models\LeaveRequest;
use App\Models\Payment;
use App\Models\QualifixTicket;
use App\Models\Student;
use App\Models\StudentAttendance;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class DashboardService
{
    /**
     * Get the count of active users by role.
     */
    public function activeUserCounts(): array
    {
        return DB::table('users')
            ->select('role', DB::raw('count(*) as total'))
            ->where('status', 'aktif')
            ->groupBy('role')
            ->pluck('total', 'role')
            ->toArray();
    }

    /**
     * Get the count of active students.
     */
    public function activeStudentCount(): int
    {
        return DB::table('students')
            ->where('status', 'aktif')
            ->count();
    }

    /**
     * Get the count of active guardians.
     */
    public function activeGuardianCount(): int
    {
        return DB::table('student_guardians')
            ->where('status', 'aktif')
            ->count();
    }

    /**
     * Get the count of active staff/tentor profiles.
     */
    public function activeStaffCount(): int
    {
        return DB::table('staff_profiles')
            ->where('status', 'aktif')
            ->count();
    }

    /**
     * Get the count of today's enrollments.
     */
    public function todayEnrollmentCount(): int
    {
        $today = Carbon::today()->toDateString();

        return DB::table('enrollments')
            ->whereDate('started_on', '<=', $today)
            ->where(function ($query) use ($today) {
                $query->whereDate('ended_on', '>=', $today)
                    ->orWhere('ended_on', null);
            })
            ->count();
    }

    /**
     * Get the count of active enrollments.
     */
    public function activeEnrollmentCount(): int
    {
        return DB::table('enrollments')
            ->where('status', 'aktif')
            ->count();
    }

    /**
     * Get the count of class sessions today.
     */
    public function todayClassSessionsCount(): int
    {
        $today = Carbon::today()->toDateString();

        return DB::table('class_sessions')
            ->whereDate('session_date', $today)
            ->count();
    }

    /**
     * Get the count of students present today.
     */
    public function todayStudentAttendanceCount(): int
    {
        $today = Carbon::today()->toDateString();

        return DB::table('student_attendance')
            ->whereDate('created_at', $today)
            ->where('status', 'hadir')
            ->count();
    }

    /**
     * Get the count of tutors present today.
     */
    public function todayTutorAttendanceCount(): int
    {
        $today = Carbon::today()->toDateString();

        return DB::table('tutor_attendance')
            ->whereDate('check_in_at', $today)
            ->where('status', 'hadir')
            ->count();
    }

    /**
     * Get the count of pending leave requests.
     */
    public function pendingLeaveRequestCount(): int
    {
        return DB::table('leave_requests')
            ->where('status', 'menunggu')
            ->count();
    }

    /**
     * Get the count of active qualifix tickets.
     */
    public function activeQualifixTicketCount(): int
    {
        return DB::table('qualifix_tickets')
            ->where('status', 'menunggu_tentor')
            ->count();
    }

    /**
     * Get the count of today's qualifix submissions.
     */
    public function todayQualifixCount(): int
    {
        $today = Carbon::today()->toDateString();

        return DB::table('qualifix_tickets')
            ->whereDate('submitted_at', $today)
            ->count();
    }

    /**
     * Get the count of branches.
     */
    public function branchCount(): int
    {
        return DB::table('branches')->count();
    }

    /**
     * Get the count of active academic periods.
     */
    public function activeAcademicPeriodCount(): int
    {
        return DB::table('academic_periods')
            ->where('is_active', true)
            ->count();
    }

    /**
     * Get the count of students by education level.
     */
    public function studentsByEducationLevel(): array
    {
        return DB::table('students')
            ->select('education_level', DB::raw('count(*) as total'))
            ->where('status', 'aktif')
            ->groupBy('education_level')
            ->pluck('total', 'education_level')
            ->toArray();
    }

    /**
     * Get the count of students by grade level.
     */
    public function studentsByGrade(): array
    {
        return DB::table('students')
            ->select('school_grade', DB::raw('count(*) as total'))
            ->where('status', 'aktif')
            ->groupBy('school_grade')
            ->pluck('total', 'school_grade')
            ->toArray();
    }

    /**
     * Get the count of pending registrations.
     */
    public function pendingRegistrationCount(): int
    {
        return DB::table('applications')
            ->where('status', 'menunggu_review')
            ->count();
    }

    /**
     * Get the count of accepted registrations.
     */
    public function acceptedRegistrationCount(): int
    {
        return DB::table('applications')
            ->where('status', 'diterima')
            ->count();
    }

    /**
     * Get the count of rejected registrations.
     */
    public function rejectedRegistrationCount(): int
    {
        return DB::table('applications')
            ->where('status', 'ditolak')
            ->count();
    }

    /**
     * Get recent activities/logs for the dashboard.
     */
    public function recentActivities(): array
    {
        $activities = [];

        // Recent leave requests
        $recentLeaves = LeaveRequest::with('student')
            ->latest()
            ->take(3)
            ->get();

        foreach ($recentLeaves as $leave) {
            $activities[] = [
                'type' => 'leave',
                'title' => 'Permintaan izin '.($leave->type ?? '-').' untuk '.($leave->student?->full_name ?? '-'),
                'description' => $leave->reason ?? '-',
                'time' => $leave->created_at?->diffForHumans(),
                'timestamp' => $leave->created_at?->getTimestamp(),
                'status' => $leave->status,
            ];
        }

        // Recent qualifix tickets
        $recentTickets = QualifixTicket::latest()
            ->take(3)
            ->get();

        foreach ($recentTickets as $ticket) {
            $waktu = $ticket->submitted_at ?? $ticket->created_at;

            $activities[] = [
                'type' => 'qualifix',
                'title' => 'Tiket QualiFix: '.($ticket->title ?? '-'),
                'description' => Str::limit($ticket->question ?? '-', 50),
                'time' => $waktu?->diffForHumans(),
                'timestamp' => $waktu?->getTimestamp(),
                'status' => $ticket->status,
            ];
        }

        // Recent enrollments
        $recentEnrollments = Enrollment::with(['student', 'classGroup'])
            ->latest('started_on')
            ->take(3)
            ->get();

        foreach ($recentEnrollments as $enrollment) {
            $activities[] = [
                'type' => 'enrollment',
                'title' => 'Enrollment '.($enrollment->student?->full_name ?? 'Tidak diketahui'),
                'description' => 'Kelas '.($enrollment->classGroup?->code ?? '-'),
                'time' => $enrollment->started_on?->diffForHumans(),
                'timestamp' => $enrollment->started_on?->getTimestamp(),
                'status' => $enrollment->status,
            ];
        }

        // Sort by time and take the 5 most recent
        usort($activities, fn ($a, $b) => ($b['timestamp'] ?? 0) <=> ($a['timestamp'] ?? 0));

        return array_map(
            fn (array $row): array => array_diff_key($row, ['timestamp' => true]),
            array_slice($activities, 0, 5),
        );
    }

    /**
     * Get the total amount of unpaid invoices.
     */
    public function totalUnpaidAmount(): float
    {
        try {
            return DB::table('invoices')
                ->where('status', 'belum_bayar')
                ->sum('amount') ?? 0.0;
        } catch (\Exception $e) {
            // Tabel invoices mungkin belum ada di database
            return 0.0;
        }
    }

    /**
     * Ringkasan dashboard admin, seluruhnya dibaca dari database.
     *
     * @return array{
     *     stats: array<int, array<string, mixed>>,
     *     registrations: array<int, array<string, mixed>>,
     *     sessions: array{sore: array<int, array<string, mixed>>, malam: array<int, array<string, mixed>>},
     *     cash: array<string, mixed>,
     *     qualifix: array<string, mixed>,
     *     agenda: array<string, mixed>
     * }
     */
    public function adminDashboard(): array
    {
        $today = Carbon::today();

        $pendingCount = Application::whereIn('status', ['menunggu_review', 'perlu_perbaikan'])->count();
        $attendance = $this->todayAttendanceRate();
        $sessions = $this->todaySessions($today);
        $cash = $this->todayCash($today);
        $qualifix = $this->todayQualifix();

        return [
            'stats' => [
                [
                    'icon' => 'users',
                    'tile' => 'bg-sand',
                    'edge' => 'border-t-gold',
                    'badge' => $this->newStudentsThisMonth().' Siswa Baru',
                    'badgeClass' => 'bg-palegold text-bark-700',
                    'label' => 'Total Siswa Aktif',
                    'value' => number_format($this->activeStudentCount(), 0, ',', '.'),
                    'note' => ClassGroup::where('status', 'aktif')->count().' Rombel Kelas Aktif',
                ],
                [
                    'icon' => 'wallet',
                    'tile' => 'bg-palegold',
                    'edge' => 'border-t-gold',
                    'badge' => $pendingCount > 0 ? 'Butuh Respon' : 'Bersih',
                    'badgeClass' => 'bg-palegold text-bark-700',
                    'label' => 'Menunggu Verifikasi',
                    'value' => $pendingCount.' Calon',
                    'note' => Application::whereDate('submitted_at', $today)->count().' pendaftar masuk hari ini',
                ],
                [
                    'icon' => 'clipboard-check',
                    'tile' => 'bg-sand',
                    'edge' => 'border-t-espresso',
                    'badge' => $sessions['live'] > 0 ? 'Sesi Berjalan' : 'Belum Mulai',
                    'badgeClass' => 'bg-sand text-ink',
                    'label' => 'Kehadiran Hari Ini',
                    'value' => $attendance['rate'] === null ? '—' : $attendance['rate'].'%',
                    'note' => $attendance['total'].' sesi ('.count($sessions['sore']).' Sore, '.count($sessions['malam']).' Malam)',
                ],
                [
                    'icon' => 'credit-card',
                    'tile' => 'bg-sand',
                    'edge' => 'border-t-espresso',
                    'badge' => 'Kasir Buka',
                    'badgeClass' => 'bg-espresso text-white',
                    'label' => 'Pemasukan Kas Hari Ini',
                    'value' => $this->formatRupiah($cash['income']),
                    'note' => 'Dari '.$cash['sppCount'].' SPP & '.$cash['registrationCount'].' Registrasi',
                ],
            ],
            'registrations' => $this->latestRegistrations(),
            'registrationTotal' => $pendingCount,
            'sessions' => $sessions,
            'cash' => $cash,
            'qualifix' => $qualifix,
            'agenda' => $this->agenda($today),
        ];
    }

    private function newStudentsThisMonth(): int
    {
        return Student::where('status', 'aktif')
            ->whereMonth('created_at', Carbon::now()->month)
            ->whereYear('created_at', Carbon::now()->year)
            ->count();
    }

    /**
     * @return array{rate: float|null, total: int, hadir: int}
     */
    private function todayAttendanceRate(): array
    {
        $counts = StudentAttendance::query()
            ->join('class_sessions', 'class_sessions.id', '=', 'student_attendance.class_session_id')
            ->whereDate('class_sessions.session_date', Carbon::today())
            ->where('class_sessions.status', '!=', 'dibatalkan')
            ->selectRaw("sum(case when student_attendance.status = 'hadir' then 1 else 0 end) as hadir")
            ->selectRaw('count(*) as total')
            ->first();

        $total = (int) ($counts->total ?? 0);
        $hadir = (int) ($counts->hadir ?? 0);

        return [
            'rate' => $total > 0 ? round($hadir / $total * 100, 1) : null,
            'total' => $total,
            'hadir' => $hadir,
        ];
    }

    /**
     * @return array{sore: array<int, array<string, mixed>>, malam: array<int, array<string, mixed>>, live: int}
     */
    private function todaySessions(Carbon $today): array
    {
        $sessions = ClassSession::with(['subject', 'staff', 'room', 'classGroup'])
            ->whereDate('session_date', $today)
            ->orderBy('start_time')
            ->get();

        $presence = StudentAttendance::whereIn('class_session_id', $sessions->pluck('id'))
            ->select('class_session_id', 'status', DB::raw('count(*) as total'))
            ->groupBy('class_session_id', 'status')
            ->get()
            ->groupBy('class_session_id');

        $rows = $sessions->map(function (ClassSession $session) use ($presence): array {
            $stats = $presence->get($session->id, collect());
            $hadir = (int) $stats->where('status', 'hadir')->sum('total');
            $tercatat = (int) $stats->sum('total');
            $kapasitas = Enrollment::where('class_group_id', $session->class_group_id)
                ->where('status', 'aktif')
                ->count();
            $target = max($tercatat, $kapasitas, $hadir);

            $presenceLabel = match ($session->status) {
                'berlangsung' => 'Berlangsung ('.$hadir.'/'.$target.' Hadir)',
                'selesai' => 'Selesai ('.$hadir.'/'.$target.' Hadir)',
                'dibatalkan' => 'Dibatalkan',
                default => 'Siap Mulai (Persiapan Tentor)',
            };

            return [
                'code' => strtoupper($session->subject?->code ?? 'KLS'),
                'tile' => match ($session->subject?->code) {
                    'FIS' => 'bg-[#fae0d8]',
                    'ING' => 'bg-[#e8ede7]',
                    default => 'bg-[#f7e4b4]',
                },
                'subject' => $session->subject?->name ?? 'Mata pelajaran',
                'topic' => $session->topic,
                'grade' => 'Kelas '.($session->classGroup?->grade ?? '-'),
                'live' => $session->status === 'berlangsung',
                'presence' => $presenceLabel,
                'tutor' => $session->staff?->full_name_with_title ?? '-',
                'room' => $session->room?->name ?? '-',
                'sesi' => Carbon::parse($session->start_time)->hour < 16 ? 'sore' : 'malam',
            ];
        });

        return [
            'sore' => $rows->where('sesi', 'sore')->values()->all(),
            'malam' => $rows->where('sesi', 'malam')->values()->all(),
            'live' => $rows->where('live', true)->count(),
        ];
    }

    /**
     * @return array{income: float, expense: float|null, balance: float, sppCount: int, registrationCount: int, paymentsCount: int}
     */
    private function todayCash(Carbon $today): array
    {
        $payments = Payment::whereDate('paid_at', $today)->where('status', 'berhasil');

        $income = (float) (clone $payments)->sum('amount');
        $sppCount = (clone $payments)->whereHas('invoice', fn ($query) => $query->where('type', 'spp_bulanan'))->count();
        $registrationCount = (clone $payments)->whereHas('invoice', fn ($query) => $query->where('type', 'registrasi'))->count();

        return [
            'income' => $income,
            'expense' => null, // Belum ada tabel pengeluaran operasional.
            'balance' => $income,
            'sppCount' => $sppCount,
            'registrationCount' => $registrationCount,
            'paymentsCount' => (clone $payments)->count(),
        ];
    }

    /**
     * @return array{masuk: int, butuhTentor: int, selesai: int, progress: int}
     */
    private function todayQualifix(): array
    {
        $today = Carbon::today();
        $masuk = QualifixTicket::whereDate('submitted_at', $today)->count();
        $butuhTentor = QualifixTicket::where('status', 'menunggu_tentor')->count();
        $selesai = QualifixTicket::whereDate('submitted_at', $today)->where('status', 'selesai')->count();

        return [
            'masuk' => $masuk,
            'butuhTentor' => $butuhTentor,
            'selesai' => $selesai,
            'progress' => $masuk > 0 ? (int) round($selesai / $masuk * 100) : 0,
        ];
    }

    /**
     * @return array{periode: string|null, semester: string|null, berjalan: bool, catatan: string}
     */
    private function agenda(Carbon $today): array
    {
        $period = AcademicPeriod::where('is_active', true)->first();
        $batch = AdmissionBatch::whereDate('closes_at', '>=', $today)
            ->orderBy('closes_at')
            ->first();

        return [
            'periode' => $period
                ? 'Semester '.ucfirst($period->semester).' T.A. '.$period->academic_year
                : 'Belum ada periode aktif',
            'berjalan' => (bool) $period,
            'catatan' => $batch
                ? 'Status Aktif Berjalan. Pendaftaran gelombang '.$batch->wave_number.' berakhir '.Carbon::parse($batch->closes_at)->translatedFormat('d F').'.'
                : 'Status Aktif Berjalan. Belum ada gelombang pendaftaran yang dibuka.',
        ];
    }

    /**
     * @return array<int, array<string, mixed>>
     */
    private function latestRegistrations(): array
    {
        $statusMap = [
            'menunggu_review' => ['Menunggu Review', 'bg-palegold text-bark-700', 'Periksa Berkas', 'bg-espresso text-white hover:bg-bark-800'],
            'perlu_perbaikan' => ['Perlu Perbaikan', 'bg-[#fbe3d9] text-[#9a3f22]', 'Lihat Bukti', 'bg-sand text-ink hover:bg-line'],
            'diterima' => ['Diterima & Terbit NIS', 'bg-sand text-muted', 'Selesai', 'bg-[#f3f1ea] text-bark-300'],
            'ditolak' => ['Ditolak', 'bg-[#fbe3d9] text-[#9a3f22]', 'Selesai', 'bg-[#f3f1ea] text-bark-300'],
        ];

        return Application::whereIn('status', array_keys($statusMap))
            ->orderByDesc('submitted_at')
            ->take(3)
            ->get()
            ->map(function (Application $application) use ($statusMap): array {
                [$label, $statusClass, $action, $actionClass] = $statusMap[$application->status];
                $invoice = $application->student_id
                    ? Invoice::where('student_id', $application->student_id)->latest()->first()
                    : null;

                return [
                    'letter' => strtoupper(Str::substr($application->full_name, 0, 1)),
                    'avatar' => 'bg-palegold',
                    'name' => $application->full_name,
                    'status' => $label,
                    'statusClass' => $statusClass,
                    'meta' => trim(($application->school_name ?? '-').' • Kelas '.($application->school_grade ?? '-')),
                    'fee' => $invoice ? 'Biaya: '.$this->formatRupiah((float) $invoice->amount) : 'Biaya: belum ditagihkan',
                    'feeNote' => $invoice ? '('.$invoice->invoice_number.')' : '',
                    'action' => $action,
                    'actionClass' => $actionClass,
                    'done' => in_array($application->status, ['diterima', 'ditolak'], true),
                ];
            })
            ->all();
    }

    private function formatRupiah(float $amount): string
    {
        return 'Rp '.number_format($amount, 0, ',', '.');
    }
}
