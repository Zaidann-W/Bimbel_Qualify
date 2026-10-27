<?php

namespace App\Services;

use Illuminate\Support\Str;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

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
        $recentLeaves = DB::table('leave_requests')
            ->latest()
            ->take(3)
            ->get();

        foreach ($recentLeaves as $leave) {
            $activities[] = [
                'type' => 'leave',
                'title' => "Permintaan izin " . ($leave->type ?? '-') . " untuk " . ($leave->student ? $leave->student->user->name : '-'),
                'description' => $leave->reason ?? '-',
                'time' => $leave->created_at->diffForHumans(),
                'status' => $leave->status,
            ];
        }

        // Recent qualifix tickets
        $recentTickets = DB::table('qualifix_tickets')
            ->latest()
            ->take(3)
            ->get();

        foreach ($recentTickets as $ticket) {
            $activities[] = [
                'type' => 'qualifix',
                'title' => "Tiket QualiFix: " . ($ticket->title ?? '-'),
                'description' => Str::limit($ticket->question ?? '-', 50),
                'time' => $ticket->submitted_at->diffForHumans(),
                'status' => $ticket->status,
            ];
        }

        // Recent enrollments
        $recentEnrollments = DB::table('enrollments')
            ->latest('started_on')
            ->take(3)
            ->get();

        foreach ($recentEnrollments as $enrollment) {
            $studentName = $enrollment->student ? ($enrollment->student->user ? $enrollment->student->user->name : 'Tidak diketahui') : 'Tidak diketahui';
            $classCode = $enrollment->classGroup ? $enrollment->classGroup->code : '-';

            $activities[] = [
                'type' => 'enrollment',
                'title' => "Enrollment {$studentName}",
                'description' => "Kelas {$classCode}",
                'time' => $enrollment->started_on->diffForHumans(),
                'status' => $enrollment->status,
            ];
        }

        // Sort by time and take the 5 most recent
        usort($activities, fn($a, $b) => strtotime($b['time']) <=> strtotime($a['time']));

        return array_slice($activities, 0, 5);
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
}