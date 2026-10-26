<?php

namespace App\Services;

use App\Http\Requests\RegistrasiRequest;
use App\Models\Application;
use App\Models\ApplicationDocument;
use App\Models\ApplicationStatusHistory;
use App\Models\StudyShift;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class RegistrationService
{
    /**
     * Process the registration form submission.
     *
     * @param  \App\Http\Requests\RegistrasiRequest  $request
     * @return array{'registration_number' => string, 'application_id' => int}
     */
    public function register(RegistrasiRequest $request): array
    {
        return DB::transaction(function () use ($request) {
            // Rate limit check
            $this->checkRateLimit($request);

            // Determine batch_id based on active admission batch
            $batchId = $this->determineBatchId();

            // Generate unique registration number
            $registrationNumber = $this->generateRegistrationNumber();

            // Prepare application data
            $validated = $request->validatedData();

            // Map form fields to application columns
            $data = [
                'registration_number' => $registrationNumber,
                'batch_id' => $batchId,
                'source' => 'online',
                'status' => 'menunggu_review',
                'submitted_at' => now(),
                'full_name' => $validated['nama_lengkap'],
                'nickname' => $validated['nama_panggilan'],
                'birth_date' => $validated['tanggal_lahir'],
                'school_name' => $validated['asal_sekolah'],
                'school_grade' => $this->mapKelasToGrade($validated['kelas']),
                'education_level' => $this->mapKelasToEducationLevel($validated['kelas']),
                'phone_wa' => $validated['wa_siswa'],
                'address' => $validated['alamat'],
                'guardian_name' => $validated['nama_wali'],
                'guardian_phone' => $validated['wa_wali'],
                'group_wa' => $validated['wa_grup'],
                'study_shift_id' => $this->mapSesiToStudyShiftId($validated['sesi']),
            ];

            // Check for NISN if provided
            if ($request->filled('nisn')) {
                $data['nisn'] = $request->nisn;
            }

            // Create application
            $application = Application::create($data);

            // Create application status history
            ApplicationStatusHistory::create([
                'application_id' => $application->id,
                'old_status' => null,
                'new_status' => 'menunggu_review',
            ]);

            // Handle file uploads (rapor)
            $this->handleFileUploads($application, $request->file('rapor_ganjil'), $request->file('rapor_genap'));

            // Return success data
            return [
                'registration_number' => $registrationNumber,
                'application_id' => $application->id,
            ];
        });
    }

    /**
     * Check rate limit for the registration submission.
     *
     * @param  \App\Http\Requests\RegistrasiRequest  $request
     * @return void
     */
    protected function checkRateLimit(RegistrasiRequest $request): void
    {
        $key = $request->ip().'_registrasi';

        if (RateLimiter::tooManyAttempts($key, 5, 60)) {
            $retryAfter = RateLimiter::availableIn($key);
            throw new \HttpException(429, 'Terlalu banyak permintaan registrasi. Silakan coba lagi dalam ' . $retryAfter . ' detik.');
        }

        RateLimiter::hit($key);
    }

    /**
     * Determine batch_id based on active admission batch.
     *
     * @return int|nullable
     */
    protected function determineBatchId(): ?int
    {
        return StudyShift::firstWhere(function ($query) {
            $query->whereRaw('opens_at <= curdate() AND closes_at >= curdate()');
        })?.admissionBatch?->id;
    }

    /**
     * Generate unique registration number in format REG-{tahun}-{4 digit urut}.
     *
     * @return string
     */
    protected function generateRegistrationNumber(): string
    {
        $year = now()->format('Y');
        $prefix = 'REG-' . $year . '-';

        // Try to find the last registration number with this year prefix
        $last = Application::where('registration_number', 'like', $prefix . '%')
            ->orderBy('registration_number', 'desc')
            ->value('registration_number');

        $counter = 1;

        if ($last) {
            // Extract the 4-digit counter from the last registration number
            $lastCounter = (int) substr($last, strlen($prefix));
            if ($lastCounter >= 1) {
                $counter = $lastCounter + 1;
            }
        }

        // Format with leading zeros to 4 digits
        $formattedCounter = str_pad($counter, 4, '0', STR_PAD_LEFT);

        // Ensure uniqueness by checking and incrementing if needed
        $unique = false;
        $attempts = 0;

        while (! $unique && $attempts < 100) {
            $candidate = $prefix . $formattedCounter;
            $exists = Application::where('registration_number', $candidate)->exists();

            if (! $exists) {
                $unique = true;
            } else {
                $formattedCounter = str_pad(++$counter, 4, '0', STR_PAD_LEFT);
            }

            $attempts++;
        }

        return $prefix . $formattedCounter;
    }

    /**
     * Map kelas value to school_grade.
     *
     * @return string|null
     */
    protected function mapKelasToGrade(string $kelas): ?string
    {
        $map = [
            'sd-4' => '4',
            'sd-5' => '5',
            'sd-6' => '6',
            'smp-7' => '7',
            'smp-8' => '8',
            'smp-9' => '9',
            'sma-10' => '10',
            'sma-11' => '11',
            'sma-12' => '12',
        ];

        return $map[$kelas] ?? null;
    }

    /**
     * Map kelas value to education_level.
     *
     * @return string|null
     */
    protected function mapKelasToEducationLevel(string $kelas): ?string
    {
        $map = [
            'sd-4' => 'SD',
            'sd-5' => 'SD',
            'sd-6' => 'SD',
            'smp-7' => 'SMP',
            'smp-8' => 'SMP',
            'smp-9' => 'SMP',
            'sma-10' => 'SMA',
            'sma-11' => 'SMA',
            'sma-12' => 'SMA',
        ];

        return $map[$kelas] ?? null;
    }

    /**
     * Map sesi value to study_shift_id.
     *
     * @return int|null
     */
    protected function mapSesiToStudyShiftId(string $sesi): ?int
    {
        $shift = StudyShift::where('name', $sesi)->first();

        return $shift?->id;
    }

    /**
     * Handle file uploads for rapor documents.
     *
     * @param  \App\Models\Application  $application
     * @param  \Illuminate\Http\UploadedFile|null  $raporGanjil
     * @param  \Illuminate\Http\UploadedFile|null  $raporGenap
     * @return void
     */
    protected function handleFileUploads($application, $raporGanjil, $raporGenap): void
    {
        $maxSize = 5 * 1024 * 1024; // 5MB
        $allowedMimes = ['application/pdf', 'image/jpeg', 'image/png'];

        // Process rapor_ganjil
        if ($raporGanjil && $raporGanjil->isValid()) {
            $this->storeDocument($application, $raporGanjil, 'rapor_ganjil', $maxSize, $allowedMimes);
        }

        // Process rapor_genap
        if ($raporGenap && $raporGenap->isValid()) {
            $this->storeDocument($application, $raporGenap, 'rapor_genap', $maxSize, $allowedMimes);
        }
    }

    /**
     * Store a single document and create application_document row.
     *
     * @param  \App\Models\Application  $application
     * @param  \Illuminate\Http\UploadedFile  $file
     * @param  string  $typeLabel
     * @param  int  $maxSize
     * @param  array  $allowedMimes
     * @return void
     */
    protected function storeDocument($application, $file, string $typeLabel, int $maxSize, array $allowedMimes): void
    {
        // Check MIME type
        if (! in_array($file->getMimeType(), $allowedMimes) && ! Str::endsWith($file->getClientOriginalName(), ['.pdf', '.jpg', '.jpeg', '.png'])) {
            throw new \InvalidArgumentException('Format berkas tidak didukung.');
        }

        // Check size
        if ($file->getSize() > $maxSize) {
            throw new \InvalidArgumentException('Ukuran berkas melebihi batas maksimum 5MB.');
        }

        // Store file in private disk
        $path = $file->store('applications/documents', 'private');

        // Create file record
        $fileModel = \App\Models\File::create([
            'original_name' => $file->getClientOriginalName(),
            'path' => $path,
            'mime_type' => $file->getMimeType() ?: 'application/octet-stream',
            'size_bytes' => $file->getSize(),
            'uploaded_by' => null, // Could be set to admin user ID later
        ]);

        // Create application_document record
        $typeMap = [
            'rapor_ganjil' => 'rapor',
            'rapor_genap' => 'rapor',
        ];

        $docType = $typeMap[$typeLabel] ?? 'unknown';

        ApplicationDocument::create([
            'application_id' => $application->id,
            'type' => $docType,
            'file_id' => $fileModel->id,
            'status' => 'menunggu',
            'is_current' => true,
        ]);
    }
}