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
        Schema::create('branches', function (Blueprint $table) {
           $table->id();
            $table->string('name')->unique();
            $table->string('code')->unique(); // Contoh: JKT, BDG, SUB
            $table->timestamps();
        });
              // Tambahkan kolom branch ke tabel users
        Schema::table('users', function (Blueprint $table) {
            $table->string('branch')->nullable()->after('division');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('branches');
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('branch');
        });
    }
};
