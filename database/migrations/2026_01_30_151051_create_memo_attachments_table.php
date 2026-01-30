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
    Schema::create('memo_attachments', function (Blueprint $table) {
        $table->id();
        $table->foreignId('memo_id')->constrained()->onDelete('cascade');
        $table->string('file_path');
        $table->string('file_name');
        $table->string('file_type'); // docx, xlsx, pdf, dll
        $table->timestamps();
    });
}
    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('memo_attachments');
    }
};
