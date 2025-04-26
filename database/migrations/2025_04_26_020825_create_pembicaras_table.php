<?php

// database/migrations/2024_08_12_000002_create_pembicaras_table.php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePembicarasTable extends Migration
{
    public function up()
    {
        Schema::create('pembicara', function (Blueprint $table) {
            $table->id();
            $table->string('nama_pembicara');
            $table->text('latar_belakang');
            $table->string('photo')->nullable();
            $table->foreignId('seminar_id')->constrained('seminar')->onDelete('cascade');
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('pembicara');
    }
}
