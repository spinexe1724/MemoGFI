<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Jalankan migrasi untuk menghapus kolom valid_until.
     */
    public function up(): void
    {
        Schema::table('memos', function (Blueprint $table) {
            // Cek dulu apakah kolomnya ada sebelum dihapus (untuk keamanan)
            if (Schema::hasColumn('memos', 'valid_until')) {
                $table->dropColumn('valid_until');
            }
        });
    }

    /**
     * Batalkan migrasi (Tambahkan kembali jika di-rollback).
     */
    public function down(): void
    {
        Schema::table('memos', function (Blueprint $table) {
            $table->date('valid_until')->nullable()->after('subject');
        });
    }
};