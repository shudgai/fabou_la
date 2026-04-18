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
        Schema::create('other_folders', function (Blueprint $blueprint) {
            $blueprint->id();
            $blueprint->string('name');
            $blueprint->string('color')->nullable();
            $blueprint->timestamps();
        });

        Schema::create('other_records', function (Blueprint $blueprint) {
            $blueprint->id();
            $blueprint->foreignId('other_folder_id')->constrained()->onDelete('cascade');
            $blueprint->string('title')->nullable();
            $blueprint->text('content')->nullable();
            $blueprint->date('record_date')->nullable();
            $blueprint->json('extra_data')->nullable(); // For flexibility
            $blueprint->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('other_records');
        Schema::dropIfExists('other_folders');
    }
};
