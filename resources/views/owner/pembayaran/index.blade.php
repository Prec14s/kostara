@extends('layouts.app')
@section('title', 'Validasi Pembayaran')
@section('content')
<h1 class="font-display text-2xl font-medium text-ink mb-1">Validasi Pembayaran</h1>
<p class="text-sm text-ink/45 mb-6">Periksa rekening/QRIS Anda secara manual, lalu validasi setiap pembayaran yang masuk.</p>

<div class="space-y-4">
@forelse ($pembayarans as $p)
    @php
        $statusColor = ['belum_bayar'=>'gray','menunggu_verifikasi'=>'amber','lunas'=>'green','terlambat'=>'red','dibatalkan'=>'gray'][$p->status];
        $statusLabel = ['belum_bayar'=>'Belum Bayar','menunggu_verifikasi'=>'Menunggu Verifikasi','lunas'=>'Lunas','terlambat'=>'Terlambat','dibatalkan'=>'Dibatalkan'][$p->status];
        $isPending = $p->status === 'menunggu_verifikasi';
    @endphp
    <div class="surface p-5 {{ $isPending ? 'border-brass/40 ring-1 ring-brass/10' : '' }} transition-all">
        <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-3">
            <div class="flex items-center gap-3">
                <x-room-tag :nomor="$p->sewa->kamar->nomor_kamar" />
                <div>
                    <p class="font-semibold text-ink">{{ $p->sewa->namaPenyewa() }}</p>
                    <p class="text-xs text-ink/45 font-mono">{{ $p->no_transaksi }} &middot; Rp{{ number_format($p->nominal,2,',','.') }}</p>
                    @if ($p->tanggal_bayar)
                        <p class="text-xs text-ink/35">Dibayar {{ $p->tanggal_bayar->format('d M Y H:i') }} via {{ ['qris'=>'QRIS','tunai'=>'Tunai'][$p->metode] ?? 'Transfer Bank' }}</p>
                    @endif
                </div>
            </div>

            <div class="flex items-center gap-3">
                <x-badge color="{{ $statusColor }}">{{ $statusLabel }}</x-badge>
                @if ($p->bukti_pembayaran)
                    <a href="{{ route('file.bukti-pembayaran', $p) }}" target="_blank" class="text-sm link-accent">Lihat Bukti</a>
                @endif
            </div>
        </div>

        @if ($p->status === 'belum_bayar' && $p->sewa->whatsappPenyewa())
            @php
                $pesanTagihan = "Halo {$p->sewa->namaPenyewa()},\n\nIni pengingat dari Kostara untuk tagihan {$p->no_transaksi} sebesar Rp".number_format($p->nominal,2,',','.')." (Kamar {$p->sewa->kamar->nomor_kamar}).\n\nSilakan lakukan pembayaran sesuai metode yang tersedia di aplikasi. Terima kasih.";
                $waDigitsTagihan = preg_replace('/\D/', '', $p->sewa->whatsappPenyewa());
                $waDigitsTagihan = str_starts_with($waDigitsTagihan, '0') ? '62'.substr($waDigitsTagihan, 1) : $waDigitsTagihan;
            @endphp
            <div class="mt-3">
                <a href="https://wa.me/{{ $waDigitsTagihan }}?text={{ rawurlencode($pesanTagihan) }}" target="_blank"
                   class="inline-flex items-center gap-1.5 text-sage font-semibold hover:underline text-xs">💬 Ingatkan Bayar via WhatsApp</a>
            </div>
        @endif

        @if ($isPending)
            <div class="mt-4 bg-brass-50 border border-brass/25 rounded-xl p-4 text-sm">
                <p class="text-ink/70">
                    <strong class="text-ink">{{ $p->sewa->namaPenyewa() }}</strong> (Kamar {{ $p->sewa->kamar->nomor_kamar }}) sudah melakukan pembayaran.
                    Silakan cek rekening Anda untuk memastikan dana sudah diterima.
                </p>
                <p class="mt-1.5 text-ink font-semibold">Sudah menerima pembayaran ini?</p>
                <div class="mt-3 flex gap-2">
                    <form method="POST" action="{{ route('owner.pembayaran.validasi', $p) }}">
                        @csrf @method('PATCH')
                        <button class="btn text-sm px-5 py-2 bg-sage text-white hover:bg-sage/90 hover:shadow-lift">✓ Ya, Validasi</button>
                    </form>
                    <form method="POST" action="{{ route('owner.pembayaran.tolak', $p) }}">
                        @csrf @method('PATCH')
                        <button class="btn-ghost text-sm px-5 py-2">Belum</button>
                    </form>
                </div>
            </div>
        @endif
    </div>
@empty
    <x-empty title="Belum ada data pembayaran" subtitle="Tagihan yang dibuat dari booking akan muncul di sini." />
@endforelse
</div>

<div class="mt-6">{{ $pembayarans->links() }}</div>
@endsection
