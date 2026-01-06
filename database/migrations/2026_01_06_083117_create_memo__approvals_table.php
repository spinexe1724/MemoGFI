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
        Schema::create('memo_approvals', function (Blueprint $table) {
            $table->id();
            // Menghubungkan ke tabel memos
            $table->foreignId('memo_id')->constrained()->onDelete('cascade');
            // Menghubungkan ke tabel users (GM yang menyetujui)
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('memo__approvals');
    }
};
