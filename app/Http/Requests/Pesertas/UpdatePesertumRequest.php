<?php

namespace App\Http\Requests\Pesertas;

use Illuminate\Foundation\Http\FormRequest;

class UpdatePesertumRequest extends FormRequest
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
     */
    public function rules(): array
    {
        return [
            'nama' => 'required|string|max:255',
			'no_telepon' => 'required|max:15',
			'email' => 'required|email|unique:pesertas,email,' . request()->segment(2),
			'alamat' => 'required|string',
			'password' => 'nullable|confirmed',
        ];
    }
}
