@extends('layouts.app')
@section('title', 'Maintenance')
@section('content')
<h1 class="font-display text-2xl font-medium text-ink mb-6">Daftar Maintenance</h1>

<x-card>
    <div class="overflow-x-auto -mx-5 px-5 sm:mx-0 sm:px-0">
    <table class="w-full min-w-[640px] text-sm border-separate border-spacing-0">
        <thead>
            <tr class="text-left text-ink/40 text-xs font-semibold uppercase tracking-wide">
                <th class="py-2.5 border-b border-line">Kamar</th><th class="py-2.5 border-b border-line">Jenis</th><th class="py-2.5 border-b border-line">Prioritas</th><th class="py-2.5 border-b border-line">Status</th>
                @if (!auth()->user()->isCustomer())<th class="py-2.5 border-b border-line"></th>@endif
            </tr>
        </thead>
        <tbody>
        @forelse ($reports as $r)
            @php
                $c = ['menunggu'=>'amber','diproses'=>'blue','selesai'=>'green','ditolak'=>'red'];
                $jenis = str_replace('_',' ', $r->jenis_masalah);
            @endphp
            <tr class="row-hover">
                <td class="py-3 border-b border-line/70">{{ $r->kamar->nomor_kamar }} - {{ $r->kamar->kos->nama }}</td>
                <td class="capitalize">{{ $jenis }}</td>
                <td class="py-3 border-b border-line/70"><x-badge color="{{ $r->prioritas === 'tinggi' ? 'red' : 'gray' }}">{{ ucfirst($r->prioritas) }}</x-badge></td>
                <td class="py-3 border-b border-line/70"><x-badge color="{{ $c[$r->status] }}">{{ ucfirst($r->status) }}</x-badge></td>
                @if (!auth()->user()->isCustomer())
                <td class="text-right">
                    <form method="POST" action="{{ route('maintenance.update-status', $r) }}" class="inline">
                        @csrf @method('PATCH')
                        <select name="status" class="text-xs !py-1.5 !px-2 rounded-lg" onchange="this.form.submit()">
                            <option value="menunggu" @selected($r->status==='menunggu')>Menunggu</option>
                            <option value="diproses" @selected($r->status==='diproses')>Diproses</option>
                            <option value="selesai" @selected($r->status==='selesai')>Selesai</option>
                            <option value="ditolak" @selected($r->status==='ditolak')>Ditolak</option>
                        </select>
                    </form>
                </td>
                @endif
            </tr>
        @empty
            <tr><td colspan="5" class="py-4 text-center text-ink/35">Belum ada laporan maintenance.</td></tr>
        @endforelse
        </tbody>
    </table>
    </div>
    <div class="mt-4">{{ $reports->links() }}</div>
</x-card>
@endsection
