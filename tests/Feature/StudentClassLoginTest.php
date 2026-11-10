<?php

namespace Tests\Feature;

use App\Models\Inventory;
use App\Models\Loan;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class StudentClassLoginTest extends TestCase
{
    use RefreshDatabase;

    public function test_student_class_is_required_and_saved_during_login(): void
    {
        $student = User::factory()->create(['role' => 'siswa']);

        $this->from('/login')
            ->post('/login', [
                'email' => $student->email,
                'password' => 'password',
                'remember' => 'on',
            ])
            ->assertRedirect('/login')
            ->assertSessionHasErrors('class');

        $this->assertGuest();
        $this->assertNull($student->fresh()->class);

        $this->post('/login', [
            'email' => $student->email,
            'password' => 'password',
            'class' => 'XI RPL 1',
            'remember' => 'on',
        ])->assertRedirect('/dashboard');

        $this->assertAuthenticatedAs($student);
        $this->assertSame('XI RPL 1', $student->fresh()->class);
    }

    public function test_admin_can_see_borrower_class_in_loan_list_and_reports(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $student = User::factory()->create([
            'role' => 'siswa',
            'class' => 'XI RPL 1',
        ]);
        Loan::factory()->create([
            'user_id' => $student->id,
            'inventory_id' => Inventory::factory()->create()->id,
        ]);
        Loan::factory()->create([
            'user_id' => $student->id,
            'inventory_id' => Inventory::factory()->create()->id,
            'status' => 'returned',
            'returned_at' => today(),
        ]);
        $teacher = User::factory()->create(['role' => 'guru', 'name' => 'Dwi Esti']);
        Loan::factory()->create([
            'user_id' => $teacher->id,
            'inventory_id' => Inventory::factory()->create()->id,
        ]);

        $this->actingAs($admin)
            ->get(route('loans.index'))
            ->assertOk()
            ->assertSee('Siswa')
            ->assertSee('Guru')
            ->assertSee('Kelas: XI RPL 1');

        $this->get(route('loans.returned'))
            ->assertOk()
        ->assertSee('Telp: -')
        ->assertSee('Kelas: XI RPL 1');

        $this->get(route('reports.index'))
            ->assertOk()
            ->assertSee('Kelas: XI RPL 1');

        $this->get(route('reports.print'))
            ->assertOk()
            ->assertSee('Kelas: XI RPL 1');

        $csv = $this->get(route('reports.export'))
            ->assertOk()
            ->streamedContent();

        $this->assertStringContainsString('Kelas', $csv);
        $this->assertStringContainsString('XI RPL 1', $csv);
    }
}
