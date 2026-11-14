<?php

namespace App\Http\Controllers\Tentor;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreQualifixAnswerRequest;
use App\Models\File;
use App\Models\QualifixTicket;
use App\Models\StaffProfile;
use App\Services\QualifixService;
use App\Services\TentorQualifixService;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\RedirectResponse;

class TentorQualifixController extends Controller
{
    public function __construct(
        private readonly TentorQualifixService $service,
        private readonly QualifixService $qualifixService,
    ) {}

    /**
     * Tampilkan halaman QualiFix tentor: antrean pertanyaan siswa dan ruang solusi.
     * Semua data diambil dari basis data (QualifixTicket yang ditugaskan kepada tentor ini).
     */
    public function index(Request $request): View
    {
        $user = $request->user();

        return view('tentor.qualifix', $this->service->qualifix($user));
    }

    /**
     * Balas pertanyaan siswa (Kirim Jawaban ke Siswa).
     */
    public function answer(QualifixTicket $ticket, StoreQualifixAnswerRequest $request): RedirectResponse
    {
        abort_unless($ticket->staff()->where('staff_profiles.id', $this->staffId())->exists(), 403,
            'Tiket ini tidak ditugaskan kepada Anda.');

        $this->qualifixService->answer($ticket, array_merge($request->validated(), [
            'file_ids' => $this->unggahLampiran($request->file('files') ?? []),
        ]));

        return redirect()->route('tentor.qualifix')
            ->with('sukses', 'Jawaban telah dikirim ke siswa.');
    }

    /**
     * Tandai tiket selesai.
     */
    public function resolve(QualifixTicket $ticket): RedirectResponse
    {
        abort_unless($ticket->staff()->where('staff_profiles.id', $this->staffId())->exists(), 403,
            'Tiket ini tidak ditugaskan kepada Anda.');

        $this->qualifixService->resolve($ticket);

        return redirect()->route('tentor.qualifix')
            ->with('sukses', 'Tiket Klinik PR ditandai selesai.');
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
            $path = $file->store('qualifix', 'local');

            $ids[] = File::create([
                'original_name' => $file->getClientOriginalName(),
                'path' => $path,
                'mime_type' => $file->getMimeType(),
                'size_bytes' => $file->getSize(),
                'uploaded_by' => auth()->id(),
            ])->id;
        }

        return $ids;
    }
}
