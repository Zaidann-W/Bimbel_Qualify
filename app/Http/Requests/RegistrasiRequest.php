<?php

namespace App\Http\Requests;

use App\Models\AdmissionBatch;
use App\Models\Application;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Validator;

class RegistrasiRequest extends FormRequest
{
    /**
     * Pesan penolakan saat tidak ada gelombang pendaftaran yang sedang terbuka.
     */
    public const PESAN_PENDAFTARAN_TUTUP = 'Pendaftaran gelombang ini sedang ditutup. Silakan tunggu pengumuman gelombang berikutnya.';

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
        return [
            'anti_spam_honeypot' => ['nullable', 'max:0'],
            'nama_lengkap' => ['required', 'string', 'max:150'],
            'nama_panggilan' => ['required', 'string', 'max:50'],
            'tanggal_lahir' => ['required', 'date'],
            'kelas' => ['required', 'string'],
            'asal_sekolah' => ['required', 'string', 'max:255'],
            'wa_siswa' => ['required', 'string', 'regex:/^[0-9]{8,15}$/'],
            'email' => ['required', 'string', 'email', 'max:190'],
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
            'anti_spam_honeypot.max' => 'Validasi keamanan gagal. Silakan coba lagi.',
            'nama_lengkap.required' => 'Nama lengkap siswa wajib diisi.',
            'nama_panggilan.required' => 'Nama panggilan siswa wajib diisi.',
            'tanggal_lahir.required' => 'Tanggal lahir siswa wajib diisi.',
            'kelas.required' => 'Kelas / jenjang wajib dipilih.',
            'asal_sekolah.required' => 'Asal sekolah wajib diisi.',
            'wa_siswa.required' => 'No. WA siswa wajib diisi.',
            'wa_siswa.regex' => 'No. WA siswa harus berisi 8-15 digit angka.',
            'email.required' => 'Email orang tua / wali wajib diisi.',
            'email.email' => 'Format email tidak valid.',
            'email.max' => 'Email maksimal 190 karakter.',
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
            'email' => $this->validated('email'),
            'alamat' => $this->validated('alamat'),
            'nama_wali' => $this->validated('nama_wali'),
            'wa_wali' => $this->validated('wa_wali'),
            'wa_grup' => $this->validated('wa_grup'),
            'sesi' => $this->validated('sesi'),
            'setuju' => $this->boolean('setuju'),
        ];
    }

    /**
     * Checks that depend on other fields or on application state.
     *
     * @return array<int, \Closure(Validator): void>
     */
    public function after(): array
    {
        return [
            // Pendaftaran hanya diterima selama masih ada gelombang yang terbuka.
            function (Validator $validator) {
                if (! AdmissionBatch::active()->exists()) {
                    $validator->errors()->add('pendaftaran', self::PESAN_PENDAFTARAN_TUTUP);
                }
            },

            // Check for duplicate NISN or nama_lengkap + tanggal_lahir
            function (Validator $validator) {
                if ($validator->errors()->any()) {
                    return;
                }

                $query = Application::whereIn('status', ['menunggu_review', 'diterima']);

                if ($this->filled('nisn')) {
                    $query->where('nisn', $this->nisn);
                }

                $existing = $query->where('full_name', $this->nama_lengkap)
                    ->whereDate('birth_date', $this->tanggal_lahir)
                    ->first();

                if ($existing) {
                    $validator->errors()->add(
                        'duplicate',
                        'Sudah ada pendaftaran dengan NISN yang sama atau kombinasi nama lengkap + tanggal lahir yang sama.'
                    );
                }
            },
        ];
    }
}
