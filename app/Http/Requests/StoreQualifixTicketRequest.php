<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class StoreQualifixTicketRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return $this->user()?->hasRole('siswa') === true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array|string>
     */
    public function rules(): array
    {
        return [
            'subject_id' => ['required', 'exists:subjects,id'],
            'class_group_id' => ['nullable', 'exists:class_groups,id'],
            'title' => ['required', 'string', 'max:255'],
            'question' => ['required', 'string'],
            'keterangan' => ['nullable', 'string'],
            'files' => ['nullable', 'array', 'max:2'],
            'files.*' => ['file', 'mimes:jpg,jpeg,png,pdf', 'max:2048'],
        ];
    }

    /**
     * Custom error messages.
     *
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'subject_id.required' => 'Mata pelajaran harus dipilih.',
            'title.required' => 'Judul soal harus diisi.',
            'question.required' => 'Isi soal atau pertanyaan harus diisi.',
            'files.array' => 'Lampiran harus berupa berkas.',
            'files.max' => 'Maksimal 2 berkas lampiran.',
            'files.*.file' => 'Setiap lampiran harus berupa berkas.',
            'files.*.mimes' => 'Format lampiran yang diizinkan: JPG, PNG, atau PDF.',
            'files.*.max' => 'Ukuran setiap lampiran maksimal 2 MB.',
        ];
    }
}
