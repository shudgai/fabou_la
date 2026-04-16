<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // This is the "法寶登記" table as per user mapping
        Schema::create('registries', function (Blueprint $table) {
            $table->id();
            $table->foreignId('master_id')->constrained();
            $table->string('name');
            $table->string('purpose')->nullable();
            $table->string('acquisition_method')->nullable();
            $table->text('remarks')->nullable();
            $table->date('record_date')->nullable();
            $table->date('obtained_date')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('registries');
    }
};
