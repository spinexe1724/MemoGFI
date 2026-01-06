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
        Schema::create('memos', function (Blueprint $table) {
            $table->id();
              $table->string('reference_no'); // e.g., 783/DIR/GFI/OL/11/2025
        $table->string('recipient');    // e.g., Seluruh Karyawan
        $table->string('sender');       // e.g., Direksi
        $table->text('cc_list')->nullable();
        $table->string('subject');
        $table->text('body_text');
        $table->string('gm_name')->default('Tohir Sutanto');
        $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('memos');
    }
};
