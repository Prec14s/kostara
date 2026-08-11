@extends('layouts.app')
@section('title', $kos->nama.' - Kostara')
@section('content')

<a href="{{ route('landing') }}" class="text-sm link-accent">&larr; Kembali ke pencarian</a>

<div class="surface mt-4 p-0 overflow-hidden">
    <div class="h-64 bg-teal-50 relative overflow-hidden">
        @if ($kos->foto)
            <img src="{{ asset('storage/'.$kos->foto) }}" class="w-full h-full object-cover">
        @else
            <div class="w-full h-full flex items-center justify-center text-teal/20 font-display text-6xl">K</div>
        @endif
    </div>

    <div class="p-6 md:p-8">
        <h1 class="font-display text-3xl font-medium text-ink">{{ $kos->nama }}</h1>
        <p class="text-ink/50 mt-1">{{ $kos->alamat }}</p>

        <div class="grid sm:grid-cols-3 gap-3 mt-6">
            <x-stat label="Kamar tersedia" value="{{ $jumlahTersedia }}" accent="sage" />
            <x-stat label="Mulai dari" value="{{ $rentangHarga['min'] ? 'Rp'.number_format($rentangHarga['min'],2,',','.') : '-' }}" accent="brass" />
            <x-stat label="Jam operasional" value="{{ $kos->jam_operasional ?? '-' }}" accent="teal" />
        </div>

        @if ($kos->deskripsi)
        <div class="mt-6">
            <h2 class="font-display text-base font-medium text-ink">Deskripsi</h2>
            <p class="text-sm text-ink/60 mt-1.5 leading-relaxed">{{ $kos->deskripsi }}</p>
        </div>
        @endif

        @if ($kos->fasilitas)
        <div class="mt-5">
            <h2 class="font-display text-base font-medium text-ink">Fasilitas umum</h2>
            <p class="text-sm text-ink/60 mt-1.5 leading-relaxed">{{ $kos->fasilitas }}</p>
        </div>
        @endif

        @auth
            @if ($kamars !== null)
                {{-- Customer sudah login: tampilkan daftar kamar & tombol booking langsung --}}
                <div class="mt-7">
                    <h2 class="font-display text-base font-medium text-ink mb-3">Pilih kamar</h2>
                    <div class="space-y-3">
                        @forelse ($kamars as $kamar)
                            @php
                                $dot = match($kamar->status) {
                                    'tersedia' => 'bg-sage',
                                    'dipesan' => 'bg-brass',
                                    'terisi' => 'bg-ink/30',
                                    'maintenance' => 'bg-clay',
                                    default => 'bg-ink/20',
                                };
                                $statusLabel = match($kamar->status) {
                                    'tersedia' => 'Tersedia',
                                    'dipesan' => 'Dipesan',
                                    'terisi' => 'Terisi',
                                    'maintenance' => 'Maintenance',
                                    default => 'Tidak Aktif',
                                };
                                $cover = $kamar->coverFoto();
                            @endphp
                            <div class="flex items-center gap-4 border border-line rounded-xl p-3 hover:border-brass/40 transition-colors">
                                <div class="w-20 h-20 rounded-lg bg-teal-50 overflow-hidden shrink-0 relative">
                                    @if ($cover)
                                        <img src="{{ asset('storage/'.$cover) }}" class="w-full h-full object-cover">
                                    @else
                                        <div class="w-full h-full flex items-center justify-center text-teal/20 font-display text-xl">K</div>
                                    @endif
                                </div>
                                <div class="flex-1 min-w-0">
                                    <div class="flex items-center gap-2">
                                        <x-room-tag :nomor="$kamar->nomor_kamar" />
                                        <p class="font-semibold text-ink text-sm truncate">{{ $kamar->nama_kamar ?? 'Kamar '.$kamar->nomor_kamar }}</p>
                                    </div>
                                    <div class="flex items-center gap-1.5 mt-1.5">
                                        <span class="w-1.5 h-1.5 rounded-full {{ $dot }}"></span>
                                        <span class="text-xs text-ink/50">{{ $statusLabel }}</span>
                                        @if ($kamar->harga_bulanan)
                                            <span class="text-xs text-ink/40">&middot; mulai Rp{{ number_format($kamar->harga_bulanan,2,',','.') }}/bulan</span>
                                        @endif
                                    </div>
                                </div>
                                @if ($kamar->status === 'tersedia')
                                    <a href="{{ route('booking.create', $kamar) }}" class="btn-accent text-sm px-4 py-2 shrink-0">Lihat & Booking</a>
                                @else
                                    <span class="text-xs text-ink/35 shrink-0">Tidak tersedia</span>
                                @endif
                            </div>
                        @empty
                            <x-empty title="Belum ada kamar di kos ini" />
                        @endforelse
                    </div>
                </div>
            @endif
        @endauth

        @guest
            <div class="mt-6 flex items-start gap-2.5 bg-linen border border-line rounded-xl p-4 text-sm text-ink/60">
                <span class="w-1.5 h-1.5 rounded-full bg-brass mt-1.5 shrink-0"></span>
                Detail kamar per kamar (nomor, ketersediaan, harga rinci) hanya bisa dilihat setelah Anda
                <strong class="text-ink">daftar / masuk</strong>.
            </div>
        @endguest

        <div class="mt-6 flex flex-col sm:flex-row gap-3">
            @guest
                <a href="{{ route('register') }}" class="flex-1 btn-primary py-3.5">
                    Daftar / Masuk untuk Lihat Kamar
                </a>
            @endguest
            <a href="{{ route('guest.kos.hubungi-owner', $kos) }}" target="_blank" class="flex-1 btn bg-sage text-white hover:bg-sage/90 hover:shadow-lift py-3.5">
                💬 Hubungi Owner via WhatsApp
            </a>
        </div>
    </div>
</div>
@endsection
