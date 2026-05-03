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
        Schema::table('imperial_graces', function (Blueprint $table) {
            if (!Schema::hasColumn('imperial_graces', 'sort_order')) {
                $table->integer('sort_order')->default(0)->after('master_id');
            }
        });
    }

    public function down(): void
    {
        Schema::table('imperial_graces', function (Blueprint $table) {
            $table->dropColumn('sort_order');
        });
    }
};
