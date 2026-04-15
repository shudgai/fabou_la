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
        Schema::create('dharma_name_treasures', function (Blueprint $table) {
            $table->id();
            $table->foreignId('treasure_id')->constrained()->onDelete('cascade');
            $table->foreignId('dharma_name_id')->constrained();
            $table->date('obtained_date')->nullable();
            $table->text('remarks')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('dharma_name_treasures');
    }
};
