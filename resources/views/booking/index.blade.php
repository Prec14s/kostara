@extends('layouts.app')
@section('title', 'Booking')
@section('content')
<h1 class="font-display text-2xl font-medium text-ink mb-6">Booking Masuk</h1>

<x-card>
    <div class="overflow-x-auto -mx-5 px-5 sm:mx-0 sm:px-0">
    <table class="w-full min-w-[640px] text-sm border-separate border-spacing-0">
        <thead>
            <tr class="text-left text-ink/40 text-xs font-semibold uppercase tracking-wide">
                <th class="py-2.5 border-b border-line">Customer</th><th class="py-2.5 border-b border-line">Kamar</th><th class="py-2.5 border-b border-line">Durasi</th><th class="py-2.5 border-b border-line">Tgl Mulai</th><th class="py-2.5 border-b border-line">Status</th><th class="py-2.5 border-b border-line"></th>
            </tr>
        </thead>
        <tbody>
        @forelse ($bookings as $b)
            @php
                $c = ['menunggu'=>'amber','disetujui'=>'green','ditolak'=>'red','dibatalkan'=>'gray','selesai'=>'blue'];
            @endphp
            <tr class="row-hover">
                <td class="py-3 border-b border-line/70">{{ $b->customer->name }}</td>
                <td class="py-3 border-b border-line/70">{{ $b->kamar->kos->nama }} - {{ $b->kamar->nomor_kamar }}</td>
                <td class="py-3 border-b border-line/70">{{ ucfirst($b->jenis_durasi) }}</td>
                <td class="py-3 border-b border-line/70">{{ $b->tanggal_mulai->format('d M Y') }}</td>
                <td class="py-3 border-b border-line/70"><x-badge color="{{ $c[$b->status] }}">{{ ucfirst($b->status) }}</x-badge></td>
                <td class="text-right space-x-2">
                    @if ($b->status === 'menunggu' && auth()->user()->isOwner())
                        <form method="POST" action="{{ route('booking.approve', $b) }}" class="inline">
                            @csrf @method('PATCH')
                            <button class="text-sage font-semibold hover:underline">Setujui</button>
                        </form>
                        <form method="POST" action="{{ route('booking.reject', $b) }}" class="inline">
                            @csrf @method('PATCH')
                            <button class="text-clay font-semibold hover:underline">Tolak</button>
                        </form>
                    @endif
                </td>
            </tr>
        @empty
            <tr><td colspan="6" class="py-4 text-center text-ink/35">Belum ada booking.</td></tr>
        @endforelse
        </tbody>
    </table>
    </div>
    <div class="mt-4">{{ $bookings->links() }}</div>
</x-card>
@endsection
