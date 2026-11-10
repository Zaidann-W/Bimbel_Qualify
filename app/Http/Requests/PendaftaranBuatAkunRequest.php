<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class PendaftaranBuatAkunRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    /**
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'ortu_name' => ['required', 'string', 'max:150'],
            'ortu_email' => ['required', 'email', 'max:190', 'unique:users,email'],
            'ortu_phone' => ['required', 'string', 'max:20', 'regex:/^[0-9]{8,15}$/'],
            'ortu_password' => ['required', 'string', 'min:8'],
            'ortu_password_confirmation' => ['required', 'string', 'same:ortu_password'],
            'siswa_name' => ['required', 'string', 'max:150'],
            'siswa_email' => ['required', 'email', 'max:190', 'unique:users,email'],
            'siswa_phone' => ['nullable', 'string', 'max:20', 'regex:/^[0-9]{8,15}$/'],
            'siswa_password' => ['required', 'string', 'min:8'],
            'siswa_password_confirmation' => ['required', 'string', 'same:siswa_password'],
        ];
    }

    public function messages(): array
    {
        return [
            'ortu_name.required' => 'Nama orang tua wajib diisi.',
            'ortu_email.required' => 'Email orang tua wajib diisi.',
            'ortu_email.email' => 'Format email orang tua tidak valid.',
            'ortu_email.unique' => 'Email orang sudah terdaftar.',
            'ortu_phone.required' => 'No. HP orang tua wajib diisi.',
            'ortu_phone.regex' => 'No. HP orang tua harus 8-15 digit angka.',
            'ortu_password.required' => 'Password orang tua wajib diisi.',
            'ortu_password.min' => 'Password orang tua minimal 8 karakter.',
            'ortu_password_confirmation.same' => 'Konfirmasi password orang tua tidak cocok.',
            'siswa_name.required' => 'Nama siswa wajib diisi.',
            'siswa_email.required' => 'Email siswa wajib diisi.',
            'siswa_email.email' => 'Format email siswa tidak valid.',
            'siswa_email.unique' => 'Email siswa sudah terdaftar.',
            'siswa_phone.regex' => 'No. HP siswa harus 8-15 digit angka.',
            'siswa_password.required' => 'Password siswa wajib diisi.',
            'siswa_password.min' => 'Password siswa minimal 8 karakter.',
            'siswa_password_confirmation.same' => 'Konfirmasi password siswa tidak cocok.',
        ];
    }
}
