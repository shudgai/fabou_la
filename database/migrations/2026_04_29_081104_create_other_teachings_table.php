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
        Schema::create('other_teachings', function (Blueprint $table) {
            $table->id();
            $table->date('date')->nullable();
            $table->foreignId('master_id')->nullable()->constrained()->onDelete('set null');
            $table->longText('content')->nullable();
            $table->string('supplement')->nullable(); 
            $table->string('target_remarks')->nullable(); 
            $table->text('remarks')->nullable(); 
            $table->json('items')->nullable(); 
            $table->text('items_footer_remarks')->nullable(); 
            $table->foreignId('user_id')->nullable()->constrained();
            $table->integer('sort_order')->default(0);
            $table->boolean('is_daily')->default(false);
            $table->softDeletes();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('other_teachings');
    }
};
