<?php

use App\Models\AcademicPeriod;
use App\Models\AdmissionBatch;
use App\Models\StudyShift;
use Database\Seeders\RegistrationSupportSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

test('seeding registration support data creates a wave that is open today', function () {
    $this->seed(RegistrationSupportSeeder::class);

    expect(AdmissionBatch::current())->not->toBeNull()
        ->and(AdmissionBatch::count())->toBe(1)
        ->and(AcademicPeriod::active()->count())->toBe(1);

    expect(StudyShift::count())->toBe(2)
        ->and(StudyShift::pluck('name')->all())->toContain('Sesi Sore')
        ->and(StudyShift::pluck('name')->all())->toContain('Sesi Malam');
});

test('seeding registration support data is idempotent', function () {
    $this->seed(RegistrationSupportSeeder::class);
    $this->seed(RegistrationSupportSeeder::class);

    expect(StudyShift::count())->toBe(2)
        ->and(AdmissionBatch::count())->toBe(1)
        ->and(AcademicPeriod::count())->toBe(1)
        ->and(AcademicPeriod::active()->count())->toBe(1)
        ->and(AdmissionBatch::current())->not->toBeNull();
});

test('an expired wave is rolled forward by reseeding', function () {
    $this->seed(RegistrationSupportSeeder::class);

    AdmissionBatch::query()->update([
        'opens_at' => now()->subYears(2),
        'closes_at' => now()->subYear(),
    ]);

    expect(AdmissionBatch::current())->toBeNull();

    $this->seed(RegistrationSupportSeeder::class);

    expect(AdmissionBatch::current())->not->toBeNull()
        ->and(AdmissionBatch::count())->toBe(1);
});
