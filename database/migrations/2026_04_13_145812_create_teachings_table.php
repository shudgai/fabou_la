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
            $table->date('date')->nullable();
            $table->foreignId('master_id')->nullable()->constrained()->onDelete('set null');
            $table->foreignId('group_id')->nullable()->constrained();
            $table->foreignId('dharma_name_id')->nullable()->constrained();
            $table->text('title');
            $table->longText('content')->nullable();
            $table->string('supplement')->nullable(); // 親友/信眾
            $table->string('target_remarks')->nullable(); // 團體/對象備註
            $table->text('remarks')->nullable(); // 系統/內部備註
            $table->json('items')->nullable(); // 所有法寶、金丹、符令、手作內容
            $table->text('items_footer_remarks')->nullable(); // 降寶清單結尾備註
            $table->foreignId('user_id')->constrained();
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
