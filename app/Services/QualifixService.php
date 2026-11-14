<?php

namespace App\Services;

use App\Models\QualifixAnswer;
use App\Models\QualifixAttachment;
use App\Models\QualifixTicket;
use App\Models\ScheduleRule;
use App\Models\StaffProfile;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class QualifixService
{
    /**
     * Generate nomor tiket unik per bulan, contoh: KPR-202611-0001.
     */
    public function generateTicketNumber(): string
    {
        $prefix = 'KPR-'.now()->format('Ym');

        $last = QualifixTicket::where('ticket_number', 'like', $prefix.'-%')
            ->max('ticket_number');

        if ($last === null) {
            return $prefix.'-0001';
        }

        $seq = (int) Str::after($last, $prefix.'-') + 1;

        return $prefix.'-'.str_pad((string) $seq, 4, '0', STR_PAD_LEFT);
    }

    /**
     * Buat tiket Klinik PR baru dan tetapkan tentor pendamping secara otomatis.
     *
     * @return array{ticket: QualifixTicket, staff: StaffProfile|null}
     */
    public function createTicket(array $data): array
    {
        return DB::transaction(function () use ($data) {
            $ticket = QualifixTicket::create([
                'ticket_number' => $this->generateTicketNumber(),
                'student_id' => $data['student_id'],
                'subject_id' => $data['subject_id'],
                'class_group_id' => $data['class_group_id'] ?? null,
                'title' => $data['title'],
                'question' => $data['question'],
                'status' => 'menunggu_tentor',
                'submitted_at' => now(),
            ]);

            // Lampiran pertanyaan (opsional).
            if (! empty($data['file_ids'])) {
                foreach ($data['file_ids'] as $fileId) {
                    QualifixAttachment::create([
                        'ticket_id' => $ticket->id,
                        'answer_id' => null,
                        'file_id' => $fileId,
                    ]);
                }
            }

            $staff = $this->assignTentor($ticket);

            return ['ticket' => $ticket, 'staff' => $staff];
        });
    }

    /**
     * Tetapkan tentor pendamping ke tiket berdasarkan jadwal mengajar (mapel + kelas).
     * Jika tidak ada jadwal yang cocok, gunakan tentor aktif dengan beban paling ringan.
     */
    public function assignTentor(QualifixTicket $ticket): ?StaffProfile
    {
        $candidateStaffIds = ScheduleRule::query()
            ->where('class_group_id', $ticket->class_group_id)
            ->where('subject_id', $ticket->subject_id)
            ->pluck('staff_id');

        $staff = $this->pilihTentorRingan($candidateStaffIds);

        if ($staff === null) {
            $staff = $this->pilihTentorRingan(collect());
        }

        if ($staff === null) {
            return null;
        }

        $ticket->staff()->attach($staff->id, [
            'assigned_at' => now(),
            'assigned_by' => Auth::id(),
        ]);

        return $staff;
    }

    /**
     * Ganti / tetapkan ulang tentor pendamping (aksi admin).
     */
    public function reassign(QualifixTicket $ticket, int $staffId): void
    {
        $ticket->staff()->detach();
        $ticket->staff()->attach($staffId, [
            'assigned_at' => now(),
            'assigned_by' => Auth::id(),
        ]);
    }

    /**
     * Balas pertanyaan siswa (tentor). Status tiket menjadi sedang_dibahas.
     *
     * @return array{answer: QualifixAnswer, attachments: QualifixAttachment[]}
     */
    public function answer(QualifixTicket $ticket, array $data): array
    {
        return DB::transaction(function () use ($ticket, $data) {
            $ticket->update(['status' => 'sedang_dibahas']);

            $answer = QualifixAnswer::create([
                'ticket_id' => $ticket->id,
                'staff_id' => $this->staffIdFromUser(),
                'body' => $data['body'],
                'status' => 'terkirim',
                'sent_at' => now(),
            ]);

            if (! empty($data['file_ids'])) {
                foreach ($data['file_ids'] as $fileId) {
                    QualifixAttachment::create([
                        'ticket_id' => $ticket->id,
                        'answer_id' => $answer->id,
                        'file_id' => $fileId,
                    ]);
                }
            }

            return ['answer' => $answer, 'attachments' => $answer->attachments];
        });
    }

    /**
     * Tandai tiket selesai (tentor atau admin).
     */
    public function resolve(QualifixTicket $ticket): void
    {
        $ticket->update([
            'status' => 'selesai',
            'resolved_at' => now(),
        ]);
    }

    /**
     * Perbarui status tiket (admin).
     */
    public function updateStatus(QualifixTicket $ticket, string $status): void
    {
        $attributes = ['status' => $status];

        if ($status === 'selesai') {
            $attributes['resolved_at'] = now();
        } elseif ($status !== 'selesai' && $ticket->status === 'selesai') {
            $attributes['resolved_at'] = null;
        }

        $ticket->update($attributes);
    }

    /**
     * Pilih tentor aktif dengan beban tiket non-selesai paling ringan.
     *
     * @param  Collection<int>  $staffIds
     */
    private function pilihTentorRingan(Collection $staffIds): ?StaffProfile
    {
        $query = StaffProfile::active()
            ->withCount(['qualificTickets as beban' => function ($q) {
                $q->where('status', '!=', 'selesai');
            }])
            ->orderBy('beban')
            ->orderBy('full_name_with_title');

        if ($staffIds->isNotEmpty()) {
            $query->whereIn('id', $staffIds);
        }

        return $query->first();
    }

    private function staffIdFromUser(): int
    {
        return StaffProfile::where('user_id', Auth::id())->value('id') ?? 0;
    }
}
