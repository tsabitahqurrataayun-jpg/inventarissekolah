<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Str;
use Illuminate\View\View;

class AuthController extends Controller
{
    public function showLogin(): View
    {
        return view('auth.login');
    }

    public function login(Request $request): RedirectResponse
    {
        $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required', 'string'],
            'class' => ['nullable', 'string', 'max:100'],
            'remember' => ['accepted'],
        ]);

        $credentials = $request->only('email', 'password');

        if (! Auth::attempt($credentials, $request->boolean('remember'))) {
            $email = Str::lower($request->string('email')->toString());
            $student = User::where('email', $email)->where('role', 'siswa')->first();
            $key = $this->studentLoginThrottleKey($email);
            RateLimiter::hit($key, 900);

            if (RateLimiter::attempts($key) >= 3) {
                $resetStatus = null;
                if ($student && RateLimiter::attempts($key) === 3) {
                    $resetStatus = Password::sendResetLink(['email' => $student->email]);
                }

                $message = match ($resetStatus) {
                    Password::RESET_LINK_SENT => 'Kata sandi salah 3 kali. Tautan ganti sandi telah dikirim ke email akun siswa. Periksa kotak masuk atau folder spam.',
                    Password::RESET_THROTTLED => 'Kata sandi salah 3 kali. Tautan reset baru saja dikirim sebelumnya; periksa email atau gunakan menu Lupa sandi setelah beberapa saat.',
                    default => 'Kata sandi salah 3 kali. Jika alamat ini terdaftar sebagai akun siswa, instruksi ganti sandi tersedia melalui email. Periksa kotak masuk atau folder spam.',
                };

                return back()
                    ->withInput($request->only('email'))
                    ->with('status', $message);
            }

            return back()->withErrors(['email' => 'Email atau kata sandi tidak sesuai.'])->withInput();
        }

        $user = Auth::user();
        if ($user->role === 'siswa') {
            $studentClass = trim((string) $request->input('class'));
            if ($studentClass === '') {
                Auth::logout();

                return back()
                    ->withErrors(['class' => 'Kelas wajib diisi untuk akun siswa.'])
                    ->withInput($request->only('email', 'class'));
            }

            $user->forceFill(['class' => $studentClass])->save();
        }

        RateLimiter::clear($this->studentLoginThrottleKey(Str::lower($request->string('email')->toString())));
        $request->session()->regenerate();

        return redirect()->intended(route('dashboard'));
    }

    public function showForgotPassword(): View
    {
        return view('auth.forgot-password');
    }

    public function sendPasswordResetLink(Request $request): RedirectResponse
    {
        $data = $request->validate(['email' => ['required', 'email']]);
        $student = User::where('email', Str::lower($data['email']))
            ->where('role', 'siswa')
            ->first();

        if ($student) {
            $status = Password::sendResetLink(['email' => $student->email]);
            if ($status === Password::RESET_THROTTLED) {
                return back()->with('status', 'Jika email tersebut terdaftar sebagai akun siswa, instruksi sudah dikirim baru-baru ini. Periksa email atau coba lagi setelah beberapa saat.');
            }
        }

        return back()->with('status', 'Jika email tersebut terdaftar sebagai akun siswa, tautan untuk mengganti kata sandi akan dikirim ke email tersebut.');
    }

    public function showResetPassword(Request $request, string $token): View
    {
        return view('auth.reset-password', [
            'token' => $token,
            'email' => $request->query('email'),
        ]);
    }

    public function resetPassword(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'token' => ['required'],
            'email' => ['required', 'email'],
            'password' => ['required', 'confirmed', 'min:8'],
        ]);

        $status = Password::reset($data, function (User $user, string $password): void {
            abort_unless($user->role === 'siswa', 403);

            $user->forceFill([
                'password' => Hash::make($password),
                'remember_token' => Str::random(60),
            ])->save();
        });

        if ($status === Password::PASSWORD_RESET) {
            RateLimiter::clear($this->studentLoginThrottleKey(Str::lower($data['email'])));

            return redirect()->route('login')->with('status', 'Kata sandi berhasil diganti. Silakan masuk dengan kata sandi baru.');
        }

        return back()->withInput($request->only('email'))->withErrors([
            'email' => 'Tautan ganti kata sandi tidak valid atau sudah kedaluwarsa. Minta tautan baru.',
        ]);
    }

    private function studentLoginThrottleKey(string $email): string
    {
        return 'student-login-failures:'.hash('sha256', $email);
    }

    public function showRegister(): View
    {
        return view('auth.register');
    }

    public function register(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:100'],
            'email' => ['required', 'email', 'max:150', 'unique:users,email'],
            'role' => ['required', 'in:siswa,guru'],
            'password' => ['required', 'confirmed', 'min:8'],
        ]);

        $user = User::create($data);
        Auth::login($user);
        $request->session()->regenerate();

        return redirect()->route('dashboard');
    }

    public function logout(Request $request): RedirectResponse
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login');
    }
}
