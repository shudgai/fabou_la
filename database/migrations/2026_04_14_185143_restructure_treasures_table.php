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
        Schema::table('treasures', function (Blueprint $table) {
            $table->dropColumn(['category', 'options']);
            
            $table->foreignId('master_id')->nullable()->constrained()->after('id');
            $table->text('purpose')->nullable()->after('name');
            $table->text('acquisition_method')->nullable()->after('purpose');
            $table->text('remarks')->nullable()->after('acquisition_method');
            $table->date('record_date')->nullable()->after('remarks');
            $table->date('obtained_date')->nullable()->after('record_date');
        });
    }

    public function down(): void
    {
        Schema::table('treasures', function (Blueprint $table) {
            $table->dropForeign(['master_id']);
            $table->dropColumn(['master_id', 'purpose', 'acquisition_method', 'remarks', 'record_date', 'obtained_date']);
            $table->string('category')->nullable();
            $table->json('options')->nullable();
        });
    }
};
