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
        Schema::table('peserta', function (Blueprint $table) {
            // Tambahkan setelah kolom 'password' atau kolom terakhir yang relevan
            $table->enum('is_verified', ['Yes', 'No'])->default('No')->after('password');
            $table->string('verification_token')->nullable()->after('is_verified');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('peserta', function (Blueprint $table) {
            $table->dropColumn(['is_verified', 'verification_token']);
        });
    }
};
