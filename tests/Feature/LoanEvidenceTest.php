<?php

namespace Tests\Feature;

use App\Models\Inventory;
use App\Models\Loan;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class LoanEvidenceTest extends TestCase
{
    use RefreshDatabase;

    public function test_loan_request_requires_and_stores_borrowing_evidence(): void
    {
        Storage::fake('local');
        $student = User::factory()->create(['role' => 'siswa']);
        $admin = User::factory()->create(['role' => 'admin']);
        $inventory = Inventory::factory()->create(['total_stock' => 5, 'available_stock' => 5]);

        $this->actingAs($student)
            ->get('/peminjaman/baru')
            ->assertOk()
            ->assertSee('mobile-menu-toggle', false)
            ->assertSee('responsive.css')
            ->assertSee('name="borrowing_evidence"', false);

        $this->post('/peminjaman', [
            'inventory_id' => $inventory->id,
            'quantity' => 1,
            'due_at' => today()->addDays(3)->toDateString(),
        ])->assertSessionHasErrors('borrowing_evidence');

        $this->assertDatabaseCount('loans', 0);

        $this->post('/peminjaman', [
            'inventory_id' => $inventory->id,
            'quantity' => 1,
            'due_at' => today()->addDays(3)->toDateString(),
            'borrowing_evidence' => UploadedFile::fake()->image('condition.jpg'),
        ])->assertRedirect('/peminjaman');

        $loan = Loan::firstOrFail();

        $this->assertSame('pending', $loan->status);
        Storage::disk('local')->assertExists($loan->borrowing_evidence_path);
        $this->actingAs($admin)
            ->get('/peminjaman')
            ->assertOk()
            ->assertSee('Bukti peminjaman')
            ->assertSee('loan-evidence-link borrowing', false);
        $this->get('/dashboard')
            ->assertOk()
            ->assertSee('Bukti peminjaman');
    }

    public function test_return_evidence_requires_admin_confirmation_before_stock_is_restored(): void
    {
        Storage::fake('local');
        $student = User::factory()->create(['role' => 'siswa']);
        $otherStudent = User::factory()->create(['role' => 'siswa']);
        $admin = User::factory()->create(['role' => 'admin']);
        $inventory = Inventory::factory()->create(['total_stock' => 5, 'available_stock' => 3]);
        $loan = Loan::factory()->create([
            'user_id' => $student->id,
            'inventory_id' => $inventory->id,
            'quantity' => 2,
            'status' => 'approved',
            'borrowing_evidence_path' => 'loan-evidence/borrowing/original.jpg',
        ]);
        Storage::disk('local')->put($loan->borrowing_evidence_path, 'borrowing proof');

        $response = $this->followingRedirects()
            ->actingAs($student)
            ->post(route('loans.return', $loan), [
                'return_evidence' => UploadedFile::fake()->image('returned.jpg'),
            ]);
        $response->assertOk();
        $this->assertSame(1, substr_count($response->getContent(), 'Bukti pengembalian dikirim.'));

        $loan->refresh();
        $this->assertSame('return_pending', $loan->status);
        Storage::disk('local')->assertExists($loan->return_evidence_path);
        $this->assertSame(3, $inventory->fresh()->available_stock);
        $this->get('/dashboard')
            ->assertOk()
            ->assertSee('Menunggu verifikasi pengembalian');

        $this->actingAs($admin)
            ->get('/dashboard')
            ->assertOk()
            ->assertSee('Menunggu verifikasi pengembalian')
            ->assertSee('Bukti pengembalian');

        $this->actingAs($otherStudent)
            ->get(route('loans.evidence', [$loan, 'return']))
            ->assertForbidden();

        $this->actingAs($admin)
            ->get(route('loans.evidence', [$loan, 'return']))
            ->assertOk()
            ->assertHeader('Content-Type', 'image/jpeg');

        $this->patch(route('loans.status', $loan), ['status' => 'returned'])
            ->assertRedirect();

        $this->assertSame('returned', $loan->fresh()->status);
        $this->assertNotNull($loan->fresh()->returned_at);
        $this->assertSame(5, $inventory->fresh()->available_stock);
    }

    public function test_admin_cannot_confirm_a_return_without_return_evidence(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $loan = Loan::factory()->create(['status' => 'approved']);

        $this->actingAs($admin)
            ->patch(route('loans.status', $loan), ['status' => 'returned'])
            ->assertUnprocessable();

        $this->assertSame('approved', $loan->fresh()->status);
    }

    public function test_admin_return_history_only_shows_confirmed_returns(): void
    {
        Storage::fake('local');
        $admin = User::factory()->create(['role' => 'admin']);
        $student = User::factory()->create(['role' => 'siswa', 'name' => 'Alya Pratama']);
        $returnedInventory = Inventory::factory()->create(['name' => 'Laptop HP', 'code' => 'AST-006']);
        $activeInventory = Inventory::factory()->create(['name' => 'Mouse', 'code' => 'AST-008']);

        $returnedLoan = Loan::factory()->create([
            'user_id' => $student->id,
            'inventory_id' => $returnedInventory->id,
            'quantity' => 2,
            'status' => 'returned',
            'returned_at' => now()->subDay(),
            'return_evidence_path' => 'loan-evidence/return/laptop-return.jpg',
        ]);
        Loan::factory()->create([
            'user_id' => $student->id,
            'inventory_id' => $activeInventory->id,
            'status' => 'return_pending',
            'return_evidence_path' => 'loan-evidence/return/mouse-return.jpg',
        ]);
        Loan::factory()->create([
            'user_id' => $student->id,
            'inventory_id' => $activeInventory->id,
            'status' => 'approved',
        ]);

        $this->actingAs($admin)
            ->get('/pengembalian')
            ->assertOk()
            ->assertSee('Pengembalian Barang')
            ->assertSee('Laptop HP')
            ->assertSee('AST-006')
            ->assertSee('Alya Pratama')
            ->assertSee('Bukti pengembalian')
            ->assertSee('Transaksi dikembalikan')
            ->assertDontSee('Mouse')
            ->assertSee('class="nav-link active" href="http://localhost/pengembalian"', false);

        $this->actingAs($student)
            ->get('/pengembalian')
            ->assertForbidden();

        $this->assertSame('returned', $returnedLoan->fresh()->status);
    }
}
