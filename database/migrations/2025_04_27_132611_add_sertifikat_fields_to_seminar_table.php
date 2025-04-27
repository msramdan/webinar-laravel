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
        Schema::table('seminar', function (Blueprint $table) {
            // Tambahkan kolom setelah kolom 'is_active'
            $table->enum('show_sertifikat', ['Yes', 'No'])->default('No')->after('is_active');
            $table->string('template_sertifikat')->nullable()->after('show_sertifikat'); // Kolom untuk menyimpan path atau nama template
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('seminar', function (Blueprint $table) {
            $table->dropColumn('show_sertifikat');
            $table->dropColumn('template_sertifikat');
        });
    }
};
