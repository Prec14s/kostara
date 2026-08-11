@extends('layouts.app')
@section('title', 'Dashboard Penjaga')
@section('content')
<h1 class="font-display text-2xl font-medium text-ink mb-6">Dashboard Penjaga</h1>

<div class="grid sm:grid-cols-3 gap-4 mb-6">
    <x-stat label="Kamar Dijaga" value="{{ $kamars->count() }}" />
    <x-stat label="Maintenance Aktif" value="{{ $maintenanceList->count() }}" />
    <x-stat label="Tugas Belum Selesai" value="{{ $tasks->count() }}" />
</div>

<div class="grid md:grid-cols-2 gap-5">
    <x-card>
        <h2 class="font-display text-base font-medium text-ink mb-3">Kamar Segera Jatuh Tempo (7 hari)</h2>
        @forelse ($jatuhTempo as $sewa)
            @php $wa = $sewa->whatsappPenyewa(); @endphp
            <div class="row-hover flex justify-between items-center py-2.5 text-sm border-b border-line/70 last:border-0">
                <div class="flex items-center gap-2.5">
                    <x-room-tag :nomor="$sewa->kamar->nomor_kamar" />
                    <div>
                        <p class="font-medium text-ink">{{ $sewa->namaPenyewa() }}</p>
                        <p class="text-xs text-brass font-semibold">{{ $sewa->tanggal_selesai->format('d M Y') }}</p>
                    </div>
                </div>
                @if ($wa)
                    @php
                        $pesan = "Halo {$sewa->namaPenyewa()},\n\nIni pengingat dari Kostara bahwa sewa kamar {$sewa->kamar->nomor_kamar} akan berakhir pada ".$sewa->tanggal_selesai->format('d M Y').". Mohon segera lakukan pembayaran/perpanjangan.\n\nTerima kasih.";
                        $waDigits = preg_replace('/\D/', '', $wa);
                        $waDigits = str_starts_with($waDigits, '0') ? '62'.substr($waDigits, 1) : $waDigits;
                    @endphp
                    <a href="https://wa.me/{{ $waDigits }}?text={{ rawurlencode($pesan) }}" target="_blank" class="text-sage font-semibold hover:underline text-xs shrink-0">💬 Ingatkan</a>
                @endif
            </div>
        @empty
            <p class="text-sm text-ink/35">Tidak ada kamar yang jatuh tempo minggu ini.</p>
        @endforelse
    </x-card>

    <x-card>
        <h2 class="font-display text-base font-medium text-ink mb-3">Tugas dari Owner</h2>
        @forelse ($tasks as $task)
            <div class="border-b last:border-0 py-2 text-sm">
                <div class="flex justify-between">
                    <span class="font-medium">{{ $task->judul }}</span>
                    <x-badge color="{{ $task->prioritas === 'tinggi' ? 'red' : 'gray' }}">{{ ucfirst($task->prioritas) }}</x-badge>
                </div>
                <form method="POST" action="{{ route('tasks.update-status', $task) }}" class="mt-1 flex gap-2 items-center">
                    @csrf @method('PATCH')
                    <select name="status" class="text-xs !py-1.5 !px-2 rounded-lg" onchange="this.form.submit()">
                        <option value="belum_dikerjakan" @selected($task->status==='belum_dikerjakan')>Belum Dikerjakan</option>
                        <option value="diproses" @selected($task->status==='diproses')>Diproses</option>
                        <option value="selesai" @selected($task->status==='selesai')>Selesai</option>
                    </select>
                </form>
            </div>
        @empty
            <p class="text-sm text-ink/35">Tidak ada tugas.</p>
        @endforelse
    </x-card>
</div>
@endsection
