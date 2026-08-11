@extends('layouts.app')
@section('title', 'Kos Saya')
@section('content')
<div class="flex justify-between items-center mb-6">
    <div>
        <h1 class="font-display text-2xl font-medium text-ink">Kos Saya</h1>
        <p class="text-sm text-ink/45 mt-0.5">Kelola properti kos yang Anda miliki.</p>
    </div>
    <a href="{{ route('owner.kos.create') }}" class="btn-primary text-sm px-4 py-2.5">+ Tambah Kos</a>
</div>

<div class="grid sm:grid-cols-2 lg:grid-cols-3 gap-5">
    @forelse ($kosList as $kos)
        <div class="group surface p-0 overflow-hidden hover:-translate-y-1 hover:shadow-lift transition-all duration-200">
            <div class="h-32 bg-teal-50 relative overflow-hidden">
                @if ($kos->foto)
                    <img src="{{ asset('storage/'.$kos->foto) }}" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-300">
                @else
                    <div class="w-full h-full flex items-center justify-center text-teal/20 font-display text-3xl">K</div>
                @endif
                <span class="absolute top-3 right-3 chip bg-white/90 text-ink/70 backdrop-blur-sm">{{ $kos->kamars_count }} kamar</span>
            </div>
            <div class="p-5">
                <div class="flex items-center justify-between">
                    <h3 class="font-display font-medium text-ink group-hover:text-teal transition-colors">{{ $kos->nama }}</h3>
                    <span class="text-xs font-mono text-ink/30">#{{ $kos->id }}</span>
                </div>
                <p class="text-sm text-ink/45 mt-0.5">{{ $kos->alamat }}</p>
                <div class="mt-4 flex gap-2 text-sm">
                    <a href="{{ route('owner.kos.kamar.index', $kos) }}" class="flex-1 text-center bg-teal-50 text-teal font-semibold rounded-xl py-2 hover:bg-teal/10 transition-colors">Kelola Kamar</a>
                    <a href="{{ route('owner.kos.edit', $kos) }}" class="flex-1 btn-ghost">Edit</a>
                </div>
            </div>
        </div>
    @empty
        <div class="col-span-full">
            <x-empty title="Belum ada kos" subtitle="Tambahkan kos pertama Anda untuk mulai mengelola kamar dan penyewa.">
                <x-slot:action>
                    <a href="{{ route('owner.kos.create') }}" class="btn-primary text-sm">+ Tambah Kos</a>
                </x-slot:action>
            </x-empty>
        </div>
    @endforelse
</div>
@endsection
