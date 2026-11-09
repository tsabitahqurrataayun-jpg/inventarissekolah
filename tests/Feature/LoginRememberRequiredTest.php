<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class LoginRememberRequiredTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_cannot_login_without_checking_remember_me(): void
    {
        $user = User::factory()->create([
            'email' => 'admin@sekolah.id',
            'password' => 'adminsiparis',
        ]);

        $response = $this->from('/login')->post('/login', [
            'email' => $user->email,
            'password' => 'adminsiparis',
        ]);

        $response->assertRedirect('/login');
        $this->assertGuest();
    }

    public function test_user_can_login_when_remember_me_is_checked(): void
    {
        $user = User::factory()->create([
            'email' => 'dwiesti@sekolah.id',
            'password' => 'gurusmkn5',
        ]);

        $response = $this->from('/login')->post('/login', [
            'email' => $user->email,
            'password' => 'gurusmkn5',
            'remember' => 'on',
        ]);

        $response->assertRedirect('/dashboard');
        $this->assertAuthenticatedAs($user);
    }
}
