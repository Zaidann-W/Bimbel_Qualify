<?php

use App\Models\Guardian;
use App\Models\Student;
use App\Models\StudentGuardian;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;

uses(RefreshDatabase::class);

beforeEach(function () {
    $this->user = User::factory()->create([
        'role' => 'ortu',
        'status' => 'aktif',
        'name' => 'Ir. Hendra Gunawan',
        'email' => 'hendra.gunawan@email.com',
        'password' => Hash::make('password123'),
    ]);

    $this->actingAs($this->user);

    $this->guardian = Guardian::create([
        'user_id' => $this->user->id,
        'name' => 'Ir. Hendra Gunawan',
        'occupation' => 'Wiraswasta',
        'phone_wa' => '+62 812-4455-8891',
        'email' => 'hendra.gunawan@email.com',
        'address' => 'Jl. Gandaria Tengah III No. 14, Kebayoran Baru, Jakarta Selatan',
    ]);

    $this->anak1 = Student::create([
        'full_name' => 'Aisyah Putri Azzahra',
        'nickname' => 'Aisyah',
        'nisn' => '0098123471',
        'phone_wa' => '0812-9843-2210',
        'address' => 'Jl. Tebet Barat Dalam No. 42',
        'school_name' => 'SMAN 8 Jakarta',
        'birth_date' => '2007-05-14',
        'school_grade' => '12',
        'education_level' => 'SMA',
        'major' => 'IPA',
        'status' => 'aktif',
    ]);

    $this->anak2 = Student::create([
        'full_name' => 'Anindya Putri Rahayu',
        'nickname' => 'Anindya',
        'nisn' => '0098123472',
        'phone_wa' => '0812-9843-2211',
        'address' => 'Jl. Menteng Raya No. 10',
        'school_name' => 'SMA 71 Jakarta',
        'birth_date' => '2009-03-20',
        'school_grade' => '10',
        'education_level' => 'SMA',
        'major' => 'IPS',
        'status' => 'aktif',
    ]);

    StudentGuardian::create([
        'student_id' => $this->anak1->id,
        'guardian_id' => $this->guardian->id,
        'relationship' => 'ayah',
        'is_primary_contact' => true,
        'is_financial_responsible' => true,
    ]);

    StudentGuardian::create([
        'student_id' => $this->anak2->id,
        'guardian_id' => $this->guardian->id,
        'relationship' => 'ayah',
        'is_primary_contact' => false,
        'is_financial_responsible' => false,
    ]);
});

test('profil orang tua page returns a successful response', function () {
    $response = $this->get('/wali/profil');

    $response->assertStatus(200);
    $response->assertSee('Profil Saya (Wali Murid)');
    $response->assertSee('Ir. Hendra Gunawan');
    $response->assertSee('Data Diri');
    $response->assertSee('Putra-Putri Terhubung');
    $response->assertSee('Aisyah Putri Azzahra');
    $response->assertSee('Anindya Putri Rahayu');
    $response->assertSee('Kata Sandi Akun');
    $response->assertSee('Notifikasi Otomatis WhatsApp');
});

test('profil orang tua menampilkan data guardian dari database', function () {
    $this->get('/wali/profil')
        ->assertStatus(200)
        ->assertSee('Ir. Hendra Gunawan')
        ->assertSee('+62 812-4455-8891')
        ->assertSee('hendra.gunawan@email.com')
        ->assertSee('Jl. Gandaria Tengah III No. 14, Kebayoran Baru, Jakarta Selatan');
});

test('profil orang tua menampilkan daftar anak yang terhubung dari database', function () {
    $this->get('/wali/profil')
        ->assertStatus(200)
        ->assertSee('Aisyah Putri Azzahra')
        ->assertSee('Anindya Putri Rahayu');
});

test('profil orang tua is reachable from its named route', function () {
    $this->get(route('wali.profil'))->assertStatus(200);
});

test('profil orang tua dapat memperbarui data diri guardian', function () {
    $response = $this->put(route('wali.profil'), [
        'name' => 'Ir. Hendra Gunawan Updated',
        'occupation' => 'Wirausaha',
        'phone_wa' => '+62 812-9999-0000',
        'email' => 'hendra.updated@email.com',
        'address' => 'Jl. Sudirman No. 100, Jakarta Pusat',
    ]);

    $response->assertStatus(302);
    $response->assertSessionHas('success');

    $this->guardian->refresh();
    expect($this->guardian->name)->toBe('Ir. Hendra Gunawan Updated');
    expect($this->guardian->occupation)->toBe('Wirausaha');
    expect($this->guardian->phone_wa)->toBe('+62 812-9999-0000');
    expect($this->guardian->email)->toBe('hendra.updated@email.com');
    expect($this->guardian->address)->toBe('Jl. Sudirman No. 100, Jakarta Pusat');
});

test('profil orang tua validasi input update data diri', function () {
    $this->put(route('wali.profil'), [
        'name' => '',
        'phone_wa' => '',
    ])->assertSessionHasErrors(['name', 'email', 'phone_wa']);
});

test('profil orang tua dapat mengubah kata sandi', function () {
    $response = $this->put(route('wali.profil.password'), [
        'current_password' => 'password123',
        'password' => 'barupassword123',
        'password_confirmation' => 'barupassword123',
    ]);

    $response->assertStatus(302);
    $response->assertSessionHas('success');

    $this->user->refresh();
    expect(Hash::check('barupassword123', $this->user->password))->toBeTrue();
});

test('profil orang tua menolak kata sandi saat ini yang salah', function () {
    $this->put(route('wali.profil.password'), [
        'current_password' => 'salah123',
        'password' => 'barupassword123',
        'password_confirmation' => 'barupassword123',
    ])->assertSessionHasErrors(['current_password']);
});

test('profil orang tua validasi konfirmasi kata sandi baru', function () {
    $this->put(route('wali.profil.password'), [
        'current_password' => 'password123',
        'password' => 'barupassword123',
        'password_confirmation' => 'tidaksama',
    ])->assertSessionHasErrors(['password']);
});
