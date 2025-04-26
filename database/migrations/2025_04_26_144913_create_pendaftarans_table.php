<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('pendaftaran', function (Blueprint $table) {
            $table->id();
            $table->foreignId('sesi_id')->constrained('sesi_seminar')->restrictOnUpdate()->cascadeOnDelete();
			$table->foreignId('peserta_id')->constrained('peserta')->restrictOnUpdate()->cascadeOnDelete();
			$table->enum('status', ['Waiting', 'Approved', 'Rejected']);
			$table->dateTime('tanggal_pengajuan');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pendaftaran');
    }
};
