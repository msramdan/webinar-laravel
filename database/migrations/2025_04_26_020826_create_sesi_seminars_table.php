<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSesiSeminarsTable extends Migration
{
    public function up()
    {
        Schema::create('sesi_seminar', function (Blueprint $table) {
            $table->id();
            $table->string('nama_sesi');
            $table->integer('kuota');
            $table->decimal('harga_tiket', 12, 2)->default(0);
            $table->string('lampiran')->nullable();
            $table->dateTime('tanggal_pelaksanaan');
            $table->string('link_gmeet')->nullable();
            $table->string('tempat_seminar');
            $table->foreignId('seminar_id')->constrained('seminar')->onDelete('cascade');
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('sesi_seminar');
    }
}
