<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class StudentAccountsTest extends TestCase
{
    use RefreshDatabase;

    public function test_only_the_retained_student_account_is_seeded_with_a_no_space_password(): void
    {
        $this->seed();

        $student = User::where('email', 'siswa@sekolah.id')->firstOrFail();
        $this->assertSame('Alya Pratama', $student->name);
        $this->assertTrue(Hash::check('siswasmkn5', $student->password));
        $this->assertSame(1, User::where('role', 'siswa')->count());
        $this->assertDatabaseMissing('users', ['email' => 'citra.lestari@sekolah.id']);

        $this->get('/login')
            ->assertOk()
            ->assertDontSee('citra.lestari@sekolah.id')
            ->assertDontSee('Daftar email siswa');

        $this->post('/login', [
            'email' => 'siswa@sekolah.id',
            'password' => 'siswasmkn5',
            'class' => 'XI RPL 1',
            'remember' => 'on',
        ])->assertRedirect('/dashboard');

        $this->assertAuthenticatedAs($student);
        $this->assertSame('XI RPL 1', $student->fresh()->class);
    }
}
