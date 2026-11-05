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

    public function test_loan_request_does_not_require_borrowing_evidence(): void
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
            ->assertDontSee('name="borrowing_evidence"', false)
            ->assertDontSee('Bukti kondisi saat menerima barang')
            ->assertSee('name="borrowed_at"', false)
            ->assertSee('Dipinjam sampai')
            ->assertSee('data-available-stock="5"', false)
            ->assertSee('data-stock-toast', false)
            ->assertSee('stock-toast.css')
            ->assertSee('loan-confirmation.css')
            ->assertSee('Apakah kamu benar mau mengajukan peminjaman?')
            ->assertSee('data-loan-confirmation-yes', false)
            ->assertSee('data-loan-confirmation-no', false);

        $this->post('/peminjaman', [
            'inventory_id' => $inventory->id,
            'quantity' => 1,
            'borrowed_at' => today()->toDateString(),
            'due_at' => today()->addDays(3)->toDateString(),
        ])->assertRedirect('/peminjaman');

        $loan = Loan::firstOrFail();

        $this->assertSame('pending', $loan->status);
        $this->assertSame(today()->toDateString(), $loan->borrowed_at->toDateString());
        $this->assertSame(today()->addDays(3)->toDateString(), $loan->due_at->toDateString());
        $this->assertNull($loan->borrowing_evidence_path);
        $this->actingAs($admin)
            ->get('/peminjaman')
            ->assertOk()
            ->assertDontSee('Bukti peminjaman');
        $this->get('/dashboard')
            ->assertOk()
            ->assertDontSee('Bukti peminjaman');
    }

    public function test_loan_due_date_cannot_be_before_the_borrowing_date(): void
    {
        Storage::fake('local');
        $student = User::factory()->create(['role' => 'siswa']);
        $inventory = Inventory::factory()->create(['total_stock' => 5, 'available_stock' => 5]);

        $this->actingAs($student)
            ->post('/peminjaman', [
                'inventory_id' => $inventory->id,
                'quantity' => 1,
                'borrowed_at' => today()->addDays(2)->toDateString(),
                'due_at' => today()->addDay()->toDateString(),
            ])
            ->assertSessionHasErrors('due_at');

        $this->assertDatabaseCount('loans', 0);
    }

    public function test_requesting_more_units_than_available_shows_a_stock_error(): void
    {
        Storage::fake('local');
        $student = User::factory()->create(['role' => 'siswa']);
        $inventory = Inventory::factory()->create([
            'name' => 'Speaker',
            'total_stock' => 2,
            'available_stock' => 1,
        ]);

        $this->actingAs($student)
            ->post('/peminjaman', [
                'inventory_id' => $inventory->id,
                'quantity' => 2,
                'borrowed_at' => today()->toDateString(),
                'due_at' => today()->addDays(3)->toDateString(),
            ])
            ->assertRedirect()
            ->assertSessionHasErrors([
                'quantity' => 'Tidak bisa mengajukan 2 unit Speaker. Barang yang tersedia hanya 1 unit.',
            ]);

        $this->assertDatabaseCount('loans', 0);
    }

    public function test_admin_dashboard_notifies_about_loans_past_the_due_date(): void
    {
        $this->travelTo(today()->startOfDay());
        $admin = User::factory()->create(['role' => 'admin']);
        $student = User::factory()->create(['role' => 'siswa', 'name' => 'Alya Pratama']);
        $inventory = Inventory::factory()->create(['name' => 'Laptop HP']);

        Loan::factory()->create([
            'user_id' => $student->id,
            'inventory_id' => $inventory->id,
            'status' => 'approved',
            'due_at' => today()->subDay(),
        ]);
        Loan::factory()->create([
            'user_id' => $student->id,
            'inventory_id' => $inventory->id,
            'status' => 'return_pending',
            'due_at' => today()->subDays(2),
        ]);
        Loan::factory()->create([
            'user_id' => $student->id,
            'inventory_id' => $inventory->id,
            'status' => 'borrowed',
            'due_at' => today(),
        ]);

        $this->actingAs($admin)
            ->get('/dashboard')
            ->assertOk()
            ->assertSee('Pengingat pengembalian terlambat')
            ->assertSee('Alya Pratama')
            ->assertSee('Laptop HP')
            ->assertSee('1 peminjaman melewati tanggal pengembalian')
            ->assertSee('role="alert"', false);

        $this->actingAs($student)
            ->get('/dashboard')
            ->assertOk()
            ->assertDontSee('Pengingat pengembalian terlambat');
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

        $this->actingAs($student)
            ->post(route('loans.return', $loan), [])
            ->assertSessionHasErrors('return_evidence');

        $this->assertSame('approved', $loan->fresh()->status);

        $this->actingAs($student)
            ->get(route('loans.index'))
            ->assertOk()
            ->assertSee('name="return_evidence"', false)
            ->assertSee('required', false);

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

    public function test_duplicate_return_evidence_submission_shows_a_friendly_message(): void
    {
        Storage::fake('local');
        $student = User::factory()->create(['role' => 'siswa']);
        $loan = Loan::factory()->create([
            'user_id' => $student->id,
            'status' => 'return_pending',
            'return_evidence_path' => 'loan-evidence/return/already-sent.jpg',
        ]);
        Storage::disk('local')->put($loan->return_evidence_path, 'original evidence');

        $this->actingAs($student)
            ->post(route('loans.return', $loan), [
                'return_evidence' => UploadedFile::fake()->image('retry.jpg'),
            ])
            ->assertRedirect(route('loans.index'))
            ->assertSessionHas('error', 'Bukti pengembalian sudah dikirim atau peminjaman ini tidak lagi aktif. Silakan tunggu verifikasi admin.');

        $this->followingRedirects()
            ->actingAs($student)
            ->get(route('loans.index'))
            ->assertOk()
            ->assertSee('Bukti pengembalian sudah dikirim atau peminjaman ini tidak lagi aktif.');

        $loan->refresh();
        $this->assertSame('return_pending', $loan->status);
        $this->assertSame('loan-evidence/return/already-sent.jpg', $loan->return_evidence_path);
        $this->assertSame('original evidence', Storage::disk('local')->get($loan->return_evidence_path));
    }

    public function test_admin_must_provide_a_reason_when_rejecting_a_loan(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $student = User::factory()->create(['role' => 'siswa', 'name' => 'Alya Pratama']);
        $inventory = Inventory::factory()->create(['name' => 'Laptop HP']);
        $loan = Loan::factory()->create([
            'user_id' => $student->id,
            'inventory_id' => $inventory->id,
            'status' => 'pending',
        ]);

        $this->actingAs($admin)
            ->get('/dashboard')
            ->assertOk()
            ->assertSee('rejection-dialog.css')
            ->assertSee('Tolak pengajuan peminjaman?')
            ->assertSee('Tuliskan alasan agar siswa atau guru mengetahui mengapa peminjaman tidak diterima.')
            ->assertSee('data-rejection-reason', false)
            ->assertSee('Tolak peminjaman')
            ->assertDontSee('window.prompt')
            ->assertSee('Apakah kamu benar ingin menerima peminjaman tersebut?')
            ->assertSee('onsubmit="return requestApprovalConfirmation(event)"', false);

        $this->get(route('loans.index'))
            ->assertOk()
            ->assertSee('onsubmit="return requestApprovalConfirmation(event)"', false);

        $this->actingAs($admin)
            ->patch(route('loans.status', $loan), ['status' => 'rejected'])
            ->assertSessionHasErrors('rejection_reason');

        $this->assertSame('pending', $loan->fresh()->status);

        $this->actingAs($admin)
            ->patch(route('loans.status', $loan), [
                'status' => 'rejected',
                'rejection_reason' => 'Barang sedang digunakan untuk kegiatan sekolah.',
            ])
            ->assertRedirect();

        $loan->refresh();
        $this->assertSame('rejected', $loan->status);
        $this->assertSame('Barang sedang digunakan untuk kegiatan sekolah.', $loan->rejection_reason);

        $this->actingAs($student)
            ->get(route('loans.index'))
            ->assertOk()
            ->assertSee('Alasan penolakan: Barang sedang digunakan untuk kegiatan sekolah.');
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
