@extends('layouts.app')
@section('title', 'Sewa')
@section('content')
<h1 class="font-display text-2xl font-medium text-ink mb-6">{{ auth()->user()->isCustomer() ? 'Sewa Saya' : 'Data Sewa' }}</h1>

@if (auth()->user()->isOwner() && $sewas->contains(fn ($s) => $s->perpanjanganMenunggu))
    <x-card class="mb-5 border-brass/40">
        <h2 class="font-display text-base font-medium text-ink mb-3">Pengajuan Perpanjangan Menunggu Persetujuan</h2>
        <div class="space-y-3">
            @foreach ($sewas as $s)
                @continue(! $s->perpanjanganMenunggu)
                @php $p = $s->perpanjanganMenunggu; @endphp
                <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 bg-brass-50 border border-brass/25 rounded-xl p-4">
                    <div class="flex items-center gap-2.5">
                        <x-room-tag :nomor="$s->kamar->nomor_kamar" />
                        <div class="text-sm">
                            <p class="font-semibold text-ink">{{ $s->namaPenyewa() }}</p>
                            <p class="text-ink/60">Ajukan perpanjangan <strong>{{ ucfirst($p->jenis_durasi) }}</strong> &middot; Rp{{ number_format($p->harga,2,',','.') }}</p>
                        </div>
                    </div>
                    <div class="flex gap-2 shrink-0">
                        <form method="POST" action="{{ route('owner.perpanjangan.approve', $p) }}">
                            @csrf @method('PATCH')
                            <button class="btn text-sm px-4 py-2 bg-sage text-white hover:bg-sage/90">Setujui</button>
                        </form>
                        <form method="POST" action="{{ route('owner.perpanjangan.reject', $p) }}" onsubmit="return confirm('Tolak pengajuan perpanjangan ini?')">
                            @csrf @method('PATCH')
                            <button class="btn-ghost text-sm px-4 py-2">Tolak</button>
                        </form>
                    </div>
                </div>
            @endforeach
        </div>
    </x-card>
@endif

<x-card>
    <div class="overflow-x-auto -mx-5 px-5 sm:mx-0 sm:px-0">
    <table class="w-full min-w-[640px] text-sm border-separate border-spacing-0">
        <thead>
            <tr class="text-left text-ink/40 text-xs font-semibold uppercase tracking-wide">
                <th class="py-2.5 border-b border-line">Penyewa</th><th class="py-2.5 border-b border-line">Kamar</th><th class="py-2.5 border-b border-line">Periode</th><th class="py-2.5 border-b border-line">Status</th>
                <th class="py-2.5 border-b border-line"></th>
            </tr>
        </thead>
        <tbody>
        @forelse ($sewas as $s)
            @php
                $c = ['menunggu_pembayaran'=>'amber','aktif'=>'green','akan_berakhir'=>'amber','selesai'=>'gray','dibatalkan'=>'red'];
                $label = ['menunggu_pembayaran'=>'Menunggu Pembayaran','aktif'=>'Aktif','akan_berakhir'=>'Akan Berakhir','selesai'=>'Selesai','dibatalkan'=>'Dibatalkan'];
                $waNomor = $s->whatsappPenyewa();
            @endphp
            <tr class="row-hover">
                <td class="py-3 border-b border-line/70 flex items-center gap-2.5">
                    <x-room-tag :nomor="$s->kamar->nomor_kamar" />
                    <span>{{ $s->namaPenyewa() }}</span>
                </td>
                <td class="py-3 border-b border-line/70">{{ $s->kamar->kos->nama }}</td>
                <td class="py-3 border-b border-line/70">{{ $s->tanggal_mulai->format('d M Y') }} &ndash; {{ $s->tanggal_selesai->format('d M Y') }}
                    <span class="text-xs text-ink/35">({{ $s->sisaHari() }} hari lagi)</span></td>
                <td class="py-3 border-b border-line/70"><x-badge color="{{ $c[$s->status] }}">{{ $label[$s->status] }}</x-badge></td>
                <td class="py-3 border-b border-line/70 text-right">
                    <div class="flex items-center justify-end gap-3">
                        @if (auth()->user()->isCustomer())
                            @if ($s->perpanjanganMenunggu)
                                <x-badge color="amber">Menunggu Persetujuan</x-badge>
                            @elseif ($s->status === 'aktif')
                                <a href="{{ route('sewa.perpanjang.create', $s) }}" class="link-accent text-sm">Perpanjang Sewa</a>
                            @endif
                        @else
                            @if ($waNomor && in_array($s->status, ['aktif', 'akan_berakhir']))
                                @php
                                    $pesan = "Halo {$s->namaPenyewa()},\n\nIni pengingat dari Kostara bahwa sewa kamar {$s->kamar->nomor_kamar} akan berakhir pada ".$s->tanggal_selesai->format('d M Y').". Mohon segera lakukan pembayaran/perpanjangan.\n\nTerima kasih.";
                                    $waDigits = preg_replace('/\D/', '', $waNomor);
                                    $waDigits = str_starts_with($waDigits, '0') ? '62'.substr($waDigits, 1) : $waDigits;
                                @endphp
                                <a href="https://wa.me/{{ $waDigits }}?text={{ rawurlencode($pesan) }}" target="_blank" class="text-sage font-semibold hover:underline text-xs whitespace-nowrap">💬 Ingatkan</a>
                            @endif

                            @if (auth()->user()->isOwner())
                                <form method="POST" action="{{ route('owner.sewa.destroy', $s) }}"
                                      onsubmit="return confirm('Hapus data sewa {{ $s->namaPenyewa() }} (Kamar {{ $s->kamar->nomor_kamar }})? Riwayat pembayaran pada sewa ini akan ikut terhapus dan tidak bisa dikembalikan.')">
                                    @csrf @method('DELETE')
                                    <button class="text-clay font-semibold hover:underline text-xs whitespace-nowrap">Hapus</button>
                                </form>
                            @endif
                        @endif
                    </div>
                </td>
            </tr>
        @empty
            <tr><td colspan="5" class="py-4 text-center text-ink/35">Belum ada data sewa.</td></tr>
        @endforelse
        </tbody>
    </table>
    </div>
    <div class="mt-4">{{ $sewas->links() }}</div>
</x-card>
@endsection
