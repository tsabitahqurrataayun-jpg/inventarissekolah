<!doctype html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Lupa sandi | SIPARIS</title>
    <link rel="stylesheet" href="{{ asset('login.css') }}">
</head>
<body class="siparis-login">
    <main class="login-frame">
        <section class="login-visual" aria-label="Informasi pemulihan akun SIPARIS">
            <div class="visual-brand"><span>S</span><div><strong>SIPARIS</strong><small>SMKN 5 SURAKARTA</small></div></div>
            <div class="visual-copy">
                <p class="visual-kicker">PEMULIHAN AKUN</p>
                <h1>Atur ulang<br><em>kata sandi.</em></h1>
                <p>Kami akan mengirim tautan aman ke email siswa yang terdaftar.</p>
            </div>
            <div class="inventory-scene"><div class="scene-glow"></div><div class="laptop"><div class="laptop-screen"><span>▦</span></div><div class="laptop-base"></div></div><div class="storage-box"><i>▣</i><b>INVENTARIS</b></div><div class="orange-block"></div><span class="scene-dot dot-one"></span><span class="scene-dot dot-two"></span></div>
            <div class="visual-footer"><span>PEMULIHAN AKUN</span><span>TERDATA · TERATUR · TERJAGA</span></div>
        </section>
        <section class="login-panel">
            <div class="login-panel-inner">
                <div class="login-intro">
                    <p class="login-kicker">LUPA KATA SANDI</p>
                    <h2>Minta tautan reset.</h2>
                    <p>Masukkan email akun siswa untuk menerima tautan penggantian kata sandi.</p>
                </div>
                @if($errors->any())<div class="login-error" role="alert">{{ $errors->first() }}</div>@endif
                @if(session('status'))<div class="login-status" role="status">{{ session('status') }}</div>@endif
                <form method="POST" action="{{ route('password.email') }}" class="login-fields">
                    @csrf
                    <label>Email siswa<input type="email" name="email" value="{{ old('email') }}" placeholder="nama@sekolah.id" autocomplete="email" required autofocus></label>
                    <button class="login-submit" type="submit">Kirim tautan <span>→</span></button>
                </form>
                <p class="login-help"><a href="{{ route('login') }}">Kembali ke login</a></p>
                <small class="login-copyright">SIPARIS · SISTEM INVENTARIS SEKOLAH</small>
            </div>
        </section>
    </main>
</body>
</html>
