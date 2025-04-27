<?php

namespace App\Http\Requests\Seminars;

use Illuminate\Foundation\Http\FormRequest;

class StoreSeminarRequest extends FormRequest
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
            'nama_seminar' => 'required|string|max:250',
            'deskripsi' => 'required|string',
            'lampiran' => 'required|image|max:4000',
            'is_active' => 'required|in:Yes,No',
            'show_sertifikat' => 'required|in:Yes,No',
            'template_sertifikat' => 'nullable|image|max:4000',
        ];
    }
}
