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
        Schema::table('dharma_name_registries', function (Blueprint $table) {
            $table->unsignedBigInteger('dharma_name_id')->nullable()->change();
            $table->string('custom_name')->nullable()->after('dharma_name_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('dharma_name_registries', function (Blueprint $table) {
            $table->unsignedBigInteger('dharma_name_id')->nullable(false)->change();
            $table->dropColumn('custom_name');
        });
    }
};
