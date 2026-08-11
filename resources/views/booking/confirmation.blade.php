@extends('layouts.app')
@section('title', 'Booking Terkirim')
@section('content')

@php
    $kamar = $booking->kamar;
    $owner = $kamar->kos->owner;
    $pesan = "Halo Pak/Bu {$owner->name},\n\nSaya {$booking->customer->name} baru saja booking Kamar {$kamar->nomor_kamar} di {$kamar->kos->nama} untuk mulai tanggal ".$booking->tanggal_mulai->format('d M Y')." (".ucfirst($booking->jenis_durasi).").\n\nMohon dikonfirmasi ya. Terima kasih.";
@endphp

<div class="max-w-lg mx-auto text-center mt-6">
    <div class="w-14 h-14 rounded-full bg-sage-50 border border-sage/30 flex items-center justify-center mx-auto mb-4">
        <span class="text-sage text-2xl">✓</span>
    </div>
    <h1 class="font-display text-2xl font-medium text-ink">Booking terkirim</h1>
    <p class="text-sm text-ink/50 mt-1.5">Data booking Anda sudah masuk ke sistem Owner dan menunggu konfirmasi.</p>

    <div class="surface mt-6 p-5 text-left">
        <div class="flex items-center gap-3 mb-4">
            <x-room-tag :nomor="$kamar->nomor_kamar" />
            <div>
                <p class="font-semibold text-ink text-sm">{{ $kamar->kos->nama }}</p>
                <p class="text-xs text-ink/45">{{ $kamar->kos->alamat }}</p>
            </div>
        </div>
        <dl class="divide-y divide-line text-sm">
            <div class="py-2 flex justify-between"><dt class="text-ink/45">Durasi</dt><dd class="font-semibold text-ink">{{ ucfirst($booking->jenis_durasi) }}</dd></div>
            <div class="py-2 flex justify-between"><dt class="text-ink/45">Tanggal mulai</dt><dd class="font-semibold text-ink">{{ $booking->tanggal_mulai->format('d M Y') }}</dd></div>
            <div class="py-2 flex justify-between"><dt class="text-ink/45">Status</dt><dd><x-badge color="amber">Menunggu Konfirmasi</x-badge></dd></div>
        </dl>
    </div>

    <div class="mt-6 flex items-start gap-2.5 bg-linen border border-line rounded-xl p-4 text-sm text-ink/60 text-left">
        <span class="w-1.5 h-1.5 rounded-full bg-brass mt-1.5 shrink-0"></span>
        Agar booking lebih cepat diproses, Anda bisa langsung menghubungi Owner via WhatsApp sekarang.
    </div>

    <a href="{{ $owner->waLink($pesan) }}" target="_blank"
       class="mt-4 w-full btn bg-sage text-white hover:bg-sage/90 hover:shadow-lift py-3.5 text-base">
        💬 Hubungi Owner via WhatsApp
    </a>

    <a href="{{ route('dashboard') }}" class="block mt-4 text-sm link-accent">Kembali ke dashboard</a>
</div>
@endsection
