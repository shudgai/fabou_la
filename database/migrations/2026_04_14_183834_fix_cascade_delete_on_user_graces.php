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
        Schema::table('user_imperial_graces', function (Blueprint $table) {
            $table->dropForeign(['registry_id']);
            $table->foreign('registry_id')
                  ->references('id')->on('imperial_grace_registries')
                  ->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::table('user_imperial_graces', function (Blueprint $table) {
            $table->dropForeign(['registry_id']);
            $table->foreign('registry_id')
                  ->references('id')->on('imperial_grace_registries');
        });
    }
};
