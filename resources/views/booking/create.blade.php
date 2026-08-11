@extends('layouts.app')
@section('title', 'Booking Kamar')
@section('content')

@php $fotos = $kamar->fotos; @endphp

<a href="{{ route('guest.kos.detail', $kamar->kos) }}" class="text-sm link-accent">&larr; {{ $kamar->kos->nama }}</a>

<div class="grid lg:grid-cols-5 gap-6 mt-4">
    <div class="lg:col-span-3">
        {{-- Galeri foto kamar --}}
        @if ($fotos->count())
            <div class="rounded-2xl overflow-hidden">
                <div class="h-56 sm:h-80 sm:grid sm:grid-cols-4 sm:grid-rows-2 gap-2">
                    <div class="h-56 sm:h-auto sm:col-span-2 sm:row-span-2 bg-teal-50">
                        <img src="{{ asset('storage/'.$fotos->first()->path) }}" class="w-full h-full object-cover">
                    </div>
                    <div class="hidden sm:grid sm:col-span-2 sm:row-span-2 sm:grid-cols-2 sm:grid-rows-2 gap-2">
                        @foreach ($fotos->skip(1)->take(4) as $foto)
                            <div class="bg-teal-50">
                                <img src="{{ asset('storage/'.$foto->path) }}" class="w-full h-full object-cover">
                            </div>
                        @endforeach
                        @for ($i = $fotos->count(); $i < 5; $i++)
                            <div class="bg-linen"></div>
                        @endfor
                    </div>
                </div>

                @if ($fotos->count() > 1)
                    <div class="flex sm:hidden gap-2 mt-2 overflow-x-auto pb-1">
                        @foreach ($fotos->skip(1) as $foto)
                            <img src="{{ asset('storage/'.$foto->path) }}" class="w-20 h-20 rounded-lg object-cover shrink-0 border border-line">
                        @endforeach
                    </div>
                @endif
            </div>
            @if ($fotos->count() < 4)
                <p class="text-xs text-ink/35 mt-2">Foto kamar ini belum lengkap ({{ $fotos->count() }} foto).</p>
            @endif
        @else
            <div class="h-80 rounded-2xl bg-teal-50 flex items-center justify-center text-teal/20 font-display text-6xl">K</div>
            <p class="text-xs text-ink/35 mt-2">Owner belum mengunggah foto untuk kamar ini.</p>
        @endif

        <div class="mt-6">
            <div class="flex items-center gap-2.5">
                <x-room-tag :nomor="$kamar->nomor_kamar" />
                <h1 class="font-display text-2xl font-medium text-ink">{{ $kamar->nama_kamar ?? 'Kamar '.$kamar->nomor_kamar }}</h1>
            </div>
            <p class="text-sm text-ink/45 mt-1">{{ $kamar->kos->nama }} &middot; {{ $kamar->kos->alamat }}</p>

            @if ($kamar->deskripsi)
                <div class="mt-5">
                    <h2 class="font-display text-base font-medium text-ink">Deskripsi</h2>
                    <p class="text-sm text-ink/60 mt-1.5 leading-relaxed">{{ $kamar->deskripsi }}</p>
                </div>
            @endif

            @if ($kamar->fasilitas)
                <div class="mt-5">
                    <h2 class="font-display text-base font-medium text-ink">Fasilitas kamar</h2>
                    <p class="text-sm text-ink/60 mt-1.5 leading-relaxed">{{ $kamar->fasilitas }}</p>
                </div>
            @endif
        </div>
    </div>

    <div class="lg:col-span-2">
        <div class="surface p-5 lg:sticky lg:top-20">
            <h2 class="font-display text-base font-medium text-ink mb-3">Daftar harga</h2>
            <div class="grid grid-cols-2 gap-3 text-sm mb-5">
                <div class="bg-linen rounded-lg p-3"><p class="text-ink/45 text-xs">Harian</p><p class="font-semibold text-ink mt-0.5">{{ $kamar->harga_harian ? 'Rp'.number_format($kamar->harga_harian,2,',','.') : '-' }}</p></div>
                <div class="bg-linen rounded-lg p-3"><p class="text-ink/45 text-xs">Mingguan</p><p class="font-semibold text-ink mt-0.5">{{ $kamar->harga_mingguan ? 'Rp'.number_format($kamar->harga_mingguan,2,',','.') : '-' }}</p></div>
                <div class="bg-linen rounded-lg p-3"><p class="text-ink/45 text-xs">Bulanan</p><p class="font-semibold text-ink mt-0.5">{{ $kamar->harga_bulanan ? 'Rp'.number_format($kamar->harga_bulanan,2,',','.') : '-' }}</p></div>
                <div class="bg-linen rounded-lg p-3"><p class="text-ink/45 text-xs">Tahunan</p><p class="font-semibold text-ink mt-0.5">{{ $kamar->harga_tahunan ? 'Rp'.number_format($kamar->harga_tahunan,2,',','.') : '-' }}</p></div>
            </div>

            <form method="POST" action="{{ route('booking.store', $kamar) }}" class="space-y-4">
                @csrf
                <div>
                    <label>Pilih Durasi</label>
                    <select name="jenis_durasi" required class="mt-1">
                        <option value="harian">Harian</option>
                        <option value="mingguan">Mingguan</option>
                        <option value="bulanan" selected>Bulanan</option>
                        <option value="tahunan">Tahunan</option>
                    </select>
                </div>
                <div>
                    <label>Tanggal Mulai</label>
                    <input type="date" name="tanggal_mulai" required min="{{ date('Y-m-d') }}" value="{{ date('Y-m-d') }}" class="mt-1">
                </div>
                <div>
                    <label>Catatan (opsional)</label>
                    <textarea placeholder="Tambahkan catatan (opsional)" name="catatan" class="mt-1"></textarea>
                </div>
                <button class="btn-primary w-full py-3">Kirim Booking</button>
            </form>
        </div>
    </div>
</div>
@endsection
