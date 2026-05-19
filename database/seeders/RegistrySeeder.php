<?php

namespace Database\Seeders;

use App\Models\Registry;
use App\Models\DharmaName;
use App\Models\DharmaNameRegistry;
use App\Models\User;
use Illuminate\Database\Seeder;

class RegistrySeeder extends Seeder
{
    public function run(): void
    {
        \Illuminate\Support\Facades\Schema::disableForeignKeyConstraints();
        Registry::truncate();
        DharmaNameRegistry::truncate();
        \Illuminate\Support\Facades\Schema::enableForeignKeyConstraints();
    }
}
