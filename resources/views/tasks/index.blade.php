@extends('layouts.app')
@section('title', 'Tugas Penjaga')
@section('content')
<div class="flex justify-between items-center mb-4">
    <h1 class="font-display text-2xl font-medium text-ink">{{ auth()->user()->isOwner() ? 'Tugas Penjaga' : 'Tugas Saya' }}</h1>
    @if (auth()->user()->isOwner())
        <a href="{{ route('owner.tasks.create') }}" class="btn-primary text-sm px-3 py-2">+ Beri Tugas</a>
    @endif
</div>

<x-card>
    <div class="overflow-x-auto -mx-5 px-5 sm:mx-0 sm:px-0">
    <table class="w-full min-w-[640px] text-sm border-separate border-spacing-0">
        <thead>
            <tr class="text-left text-ink/40 text-xs font-semibold uppercase tracking-wide">
                <th class="py-2.5 border-b border-line">Judul</th><th class="py-2.5 border-b border-line">Penjaga</th><th class="py-2.5 border-b border-line">Prioritas</th><th class="py-2.5 border-b border-line">Deadline</th><th class="py-2.5 border-b border-line">Status</th>@if (auth()->user()->isPenjaga())<th class="py-2.5 border-b border-line"></th>@endif
            </tr>
        </thead>
        <tbody>
        @forelse ($tasks as $t)
            @php $c = ['belum_dikerjakan'=>'gray','diproses'=>'amber','selesai'=>'green']; @endphp
            <tr class="row-hover">
                <td class="py-3 border-b border-line/70">{{ $t->judul }}<div class="text-xs text-ink/35">{{ $t->kamar->nomor_kamar ?? '' }}</div></td>
                <td class="py-3 border-b border-line/70">{{ $t->penjaga->name }}</td>
                <td class="py-3 border-b border-line/70"><x-badge color="{{ $t->prioritas === 'tinggi' ? 'red' : 'gray' }}">{{ ucfirst($t->prioritas) }}</x-badge></td>
                <td class="py-3 border-b border-line/70">{{ $t->deadline?->format('d M Y') ?? '-' }}</td>
                <td class="py-3 border-b border-line/70"><x-badge color="{{ $c[$t->status] }}">{{ ucfirst(str_replace('_',' ',$t->status)) }}</x-badge></td>
                @if (auth()->user()->isPenjaga())
                <td class="text-right">
                    <form method="POST" action="{{ route('tasks.update-status', $t) }}">
                        @csrf @method('PATCH')
                        <select name="status" class="text-xs !py-1.5 !px-2 rounded-lg" onchange="this.form.submit()">
                            <option value="belum_dikerjakan" @selected($t->status==='belum_dikerjakan')>Belum Dikerjakan</option>
                            <option value="diproses" @selected($t->status==='diproses')>Diproses</option>
                            <option value="selesai" @selected($t->status==='selesai')>Selesai</option>
                        </select>
                    </form>
                </td>
                @endif
            </tr>
        @empty
            <tr><td colspan="6" class="py-4 text-center text-ink/35">Belum ada tugas.</td></tr>
        @endforelse
        </tbody>
    </table>
    </div>
    <div class="mt-4">{{ $tasks->links() }}</div>
</x-card>
@endsection
