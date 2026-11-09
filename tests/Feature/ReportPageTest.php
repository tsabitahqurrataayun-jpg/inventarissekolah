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
            ->assertSee('sidebar-nav.css')
            ->assertSee('dashboard-metrics.css')
            ->assertSee('Apakah kamu benar ingin menerima peminjaman tersebut?')
            ->assertSee('data-approval-confirmation-yes', false)
            ->assertSee('data-approval-confirmation-no', false)
            ->assertSee('Selamat pagi, Admin SIPARIS')
            ->assertSee('class="role-pill">Admin</span>', false)
            ->assertSee('class="admin-metric metric-link" href="http://localhost/inventaris"', false)
            ->assertSee('class="admin-metric metric-link" href="http://localhost/peminjaman"', false)
            ->assertDontSee('Selamat pagi, Admin Sarana');
    }

    public function test_student_dashboard_metrics_link_to_relevant_pages(): void
    {
        $student = User::factory()->create(['role' => 'siswa']);

        $this->actingAs($student)
            ->get('/dashboard')
            ->assertOk()
            ->assertSee('class="role-pill">Siswa</span>', false)
            ->assertSee('class="student-metric metric-link" href="http://localhost/peminjaman/baru"', false)
            ->assertSee('class="student-metric metric-link" href="http://localhost/peminjaman"', false);
    }

    public function test_dashboard_calendar_dates_show_due_loan_details(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $borrower = User::factory()->create(['role' => 'guru', 'name' => 'Guru Kalender']);
        $inventory = Inventory::factory()->create(['name' => 'Laptop Jatuh Tempo']);
        $dueDate = today()->startOfMonth()->addDays(10);

        Loan::factory()->create([
            'user_id' => $borrower->id,
            'inventory_id' => $inventory->id,
            'quantity' => 2,
            'status' => 'borrowed',
            'due_at' => $dueDate,
        ]);

        $this->actingAs($admin)
            ->get('/dashboard')
            ->assertOk()
            ->assertSee('return-date-dialog.css')
            ->assertSee('data-return-date-button', false)
            ->assertSee('data-due-date="'.$dueDate->format('Y-m-d').'"', false)
            ->assertSee('Laptop Jatuh Tempo')
            ->assertSee('Guru Kalender')
            ->assertSee('2 unit')
            ->assertSee('JATUH TEMPO PEMINJAMAN');
    }

    public function test_admin_dashboard_shows_unreturned_loans_before_confirmed_returns(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $borrower = User::factory()->create(['role' => 'guru', 'name' => 'Bu Rina']);
        $borrowedItem = Inventory::factory()->create(['name' => 'Speaker Belum Kembali']);
        $returnedItem = Inventory::factory()->create(['name' => 'Speaker Sudah Kembali']);

        Loan::factory()->create([
            'user_id' => $borrower->id,
            'inventory_id' => $borrowedItem->id,
            'status' => 'borrowed',
            'due_at' => today()->addDays(2),
        ]);
        Loan::factory()->create([
            'user_id' => $borrower->id,
            'inventory_id' => $returnedItem->id,
            'status' => 'returned',
            'returned_at' => now(),
        ]);

        $this->actingAs($admin)
            ->get('/dashboard')
            ->assertOk()
            ->assertSee('Status Peminjaman Terbaru')
            ->assertSee('Barang yang belum dikembalikan ditampilkan terlebih dahulu')
            ->assertSee('Belum dikembalikan · jatuh tempo')
            ->assertSeeInOrder(['Speaker Belum Kembali', 'Speaker Sudah Kembali']);
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
            ->assertSee('Diagram status peminjaman')
            ->assertSee('Tren transaksi 6 bulan')
            ->assertSee('Barang paling sering dipinjam')
            ->assertSee('Laptop Lenovo ThinkPad')
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
            ->assertSee('Diagram status peminjaman')
            ->assertSee('Grafik batang jumlah transaksi dalam enam bulan terakhir')
            ->assertSee('Barang paling sering dipinjam')
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
