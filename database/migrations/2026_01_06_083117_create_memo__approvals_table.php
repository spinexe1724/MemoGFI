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
            
            // Relasi ke tabel users (siapa yang menandatangani)
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            
            // Relasi ke tabel memos (memo mana yang ditandatangani)
            $table->foreignId('memo_id')->constrained()->onDelete('cascade');
            
            // Catatan tambahan (misal: "Diterbitkan", "Disetujui oleh Manager", dll)
            $table->string('note')->nullable();
            
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
