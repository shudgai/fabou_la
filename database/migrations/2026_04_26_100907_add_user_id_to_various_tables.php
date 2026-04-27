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
        Schema::table('registries', function (Blueprint $table) {
            $table->foreignId('user_id')->nullable()->constrained()->onDelete('set null')->after('id');
        });
        Schema::table('imperial_graces', function (Blueprint $table) {
            $table->foreignId('user_id')->nullable()->constrained()->onDelete('set null')->after('id');
        });
        Schema::table('other_folders', function (Blueprint $table) {
            $table->foreignId('user_id')->nullable()->constrained()->onDelete('set null')->after('id');
        });
        Schema::table('other_records', function (Blueprint $table) {
            $table->foreignId('user_id')->nullable()->constrained()->onDelete('set null')->after('id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('registries', function (Blueprint $table) {
            $table->dropForeign(['user_id']);
            $table->dropColumn('user_id');
        });
        Schema::table('imperial_graces', function (Blueprint $table) {
            $table->dropForeign(['user_id']);
            $table->dropColumn('user_id');
        });
        Schema::table('other_folders', function (Blueprint $table) {
            $table->dropForeign(['user_id']);
            $table->dropColumn('user_id');
        });
        Schema::table('other_records', function (Blueprint $table) {
            $table->dropForeign(['user_id']);
            $table->dropColumn('user_id');
        });
    }
};
