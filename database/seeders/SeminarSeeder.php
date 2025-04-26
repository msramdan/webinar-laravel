<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class SeminarSeeder extends Seeder
{
    public function run(): void
    {
        // === Seminar 1 ===
        $seminar1 = DB::table('seminar')->insertGetId([
            'nama_seminar' => 'Seminar Teknologi AI',
            'deskripsi' => 'Membahas perkembangan AI di Indonesia.',
            'lampiran' => 'ai_teknologi.pdf',
            'is_active' => 'Yes',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        DB::table('sponsor')->insert([
            'nama_sponsor' => 'TechSponsor',
            'gambar' => 'techsponsor.png',
            'seminar_id' => $seminar1,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        DB::table('pembicara')->insert([
            'nama_pembicara' => 'Budi Santoso',
            'latar_belakang' => 'Peneliti di bidang AI dan Machine Learning.',
            'photo' => 'budi.png',
            'seminar_id' => $seminar1,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        DB::table('sesi_seminar')->insert([
            'kuota' => 100,
            'nama_sesi' => 'Sesi 1',
            'harga_tiket' => 100000,
            'lampiran' => 'jadwal1.pdf',
            'tanggal_pelaksanaan' => Carbon::now()->addDays(10),
            'link_gmeet' => 'https://meet.google.com/abc-defg-hij',
            'seminar_id' => $seminar1,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        // === Seminar 2 ===
        $seminar2 = DB::table('seminar')->insertGetId([
            'nama_seminar' => 'Seminar Cloud Computing',
            'deskripsi' => 'Cloud sebagai fondasi transformasi digital.',
            'lampiran' => 'cloud_computing.pdf',
            'is_active' => 'Yes',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        DB::table('sponsor')->insert([
            'nama_sponsor' => 'CloudCorp',
            'gambar' => 'cloudcorp.png',
            'seminar_id' => $seminar2,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        DB::table('pembicara')->insert([
            'nama_pembicara' => 'Siti Aminah',
            'latar_belakang' => 'Konsultan cloud dan arsitek sistem.',
            'photo' => 'siti.png',
            'seminar_id' => $seminar2,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        DB::table('sesi_seminar')->insert([
            'kuota' => 80,
            'nama_sesi' => 'Sesi 1',
            'harga_tiket' => 120000,
            'lampiran' => 'jadwal2.pdf',
            'tanggal_pelaksanaan' => Carbon::now()->addDays(15),
            'link_gmeet' => 'https://meet.google.com/xyz-uvw-123',
            'seminar_id' => $seminar2,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }
}
