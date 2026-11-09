<?php

use App\Models\AcademicPeriod;
use App\Models\AdmissionBatch;
use App\Models\Application;
use App\Models\ApplicationStatusHistory;
use App\Models\StudyShift;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;

uses(RefreshDatabase::class);

beforeEach(function () {
    $this->period = AcademicPeriod::create([
        'academic_year' => '2025/2026',
        'semester' => 'ganjil',
        'starts_on' => now()->subMonths(3),
        'ends_on' => now()->addMonths(3),
        'is_active' => true,
    ]);

    $this->batch = AdmissionBatch::create([
        'academic_period_id' => $this->period->id,
        'name' => 'Gelombang 1',
        'wave_number' => 1,
        'opens_at' => now()->subDay(),
        'closes_at' => now()->addDay(),
        'quota' => 100,
    ]);

    $this->shift = StudyShift::create([
        'name' => 'Sesi Sore',
        'starts_at' => '15:30:00',
        'ends_at' => '17:30:00',
    ]);
});

function registrasiPayload(): array
{
    return [
        'anti_spam_honeypot' => 'terisi',
        'nama_lengkap' => 'Budi Santoso',
        'nama_panggilan' => 'Budi',
        'tanggal_lahir' => '2012-05-12',
        'kelas' => 'smp-7',
        'asal_sekolah' => 'SDN Contoh 1',
        'wa_siswa' => '081234567890',
        'alamat' => 'Jl. Pendidikan No. 1',
        'nama_wali' => 'Siti Aminah',
        'wa_wali' => '081234567891',
        'wa_grup' => '081234567892',
        'sesi' => 'sore',
        'setuju' => '1',
        'rapor_ganjil' => UploadedFile::fake()->createWithContent(
            'rapor-ganjil.pdf',
            "%PDF-1.4\n1 0 obj\n<< /Type /Catalog >>\nendobj\ntrailer\n%%EOF"
        ),
        'rapor_genap' => UploadedFile::fake()->createWithContent(
            'rapor-genap.pdf',
            "%PDF-1.4\n1 0 obj\n<< /Type /Catalog >>\nendobj\ntrailer\n%%EOF"
        ),
    ];
}

test('submitting the registration form creates an application', function () {
    $response = $this->post(route('registrasi.simpan'), registrasiPayload());

    $response->assertRedirect(route('registrasi.konfirmasi'));

    $application = Application::where('full_name', 'Budi Santoso')->first();

    expect($application)->not->toBeNull()
        ->and($application->registration_number)->toStartWith('REG-'.now()->format('Y').'-')
        ->and($application->batch_id)->toBe($this->batch->id)
        ->and($application->study_shift_id)->toBe($this->shift->id)
        ->and($application->school_grade)->toBe('7')
        ->and($application->education_level)->toBe('SMP')
        ->and($application->status)->toBe('menunggu_review');

    expect(ApplicationStatusHistory::where('application_id', $application->id)->count())->toBe(1);
});

test('submitting the registration form stores the rapor documents', function () {
    $response = $this->post(route('registrasi.simpan'), registrasiPayload());

    $response->assertRedirect(route('registrasi.konfirmasi'));

    $application = Application::where('full_name', 'Budi Santoso')->first();

    expect($application->documents()->count())->toBe(2)
        ->and($application->documents()->pluck('type')->unique()->all())->toBe(['rapor']);
});

test('submitting the registration form flashes the registration number', function () {
    $response = $this->post(route('registrasi.simpan'), registrasiPayload());

    $response->assertRedirect(route('registrasi.konfirmasi'))
        ->assertSessionHas('registration_number');

    $this->get(route('registrasi.konfirmasi'))
        ->assertOk()
        ->assertSee('Terima kasih');
});

test('registration form validation rejects an incomplete submission', function () {
    $payload = registrasiPayload();
    unset($payload['nama_lengkap'], $payload['rapor_ganjil']);

    $this->from(route('registrasi'))
        ->post(route('registrasi.simpan'), $payload)
        ->assertRedirect(route('registrasi'))
        ->assertSessionHasErrors(['nama_lengkap', 'rapor_ganjil']);

    expect(Application::count())->toBe(0);
});

test('server validation errors are listed above the form', function () {
    $payload = registrasiPayload();
    unset($payload['nama_lengkap']);

    $this->from(route('registrasi'))
        ->post(route('registrasi.simpan'), $payload)
        ->assertRedirect(route('registrasi'));

    $this->get(route('registrasi'))
        ->assertOk()
        ->assertSee('Pendaftaran belum dapat diproses')
        ->assertSee('Nama lengkap siswa wajib diisi.');
});

test('registration is rejected while no admission batch is open', function () {
    AdmissionBatch::query()->delete();

    $this->from(route('registrasi'))
        ->post(route('registrasi.simpan'), registrasiPayload())
        ->assertRedirect(route('registrasi'))
        ->assertSessionHasErrors('pendaftaran');

    expect(Application::count())->toBe(0);
});

test('registration page replaces the form with a closed notice when no batch is open', function () {
    AdmissionBatch::query()->delete();

    $this->get(route('registrasi'))
        ->assertOk()
        ->assertSee('Pendaftaran Ditutup')
        ->assertDontSee('id="form-pendaftaran"', false);
});

test('registration page shows the open admission wave and academic year', function () {
    $this->get(route('registrasi'))
        ->assertOk()
        ->assertSee('Gelombang 1 Aktif')
        ->assertSee('Tahun Ajaran '.$this->period->academic_year)
        ->assertSee('form-pendaftaran');
});

test('registration rejects an application that already exists', function () {
    Application::create([
        'registration_number' => 'REG-2026-0001',
        'batch_id' => $this->batch->id,
        'full_name' => 'Budi Santoso',
        'birth_date' => '2012-05-12',
        'status' => 'menunggu_review',
    ]);

    $this->from(route('registrasi'))
        ->post(route('registrasi.simpan'), registrasiPayload())
        ->assertRedirect(route('registrasi'))
        ->assertSessionHasErrors('duplicate');

    expect(Application::count())->toBe(1);
});

test('confirmation page redirects to the form without a submitted registration', function () {
    $this->get(route('registrasi.konfirmasi'))
        ->assertRedirect(route('registrasi'));
});
