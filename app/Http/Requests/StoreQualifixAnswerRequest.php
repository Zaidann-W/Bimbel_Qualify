<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class StoreQualifixAnswerRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return $this->user()?->hasRole('tentor') === true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array|string>
     */
    public function rules(): array
    {
        return [
            'body' => ['required', 'string'],
            'files' => ['nullable', 'array', 'max:5'],
            'files.*' => ['file', 'mimes:jpg,jpeg,png,pdf', 'max:5120'],
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
            'body.required' => 'Teks jawaban harus diisi.',
            'files.array' => 'Lampiran harus berupa berkas.',
            'files.max' => 'Maksimal 5 berkas lampiran.',
            'files.*.file' => 'Setiap lampiran harus berupa berkas.',
            'files.*.mimes' => 'Format lampiran yang diizinkan: JPG, PNG, atau PDF.',
            'files.*.max' => 'Ukuran setiap lampiran maksimal 5 MB.',
        ];
    }
}
