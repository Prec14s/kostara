@extends('layouts.app')
@section('title', 'Detail Pembayaran')
@section('content')
<a href="{{ route('customer.pembayaran.index') }}" class="text-sm link-accent">&larr; Pembayaran Saya</a>

@php
    $owner = $pembayaran->sewa->kamar->kos->owner;
    $statusColor = ['belum_bayar'=>'gray','menunggu_verifikasi'=>'amber','lunas'=>'green','terlambat'=>'red','dibatalkan'=>'gray'][$pembayaran->status];
    $statusLabel = ['belum_bayar'=>'Belum Bayar','menunggu_verifikasi'=>'Menunggu Verifikasi','lunas'=>'Lunas','terlambat'=>'Terlambat','dibatalkan'=>'Dibatalkan'][$pembayaran->status];
@endphp

<x-card class="max-w-lg mt-3">
    <div class="flex justify-between items-center mb-4">
        <h1 class="font-display text-xl font-medium text-ink">{{ $pembayaran->no_transaksi }}</h1>
        <x-badge color="{{ $statusColor }}">{{ $statusLabel }}</x-badge>
    </div>

    <p class="text-sm text-ink/45">Kamar {{ $pembayaran->sewa->kamar->nomor_kamar }} - {{ $pembayaran->sewa->kamar->kos->nama }}</p>
    <p class="text-3xl font-display font-medium text-teal mt-2">Rp{{ number_format($pembayaran->nominal,2,',','.') }}</p>

    @if (in_array($pembayaran->status, ['belum_bayar']))
        <div class="mt-5 pt-4 border-t border-line">
            <h2 class="font-display text-base font-medium text-ink mb-3">Pilih Metode Pembayaran</h2>

            <div class="space-y-2.5">
                @if ($owner->bank_account_number)
                    <div class="bg-linen rounded-xl p-4 text-sm border border-line">
                        <p class="font-semibold text-ink">🏦 Transfer Bank</p>
                        <p class="text-ink/70 mt-1">{{ $owner->bank_name }} &middot; <span class="font-mono">{{ $owner->bank_account_number }}</span></p>
                        <p class="text-ink/45 text-xs mt-0.5">a.n. {{ $owner->bank_account_holder ?: '-' }}</p>
                    </div>
                @endif

                @if ($owner->qris_image)
                    <div class="bg-linen rounded-xl p-4 text-sm border border-line">
                        <p class="font-semibold text-ink mb-2">📱 QRIS</p>
                        <img src="{{ asset('storage/'.$owner->qris_image) }}" class="w-40 h-40 object-contain border border-line rounded-lg bg-white">
                    </div>
                @endif

                @if ($owner->terima_tunai)
                    <div class="bg-linen rounded-xl p-4 text-sm border border-line">
                        <p class="font-semibold text-ink">💵 Tunai (Cash)</p>
                        <p class="text-ink/60 text-xs mt-1">Bayar langsung ke Owner secara tunai, lalu tandai "Bayar Tunai" di bawah.</p>
                    </div>
                @endif

                @if (!$owner->punyaMetodePembayaran())
                    <x-empty title="Owner belum mengatur metode pembayaran" subtitle="Hubungi Owner untuk info cara membayar." />
                @endif
            </div>
        </div>

        @if ($owner->punyaMetodePembayaran())
        <form method="POST" action="{{ route('customer.pembayaran.upload', $pembayaran) }}" enctype="multipart/form-data" class="mt-5 space-y-3 pt-4 border-t border-line">
            @csrf
            <h2 class="font-display text-base font-medium text-ink">Konfirmasi Pembayaran</h2>
            <div>
                <label>Metode yang digunakan</label>
                <select name="metode" id="metode-bayar" required class="mt-1" onchange="document.getElementById('bukti-field').style.display = this.value === 'tunai' ? 'none' : 'block'">
                    @if ($owner->bank_account_number)<option value="transfer_bank">Transfer Bank</option>@endif
                    @if ($owner->qris_image)<option value="qris">QRIS</option>@endif
                    @if ($owner->terima_tunai)<option value="tunai">Tunai (Cash)</option>@endif
                </select>
            </div>
            <div id="bukti-field">
                <label>Bukti Pembayaran (screenshot/foto)</label>
                <input type="file" name="bukti_pembayaran" accept="image/*" class="mt-1">
                <p class="text-xs text-ink/40 mt-1">Tidak perlu diisi jika memilih metode Tunai.</p>
            </div>
            <button class="btn-primary">Kirim Konfirmasi Pembayaran</button>
        </form>
        @endif
    @elseif ($pembayaran->status === 'menunggu_verifikasi')
        <div class="mt-4 flex items-start gap-2.5 bg-brass-50 border border-brass/25 rounded-xl p-4 text-sm text-ink/70">
            <span class="w-1.5 h-1.5 rounded-full bg-brass mt-1.5 shrink-0"></span> Bukti pembayaran Anda sudah terkirim dan sedang menunggu validasi dari Owner.
        </div>
        @if ($pembayaran->bukti_pembayaran)
            <a href="{{ route('file.bukti-pembayaran', $pembayaran) }}" target="_blank" class="text-sm link-accent mt-2 inline-block">Lihat bukti yang diunggah</a>
        @endif
    @elseif ($pembayaran->status === 'lunas')
        <div class="mt-4 flex items-start gap-2.5 bg-sage-50 border border-sage/25 rounded-xl p-4 text-sm text-ink/70">
            <span class="w-1.5 h-1.5 rounded-full bg-sage mt-1.5 shrink-0"></span> Pembayaran sudah divalidasi oleh Owner pada {{ $pembayaran->validated_at?->format('d M Y H:i') }}.
        </div>
    @endif
</x-card>
@endsection
