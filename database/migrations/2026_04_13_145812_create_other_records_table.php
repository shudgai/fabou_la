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
        Schema::create('other_records', function (Blueprint $table) {
            $table->id();
            $table->foreignId('other_folder_id')->constrained();
            $table->foreignId('master_id')->nullable()->constrained();
            $table->string('title');
            $table->text('content')->nullable();
            $table->date('record_date');
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
        Schema::dropIfExists('other_records');
    }
};
