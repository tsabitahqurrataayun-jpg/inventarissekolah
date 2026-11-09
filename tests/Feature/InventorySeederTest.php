<?php

namespace Tests\Feature;

use App\Models\Inventory;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class InventorySeederTest extends TestCase
{
    use RefreshDatabase;

    public function test_it_seeds_the_requested_inventory_and_stock_counts(): void
    {
        $this->seed();

        $expectedStock = [
            'AST-001' => 4,
            'AST-006' => 3,
            'AST-007' => 10,
            'AST-008' => 20,
            'AST-009' => 10,
            'AST-011' => 3,
            'AST-012' => 1,
            'AST-013' => 30,
            'AST-014' => 1,
            'AST-015' => 1,
            'AST-016' => 1,
            'AST-017' => 1,
            'AST-018' => 1,
        ];

        foreach ($expectedStock as $code => $stock) {
            $inventory = Inventory::where('code', $code)->firstOrFail();

            $this->assertSame($stock, $inventory->total_stock);
            $this->assertSame($stock, $inventory->available_stock);
        }

        $this->assertSame('Obeng mini (1 set)', Inventory::where('code', 'AST-012')->value('name'));
        $this->assertSame('Proyektor Epson', Inventory::where('code', 'AST-001')->value('name'));
        $this->assertSame(0, Inventory::whereIn('code', ['AST-003', 'AST-010', 'AST-019'])->count());
    }
}
