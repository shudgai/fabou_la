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
        Schema::create('imperial_graces', function (Blueprint $table) {
            $table->id();
            $table->foreignId('master_id')->constrained();
            $table->string('grace_name');
            $table->text('description')->nullable();
            $table->date('record_date');
            $table->date('know_date')->nullable();
            $table->date('report_date')->nullable();
            $table->string('status')->default('未求得'); // 未求得、已求得、已登記
            $table->integer('order')->default(0);
            $table->foreignId('user_id')->constrained();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('imperial_graces');
    }
};
