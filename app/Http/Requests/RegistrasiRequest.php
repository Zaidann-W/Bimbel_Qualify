<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\DB;

class RegistrasiRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        $nisnExists = $this->filled('nisn');

        return [
            'anti_spam_honeypot' => ['required','string','min:1'],
            'nama_lengkap' => ['required', 'string', 'max:150'],
            'nama_panggilan' => ['required', 'string', 'max:50'],
            'tanggal_lahir' => ['required', 'date'],
            'kelas' => ['required', 'string'],
            'asal_sekolah' => ['required', 'string', 'max:255'],
            'wa_siswa' => ['required', 'string', 'regex:/^[0-9]{8,15}$/'],
            'alamat' => ['required', 'string'],
            'rapor_ganjil' => ['required', 'file', 'mimes:pdf,jpg,png', 'max:5120'],
            'rapor_genap' => ['required', 'file', 'mimes:pdf,jpg,png', 'max:5120'],
            'nama_wali' => ['required', 'string', 'max:150'],
            'wa_wali' => ['required', 'string', 'regex:/^[0-9]{8,15}$/'],
            'wa_grup' => ['required', 'string', 'regex:/^[0-9]{8,15}$/'],
            'sesi' => ['required'],
            'setuju' => ['required', 'boolean'],
        ];
    }

    /**
     * Get custom messages for validation errors.
     *
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'anti_spam_honeypot.required' => 'Validasi keamanan gagal. Silakan coba lagi.',
            'anti_spam_honeypot.string' => 'Validasi keamanan gagal. Silakan coba lagi.',
            'anti_spam_honeypot.min' => 'Validasi keamanan gagal. Silakan coba lagi.',
            'nama_lengkap.required' => 'Nama lengkap siswa wajib diisi.',
            'nama_panggilan.required' => 'Nama panggilan siswa wajib diisi.',
            'tanggal_lahir.required' => 'Tanggal lahir siswa wajib diisi.',
            'kelas.required' => 'Kelas / jenjang wajib dipilih.',
            'asal_sekolah.required' => 'Asal sekolah wajib diisi.',
            'wa_siswa.required' => 'No. WA siswa wajib diisi.',
            'wa_siswa.regex' => 'No. WA siswa harus berisi 8-15 digit angka.',
            'alamat.required' => 'Alamat tempat tinggal wajib diisi.',
            'rapor_ganjil.required' => 'Berkas rapor semester ganjil wajib diunggah.',
            'rapor_ganjil.mimes' => 'Berkas rahar harus berformat PDF, JPG, atau PNG.',
            'rapor_ganjil.max' => 'Ukuran berkas maksimal 5MB.',
            'rapor_genap.required' => 'Berkas rapor semester genap wajib diunggah.',
            'rapor_genap.mimes' => 'Berkas rapor harus berformat PDF, JPG, atau PNG.',
            'rapor_genap.max' => 'Ukuran berkas maksimal 5MB.',
            'nama_wali.required' => 'Nama orang tua / wali wajib diisi.',
            'wa_wali.required' => 'No. WA wali wajib diisi.',
            'wa_wali.regex' => 'No. WA wali harus berisi 8-15 digit angka.',
            'wa_grup.required' => 'No. WhatsApp grup wajib diisi.',
            'wa_grup.regex' => 'No. WhatsApp grup harus berisi 8-15 digit angka.',
            'sesi.required' => 'Pilihan sesi belajar wajib dipilih.',
            'setuju.required' => 'Anda harus menyetujui data yang dimasukkan.',
        ];
    }

    /**
     * Get the validate data passed to the FormRequest.
     *
     * @return array<string, mixed>
     */
    public function validatedData(): array
    {
        return [
            'nama_lengkap' => $this->validated('nama_lengkap'),
            'nama_panggilan' => $this->validated('nama_panggilan'),
            'tanggal_lahir' => $this->validated('tanggal_lahir'),
            'kelas' => $this->validated('kelas'),
            'asal_sekolah' => $this->validated('asal_sekolah'),
            'wa_siswa' => $this->validated('wa_siswa'),
            'alamat' => $this->validated('alamat'),
            'nama_wali' => $this->validated('nama_wali'),
            'wa_wali' => $this->validated('wa_wali'),
            'wa_grup' => $this->validated('wa_grup'),
            'sesi' => $this->validated('sesi'),
            'setuju' => $this->boolean('setuju'),
        ];
    }

    /**
     * Configure the validator backend.
     *
     * @param  \Illuminate\Validation\Validator  $validator
     * @return void
     */
    protected function withValidator($validator)
    {
        $validator->after(function ($validator) {
            // Check for duplicate NISN or nama_lengkap + tanggal_lahir
            $query = Application::where('status', 'menunggu_review')
                ->orWhere('status', 'diterima');

            if ($nisnExists) {
                $query->where('nisn', $this->nisn);
            }

            $existing = $query->where('full_name', $this->nama_lengkap)
                ->where('student_birth_date', $this->tanggal_lahir)
                ->first();

            if ($existing) {
                $validator->errors()->add('duplicate',
                    'Sudah ada pendaftaran dengan NISN yang sama atau kombinasi nama lengkap + tanggal lahir yang sama.');
            }
        });
    }
}
