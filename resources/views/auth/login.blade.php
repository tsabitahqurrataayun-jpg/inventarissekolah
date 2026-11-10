<!doctype html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Login | SIPARIS</title>
    <link rel="stylesheet" href="{{ asset('login.css') }}">
</head>
<body class="siparis-login">
    <main class="login-frame">
        <section class="login-visual" aria-label="Ilustrasi pengelolaan inventaris">
            <div class="visual-brand"><span>S</span><div><strong>SIPARIS</strong><small>SMKN 5 SURAKARTA</small></div></div>
            <div class="visual-copy"><p class="visual-kicker">SISTEM PEMINJAMAN SEKOLAH</p><h1>Kelola alat.<br><em>Bergerak lebih mudah.</em></h1><p>Temukan, ajukan, dan pantau inventaris sekolah dalam satu ruang yang rapi.</p></div>
            <div class="inventory-scene"><div class="scene-glow"></div><div class="laptop"><div class="laptop-screen"><span>▦</span></div><div class="laptop-base"></div></div><div class="storage-box"><i>▣</i><b>INVENTARIS</b></div><div class="orange-block"></div><span class="scene-dot dot-one"></span><span class="scene-dot dot-two"></span></div>
            <div class="visual-footer"><span>01 / AKSES PENGGUNA</span><span>TERDATA · TERATUR · TERJAGA</span></div>
        </section>
        <section class="login-panel">
            <div class="login-panel-inner">
                <div class="login-intro"><p class="login-kicker">RUANG PENGGUNA</p><h2>Selamat datang.</h2><p>Masuk untuk melanjutkan ke sistem peminjaman inventaris.</p></div>
                @if($errors->any())<div class="login-error">{{ $errors->first() }}</div>@endif
                @if(session('status'))<div class="login-status" role="status">{{ session('status') }}</div>@endif
                <form method="POST" action="{{ route('login') }}" class="login-fields">
                    @csrf
                    <label>Username / Email<input type="email" name="email" value="{{ old('email') }}" placeholder="nama@sekolah.id" required autofocus></label>
                    <label>Kelas (wajib untuk siswa)<input name="class" value="{{ old('class') }}" placeholder="Contoh: XI RPL 1" maxlength="100"></label>
                    <label>Password<div class="password-field"><input type="password" name="password" id="loginPassword" placeholder="Masukkan password" required><button type="button" class="toggle-password" aria-label="Lihat password" data-target="loginPassword">◉</button></div></label>
                    <div class="login-options"><label class="remember"><input type="checkbox" name="remember" required> <span>Ingat saya</span></label></div>
                    <button class="login-submit" type="submit">Login <span>→</span></button>
                </form>
                <script>
                    document.addEventListener('DOMContentLoaded', function () {
                        document.querySelectorAll('.toggle-password').forEach(function (button) {
                            button.addEventListener('click', function () {
                                const target = document.getElementById(button.dataset.target);
                                if (!target) return;
                                const isPassword = target.type === 'password';
                                target.type = isPassword ? 'text' : 'password';
                                button.textContent = isPassword ? '◌' : '◉';
                                button.setAttribute('aria-label', isPassword ? 'Sembunyikan password' : 'Lihat password');
                            });
                        });
                    });
                </script>
                <p class="login-help"><a href="{{ route('password.request') }}">Lupa sandi?</a></p>
                <p class="login-help">Belum punya akun? <a href="{{ route('register') }}">Buat akun</a></p>
                <small class="login-copyright">SIPARIS · SISTEM INVENTARIS SEKOLAH</small>
            </div>
        </section>
    </main>
</body>
</html>
