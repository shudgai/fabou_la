<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('dharma_name_registries', function (Blueprint $table) {
            $table->id();
            $table->foreignId('registry_id')->constrained()->onDelete('cascade');
            $table->foreignId('dharma_name_id')->constrained();
            $table->date('obtained_date')->nullable();
            $table->text('remarks')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('dharma_name_registries');
    }
};
