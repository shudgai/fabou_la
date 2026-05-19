<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $backupDir = database_path('seeders/data');
        $hasBackup = file_exists($backupDir . '/users.json') && file_exists($backupDir . '/treasures.json');

        if ($hasBackup) {
            $this->command->info("---------------------------------------------");
            $this->command->info("  [Antigravity] Backup JSON files detected!");
            $this->command->info("  Automatically restoring custom database...");
            $this->command->info("---------------------------------------------");
            $this->restoreFromBackup($backupDir);
            $this->command->info("---------------------------------------------");
            $this->command->info("  Database successfully restored from backup!");
            $this->command->info("---------------------------------------------");
            return;
        }

        $this->call([
            MasterSeeder::class,
            UserSeeder::class,
            DharmaNameSeeder::class,
            GroupSeeder::class,
            DharmaGroupSeeder::class,
            TreasureSeeder::class,
            ImperialGraceSeeder::class,
            RegistrySeeder::class,
            TeachingSeeder::class,
            GrudgeSeeder::class,
            MilitarySeeder::class,
            OtherSeeder::class,
        ]);

        if (app()->environment('local', 'testing')) {
            $this->call([
                // TeachingSampleSeeder::class, // 註解掉範例資料
            ]);
        }
    }

    /**
     * Restore all tables from pretty printed JSON backup files
     */
    private function restoreFromBackup(string $dir): void
    {
        $tables = [
            'users',
            'roles',
            'role_user',
            'masters',
            'dharma_names',
            'groups',
            'dharma_groups',
            'treasures',
            'imperial_graces',
            'registries',
            'dharma_name_registries',
            'teachings',
            'grudges',
            'military_records',
            'other_records',
            'weekly_posts',
            'self_posts',
            'other_folders'
        ];

        $clearTables = [
            'imperial_graces',
            'registries',
            'dharma_name_registries',
            'teachings',
            'grudges',
            'military_records',
            'other_records',
            'weekly_posts',
            'self_posts',
            'other_folders'
        ];

        DB::statement('SET FOREIGN_KEY_CHECKS=0;');

        foreach ($tables as $table) {
            // Check if the table is requested to be empty
            if (in_array($table, $clearTables)) {
                if (Schema::hasTable($table)) {
                    DB::table($table)->truncate();
                    $this->command->info("  ✓ Cleared table '{$table}' (empty by request).");
                }
                continue;
            }

            $jsonPath = $dir . '/' . $table . '.json';
            if (!file_exists($jsonPath) || !Schema::hasTable($table)) {
                continue;
            }

            $json = file_get_contents($jsonPath);
            $rows = json_decode($json, true);

            if (is_array($rows) && !empty($rows)) {
                DB::table($table)->truncate();
                
                // Chunk insertion to prevent potential max placeholder limits
                $chunks = array_chunk($rows, 100);
                foreach ($chunks as $chunk) {
                    DB::table($table)->insert($chunk);
                }
                
                $this->command->info("  ✓ Restored table '{$table}' -> " . count($rows) . " rows.");
            } else {
                DB::table($table)->truncate();
                $this->command->info("  ✓ Cleared table '{$table}' (empty backup).");
            }
        }

        DB::statement('SET FOREIGN_KEY_CHECKS=1;');
    }
}

