<?php

namespace Tests\Feature;

use App\Models\Loan;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class TeacherAccountSeederTest extends TestCase
{
    use RefreshDatabase;

    public function test_it_renames_the_existing_teacher_account_and_preserves_loan_history(): void
    {
        $teacher = User::factory()->create([
            'name' => 'Bima Santoso',
            'email' => 'guru@sekolah.id',
            'role' => 'guru',
            'password' => 'gurusmkn5',
        ]);
        $loan = Loan::factory()->create(['user_id' => $teacher->id]);

        $this->seed();

        $teacher->refresh();
        $loan->refresh();

        $this->assertSame('Dwi Esti', $teacher->name);
        $this->assertSame('dwiesti@sekolah.id', $teacher->email);
        $this->assertSame('guru', $teacher->role);
        $this->assertTrue(Hash::check('gurusmkn5', $teacher->password));
        $this->assertSame($teacher->id, $loan->user_id);
        $this->assertDatabaseMissing('users', ['email' => 'guru@sekolah.id']);

        $this->post('/login', [
            'email' => 'dwiesti@sekolah.id',
            'password' => 'gurusmkn5',
            'remember' => 'on',
        ])->assertRedirect('/dashboard');

        $this->assertAuthenticatedAs($teacher);
        $this->get('/dashboard')
            ->assertOk()
            ->assertSee('Selamat pagi, Dwi Esti');
    }
}
