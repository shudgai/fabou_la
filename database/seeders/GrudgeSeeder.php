<?php

namespace Database\Seeders;

use App\Models\Grudge;
use App\Models\User;
use App\Models\DharmaName;
use Illuminate\Database\Seeder;

class GrudgeSeeder extends Seeder
{
    public function run(): void
    {
        \Illuminate\Support\Facades\Schema::disableForeignKeyConstraints();
        Grudge::truncate();
        \Illuminate\Support\Facades\Schema::enableForeignKeyConstraints();
    }
}
