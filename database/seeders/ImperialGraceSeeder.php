<?php

namespace Database\Seeders;

use App\Models\ImperialGrace;
use App\Models\User;
use Illuminate\Database\Seeder;

class ImperialGraceSeeder extends Seeder
{
    public function run(): void
    {
        \Illuminate\Support\Facades\Schema::disableForeignKeyConstraints();
        ImperialGrace::truncate();
        \Illuminate\Support\Facades\Schema::enableForeignKeyConstraints();
    }
}
