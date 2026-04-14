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
        Schema::create('teachings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('master_id')->constrained();//哪位仙師
            $table->foreignId('group_id')->nullable()->constrained();//哪個團體
            $table->foreignId('dharma_name_id')->nullable()->constrained();//哪位法號
            $table->text('title');
            $table->longText('content')->nullable();
            $table->json('supplement')->nullable();
            $table->foreignId('user_id')->constrained();//誰存的
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
