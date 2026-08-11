@extends('layouts.app')
@section('title', 'Pengumuman')
@section('content')
<div class="flex justify-between items-center mb-4">
    <h1 class="font-display text-2xl font-medium text-ink">Pengumuman</h1>
    @if (auth()->user()->isOwner())
        <a href="{{ route('owner.announcements.create') }}" class="btn-primary text-sm px-3 py-2">+ Buat Pengumuman</a>
    @endif
</div>

<div class="space-y-4">
@forelse ($announcements as $a)
    <x-card>
        <h2 class="font-medium">{{ $a->judul }}</h2>
        <p class="text-sm text-ink/60 mt-1">{{ $a->isi }}</p>
        <p class="text-xs text-ink/35 mt-2">{{ $a->kos->nama }} &middot; {{ $a->created_at->format('d M Y H:i') }}</p>
    </x-card>
@empty
    <p class="text-ink/45">Belum ada pengumuman.</p>
@endforelse
</div>
@endsection
