<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('memo_approvals', function (Blueprint $table) {
            // Mengubah tipe data kolom 'note' menjadi text agar bisa menampung ribuan karakter
            $table->text('note')->nullable()->change();
        });
    }

    public function down(): void
    {
        Schema::table('memo_approvals', function (Blueprint $table) {
            // Kembalikan ke string jika di-rollback
            $table->string('note')->nullable()->change();
        });
    }
};