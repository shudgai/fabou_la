<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // teachings: speed up per-user, per-master, per-date queries
        Schema::table('teachings', function (Blueprint $table) {
            if (!$this->indexExists('teachings', 'idx_teachings_user_master_date')) {
                $table->index(['user_id', 'master_id', 'date'], 'idx_teachings_user_master_date');
            }
            if (!$this->indexExists('teachings', 'idx_teachings_user_is_daily')) {
                $table->index(['user_id', 'is_daily'], 'idx_teachings_user_is_daily');
            }
        });

        // registries: speed up per-user, per-master, per-category queries
        Schema::table('registries', function (Blueprint $table) {
            if (!$this->indexExists('registries', 'idx_registries_user_master_cat')) {
                $table->index(['user_id', 'master_id', 'category'], 'idx_registries_user_master_cat');
            }
        });

        // imperial_graces: speed up per-user, per-master, status queries
        Schema::table('imperial_graces', function (Blueprint $table) {
            if (!$this->indexExists('imperial_graces', 'idx_imperial_graces_user_master_status')) {
                $table->index(['user_id', 'master_id', 'status'], 'idx_imperial_graces_user_master_status');
            }
        });

        // military_records: speed up per-user, per-army queries
        Schema::table('military_records', function (Blueprint $table) {
            if (!$this->indexExists('military_records', 'idx_military_records_user_army')) {
                $table->index(['user_id', 'army_type'], 'idx_military_records_user_army');
            }
        });

        // grudges: speed up per-user queries
        Schema::table('grudges', function (Blueprint $table) {
            if (!$this->indexExists('grudges', 'idx_grudges_user_id')) {
                $table->index(['user_id'], 'idx_grudges_user_id');
            }
        });
    }

    public function down(): void
    {
        Schema::table('teachings', function (Blueprint $table) {
            $table->dropIndex('idx_teachings_user_master_date');
            $table->dropIndex('idx_teachings_user_is_daily');
        });
        Schema::table('registries', function (Blueprint $table) {
            $table->dropIndex('idx_registries_user_master_cat');
        });
        Schema::table('imperial_graces', function (Blueprint $table) {
            $table->dropIndex('idx_imperial_graces_user_master_status');
        });
        Schema::table('military_records', function (Blueprint $table) {
            $table->dropIndex('idx_military_records_user_army');
        });
        Schema::table('grudges', function (Blueprint $table) {
            $table->dropIndex('idx_grudges_user_id');
        });
    }

    private function indexExists(string $table, string $indexName): bool
    {
        try {
            $indexes = \Illuminate\Support\Facades\DB::select("SHOW INDEX FROM `{$table}` WHERE Key_name = ?", [$indexName]);
            return !empty($indexes);
        } catch (\Exception $e) {
            return false;
        }
    }
};
