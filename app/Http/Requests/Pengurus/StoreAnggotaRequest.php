<?php

namespace App\Http\Requests\Pengurus;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreAnggotaRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return $this->user()?->role === 'pengurus';
    }

    protected function prepareForValidation(): void
    {
        $this->merge([
            'name' => $this->input('name', $this->input('nama')),
            'no_hp' => $this->input('no_hp', $this->input('hp')),
        ]);
    }

    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        $anggota = $this->route('anggota') ?? $this->route('anggotum');
        $userId = is_object($anggota) ? $anggota->getKey() : $anggota;

        return [
            'name'              => ['required', 'string', 'max:255'],
            'email'             => ['required', 'email', 'max:255', Rule::unique('users', 'email')->ignore($userId)],
            'no_hp'             => ['nullable', 'string', 'max:20'],
            'pekerjaan'         => ['nullable', 'string', 'max:100'],
            'penghasilan'       => ['nullable', 'numeric', 'min:0', 'max:99999999999999'],
            'tgl_gabung'        => ['nullable', 'date'],
            'pokok'             => ['nullable', 'numeric', 'min:0', 'max:99999999999999'],
            'status'             => ['required', Rule::in(['Aktif', 'Calon', 'Non-Aktif'])],
            'password'          => [$userId ? 'nullable' : 'required', 'string', 'min:8'],
        ];
    }

    /**
     * Custom validation messages.
     */
    public function messages(): array
    {
        return [
            'name.required'              => 'Nama lengkap wajib diisi.',
            'email.required'             => 'Email wajib diisi.',
            'email.unique'               => 'Email sudah terdaftar.',
            'status.required'            => 'Status wajib dipilih.',
            'status.in'                  => 'Status tidak valid.',
            'password.required'          => 'Password wajib diisi untuk anggota baru.',
            'password.min'               => 'Password minimal 8 karakter.',
            'pokok.numeric'              => 'Simpanan pokok harus berupa angka.',
            'penghasilan.numeric'        => 'Penghasilan harus berupa angka.',
        ];
    }
}
