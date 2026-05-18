<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::table('registries', function (Blueprint $table) {
            $table->text('name')->nullable()->change();
        });
        Schema::table('imperial_graces', function (Blueprint $table) {
            $table->text('name')->nullable()->change();
        });
        Schema::table('grudges', function (Blueprint $table) {
            $table->text('user_name')->nullable()->change();
            $table->text('user_remarks')->nullable()->change();
            $table->text('destination')->nullable()->change();
        });
        Schema::table('military_records', function (Blueprint $table) {
            $table->text('user_name')->nullable()->change();
            $table->text('user_remarks')->nullable()->change();
            $table->text('destination')->nullable()->change();
        });
        Schema::table('weekly_posts', function (Blueprint $table) {
            $table->text('title')->nullable()->change();
            $table->text('original_content')->nullable()->change();
        });
        Schema::table('self_posts', function (Blueprint $table) {
            $table->text('original_content')->nullable()->change();
        });
        Schema::table('other_teachings', function (Blueprint $table) {
            $table->text('content')->nullable()->change();
            $table->text('supplement')->nullable()->change();
            $table->text('target_remarks')->nullable()->change();
            $table->text('remarks')->nullable()->change();
            $table->text('items_footer_remarks')->nullable()->change();
        });
    }

    public function down(): void {
        // Can't reliably rollback text columns that contain encrypted data to varchar without data loss.
    }
};
