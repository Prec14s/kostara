@extends('layouts.app')
@section('title', 'Kostara - Kamar Kosong')
@section('content')

<section class="relative overflow-hidden rounded-3xl bg-ink px-6 py-14 md:px-12 md:py-20 -mt-2 mb-10">
    <div class="absolute -top-24 -right-24 w-96 h-96 rounded-full bg-brass/20 blur-3xl"></div>
    <div class="absolute bottom-0 left-1/3 w-72 h-72 rounded-full bg-teal/40 blur-3xl"></div>

    <div class="relative max-w-2xl">
        <span class="inline-flex items-center gap-2 text-brass text-xs font-semibold tracking-widest uppercase mb-4">
            <span class="w-6 h-px bg-brass"></span> Kamar kosong &middot; siap dihuni
        </span>
        <h1 class="font-display text-4xl md:text-5xl font-medium text-white leading-[1.1]">
            Lihat kamar <span class="text-brass italic">kosong</span>,<br>langsung booking.
        </h1>
        <p class="text-white/60 mt-4 max-w-md">
            Cek kamar yang masih tersedia, hubungi Owner langsung lewat WhatsApp, atau daftar untuk booking online.
        </p>

        <form method="GET" action="{{ route('landing') }}" class="mt-8 flex gap-2 max-w-md">
            <input type="search" name="q" value="{{ $search }}" placeholder="Cari nomor atau nama kamar..."
                class="flex-1 !bg-white !border-0 !shadow-lift !py-3.5">
            <button class="btn-accent px-6">Cari</button>
        </form>

        @guest
        <div class="flex flex-wrap gap-3 mt-6">
            <a href="{{ route('register') }}" class="btn-primary !bg-white !text-ink hover:!bg-brass hover:!text-white">Daftar / Masuk untuk Booking</a>
        </div>
        @endguest
    </div>
</section>

<div class="flex items-center justify-between mb-5">
    <h2 class="font-display text-xl font-medium text-ink">Kamar tersedia</h2>
    <span class="text-xs text-ink/40 font-mono">{{ $totalKosong }} kamar kosong</span>
</div>

<div class="grid sm:grid-cols-2 lg:grid-cols-3 gap-6">
    @forelse ($kamars as $kamar)
        @php $cover = $kamar->coverFoto(); @endphp
        <div class="group surface overflow-hidden hover:-translate-y-1 hover:shadow-lift transition-all duration-200 p-0">
            <div class="h-40 bg-teal-50 relative overflow-hidden">
                @if ($cover)
                    <img src="{{ asset('storage/'.$cover) }}" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-300">
                @else
                    <div class="w-full h-full flex items-center justify-center text-teal/20 font-display text-4xl">K</div>
                @endif
                <div class="absolute top-3 left-3"><x-room-tag :nomor="$kamar->nomor_kamar" /></div>
                <div class="absolute top-3 right-3 chip bg-sage text-white">Kosong</div>
            </div>
            <div class="p-5">
                <h3 class="font-display text-lg font-medium text-ink group-hover:text-teal transition-colors">{{ $kamar->nama_kamar ?? 'Kamar '.$kamar->nomor_kamar }}</h3>
                <p class="text-sm text-ink/50 mt-0.5">{{ $kamar->kos->nama }}</p>
                @if ($kamar->harga_bulanan)
                    <p class="text-sm font-semibold text-brass mt-2">Rp{{ number_format($kamar->harga_bulanan,2,',','.') }}<span class="text-ink/40 font-normal">/bulan</span></p>
                @endif
                <div class="mt-4 flex gap-2">
                    @auth
                        @if (auth()->user()->isCustomer())
                            <a href="{{ route('booking.create', $kamar) }}" class="flex-1 text-center text-sm bg-teal-50 text-teal rounded-xl py-2 font-semibold hover:bg-teal/10 transition-colors">Lihat & Booking</a>
                        @endif
                    @else
                        <a href="{{ route('register') }}" class="flex-1 text-center text-sm bg-teal-50 text-teal rounded-xl py-2 font-semibold hover:bg-teal/10 transition-colors">Lihat & Booking</a>
                    @endauth
                    <a href="{{ route('guest.kos.hubungi-owner', [$kamar->kos, 'kamar' => $kamar->id]) }}" target="_blank" class="flex-1 text-center text-sm bg-sage-50 text-sage rounded-xl py-2 font-semibold hover:bg-sage/15 transition-colors">WhatsApp</a>
                </div>
            </div>
        </div>
    @empty
        <div class="col-span-full">
            <x-empty title="Belum ada kamar kosong" subtitle="Semua kamar sedang terisi, atau Owner belum menambahkan kamar. Coba lagi nanti." />
        </div>
    @endforelse
</div>

<div class="mt-8">{{ $kamars->links() }}</div>
@endsection
