<?php

namespace App\Http\Requests\Pesertas;


use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdatePesertaRequest extends FormRequest
{
    public function rules(): array
    {
        $pesertaId = request()->segment(2);
        return [
            'nama'        => ['required', 'string', 'max:255'],
            'no_telepon'  => ['required', 'string', 'max:15', 'regex:/^[0-9]+$/'],
            'email' => ['required', 'email', 'unique:peserta,email,' . $pesertaId],
            'alamat'      => ['required', 'string'],
            'kampus_id'   => ['required', 'integer', 'exists:kampus,id'],
            'password'    => ['nullable', 'string', 'min:6', 'confirmed'],
            'is_verified' => ['required', Rule::in(['Yes', 'No'])], // Tambahkan validasi is_verified
        ];
    }

    public function messages(): array
    {
        return [
            'no_telepon.regex' => 'No. telepon hanya boleh berisi angka.',
            'kampus_id.exists' => 'Kampus yang dipilih tidak valid.',
            'is_verified.required' => 'Status verifikasi wajib dipilih.',
            'is_verified.in' => 'Status verifikasi tidak valid.',
        ];
    }
}
