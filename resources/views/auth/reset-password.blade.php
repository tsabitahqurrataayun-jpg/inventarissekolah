<!doctype html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Ganti sandi | SIPARIS</title>
    <link rel="stylesheet" href="{{ asset('login.css') }}">
</head>
<body class="siparis-login">
    <main class="login-frame">
        <section class="login-visual" aria-label="Informasi penggantian kata sandi SIPARIS">
            <div class="visual-brand"><span>S</span><div><strong>SIPARIS</strong><small>SMKN 5 SURAKARTA</small></div></div>
            <div class="visual-copy">
                <p class="visual-kicker">PEMULIHAN AKUN</p>
                <h1>Buat kata sandi<br><em>yang baru.</em></h1>
                <p>Gunakan kata sandi baru untuk melanjutkan memakai SIPARIS.</p>
            </div>
            <div class="inventory-scene"><div class="scene-glow"></div><div class="laptop"><div class="laptop-screen"><span>▦</span></div><div class="laptop-base"></div></div><div class="storage-box"><i>▣</i><b>INVENTARIS</b></div><div class="orange-block"></div><span class="scene-dot dot-one"></span><span class="scene-dot dot-two"></span></div>
            <div class="visual-footer"><span>KATA SANDI BARU</span><span>TERDATA · TERATUR · TERJAGA</span></div>
        </section>
        <section class="login-panel">
            <div class="login-panel-inner">
                <div class="login-intro">
                    <p class="login-kicker">GANTI KATA SANDI</p>
                    <h2>Buat sandi baru.</h2>
                    <p>Masukkan kata sandi baru minimal 8 karakter.</p>
                </div>
                @if($errors->any())<div class="login-error" role="alert">{{ $errors->first() }}</div>@endif
                <form method="POST" action="{{ route('password.update') }}" class="login-fields">
                    @csrf
                    <input type="hidden" name="token" value="{{ $token }}">
                    <label>Email siswa<input type="email" name="email" value="{{ old('email', $email) }}" autocomplete="email" required autofocus></label>
                    <label>Kata sandi baru<input type="password" name="password" placeholder="Minimal 8 karakter" autocomplete="new-password" required></label>
                    <label>Ulangi kata sandi<input type="password" name="password_confirmation" placeholder="Ketik ulang kata sandi" autocomplete="new-password" required></label>
                    <button class="login-submit" type="submit">Simpan kata sandi <span>→</span></button>
                </form>
                <p class="login-help"><a href="{{ route('login') }}">Kembali ke login</a></p>
                <small class="login-copyright">SIPARIS · SISTEM INVENTARIS SEKOLAH</small>
            </div>
        </section>
    </main>
</body>
</html>
