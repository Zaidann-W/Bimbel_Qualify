<?php

namespace App\Http\Requests;

use App\Services\PilihanPendaftaranService;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

/**
 * Validasi form data siswa (admin).
 *
 * Rules bagian data calon siswa & kontak wali sengaja dibuat sama dengan
 * RegistrasiRequest supaya formulir pendaftaran dan form edit data siswa
 * selalu memakai berkas data yang identik.
 */
class SiswaRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Normalisasi input sebelum divalidasi: kelas dipecah menjadi jenjang &
     * tingkat, nomor WhatsApp dibersihkan dari tanda baca.
     */
    protected function prepareForValidation(): void
    {
        $merge = [];

        $kelas = $this->input('kelas');

        if (is_string($kelas) && $kelas !== '') {
            $merge['education_level'] = PilihanPendaftaranService::jenjang($kelas);
            $merge['school_grade'] = PilihanPendaftaranService::tingkat($kelas);
        }

        foreach (['phone_wa', 'guardian_phone', 'group_wa'] as $field) {
            if ($this->filled($field)) {
                $merge[$field] = preg_replace('/\D+/', '', (string) $this->input($field));
            }
        }

        if ($merge !== []) {
            $this->merge($merge);
        }
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        $student = $this->route('student');
        $studentId = $student?->id;

        return [
            // Bagian 1 — data calon siswa (sama dengan formulir pendaftaran).
            'full_name' => ['required', 'string', 'max:150'],
            'nickname' => ['required', 'string', 'max:50'],
            'birth_date' => ['required', 'date'],
            'kelas' => ['required', 'string', Rule::in(array_keys(PilihanPendaftaranService::KELAS))],
            'school_name' => ['required', 'string', 'max:150'],
            'phone_wa' => ['required', 'string', 'regex:/^[0-9]{8,15}$/'],
            'address' => ['required', 'string', 'max:500'],
            'education_level' => ['nullable', Rule::in(['SD', 'SMP', 'SMA'])],
            'school_grade' => ['nullable', 'string', 'max:10'],

            // Bagian 2 — kontak wali & pengaturan grup (sama dengan formulir pendaftaran).
            'guardian_name' => ['required', 'string', 'max:150'],
            'guardian_phone' => ['required', 'string', 'regex:/^[0-9]{8,15}$/'],
            'group_wa' => ['required', 'string', 'regex:/^[0-9]{8,15}$/'],
            'sesi' => ['required', Rule::in(array_keys(PilihanPendaftaranService::SESI))],

            // Data internal admin — tidak ada di formulir pendaftaran.
            'nis' => ['nullable', 'string', 'max:30', Rule::unique('students', 'nis')->ignore($studentId)],
            'nisn' => ['nullable', 'string', 'max:20', Rule::unique('students', 'nisn')->ignore($studentId)],
            'status' => ['required', Rule::in(['aktif', 'cuti', 'nonaktif', 'lulus'])],
            'class_group_id' => ['nullable', 'exists:class_groups,id'],
            'birth_place' => ['nullable', 'string', 'max:100'],
            'email' => ['nullable', 'email', 'max:190'],
            'major' => ['nullable', 'string', 'max:30'],
            'curriculum' => ['nullable', 'string', 'max:50'],
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
            'full_name.required' => 'Nama lengkap siswa wajib diisi.',
            'nickname.required' => 'Nama panggilan siswa wajib diisi.',
            'birth_date.required' => 'Tanggal lahir siswa wajib diisi.',
            'kelas.required' => 'Kelas / jenjang wajib dipilih.',
            'kelas.in' => 'Kelas / jenjang yang dipilih tidak tersedia.',
            'school_name.required' => 'Asal sekolah wajib diisi.',
            'phone_wa.required' => 'No. WA siswa wajib diisi.',
            'phone_wa.regex' => 'No. WA siswa harus berisi 8-15 digit angka.',
            'address.required' => 'Alamat tempat tinggal wajib diisi.',
            'guardian_name.required' => 'Nama orang tua / wali wajib diisi.',
            'guardian_phone.required' => 'No. WA wali wajib diisi.',
            'guardian_phone.regex' => 'No. WA wali harus berisi 8-15 digit angka.',
            'group_wa.required' => 'No. WhatsApp grup wajib diisi.',
            'group_wa.regex' => 'No. WhatsApp grup harus berisi 8-15 digit angka.',
            'sesi.required' => 'Pilihan sesi belajar wajib dipilih.',
            'sesi.in' => 'Pilihan sesi belajar tidak tersedia.',
            'status.required' => 'Status siswa wajib dipilih.',
        ];
    }

    /**
     * Get custom attributes for validator errors.
     *
     * @return array<string, string>
     */
    public function attributes(): array
    {
        return [
            'full_name' => 'nama lengkap siswa',
            'nickname' => 'nama panggilan siswa',
            'birth_date' => 'tanggal lahir siswa',
            'kelas' => 'kelas / jenjang',
            'school_name' => 'asal sekolah',
            'phone_wa' => 'no. WA siswa',
            'address' => 'alamat tempat tinggal',
            'guardian_name' => 'nama orang tua / wali',
            'guardian_phone' => 'no. WA wali',
            'group_wa' => 'no. WhatsApp grup',
            'sesi' => 'sesi belajar',
            'class_group_id' => 'kelas bimbel',
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
            function (Validator $validator): void {
                if ($validator->errors()->any()) {
                    return;
                }

                $sesi = $this->input('sesi');

                if (is_string($sesi) && PilihanPendaftaranService::studyShiftId($sesi) === null) {
                    $validator->errors()->add('sesi', 'Sesi belajar yang dipilih belum tersedia di daftar sesi.');
                }
            },
        ];
    }
}
