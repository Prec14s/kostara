@extends('layouts.app')
@section('title', 'Pembayaran Saya')
@section('content')
<h1 class="font-display text-2xl font-medium text-ink mb-6">Pembayaran Saya</h1>

<x-card>
    <div class="overflow-x-auto -mx-5 px-5 sm:mx-0 sm:px-0">
    <table class="w-full min-w-[640px] text-sm border-separate border-spacing-0">
        <thead>
            <tr class="text-left text-ink/40 text-xs font-semibold uppercase tracking-wide">
                <th class="py-2.5 border-b border-line">No. Transaksi</th><th class="py-2.5 border-b border-line">Kamar</th><th class="py-2.5 border-b border-line">Nominal</th><th class="py-2.5 border-b border-line">Status</th><th class="py-2.5 border-b border-line"></th>
            </tr>
        </thead>
        <tbody>
        @forelse ($pembayarans as $p)
            @php
                $statusColor = ['belum_bayar'=>'gray','menunggu_verifikasi'=>'amber','lunas'=>'green','terlambat'=>'red','dibatalkan'=>'gray'][$p->status];
                $statusLabel = ['belum_bayar'=>'Belum Bayar','menunggu_verifikasi'=>'Menunggu Verifikasi','lunas'=>'Lunas','terlambat'=>'Terlambat','dibatalkan'=>'Dibatalkan'][$p->status];
            @endphp
            <tr class="row-hover">
                <td class="py-3 border-b border-line/70">{{ $p->no_transaksi }}</td>
                <td class="py-3 border-b border-line/70">{{ $p->sewa->kamar->kos->nama }} - {{ $p->sewa->kamar->nomor_kamar }}</td>
                <td class="py-3 border-b border-line/70">Rp{{ number_format($p->nominal,2,',','.') }}</td>
                <td class="py-3 border-b border-line/70"><x-badge color="{{ $statusColor }}">{{ $statusLabel }}</x-badge></td>
                <td class="text-right"><a href="{{ route('customer.pembayaran.show', $p) }}" class="link-accent">Detail</a></td>
            </tr>
        @empty
            <tr><td colspan="5" class="py-4 text-center text-ink/35">Belum ada tagihan.</td></tr>
        @endforelse
        </tbody>
    </table>
    </div>
</x-card>
@endsection
