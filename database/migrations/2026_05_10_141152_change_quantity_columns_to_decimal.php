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
        Schema::table('military_records', function (Blueprint $table) {
            $table->decimal('quantity', 65, 0)->default(0)->change();
            $table->decimal('yan_zun', 65, 0)->default(0)->change();
            $table->decimal('yan_an', 65, 0)->default(0)->change();
            $table->decimal('long_sheng', 65, 0)->default(0)->change();
            $table->decimal('long_zhan', 65, 0)->default(0)->change();
            $table->decimal('yan_jue', 65, 0)->default(0)->change();
            $table->decimal('yan_ze', 65, 0)->default(0)->change();
            $table->decimal('yan_di', 65, 0)->default(0)->change();
            $table->decimal('yan_yuan', 65, 0)->default(0)->change();
        });

        Schema::table('grudges', function (Blueprint $table) {
            $table->decimal('quantity', 65, 0)->default(0)->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('military_records', function (Blueprint $table) {
            $table->bigInteger('quantity')->default(0)->change();
            $table->integer('yan_zun')->default(0)->change();
            $table->integer('yan_an')->default(0)->change();
            $table->integer('long_sheng')->default(0)->change();
            $table->integer('long_zhan')->default(0)->change();
            $table->integer('yan_jue')->default(0)->change();
            $table->integer('yan_ze')->default(0)->change();
            $table->integer('yan_di')->default(0)->change();
            $table->integer('yan_yuan')->default(0)->change();
        });

        Schema::table('grudges', function (Blueprint $table) {
            $table->bigInteger('quantity')->default(0)->change();
        });
    }
};
