<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('imperial_graces', function (Blueprint $table) {
            if (!Schema::hasColumn('imperial_graces', 'is_multi')) {
                $table->boolean('is_multi')->default(false)->after('name');
            }
        });

        Schema::table('dharma_name_registries', function (Blueprint $table) {
            if (!Schema::hasColumn('dharma_name_registries', 'imperial_grace_id')) {
                $table->unsignedBigInteger('imperial_grace_id')->nullable()->after('registry_id');
                $table->index('imperial_grace_id');
            }
        });
    }

    public function down(): void
    {
        Schema::table('imperial_graces', function (Blueprint $table) {
            $table->dropColumn('is_multi');
        });

        Schema::table('dharma_name_registries', function (Blueprint $table) {
            $table->dropColumn('imperial_grace_id');
        });
    }
};
