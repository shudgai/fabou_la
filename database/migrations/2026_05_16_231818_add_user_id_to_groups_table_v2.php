<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('groups', function (Blueprint $table) {
            // 1. Add user_id column
            $table->unsignedBigInteger('user_id')->nullable()->after('id');
            
            // 2. Drop the existing global unique name constraint
            $table->dropUnique('groups_name_unique');
            
            // 3. Add a new composite unique constraint (user_id, name)
            $table->unique(['user_id', 'name']);
            
            // 4. Add foreign key (optional but recommended)
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
        });

        // 5. Assign existing groups to the first admin user (usually ID 1 or the first user)
        $firstUser = \App\Models\User::first();
        if ($firstUser) {
            \App\Models\Group::whereNull('user_id')->update(['user_id' => $firstUser->id]);
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('groups', function (Blueprint $table) {
            $table->dropForeign(['user_id']);
            $table->dropUnique(['user_id', 'name']);
            $table->dropColumn('user_id');
            $table->unique('name', 'groups_name_unique');
        });
    }
};
