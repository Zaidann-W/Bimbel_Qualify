<?php

use App\Models\User;
use App\Services\SessionService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\File;

uses(RefreshDatabase::class);

/**
 * Route dashboard untuk setiap role.
 *
 * @return array<string, string>
 */
function dashboardRouteByRole(): array
{
    return [
        'admin' => 'admin.dashboard',
        'manager' => 'manager.dashboard',
        'owner' => 'owner.dashboard',
        'tentor' => 'tentor.dashboard',
        'siswa' => 'siswa.dashboard',
        'ortu' => 'ortu.dashboard',
    ];
}

test('logout mengeluarkan user dan menghapus seluruh session untuk semua role', function (string $role) {
    $user = User::factory()->create(['role' => $role, 'status' => 'aktif']);

    $response = $this->actingAs($user)
        ->withSession([SessionService::ACTIVITY_KEY => now()->timestamp])
        ->post(route('logout'));

    $response->assertRedirect(route('login'));
    $response->assertSessionHas('status');
    $response->assertSessionMissing(SessionService::ACTIVITY_KEY);
    $response->assertCookieExpired(config('session.activity_cookie'));

    $this->assertGuest();

    // Tidak ada session nyangkut: seluruh area role kembali meminta login.
    $this->get(route(dashboardRouteByRole()[$role]))->assertRedirect(route('login'));
})->with(array_keys(dashboardRouteByRole()));

test('setelah logout user wajib login ulang dengan kredensialnya', function () {
    $user = User::factory()->create(['role' => 'admin', 'status' => 'aktif', 'password' => 'password']);

    $this->actingAs($user)->post(route('logout'))->assertRedirect(route('login'));

    $this->post(route('login.simpan'), [
        'email' => $user->email,
        'password' => 'password',
    ])->assertRedirect(route('admin.dashboard'));

    $this->assertAuthenticated();
});

test('user yang melewati batas waktu diam otomatis diminta login ulang', function (string $role) {
    $user = User::factory()->create(['role' => $role, 'status' => 'aktif']);

    $response = $this->actingAs($user)
        ->withSession([
            SessionService::ACTIVITY_KEY => now()->subMinutes((int) config('session.idle_timeout') + 5)->timestamp,
        ])
        ->get('/');

    $response->assertRedirect(route('login'));
    $response->assertSessionHas('status');
    $response->assertCookieExpired(config('session.activity_cookie'));
    $this->assertGuest();
})->with(array_keys(dashboardRouteByRole()));

test('batas waktu diam tetap dipaksa ketika session di perangkat sudah hilang', function () {
    $user = User::factory()->create(['role' => 'admin', 'status' => 'aktif']);

    $response = $this->actingAs($user)
        ->withCookies([
            config('session.activity_cookie') => (string) now()->subMinutes((int) config('session.idle_timeout') + 15)->timestamp,
        ])
        ->get('/');

    $response->assertRedirect(route('login'));
    $this->assertGuest();
});

test('user yang aktif tidak dikeluarkan oleh batas waktu diam', function () {
    $user = User::factory()->create(['role' => 'tentor', 'status' => 'aktif']);

    $response = $this->actingAs($user)
        ->withSession([SessionService::ACTIVITY_KEY => now()->subMinutes(2)->timestamp])
        ->get('/');

    $response->assertOk();
    $response->assertSessionHas(SessionService::ACTIVITY_KEY);
    $response->assertCookie(config('session.activity_cookie'));
    $this->assertAuthenticated();
});

test('dashboard setiap role menyediakan tombol logout', function (string $role, string $routeName) {
    $user = User::factory()->create(['role' => $role, 'status' => 'aktif']);

    $this->actingAs($user)
        ->withSession([SessionService::ACTIVITY_KEY => now()->timestamp])
        ->get(route($routeName))
        ->assertOk()
        ->assertSee(route('logout'), false)
        ->assertSee('Keluar');
})->with(collect(dashboardRouteByRole())
    ->map(fn (string $routeName, string $role) => [$role, $routeName])
    ->values()
    ->all());

test('setiap layout role menyediakan form logout', function (string $layout) {
    $html = view($layout)->render();

    expect($html)->toContain(route('logout'))
        ->toContain('method="POST"')
        ->toContain('_token');
})->with([
    'layouts.admin',
    'layouts.manager',
    'layouts.owner',
    'layouts.siswa',
    'layouts.tentor',
    'layouts.wali',
]);

test('layout role tidak lagi menautkan keluar ke halaman login', function () {
    foreach (['admin', 'manager', 'owner', 'siswa', 'tentor', 'wali'] as $layout) {
        expect(File::get(resource_path("views/layouts/{$layout}.blade.php")))
            ->not->toContain('href="{{ route(\'login\') }}"');
    }
});
