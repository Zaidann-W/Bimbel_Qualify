<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\LeaveRequest;
use App\Models\StudentAttendance;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class IzinController extends Controller
{
    /**
     * Putuskan pengajuan izin / sakit yang menunggu verifikasi admin.
     *
     * Pengajuan yang disetujui langsung dicatat ke presensi siswa pada sesi
     * terkait supaya angka kehadiran pada halaman absensi ikut sesuai.
     */
    public function update(Request $request, LeaveRequest $leaveRequest): RedirectResponse
    {
        $validated = $request->validate([
            'status' => ['required', Rule::in(['disetujui', 'ditolak'])],
        ]);

        $leaveRequest->update([
            'status' => $validated['status'],
            'reviewed_by' => $request->user()->id,
            'reviewed_at' => now(),
        ]);

        if ($validated['status'] === 'disetujui' && $leaveRequest->class_session_id) {
            StudentAttendance::updateOrCreate(
                [
                    'class_session_id' => $leaveRequest->class_session_id,
                    'student_id' => $leaveRequest->student_id,
                ],
                [
                    'status' => 'izin',
                    'note' => 'Izin disetujui: '.($leaveRequest->type ?: '-'),
                    'recorded_by' => $request->user()->id,
                ]
            );
        }

        return back()->with('status', 'Pengajuan izin berhasil '.$validated['status'].'.');
    }
}
