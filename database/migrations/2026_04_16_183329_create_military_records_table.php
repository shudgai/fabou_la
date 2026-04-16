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
        Schema::create('military_records', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('user_id')->nullable();
            $table->string('user_name')->nullable();
            $table->string('user_remarks')->nullable();
            $table->string('status')->default('待處理');
            $table->string('army_type'); // 虎甲軍, 虎賁軍, 黑曜軍, 耀紫軍
            $table->string('destination')->default('未處理');
            
            // 數量統計
            $table->bigInteger('quantity')->default(0); // 總量
            $table->integer('yan_zun')->default(0);    // 閻尊 (黑曜軍專用)
            $table->integer('yan_an')->default(0);     // 閻闇 (黑曜軍專用)
            $table->integer('long_sheng')->default(0); // 龍勝 (耀紫軍專用)
            $table->integer('long_zhan')->default(0);  // 龍戰 (耀紫軍專用)
            
            $table->date('know_date')->nullable();
            $table->date('process_date')->nullable();
            $table->text('remarks_text')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('military_records');
    }
};
