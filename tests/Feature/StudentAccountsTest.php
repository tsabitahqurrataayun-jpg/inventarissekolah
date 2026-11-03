<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class StudentAccountsTest extends TestCase
{
    use RefreshDatabase;

    public function test_seeded_students_can_log_in_with_the_shared_password(): void
    {
        $this->seed();

        $students = User::where('role', 'siswa')
            ->where('email', '!=', 'siswa@sekolah.id')
            ->get();

        $this->assertCount(10, $students);

        foreach ($students as $student) {
            $this->assertTrue(Hash::check('siswa smkn5', $student->password));
        }

        $this->followingRedirects()->get('/')
            ->assertOk()
            ->assertSee('citra.lestari@sekolah.id')
            ->assertSee('rizky.ramadhan@sekolah.id');

        $student = $students->firstWhere('email', 'citra.lestari@sekolah.id');

        $this->post('/login', [
            'email' => $student->email,
            'password' => 'siswa smkn5',
            'remember' => 'on',
        ])->assertRedirect('/dashboard');

        $this->assertAuthenticatedAs($student);
    }
}
