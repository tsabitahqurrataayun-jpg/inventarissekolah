<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

return new class extends Migration
{
    public function up(): void
    {
        $inventories = DB::table('inventories')
            ->whereIn('code', ['AST-003', 'AST-010', 'AST-019'])
            ->get(['id', 'image_path']);
        $inventoryIds = $inventories->pluck('id');

        $evidencePaths = DB::table('loans')
            ->whereIn('inventory_id', $inventoryIds)
            ->get(['borrowing_evidence_path', 'return_evidence_path'])
            ->flatMap(fn ($loan) => [$loan->borrowing_evidence_path, $loan->return_evidence_path])
            ->filter()
            ->unique();

        foreach ($evidencePaths as $path) {
            if (Storage::disk('local')->exists($path) && ! Storage::disk('local')->delete($path)) {
                throw new RuntimeException("Unable to delete loan evidence file: {$path}");
            }
        }

        foreach ($inventories->pluck('image_path')->filter()->unique() as $path) {
            if (Storage::disk('public')->exists($path) && ! Storage::disk('public')->delete($path)) {
                throw new RuntimeException("Unable to delete inventory image: {$path}");
            }
        }

        DB::table('inventories')->whereIn('id', $inventoryIds)->delete();
    }

    public function down(): void
    {
        throw new RuntimeException('Deleted inventory and loan data cannot be restored by rolling back this migration.');
    }
};
