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
            $table->integer('yan_jue')->default(0)->after('long_zhan');
            $table->integer('yan_ze')->default(0)->after('yan_jue');
            $table->integer('yan_di')->default(0)->after('yan_ze');
            $table->integer('yan_yuan')->default(0)->after('yan_di');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('military_records', function (Blueprint $table) {
            $table->dropColumn(['yan_jue', 'yan_ze', 'yan_di', 'yan_yuan']);
        });
    }
};
