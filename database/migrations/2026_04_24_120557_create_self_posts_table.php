<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('self_posts', function (Blueprint $table) {
            $table->id();
            $table->date('date')->nullable();
            $table->string('message_type')->nullable(); // 玄訊/非玄訊
            $table->unsignedBigInteger('master_id')->nullable(); // 仙師
            $table->text('original_content')->nullable(); // 文章
            $table->text('modified_content')->nullable(); // 修改後文章
            $table->string('modified_by')->nullable(); // 由那位師兄姐修改
            $table->integer('sort_order')->default(0);
            $table->softDeletes();
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('self_posts');
    }
};
