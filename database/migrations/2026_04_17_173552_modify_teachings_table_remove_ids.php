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
        Schema::table('teachings', function (Blueprint $table) {
            $table->dropForeign(['group_id']);
            $table->dropColumn('group_id');
            $table->dropForeign(['dharma_name_id']);
            $table->dropColumn('dharma_name_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('teachings', function (Blueprint $table) {
            $table->foreignId('group_id')->nullable()->constrained();
            $table->foreignId('dharma_name_id')->nullable()->constrained();
        });
    }
};
