@extends('layouts.app')
@section('title', 'Akses Ditolak')
@section('content')
<div class="max-w-md mx-auto text-center mt-16">
    <div class="w-14 h-14 rounded-full bg-clay-50 border border-clay/30 flex items-center justify-center mx-auto mb-4">
        <span class="text-clay text-2xl font-display">403</span>
    </div>
    <h1 class="font-display text-2xl font-medium text-ink">Akses ditolak</h1>
    <p class="text-sm text-ink/50 mt-2 leading-relaxed">
        {{ $exception->getMessage() ?: 'Anda tidak memiliki akses ke halaman ini. Data ini kemungkinan milik akun lain, atau role Anda tidak diizinkan membukanya.' }}
    </p>
    <a href="{{ auth()->check() ? route('dashboard') : route('landing') }}" class="inline-block mt-6 btn-primary">Kembali ke Dashboard</a>
</div>
@endsection
