@extends('layouts.app')
@section('title', 'Laporan Pendapatan')
@section('content')

<div class="mb-6">
    <h1 class="font-display text-2xl font-medium text-ink">Laporan Pendapatan</h1>
    <p class="text-sm text-ink/45 mt-0.5">Ringkasan pemasukan dari seluruh pembayaran yang sudah Lunas.</p>
</div>

<div class="grid grid-cols-2 lg:grid-cols-4 gap-4 mb-8">
    <x-stat label="Hari Ini" value="Rp{{ number_format($hariIni,2,',','.') }}" accent="brass" />
    <x-stat label="Minggu Ini" value="Rp{{ number_format($mingguIni,2,',','.') }}" accent="teal" />
    <x-stat label="Bulan Ini" value="Rp{{ number_format($bulanIni,2,',','.') }}" accent="sage" />
    <x-stat label="Tahun Ini ({{ date('Y') }})" value="Rp{{ number_format($tahunIni,2,',','.') }}" accent="brass" />
</div>

<div class="grid lg:grid-cols-2 gap-6 mb-6">
    {{-- Grafik pendapatan per bulan --}}
    <x-card>
        <h2 class="font-display text-base font-medium text-ink mb-1">Pendapatan per Bulan</h2>
        <p class="text-xs text-ink/40 mb-5">Tahun {{ date('Y') }}</p>

        @php
            $namaBulan = ['Jan','Feb','Mar','Apr','Mei','Jun','Jul','Agu','Sep','Okt','Nov','Des'];
            $maxBulan = max(array_values($perBulan)) ?: 1;
        @endphp
        <div class="flex items-end justify-between gap-1.5 h-40">
            @foreach ($perBulan as $bulan => $nilai)
                @php $tinggi = $nilai > 0 ? max(6, ($nilai / $maxBulan) * 100) : 3; @endphp
                <div class="flex-1 flex flex-col items-center justify-end h-full group relative">
                    <div class="absolute -top-6 opacity-0 group-hover:opacity-100 transition-opacity text-[10px] font-mono text-ink whitespace-nowrap bg-white border border-line rounded px-1.5 py-0.5 shadow-sm z-10">
                        Rp{{ number_format($nilai,0,',','.') }}
                    </div>
                    <div class="w-full rounded-t-md {{ $bulan == now()->month ? 'bg-brass' : 'bg-teal/70' }} hover:bg-brass transition-colors" style="height: {{ $tinggi }}%"></div>
                    <span class="text-[10px] text-ink/40 mt-1.5">{{ $namaBulan[$bulan - 1] }}</span>
                </div>
            @endforeach
        </div>
    </x-card>

    {{-- Grafik 7 hari terakhir --}}
    <x-card>
        <h2 class="font-display text-base font-medium text-ink mb-1">7 Hari Terakhir</h2>
        <p class="text-xs text-ink/40 mb-5">Termasuk hari ini</p>

        @php $max7 = max(array_values($per7Hari)) ?: 1; @endphp
        <div class="flex items-end justify-between gap-2 h-40">
            @foreach ($per7Hari as $tgl => $nilai)
                @php $tinggi = $nilai > 0 ? max(6, ($nilai / $max7) * 100) : 3; @endphp
                <div class="flex-1 flex flex-col items-center justify-end h-full group relative">
                    <div class="absolute -top-6 opacity-0 group-hover:opacity-100 transition-opacity text-[10px] font-mono text-ink whitespace-nowrap bg-white border border-line rounded px-1.5 py-0.5 shadow-sm z-10">
                        Rp{{ number_format($nilai,0,',','.') }}
                    </div>
                    <div class="w-full rounded-t-md bg-sage/70 hover:bg-sage transition-colors" style="height: {{ $tinggi }}%"></div>
                    <span class="text-[9px] text-ink/40 mt-1.5 text-center leading-tight">{{ $tgl }}</span>
                </div>
            @endforeach
        </div>
    </x-card>
</div>

<x-card>
    <div class="flex items-center justify-between mb-4">
        <h2 class="font-display text-base font-medium text-ink">Transaksi Lunas Terbaru</h2>
        <span class="text-xs text-ink/40 font-mono">{{ $totalTransaksi }} total transaksi lunas</span>
    </div>

    <div class="overflow-x-auto -mx-5 px-5 sm:mx-0 sm:px-0">
    <table class="w-full min-w-[600px] text-sm border-separate border-spacing-0">
        <thead>
            <tr class="text-left text-ink/40 text-xs font-semibold uppercase tracking-wide">
                <th class="py-2.5 border-b border-line">Tanggal</th>
                <th class="py-2.5 border-b border-line">Penyewa</th>
                <th class="py-2.5 border-b border-line">Kamar</th>
                <th class="py-2.5 border-b border-line text-right">Nominal</th>
            </tr>
        </thead>
        <tbody>
        @forelse ($transaksiTerbaru as $t)
            <tr class="row-hover">
                <td class="py-3 border-b border-line/70">{{ $t->tanggal_bayar?->format('d M Y') }}</td>
                <td class="py-3 border-b border-line/70">{{ $t->sewa->namaPenyewa() }}</td>
                <td class="py-3 border-b border-line/70">{{ $t->sewa->kamar->nomor_kamar }} &middot; {{ $t->sewa->kamar->kos->nama }}</td>
                <td class="py-3 border-b border-line/70 text-right font-semibold text-sage">Rp{{ number_format($t->nominal,2,',','.') }}</td>
            </tr>
        @empty
            <tr><td colspan="4" class="py-8 text-center text-ink/35">Belum ada transaksi Lunas.</td></tr>
        @endforelse
        </tbody>
    </table>
    </div>
</x-card>
@endsection
