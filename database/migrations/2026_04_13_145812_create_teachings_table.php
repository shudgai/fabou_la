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
        Schema::create('teachings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('master_id')->constrained();
            $table->string('treasure_name');
            $table->text('description')->nullable();
            $table->date('record_date');
            $table->string('status')->default('active');
            $table->json('excel_rows')->nullable();
            $table->foreignId('user_id')->constrained();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('teachings');
    }
};
