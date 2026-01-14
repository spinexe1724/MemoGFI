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
    $table->foreignId('user_id')->constrained()->onDelete('cascade');
    $table->string('reference_no');
    $table->string('recipient');
    $table->string('sender');
    $table->text('cc_list')->nullable();
    $table->string('subject');
    $table->boolean('is_draft')->default(true);
    $table->text('body_text');
    $table->date('valid_until')->nullable();
    $table->boolean('is_fully_approved')->default(false);
    $table->boolean('is_rejected')->default(false);
    
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
