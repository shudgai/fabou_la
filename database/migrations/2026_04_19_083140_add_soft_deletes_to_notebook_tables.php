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
        Schema::table('registries', function (Blueprint $table) { $table->softDeletes(); });
        Schema::table('imperial_graces', function (Blueprint $table) { $table->softDeletes(); });
        Schema::table('teachings', function (Blueprint $table) { $table->softDeletes(); });
        Schema::table('grudges', function (Blueprint $table) { $table->softDeletes(); });
        Schema::table('military_records', function (Blueprint $table) { $table->softDeletes(); });
        Schema::table('other_records', function (Blueprint $table) { $table->softDeletes(); });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('registries', function (Blueprint $table) { $table->dropSoftDeletes(); });
        Schema::table('imperial_graces', function (Blueprint $table) { $table->dropSoftDeletes(); });
        Schema::table('teachings', function (Blueprint $table) { $table->dropSoftDeletes(); });
        Schema::table('grudges', function (Blueprint $table) { $table->dropSoftDeletes(); });
        Schema::table('military_records', function (Blueprint $table) { $table->dropSoftDeletes(); });
        Schema::table('other_records', function (Blueprint $table) { $table->dropSoftDeletes(); });
    }
};
