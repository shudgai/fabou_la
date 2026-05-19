<?php

namespace Database\Seeders;

use App\Models\OtherFolder;
use App\Models\User;
use Illuminate\Database\Seeder;

class OtherSeeder extends Seeder
{
    public function run(): void
    {
        \Illuminate\Support\Facades\Schema::disableForeignKeyConstraints();
        OtherFolder::truncate();
        \Illuminate\Support\Facades\Schema::enableForeignKeyConstraints();
    }
}
