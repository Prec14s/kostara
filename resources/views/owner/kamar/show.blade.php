@extends('layouts.app')
@section('title', 'Detail Kamar '.$kamar->nomor_kamar)
@section('content')

<div class="flex items-center justify-between">
    <a href="{{ route('dashboard') }}" class="text-sm link-accent">&larr; Kembali ke dashboard</a>
    <a href="{{ route('owner.kamar.edit', $kamar) }}" class="text-sm link-accent">Kelola Foto &amp; Data Kamar &rarr;</a>
</div>

@php $sewa = $kamar->sewaAktif; @endphp

<div class="surface mt-4 max-w-xl overflow-hidden p-0">
    <div class="bg-ink px-6 py-6 relative overflow-hidden">
        <div class="absolute -top-10 -right-10 w-40 h-40 rounded-full bg-brass/20 blur-2xl"></div>
        <div class="relative flex items-center justify-between">
            <div class="flex items-center gap-3">
                <x-room-tag :nomor="$kamar->nomor_kamar" />
                <div>
                    <p class="text-white font-display text-lg font-medium">{{ $kamar->nama_kamar ?? 'Kamar '.$kamar->nomor_kamar }}</p>
                    <p class="text-white/40 text-xs">{{ $kamar->kos->nama }}</p>
                </div>
            </div>
            <x-badge color="amber">● Terisi</x-badge>
        </div>
    </div>

    <div class="p-6">
    @if ($kamar->fotos->count())
        <div class="grid grid-cols-4 gap-2 mb-5">
            @foreach ($kamar->fotos->take(4) as $foto)
                <a href="{{ asset('storage/'.$foto->path) }}" target="_blank" class="aspect-square rounded-lg overflow-hidden border border-line block">
                    <img src="{{ asset('storage/'.$foto->path) }}" class="w-full h-full object-cover hover:scale-105 transition-transform duration-200">
                </a>
            @endforeach
        </div>
    @endif

    @if ($sewa)
        <dl class="divide-y divide-line text-sm">
            <div class="py-2.5 flex justify-between"><dt class="text-ink/45">Nama</dt><dd class="font-semibold text-ink">{{ $sewa->namaPenyewa() }}</dd></div>

            @if ($sewa->penyewaLangsung)
                <div class="py-2.5 flex justify-between items-center">
                    <dt class="text-ink/45">No. KTP</dt>
                    <dd class="font-semibold text-ink text-right">
                        {{ $sewa->penyewaLangsung->no_ktp ?? '-' }}
                        @if ($sewa->penyewaLangsung->foto_ktp)
                            <a class="link-accent block text-xs font-normal" href="{{ route('file.ktp', $sewa->penyewaLangsung) }}" target="_blank">Lihat foto KTP &rarr;</a>
                        @endif
                    </dd>
                </div>
            @endif

            <div class="py-2.5 flex justify-between"><dt class="text-ink/45">WhatsApp</dt><dd class="font-semibold text-ink font-mono text-xs">{{ $sewa->whatsappPenyewa() ?? '-' }}</dd></div>
            <div class="py-2.5 flex justify-between"><dt class="text-ink/45">Mulai sewa</dt><dd class="font-semibold text-ink">{{ $sewa->tanggal_mulai->format('d M Y') }}</dd></div>
            <div class="py-2.5 flex justify-between"><dt class="text-ink/45">Habis sewa</dt><dd class="font-semibold text-ink">{{ $sewa->tanggal_selesai->format('d M Y') }}</dd></div>
            <div class="py-2.5 flex justify-between"><dt class="text-ink/45">Sisa waktu</dt><dd class="font-semibold text-ink">{{ $sewa->sisaHari() }} hari</dd></div>
            <div class="py-2.5 flex justify-between"><dt class="text-ink/45">Jenis sewa</dt><dd class="font-semibold text-ink">{{ ucfirst($sewa->jenis_durasi) }}</dd></div>
        </dl>

        <div class="mt-5 rounded-2xl bg-gradient-to-br from-teal to-teal-dark p-5 text-white relative overflow-hidden">
            <div class="absolute -bottom-6 -right-6 w-28 h-28 rounded-full bg-white/5"></div>
            <p class="text-xs text-white/60">Total pembayaran diterima</p>
            <p class="text-3xl font-display font-medium mt-1">Rp{{ number_format($sewa->totalPembayaranLunas(),2,',','.') }}</p>
            <p class="text-[11px] text-white/40 mt-1">Akumulasi seluruh transaksi berstatus Lunas</p>
        </div>

        <div class="mt-5 flex gap-3">
            <a href="{{ route('sewa.index') }}" class="flex-1 btn-ghost text-sm">Riwayat Pembayaran</a>
            @if ($sewa->whatsappPenyewa())
                @php
                    $pesan = "Halo {$sewa->namaPenyewa()},\n\nIni pesan dari Owner Kostara terkait kamar {$kamar->nomor_kamar}.";
                    $waNumber = preg_replace('/\D/', '', $sewa->whatsappPenyewa());
                    $waNumber = str_starts_with($waNumber, '0') ? '62'.substr($waNumber,1) : $waNumber;
                @endphp
                <a href="https://wa.me/{{ $waNumber }}?text={{ rawurlencode($pesan) }}" target="_blank"
                   class="flex-1 text-center text-sm bg-sage-50 text-sage rounded-xl py-2.5 font-semibold hover:bg-sage/15 transition-colors">💬 WhatsApp Penyewa</a>
            @endif
        </div>
    @else
        <x-empty title="Data sewa tidak ditemukan" subtitle="Kamar berstatus Terisi namun belum ada data sewa aktif." />
    @endif
    </div>
</div>
@endsection
