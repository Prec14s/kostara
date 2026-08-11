@extends('layouts.app')
@section('title', 'Daftar - Kostara')
@section('content')
<div class="grid lg:grid-cols-2 gap-0 -mt-2 rounded-3xl overflow-hidden surface p-0 min-h-[620px]">

    <div class="relative hidden lg:flex flex-col justify-between bg-ink p-10 overflow-hidden">
        <div class="absolute -top-20 -left-20 w-72 h-72 rounded-full bg-teal/50 blur-3xl"></div>
        <div class="absolute bottom-16 -right-10 w-56 h-56 rounded-full bg-brass/20 blur-3xl"></div>
        <a href="{{ route('landing') }}" class="relative flex items-center gap-2 text-white font-display font-semibold text-xl">
            <span class="w-8 h-8 rounded-lg bg-brass/90 flex items-center justify-center text-ink text-sm font-sans font-bold">K</span>
            Kostara
        </a>
        <div class="relative">
            <p class="font-display text-3xl text-white leading-snug">Lihat kamar,<br>langsung booking.</p>
            <p class="text-white/50 text-sm mt-3 max-w-xs">Setelah daftar, Anda bisa melihat harga tiap durasi, ketersediaan real-time, dan mengajukan booking online.</p>
        </div>
    </div>

    <div class="p-8 md:p-12 flex flex-col justify-center">
        <h1 class="font-display text-2xl font-medium text-ink mb-1">Buat akun Customer</h1>
        <p class="text-sm text-ink/50 mb-7">Gratis, hanya butuh beberapa detik.</p>

        <form method="POST" action="{{ route('register') }}" class="space-y-4">
            @csrf
            <div>
                <label>Nama Lengkap</label>
                <input placeholder="Contoh: Budi Santoso" type="text" name="name" value="{{ old('name') }}" required>
            </div>
            <div>
                <label>Email</label>
                <input placeholder="nama@email.com" type="email" name="email" value="{{ old('email') }}" required>
            </div>
            <div>
                <label>Nomor WhatsApp</label>
                <input type="text" name="phone" value="{{ old('phone') }}" required placeholder="08xxxxxxxxxx">
            </div>
            <div>
                <label>Password</label>
                <input placeholder="Minimal 8 karakter" type="password" name="password" required>
            </div>
            <div>
                <label>Konfirmasi Password</label>
                <input placeholder="Ulangi password yang sama" type="password" name="password_confirmation" required>
            </div>
            <button class="btn-primary w-full py-3">Daftar</button>
        </form>

        <p class="text-sm text-ink/50 mt-6">
            Sudah punya akun? <a href="{{ route('login') }}" class="link-accent">Masuk di sini</a>
        </p>
    </div>
</div>
@endsection
