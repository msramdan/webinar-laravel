<?php

namespace App\Http\Requests\Pendaftarans;

use Illuminate\Foundation\Http\FormRequest;

class UpdatePendaftaranRequest extends FormRequest
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
            'sesi_id' => 'required|exists:App\Models\Sesi,id',
			'peserta_id' => 'required|exists:App\Models\Pesertum,id',
			'status' => 'required|in:Waiting,Approved,Rejected',
			'tanggal_pengajuan' => 'required',
        ];
    }
}
