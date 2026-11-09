<?php

namespace Tests\Feature;

use App\Models\Inventory;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class InventoryCatalogTest extends TestCase
{
    use RefreshDatabase;

    public function test_students_and_teachers_can_view_the_catalog_and_choose_an_inventory_to_borrow(): void
    {
        $inventory = Inventory::factory()->create([
            'name' => 'Barang Contoh',
            'image_path' => 'inventories/barang-contoh.jpg',
        ]);

        foreach (['siswa', 'guru'] as $role) {
            $this->actingAs(User::factory()->create(['role' => $role]))
                ->get(route('inventories.catalog'))
                ->assertOk()
                ->assertSee('Barang Contoh')
                ->assertSee('Foto Barang Contoh')
                ->assertSee(route('inventories.image', $inventory), false)
                ->assertSee(route('loans.create', ['inventory_id' => $inventory->id]), false)
                ->assertSee('Klik untuk memesan')
                ->assertSee(route('inventories.catalog'), false);

            $this->get(route('loans.create', ['inventory_id' => $inventory->id]))
                ->assertOk()
                ->assertSee('selected', false);
        }
    }

    public function test_only_students_and_teachers_can_open_the_catalog(): void
    {
        $this->actingAs(User::factory()->create(['role' => 'admin']))
            ->get(route('inventories.catalog'))
            ->assertForbidden();
    }

    public function test_admin_can_create_an_inventory_with_a_photo(): void
    {
        Storage::fake('public');
        $admin = User::factory()->create(['role' => 'admin']);

        $response = $this->actingAs($admin)->post(route('inventories.store'), [
            'code' => 'AST-101',
            'name' => 'Kamera',
            'category' => 'Multimedia',
            'total_stock' => 2,
            'condition' => 'Baik',
            'image' => UploadedFile::fake()->image('kamera.jpg'),
        ]);

        $response->assertRedirect();
        $inventory = Inventory::where('code', 'AST-101')->firstOrFail();
        $this->assertSame(2, $inventory->available_stock);
        $this->assertNotNull($inventory->image_path);
        Storage::disk('public')->assertExists($inventory->image_path);
    }

    public function test_authenticated_users_can_view_an_inventory_photo(): void
    {
        Storage::fake('public');
        Storage::disk('public')->put('inventories/kamera.jpg', 'photo contents');
        $inventory = Inventory::factory()->create(['image_path' => 'inventories/kamera.jpg']);

        $this->actingAs(User::factory()->create(['role' => 'siswa']))
            ->get(route('inventories.image', $inventory))
            ->assertOk()
            ->assertHeader('Cache-Control', 'no-store, private')
            ->assertHeader('X-Content-Type-Options', 'nosniff');
    }
}
