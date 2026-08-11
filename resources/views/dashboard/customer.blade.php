@extends('layouts.app')
@section('title', 'Dashboard Customer')
@section('content')
<h1 class="font-display text-2xl font-medium text-ink mb-6">Dashboard Customer</h1>

<div class="grid md:grid-cols-2 gap-5">
    <x-card>
        <h2 class="font-medium mb-3">Sewa Aktif</h2>
        @forelse ($sewaAktif as $sewa)
            <div class="border-b last:border-0 py-2 text-sm">
                <p class="font-medium">{{ $sewa->kamar->kos->nama }} - Kamar {{ $sewa->kamar->nomor_kamar }}</p>
                <p class="text-ink/45">Berakhir {{ $sewa->tanggal_selesai->format('d M Y') }} ({{ $sewa->sisaHari() }} hari lagi)</p>
            </div>
        @empty
            <p class="text-sm text-ink/35">Belum ada sewa aktif. <a href="{{ route('landing') }}" class="link-accent">Cari kos</a>.</p>
        @endforelse
    </x-card>

    <x-card>
        <h2 class="font-medium mb-3">Tagihan Belum Lunas</h2>
        @forelse ($tagihan as $t)
            <div class="border-b last:border-0 py-2 text-sm flex justify-between items-center">
                <div>
                    <p class="font-medium">{{ $t->no_transaksi }}</p>
                    <p class="text-ink/45">Rp{{ number_format($t->nominal,2,',','.') }}</p>
                </div>
                <a href="{{ route('customer.pembayaran.show', $t) }}" class="link-accent text-xs font-medium">Bayar &rarr;</a>
            </div>
        @empty
            <p class="text-sm text-ink/35">Tidak ada tagihan tertunda.</p>
        @endforelse
    </x-card>
</div>

<x-card class="mt-5">
    <h2 class="font-medium mb-3">Booking Terbaru</h2>
    @forelse ($bookings as $b)
        <div class="border-b last:border-0 py-2 text-sm flex justify-between">
            <span>{{ $b->kamar->kos->nama }} - Kamar {{ $b->kamar->nomor_kamar }}</span>
            <x-badge color="{{ $b->status === 'disetujui' ? 'green' : ($b->status==='ditolak' ? 'red' : 'amber') }}">{{ ucfirst($b->status) }}</x-badge>
        </div>
    @empty
        <p class="text-sm text-ink/35">Belum ada booking.</p>
    @endforelse
</x-card>
@endsection
