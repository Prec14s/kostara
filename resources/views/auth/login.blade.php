@extends('layouts.app')
@section('title', 'Masuk - Kostara')
@section('content')
<div class="grid lg:grid-cols-2 gap-0 -mt-2 rounded-3xl overflow-hidden surface p-0 min-h-[520px]">

    <div class="relative hidden lg:flex flex-col justify-between bg-ink p-10 overflow-hidden">
        <div class="absolute -top-20 -right-20 w-72 h-72 rounded-full bg-brass/20 blur-3xl"></div>
        <div class="absolute bottom-10 -left-10 w-56 h-56 rounded-full bg-teal/50 blur-3xl"></div>
        <a href="{{ route('landing') }}" class="relative flex items-center gap-2 text-white font-display font-semibold text-xl">
            <span class="w-8 h-8 rounded-lg bg-brass/90 flex items-center justify-center text-ink text-sm font-sans font-bold">K</span>
            Kostara
        </a>
        <div class="relative">
            <p class="font-display text-3xl text-white leading-snug">Satu pintu masuk<br>untuk semua penghuni kos.</p>
            <p class="text-white/50 text-sm mt-3 max-w-xs">Owner, Penjaga, dan Customer masuk lewat halaman yang sama — akses menyesuaikan peran masing-masing.</p>
        </div>
    </div>

    <div class="p-8 md:p-12 flex flex-col justify-center">
        <h1 class="font-display text-2xl font-medium text-ink mb-1">Selamat datang kembali</h1>
        <p class="text-sm text-ink/50 mb-7">Masuk untuk melanjutkan ke akun Anda.</p>

        <form method="POST" action="{{ route('login') }}" class="space-y-4">
            @csrf
            <div>
                <label>Email</label>
                <input placeholder="nama@email.com" type="email" name="email" value="{{ old('email') }}" required autofocus>
            </div>
            <div>
                <label>Password</label>
                <input placeholder="Masukkan password Anda" type="password" name="password" required>
            </div>
            <label class="flex items-center gap-2 text-sm !mb-0 font-normal text-ink/60">
                <input type="checkbox" name="remember"> Ingat saya
            </label>
            <button class="btn-primary w-full py-3">Masuk</button>
        </form>

        <p class="text-sm text-ink/50 mt-6">
            Belum punya akun (Customer)? <a href="{{ route('register') }}" class="link-accent">Daftar di sini</a>
        </p>
        <p class="text-sm text-ink/50 mt-1">
            <a href="{{ route('landing') }}" class="link-accent">&larr; Kembali ke beranda</a>
        </p>
    </div>
</div>
@endsection
