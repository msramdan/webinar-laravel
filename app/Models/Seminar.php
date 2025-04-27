<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Seminar extends Model
{
    use HasFactory;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'seminar'; //

    /**
     * The attributes that are mass assignable.
     *
     * @var string[]
     */
    // Tambahkan 'show_sertifikat' dan 'template_sertifikat' di sini
    protected $fillable = [
        'nama_seminar',
        'deskripsi',
        'lampiran',
        'is_active',
        'show_sertifikat', // Tambahkan ini
        'template_sertifikat' // Tambahkan ini
    ]; //

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'nama_seminar' => 'string',
            'deskripsi' => 'string',
            'lampiran' => 'string',
            'created_at' => 'datetime:Y-m-d H:i:s',
            'updated_at' => 'datetime:Y-m-d H:i:s'
            // Anda bisa menambahkan casting untuk 'show_sertifikat' jika diperlukan,
            // meskipun ENUM biasanya ditangani sebagai string.
        ]; //
    }
}
