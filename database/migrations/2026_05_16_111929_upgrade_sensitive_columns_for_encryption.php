<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('registries', function (Blueprint $table) {
            $table->text('purpose')->nullable()->change();
            $table->text('effect')->nullable()->change();
            $table->text('acquisition_method')->nullable()->change();
            $table->text('content')->nullable()->change();
            $table->text('remarks')->nullable()->change();
        });

        Schema::table('dharma_name_registries', function (Blueprint $table) {
            $table->text('custom_name')->nullable()->change();
            $table->text('remarks')->nullable()->change();
            $table->text('related_personnel')->nullable()->change();
        });

        Schema::table('teachings', function (Blueprint $table) {
            $table->mediumText('content')->nullable()->change();
            $table->mediumText('items')->nullable()->change();
            $table->text('items_footer_remarks')->nullable()->change();
        });

        Schema::table('grudges', function (Blueprint $table) {
            $table->text('remarks_text')->nullable()->change();
        });

        Schema::table('military_records', function (Blueprint $table) {
            $table->text('remarks_text')->nullable()->change();
        });

        Schema::table('other_records', function (Blueprint $table) {
            $table->text('title')->nullable()->change();
            $table->mediumText('content')->nullable()->change();
        });

        Schema::table('imperial_graces', function (Blueprint $table) {
            $table->text('purpose')->nullable()->change();
            $table->text('remarks')->nullable()->change();
        });
    }

    public function down(): void
    {
        // No need to revert specifically as these are just widening the columns
    }
};
