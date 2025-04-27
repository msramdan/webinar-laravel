<?php

namespace App\Http\Requests\Pesertas;

use Illuminate\Foundation\Http\FormRequest;

class StorePesertaRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'nama'        => ['required', 'string', 'max:255'],
            'no_telepon'  => ['required', 'string', 'max:15', 'regex:/^[0-9]+$/'],
            'email'       => ['required', 'string', 'email', 'max:255', 'unique:peserta,email'],
            'alamat'      => ['required', 'string'],
            'kampus_id'   => ['required', 'integer', 'exists:kampus,id'],
            'password'    => ['required', 'string', 'min:6', 'confirmed'],
        ];
    }

    public function messages(): array
    {
        return [
            'no_telepon.regex' => 'No. telepon hanya boleh berisi angka.',
            'kampus_id.exists' => 'Kampus yang dipilih tidak valid.',
        ];
    }
}
