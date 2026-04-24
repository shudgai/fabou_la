<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('weekly_posts', function (Blueprint $table) {
            $table->id();
            $table->date('date')->nullable();
            $table->string('status')->nullable(); // 合格 / 不合格
            $table->string('title')->nullable(); // 抬頭
            $table->text('original_content')->nullable(); // 文章
            $table->text('modified_content')->nullable(); // 鋪文文章（修改後文章）
            $table->integer('sort_order')->default(0);
            $table->softDeletes();
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('weekly_posts');
    }
};
