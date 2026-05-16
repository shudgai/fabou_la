<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Registry;
use App\Models\DharmaNameRegistry;
use App\Models\Teaching;
use App\Models\Grudge;
use App\Models\MilitaryRecord;
use App\Models\OtherRecord;
use App\Models\ImperialGrace;
use Illuminate\Support\Facades\Crypt;

class EncryptExistingData extends Command
{
    protected $signature = 'data:encrypt';
    protected $description = 'Encrypt existing plain text data in sensitive columns';

    public function handle()
    {
        $this->info('Starting encryption of existing data...');

        // 1. Registries
        $this->encryptTable(Registry::class, ['purpose', 'effect', 'acquisition_method', 'content', 'remarks']);

        // 2. DharmaNameRegistry
        $this->encryptTable(DharmaNameRegistry::class, ['custom_name', 'remarks', 'related_personnel']);

        // 3. Teachings
        $this->encryptTable(Teaching::class, ['content', 'items', 'items_footer_remarks']);

        // 4. Grudges
        $this->encryptTable(Grudge::class, ['remarks_text']);

        // 5. MilitaryRecord
        $this->encryptTable(MilitaryRecord::class, ['remarks_text']);

        // 6. OtherRecord
        $this->encryptTable(OtherRecord::class, ['title', 'content']);

        // 7. ImperialGrace
        $this->encryptTable(ImperialGrace::class, ['purpose', 'remarks']);

        $this->info('Encryption complete!');
    }

    private function encryptTable($modelClass, $columns)
    {
        $this->info("Processing {$modelClass}...");
        $tableName = (new $modelClass)->getTable();
        
        $query = \Illuminate\Support\Facades\DB::table($tableName);
        $items = $query->get();
        $count = 0;

        foreach ($items as $item) {
            $updates = [];
            foreach ($columns as $column) {
                if (!isset($item->$column)) continue;
                $value = $item->$column;
                
                if ($value === null || $value === '') continue;

                // Check if already encrypted (Laravel encryption starts with certain prefix or looks like base64-json)
                $isEncrypted = false;
                try {
                    $decrypted = Crypt::decryptString($value);
                    $isEncrypted = true;
                } catch (\Exception $e) {
                    $isEncrypted = false;
                }

                if (!$isEncrypted) {
                    $updates[$column] = Crypt::encryptString($value);
                }
            }

            if (!empty($updates)) {
                \Illuminate\Support\Facades\DB::table($tableName)
                    ->where('id', $item->id)
                    ->update($updates);
                $count++;
            }
        }

        $this->info("Encrypted {$count} records in {$tableName}");
    }
}
