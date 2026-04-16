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
        // This is the "重大皇恩" (Specialist/Master List) as per user mapping
        Schema::create('imperial_graces', function (Blueprint $table) {
            $table->id();
            $table->foreignId('master_id')->constrained();
            $table->string('name'); 
            $table->string('count')->nullable(); // 次數
            $table->text('purpose')->nullable(); // 法寶用意
            $table->date('record_date')->nullable(); // 得知日期
            $table->date('obtained_date')->nullable(); // 求得日期
            $table->string('status')->default('未求得'); 
            $table->text('remarks')->nullable(); // 備註
            $table->timestamps();
        });

        // Associated pivot for users if needed
        Schema::create('user_imperial_graces', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('imperial_grace_id')->constrained()->onDelete('cascade');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('user_imperial_graces');
        Schema::dropIfExists('imperial_graces');
    }
};
