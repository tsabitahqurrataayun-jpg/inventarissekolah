<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('inventories', function (Blueprint $table) {
            $table->string('image_path')->nullable();
        });

        DB::table('inventories')
            ->where('code', 'AST-013')
            ->update(['image_path' => 'inventories/arduino-uno.png']);
    }

    public function down(): void
    {
        Schema::table('inventories', function (Blueprint $table) {
            $table->dropColumn('image_path');
        });
    }
};
