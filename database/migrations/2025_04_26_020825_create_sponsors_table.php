<?php

// database/migrations/2024_08_12_000001_create_sponsors_table.php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSponsorsTable extends Migration
{
    public function up()
    {
        Schema::create('sponsor', function (Blueprint $table) {
            $table->id();
            $table->string('nama_sponsor');
            $table->string('gambar')->nullable();
            $table->foreignId('seminar_id')->constrained('seminar')->onDelete('cascade');
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('sponsor');
    }
}
