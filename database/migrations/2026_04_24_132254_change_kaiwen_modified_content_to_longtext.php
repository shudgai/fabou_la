<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('weekly_posts', function (Blueprint $table) {
            DB::statement('ALTER TABLE weekly_posts MODIFY modified_content LONGTEXT');
        });
        Schema::table('self_posts', function (Blueprint $table) {
            DB::statement('ALTER TABLE self_posts MODIFY modified_content LONGTEXT');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('weekly_posts', function (Blueprint $table) {
            DB::statement('ALTER TABLE weekly_posts MODIFY modified_content TEXT');
        });
        Schema::table('self_posts', function (Blueprint $table) {
            DB::statement('ALTER TABLE self_posts MODIFY modified_content TEXT');
        });
    }
};
