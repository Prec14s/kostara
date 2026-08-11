@extends('layouts.app')
@section('title', 'Kamar - '.$kos->nama)
@section('content')
<a href="{{ route('owner.kos.index') }}" class="text-sm link-accent">&larr; Kos Saya</a>
<div class="flex justify-between items-center my-5">
    <div>
        <h1 class="font-display text-2xl font-medium text-ink">Kamar &middot; {{ $kos->nama }}</h1>
        <p class="text-sm text-ink/45 mt-0.5">{{ $kamars->count() }} kamar terdaftar</p>
    </div>
    <a href="{{ route('owner.kos.kamar.create', $kos) }}" class="btn-primary text-sm px-4 py-2.5">+ Tambah Kamar</a>
</div>

<div class="grid sm:grid-cols-2 lg:grid-cols-3 gap-5">
    @forelse ($kamars as $kamar)
        @php
            $c = ['tersedia'=>'blue','dipesan'=>'amber','terisi'=>'green','maintenance'=>'red','nonaktif'=>'gray'];
            $cover = $kamar->coverFoto();
        @endphp
        <div class="surface p-0 overflow-hidden group">
            <div class="h-36 bg-teal-50 relative overflow-hidden">
                @if ($cover)
                    <img src="{{ asset('storage/'.$cover) }}" class="w-full h-full object-cover">
                @else
                    <div class="w-full h-full flex items-center justify-center text-teal/20 font-display text-3xl">K</div>
                @endif
                @if ($kamar->fotos->count() < 4)
                    <span class="absolute top-3 right-3 chip bg-white/90 text-brass backdrop-blur-sm">{{ $kamar->fotos->count() }}/4 foto</span>
                @endif
            </div>
            <div class="px-4 pt-3">
                <x-room-tag :nomor="$kamar->nomor_kamar" />
            </div>
            <div class="p-5 pt-2">
                <div class="flex items-center justify-between">
                    <p class="font-semibold text-ink text-sm">{{ $kamar->nama_kamar ?? 'Kamar '.$kamar->nomor_kamar }}</p>
                    <x-badge color="{{ $c[$kamar->status] }}">{{ ucfirst($kamar->status) }}</x-badge>
                </div>
                <p class="text-xs text-ink/45 mt-1">{{ $kamar->penjaga->name ? 'Dijaga oleh '.$kamar->penjaga->name : 'Belum ada penjaga' }}</p>
                <p class="text-sm font-semibold text-teal mt-2">{{ $kamar->harga_bulanan ? 'Rp'.number_format($kamar->harga_bulanan,2,',','.').'/bulan' : 'Harga belum diatur' }}</p>

                <div class="mt-4 flex gap-2">
                    <a href="{{ route('owner.kamar.edit', $kamar) }}" class="flex-1 btn-primary text-sm py-2">Edit Kamar</a>
                    @if ($kamar->status === 'terisi')
                        <a href="{{ route('owner.kamar.show', $kamar) }}" class="flex-1 btn-ghost text-sm py-2">Detail</a>
                    @else
                        <form method="POST" action="{{ route('owner.kamar.destroy', $kamar) }}"
                              onsubmit="return confirm('Hapus Kamar {{ $kamar->nomor_kamar }}? Foto dan riwayat sewa/pembayaran kamar ini (jika ada) akan ikut terhapus permanen.')">
                            @csrf @method('DELETE')
                            <button type="submit" class="btn-danger text-sm py-2 px-3.5" title="Hapus kamar">Hapus</button>
                        </form>
                    @endif
                </div>
            </div>
        </div>
    @empty
        <div class="col-span-full">
            <x-empty title="Belum ada kamar di kos ini" subtitle="Tambahkan kamar A sampai H (atau sesuai kebutuhan Anda) beserta foto dan harga.">
                <x-slot:action>
                    <a href="{{ route('owner.kos.kamar.create', $kos) }}" class="btn-primary text-sm">+ Tambah Kamar</a>
                </x-slot:action>
            </x-empty>
        </div>
    @endforelse
</div>
@endsection
