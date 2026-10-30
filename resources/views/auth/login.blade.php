@extends('layouts.auth')
@section('title', 'Masuk ke akun')
@section('content')
<div class="login-shell">
    <section class="login-visual">
        <a href="{{ route('login') }}" class="brand-lockup login-brand"><span class="brand-mark">E</span><span><strong>Enspire Learning</strong><small>Belajar · Tumbuh · Raih Mimpi</small></span></a>
        <div class="login-copy">
            <span class="eyebrow">RUANG UNTUK BERTUMBUH</span>
            <h1>Langkah kecil hari ini,<br><em>masa depan</em> yang lebih besar.</h1>
            <p>Temukan cara belajar yang menyenangkan, terarah, dan sesuai dengan tujuanmu.</p>
        </div>
        <div class="login-illustration" aria-hidden="true">
            <div class="ill-circle circle-one"></div><div class="ill-circle circle-two"></div>
            <div class="book-stack"><span></span><span></span><span></span><b>✦</b></div>
            <div class="plant"><i></i><i></i><i></i><span></span></div>
            <div class="floating-note">Belajar hari ini<br><strong>lebih baik dari kemarin.</strong></div>
        </div>
        <div class="login-visual-footer">© {{ date('Y') }} Enspire Learning</div>
    </section>
    <section class="login-panel">
        <div class="login-card">
            <div class="login-welcome"><span class="login-icon">✦</span><p class="eyebrow">SELAMAT DATANG</p><h2>Masuk ke akunmu</h2><p>Silakan masuk untuk melanjutkan aktivitas belajar dan mengajar.</p></div>
            <form method="POST" action="{{ route('login.store') }}" class="form-stack">
                @csrf
                <label class="field-label" for="email">Email</label>
                <input class="form-control" id="email" type="email" name="email" value="{{ old('email') }}" placeholder="nama@email.com" autocomplete="username" required autofocus>
                @error('email') <span class="field-error">{{ $message }}</span> @enderror
                <label class="field-label" for="password">Password</label>
                <div class="password-wrap" x-data="{ show: false }"><input class="form-control" id="password" :type="show ? 'text' : 'password'" name="password" placeholder="Masukkan password" autocomplete="current-password" required><button type="button" @click="show = !show" class="password-toggle" x-text="show ? 'Sembunyikan' : 'Lihat'"></button></div>
                @error('password') <span class="field-error">{{ $message }}</span> @enderror
                <label class="checkbox-line"><input type="checkbox" name="remember" value="1"> <span>Ingat saya di perangkat ini</span></label>
                <button class="btn btn-primary btn-full" type="submit">Masuk ke Enspire <span>→</span></button>
            </form>
            <div class="login-hint"><span>ⓘ</span> Akun disediakan oleh pihak Enspire Learning. Registrasi mandiri tidak tersedia.</div>
        </div>
    </section>
</div>
@endsection
