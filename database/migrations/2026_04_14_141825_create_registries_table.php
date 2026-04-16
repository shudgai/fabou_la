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
        Schema::create('registries', function (Blueprint $table) {
            $table->id();
            $table->foreignId('master_id')->constrained(); // 仙師
            $table->string('name'); // 法寶名稱
            $table->string('count')->nullable(); // 次數
            $table->text('purpose')->nullable(); // 法寶用意
            $table->text('acquisition_method')->nullable(); // 求寶方式
            $table->date('record_date')->nullable(); // 得知日期
            $table->date('obtained_date')->nullable(); // 求得日期
            $table->string('status')->default('未求得'); // 狀態
            $table->text('remarks')->nullable(); // 備註
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
