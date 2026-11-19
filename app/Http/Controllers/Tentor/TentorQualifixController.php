<?php

namespace App\Http\Controllers\Tentor;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreQualifixAnswerRequest;
use App\Models\File;
use App\Models\QualifixAnswer;
use App\Models\QualifixAttachment;
use App\Models\QualifixTicket;
use App\Models\StaffProfile;
use App\Services\QualifixService;
use App\Services\TentorQualifixService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Response;

class TentorQualifixController extends Controller
{
    public function __construct(
        private readonly TentorQualifixService $service,
        private readonly QualifixService $qualifixService,
    ) {}

    /**
     * Tampilkan halaman QualiFix tentor: antrean pertanyaan siswa dan ruang solusi.
     */
    public function index(Request $request): View
    {
        $user = $request->user();
        $tab = (string) $request->query('tab', 'semua');
        $ticketId = $request->query('ticket_id') ? (int) $request->query('ticket_id') : null;

        return view('tentor.qualifix', $this->service->qualifix($user, $tab, $ticketId));
    }

    /**
     * Ambil/klaim tiket pertanyaan agar ditugaskan kepada tentor ini.
     */
    public function claim(QualifixTicket $ticket): RedirectResponse
    {
        $staffId = $this->staffId();
        abort_unless($staffId > 0, 403, 'Profil tentor tidak ditemukan.');

        if (! $ticket->staff()->where('staff_profiles.id', $staffId)->exists()) {
            $ticket->staff()->attach($staffId, [
                'assigned_at' => now(),
            ]);
        }

        return redirect()->route('tentor.qualifix', ['ticket_id' => $ticket->id])
            ->with('sukses', 'Pertanyaan berhasil diambil/ditugaskan kepada Anda.')
            ->with('success', 'Pertanyaan berhasil diambil/ditugaskan kepada Anda.');
    }

    /**
     * Balas pertanyaan siswa (Kirim Jawaban ke Siswa).
     */
    public function answer(QualifixTicket $ticket, StoreQualifixAnswerRequest $request): RedirectResponse
    {
        abort_if($ticket->status === 'selesai', 403, 'Pertanyaan ini telah selesai dan siswa telah memberikan rating. Jawaban tidak dapat ditambahkan lagi.');

        $staffId = $this->staffId();
        abort_unless($staffId > 0, 403, 'Profil tentor tidak ditemukan.');

        // Jika belum ada tentor yang ditugaskan, atau tiket sudah dijawab (sedang dibahas) sehingga tentor lain ikut berkontribusi
        if (! $ticket->staff()->where('staff_profiles.id', $staffId)->exists()) {
            if ($ticket->status === 'sedang_dibahas' || $ticket->staff()->doesntExist()) {
                $ticket->staff()->attach($staffId, [
                    'assigned_at' => now(),
                ]);
            } else {
                abort(403, 'Tiket ini tidak ditugaskan kepada Anda.');
            }
        }

        $files = $request->file('files');
        $fileList = is_array($files) ? $files : ($files ? [$files] : []);

        $this->qualifixService->answer($ticket, array_merge($request->validated(), [
            'file_ids' => $this->unggahLampiran($fileList),
        ]));

        return redirect()->route('tentor.qualifix', ['ticket_id' => $ticket->id])
            ->with('sukses', 'Jawaban telah dikirim ke siswa.')
            ->with('success', 'Jawaban telah dikirim ke siswa.');
    }

    /**
     * Perbarui jawaban yang telah diberikan (hanya oleh tentor yang membuat jawaban dan belum berstatus selesai).
     */
    public function updateAnswer(QualifixAnswer $answer, Request $request): RedirectResponse
    {
        $staffId = $this->staffId();
        abort_unless($staffId > 0, 403, 'Profil tentor tidak ditemukan.');

        $ticket = $answer->ticket;
        abort_if($ticket && $ticket->status === 'selesai', 403, 'Pertanyaan ini telah selesai dan siswa telah memberikan rating. Jawaban tidak dapat diubah lagi.');

        abort_unless($answer->staff_id === $staffId, 403, 'Anda hanya dapat mengedit jawaban yang Anda berikan sendiri.');

        $validated = $request->validate([
            'body' => 'required|string|min:3',
            'files' => 'nullable|array|max:5',
            'files.*' => 'file|max:5120|mimes:jpg,jpeg,png,pdf',
        ]);

        $answer->update([
            'body' => $validated['body'],
        ]);

        if ($request->hasFile('files')) {
            $fileIds = $this->unggahLampiran($request->file('files'));
            foreach ($fileIds as $fileId) {
                QualifixAttachment::create([
                    'ticket_id' => $answer->ticket_id,
                    'answer_id' => $answer->id,
                    'file_id' => $fileId,
                ]);
            }
        }

        return redirect()->route('tentor.qualifix', ['ticket_id' => $answer->ticket_id])
            ->with('sukses', 'Jawaban berhasil diperbarui.')
            ->with('success', 'Jawaban berhasil diperbarui.');
    }

    /**
     * Unduh atau tampilkan berkas lampiran tiket/jawaban.
     */
    public function downloadFile(File $file): Response
    {
        abort_if(! Storage::disk('local')->exists($file->path), 404, 'Berkas tidak ditemukan.');

        return Storage::disk('local')->response($file->path, $file->original_name);
    }

    /**
     * Tandai tiket selesai.
     */
    public function resolve(QualifixTicket $ticket): RedirectResponse
    {
        abort_unless($ticket->staff()->where('staff_profiles.id', $this->staffId())->exists(), 403,
            'Tiket ini tidak ditugaskan kepada Anda.');

        $this->qualifixService->resolve($ticket);

        return redirect()->route('tentor.qualifix', ['ticket_id' => $ticket->id])
            ->with('sukses', 'Tiket Klinik PR ditandai selesai.')
            ->with('success', 'Tiket Klinik PR ditandai selesai.');
    }

    private function staffId(): int
    {
        return StaffProfile::where('user_id', auth()->id())->value('id') ?? 0;
    }

    /**
     * Unggah lampiran jawaban dan catat ke tabel files.
     *
     * @return array<int>
     */
    private function unggahLampiran(array $berkas): array
    {
        $ids = [];

        foreach ($berkas as $file) {
            if (! $file || ! $file->isValid()) {
                continue;
            }

            $path = $file->store('qualifix', 'local');

            $ids[] = File::create([
                'original_name' => $file->getClientOriginalName(),
                'path' => $path,
                'mime_type' => $file->getClientMimeType() ?: $file->getMimeType(),
                'size_bytes' => $file->getSize(),
                'uploaded_by' => auth()->id(),
            ])->id;
        }

        return $ids;
    }
}
