<?php

namespace Database\Seeders;

use App\Models\MilitaryRecord;
use App\Models\User;
use Illuminate\Database\Seeder;

class MilitarySeeder extends Seeder
{
    public function run(): void
    {
        \Illuminate\Support\Facades\Schema::disableForeignKeyConstraints();
        MilitaryRecord::truncate();
        \Illuminate\Support\Facades\Schema::enableForeignKeyConstraints();
    }
}
