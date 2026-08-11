@extends('layouts.app')
@section('title', 'Profil Saya')
@section('content')
<h1 class="font-display text-2xl font-medium text-ink mb-6">Profil Saya</h1>

<x-card class="max-w-lg">
    <form method="POST" action="{{ route('profile.update') }}" class="space-y-4">
        @csrf @method('PUT')
        <div>
            <label class="text-sm font-medium">Nama Lengkap</label>
            <input placeholder="Contoh: Budi Santoso" type="text" name="name" value="{{ old('name', $user->name) }}" required class="mt-1">
        </div>
        <div>
            <label class="text-sm font-medium">Email</label>
            <input placeholder="nama@email.com" type="email" name="email" value="{{ old('email', $user->email) }}" required class="mt-1">
        </div>
        <div>
            <label class="text-sm font-medium">Nomor WhatsApp</label>
            <input placeholder="08xxxxxxxxxx" type="text" name="phone" value="{{ old('phone', $user->phone) }}" class="mt-1">
        </div>
        <p class="text-xs text-ink/35">Role: <strong>{{ ucfirst($user->role) }}</strong> (hanya dapat diubah oleh Superadmin)</p>
        <button class="btn-primary">Simpan</button>
    </form>

    @if ($user->isOwner())
        <div class="mt-4 pt-4 border-t">
            <a href="{{ route('owner.payment-settings.edit') }}" class="text-sm link-accent">Kelola Metode Pembayaran (rekening/QRIS) &rarr;</a>
        </div>
    @endif
</x-card>
@endsection
