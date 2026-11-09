<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        if (DB::table('inventories')->where('code', 'AST-001')->exists()) {
            return;
        }

        DB::table('inventories')->insert([
            'code' => 'AST-001',
            'name' => 'Proyektor Epson',
            'category' => 'Elektronik',
            'description' => null,
            'total_stock' => 4,
            'available_stock' => 4,
            'condition' => 'Baik',
            'image_path' => 'inventories/proyektor-epson.png',
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }

    public function down(): void
    {
        throw new RuntimeException('Proyektor Epson restoration is retained and cannot be rolled back.');
    }
};
