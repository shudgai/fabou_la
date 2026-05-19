<?php

namespace Database\Seeders;

use App\Models\Teaching;
use App\Models\DharmaName;
use App\Models\User;
use Illuminate\Database\Seeder;

class TeachingSeeder extends Seeder
{
    public function run(): void
    {
        \Illuminate\Support\Facades\Schema::disableForeignKeyConstraints();
        Teaching::truncate();
        \Illuminate\Support\Facades\Schema::enableForeignKeyConstraints();
    }
}
