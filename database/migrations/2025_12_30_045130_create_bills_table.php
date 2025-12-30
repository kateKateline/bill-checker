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
        Schema::create('bills', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique();
            $table->string('file_path');
            $table->longText('raw_text')->nullable();
            $table->string('ocr_engine')->default('paddleocr');
            $table->timestamp('ocr_completed_at')->nullable();
            $table->string('hospital_name')->nullable();
            $table->decimal('total_price', 15, 2)->nullable();
            $table->enum('status', ['pending', 'ocr_completed', 'analyzed', 'failed'])->default('pending');
            $table->string('session_id')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('bills');
    }
};
