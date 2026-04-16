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
        Schema::create('user_registries', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained(); // 誰求得
            $table->foreignId('registry_id')->constrained('registries')->onDelete('cascade'); // 總登記表ID
            $table->date('obtained_date')->nullable(); // 求得日期
            $table->text('remarks')->nullable(); // 備註
            $table->timestamp('record_date')->useCurrent();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('user_registries');
    }
};
