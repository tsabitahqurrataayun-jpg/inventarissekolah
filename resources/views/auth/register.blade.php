<!doctype html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Buat akun | SIPARIS</title>
    <link rel="stylesheet" href="{{ asset('login.css') }}">
</head>
<body class="siparis-login">
    <main class="login-frame register-frame">
        <section class="login-visual" aria-label="Informasi pendaftaran SIPARIS">
            <div class="visual-brand"><span>S</span><div><strong>SIPARIS</strong><small>SMKN 5 SURAKARTA</small></div></div>
            <div class="visual-copy">
                <p class="visual-kicker">AKSES WARGA SEKOLAH</p>
                <h1>Mulai<br><em>meminjam.</em></h1>
                <p>Buat akun untuk mengajukan dan memantau peminjaman inventaris sekolah.</p>
            </div>
            <div class="inventory-scene"><div class="scene-glow"></div><div class="laptop"><div class="laptop-screen"><span>▦</span></div><div class="laptop-base"></div></div><div class="storage-box"><i>▣</i><b>INVENTARIS</b></div><div class="orange-block"></div><span class="scene-dot dot-one"></span><span class="scene-dot dot-two"></span></div>
            <div class="visual-footer"><span>02 / AKUN PENGGUNA</span><span>TERDATA · TERATUR · TERJAGA</span></div>
        </section>
        <section class="login-panel register-panel">
            <div class="login-panel-inner register-panel-inner">
                <div class="login-intro register-intro">
                    <p class="login-kicker">AKUN BARU</p>
                    <h2>Daftar sekarang.</h2>
                    <p>Lengkapi data berikut untuk mulai menggunakan SIPARIS.</p>
                </div>
                @if($errors->any())
                    <div class="login-error" role="alert">{{ $errors->first() }}</div>
                @endif
                <form method="POST" action="{{ route('register') }}" class="login-fields register-fields">
                    @csrf
                    <label>Nama lengkap<input name="name" value="{{ old('name') }}" placeholder="Nama sesuai identitas" autocomplete="name" required autofocus></label>
                    <label>Email sekolah<input type="email" name="email" value="{{ old('email') }}" placeholder="nama@sekolah.id" autocomplete="email" required></label>
                    <label>Peran
                        <select name="role" required>
                            <option value="siswa" @selected(old('role', 'siswa') === 'siswa')>Siswa</option>
                            <option value="guru" @selected(old('role') === 'guru')>Guru</option>
                        </select>
                    </label>
                    <label>Kata sandi<input type="password" name="password" placeholder="Minimal 8 karakter" autocomplete="new-password" required></label>
                    <label>Ulangi kata sandi<input type="password" name="password_confirmation" placeholder="Ketik ulang kata sandi" autocomplete="new-password" required></label>
                    <button class="login-submit" type="submit">Buat akun <span>→</span></button>
                </form>
                <p class="login-help register-help">Sudah punya akun? <a href="{{ route('login') }}">Masuk di sini</a></p>
                <small class="login-copyright">SIPARIS · SISTEM INVENTARIS SEKOLAH</small>
            </div>
        </section>
    </main>
</body>
</html>
