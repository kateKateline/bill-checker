<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('bill_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('bill_id')
                ->constrained('bills')
                ->cascadeOnDelete();

            $table->string('item_name');
            $table->string('category');
            $table->decimal('price', 15, 2);
            $table->enum('status', ['safe', 'review', 'danger'])->default('safe');
            $table->string('label')->nullable();
            $table->text('description')->nullable();
            $table->timestamps();

            $table->index('status');
            $table->index('category');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('bill_items');
    }
};
