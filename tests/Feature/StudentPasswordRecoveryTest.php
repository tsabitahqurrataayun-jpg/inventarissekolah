<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Auth\Notifications\ResetPassword;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Password;
use Tests\TestCase;

class StudentPasswordRecoveryTest extends TestCase
{
    use RefreshDatabase;

    public function test_third_failed_student_login_sends_a_password_reset_link(): void
    {
        Notification::fake();
        $student = User::factory()->create(['role' => 'siswa', 'email' => 'student@example.test']);

        foreach (range(1, 2) as $attempt) {
            $this->from('/login')
                ->post('/login', [
                    'email' => $student->email,
                    'password' => 'wrong-password',
                    'remember' => 'on',
                ])
                ->assertRedirect('/login')
                ->assertSessionHasErrors('email');
        }

        Notification::assertNothingSent();

        $this->from('/login')
            ->post('/login', [
                'email' => $student->email,
                'password' => 'wrong-password',
                'remember' => 'on',
            ])
            ->assertRedirect('/login')
            ->assertSessionHas('status', 'Kata sandi salah 3 kali. Tautan ganti sandi telah dikirim ke email akun siswa. Periksa kotak masuk atau folder spam.');

        Notification::assertSentTo($student, ResetPassword::class);
    }

    public function test_teacher_failed_logins_do_not_send_a_student_password_reset_link(): void
    {
        Notification::fake();
        $teacher = User::factory()->create(['role' => 'guru', 'email' => 'teacher@example.test']);

        foreach (range(1, 3) as $attempt) {
            $this->from('/login')
                ->post('/login', [
                    'email' => $teacher->email,
                    'password' => 'wrong-password',
                    'remember' => 'on',
                ])
                ->assertRedirect('/login');
        }

        Notification::assertNothingSent();
    }

    public function test_student_can_request_a_reset_link_and_reset_their_password(): void
    {
        Notification::fake();
        $student = User::factory()->create(['role' => 'siswa', 'email' => 'student@example.test']);

        $this->get(route('password.request'))
            ->assertOk()
            ->assertSee('Minta tautan reset.');

        $this->post(route('password.email'), ['email' => $student->email])
            ->assertRedirect()
            ->assertSessionHas('status');

        Notification::assertSentTo($student, ResetPassword::class);

        $token = Password::createToken($student);
        $this->post(route('password.update'), [
            'token' => $token,
            'email' => $student->email,
            'password' => 'new-secure-password',
            'password_confirmation' => 'new-secure-password',
        ])->assertRedirect(route('login'))
            ->assertSessionHas('status', 'Kata sandi berhasil diganti. Silakan masuk dengan kata sandi baru.');

        $this->assertTrue(Hash::check('new-secure-password', $student->fresh()->password));
    }

    public function test_password_reset_requests_do_not_disclose_non_student_accounts(): void
    {
        Notification::fake();
        $teacher = User::factory()->create(['role' => 'guru', 'email' => 'teacher@example.test']);

        $this->post(route('password.email'), ['email' => $teacher->email])
            ->assertRedirect()
            ->assertSessionHas('status', 'Jika email tersebut terdaftar sebagai akun siswa, tautan untuk mengganti kata sandi akan dikirim ke email tersebut.');

        Notification::assertNothingSent();
    }
}
