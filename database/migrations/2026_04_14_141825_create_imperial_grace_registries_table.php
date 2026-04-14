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
        Schema::create('imperial_grace_registries', function (Blueprint $table) {
            $table->id();
            $table->foreignId('master_id')->constrained(); // 仙師
            $table->string('name'); // 法寶名稱
            $table->text('purpose')->nullable(); // 法寶用意
            $table->text('acquisition_method')->nullable(); // 求寶方式
            $table->date('record_date')->nullable(); // 日期
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('imperial_grace_registries');
    }
};
