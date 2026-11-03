<?php

namespace Tests\Feature;

use App\Models\Inventory;
use App\Models\Loan;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ReportPageTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_dashboard_shows_the_siparis_greeting(): void
    {
        $admin = User::factory()->create(['role' => 'admin', 'name' => 'Admin Sarana']);

        $this->actingAs($admin)
            ->get('/dashboard')
            ->assertOk()
            ->assertSee('Selamat pagi, Admin SIPARIS')
            ->assertDontSee('Selamat pagi, Admin Sarana');
    }

    public function test_admin_can_view_laporan_page_with_loan_statistics(): void
    {
        $admin = User::factory()->create(['role' => 'admin', 'email' => 'admin@sekolah.id']);
        $user = User::factory()->create(['role' => 'siswa', 'email' => 'siswa@sekolah.id']);
        $inventory = Inventory::factory()->create(['name' => 'Laptop Lenovo ThinkPad', 'available_stock' => 5]);

        Loan::factory()->create([
            'user_id' => $user->id,
            'inventory_id' => $inventory->id,
            'quantity' => 2,
            'status' => 'pending',
            'due_at' => now()->addDays(7),
        ]);

        Loan::factory()->create([
            'user_id' => $user->id,
            'inventory_id' => $inventory->id,
            'quantity' => 1,
            'status' => 'returned',
            'due_at' => now()->subDays(2),
            'returned_at' => now(),
        ]);

        $this->actingAs($admin)
            ->get('/laporan')
            ->assertOk()
            ->assertSee('Laporan Peminjaman')
            ->assertSee('Menunggu Verifikasi')
            ->assertSee('Dikembalikan');
    }

    public function test_admin_can_open_a_print_ready_filtered_report(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $borrower = User::factory()->create(['role' => 'siswa', 'name' => 'Alya Pratama']);
        $inventory = Inventory::factory()->create(['name' => 'Laptop HP', 'code' => 'AST-006']);

        Loan::factory()->create([
            'user_id' => $borrower->id,
            'inventory_id' => $inventory->id,
            'quantity' => 2,
            'status' => 'pending',
        ]);

        $this->actingAs($admin)
            ->get('/laporan/cetak?status=pending')
            ->assertOk()
            ->assertSee('Rekap Peminjaman Inventaris')
            ->assertSee('SMK NEGERI 5 SURAKARTA')
            ->assertSee('Alya Pratama')
            ->assertSee('Laptop HP')
            ->assertSee('2 unit')
            ->assertSee('Cetak / Simpan PDF')
            ->assertDontSee('Data Inventaris');
    }

    public function test_excel_csv_export_is_readable_and_obeys_the_status_filter(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $borrower = User::factory()->create(['role' => 'siswa', 'name' => 'Alya Pratama']);
        $inventory = Inventory::factory()->create(['name' => 'Laptop HP', 'code' => 'AST-006']);

        Loan::factory()->create([
            'user_id' => $borrower->id,
            'inventory_id' => $inventory->id,
            'quantity' => 2,
            'status' => 'pending',
        ]);
        Loan::factory()->create([
            'user_id' => $borrower->id,
            'inventory_id' => $inventory->id,
            'status' => 'returned',
        ]);

        $response = $this->actingAs($admin)
            ->get('/laporan/export?status=pending')
            ->assertDownload('rekap-peminjaman-pending.csv');

        $csv = $response->streamedContent();

        $this->assertStringStartsWith("\xEF\xBB\xBF", $csv);
        $rows = array_map(
            fn (string $row): array => str_getcsv($row, ';', '"', ''),
            preg_split('/\r\n|\n|\r/', substr($csv, 3), -1, PREG_SPLIT_NO_EMPTY),
        );

        $this->assertSame([
            'No',
            'Nama Peminjam',
            'Peran',
            'Nama Inventaris',
            'Kode Inventaris',
            'Jumlah',
            'Tanggal Pinjam',
            'Jatuh Tempo',
            'Tanggal Kembali',
            'Status',
        ], $rows[0]);
        $this->assertSame('Alya Pratama', $rows[1][1]);
        $this->assertSame('Laptop HP', $rows[1][3]);
        $this->assertSame('2 unit', $rows[1][5]);
        $this->assertSame('Menunggu Verifikasi', $rows[1][9]);
        $this->assertCount(2, $rows);
    }
}
