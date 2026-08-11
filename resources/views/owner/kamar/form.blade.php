@extends('layouts.app')
@section('title', $kamar->exists ? 'Edit Kamar' : 'Tambah Kamar')
@section('content')
<h1 class="font-display text-2xl font-medium text-ink mb-6">{{ $kamar->exists ? 'Edit Kamar' : 'Tambah Kamar' }} - {{ $kos->nama }}</h1>

<x-card class="max-w-xl">
    <form method="POST"
        action="{{ $kamar->exists ? route('owner.kamar.update', $kamar) : route('owner.kos.kamar.store', $kos) }}"
        enctype="multipart/form-data" class="space-y-4">
        @csrf
        @if ($kamar->exists) @method('PUT') @endif

        <div class="grid grid-cols-2 gap-4">
            <div>
                <label class="text-sm font-medium">Nomor Kamar</label>
                <input placeholder="Contoh: K1, 101" type="text" name="nomor_kamar" value="{{ old('nomor_kamar', $kamar->nomor_kamar) }}" required class="mt-1">
            </div>
            <div>
                <label class="text-sm font-medium">Nama Kamar</label>
                <input placeholder="Contoh: Kamar Standar" type="text" name="nama_kamar" value="{{ old('nama_kamar', $kamar->nama_kamar) }}" class="mt-1">
            </div>
        </div>

        <div>
            <label class="text-sm font-medium">Deskripsi</label>
            <textarea placeholder="Ceritakan sedikit tentang kamar ini..." name="deskripsi" class="mt-1">{{ old('deskripsi', $kamar->deskripsi) }}</textarea>
        </div>
        <div>
            <label class="text-sm font-medium">Fasilitas</label>
            <textarea placeholder="Contoh: WiFi, dapur bersama, area parkir, laundry" name="fasilitas" class="mt-1">{{ old('fasilitas', $kamar->fasilitas) }}</textarea>
        </div>

        <div class="grid grid-cols-2 gap-4">
            <div><label class="text-sm font-medium">Harga Harian</label>
                <x-currency-input name="harga_harian" :value="old('harga_harian', $kamar->harga_harian)" placeholder="100.000" /></div>
            <div><label class="text-sm font-medium">Harga Mingguan</label>
                <x-currency-input name="harga_mingguan" :value="old('harga_mingguan', $kamar->harga_mingguan)" placeholder="500.000" /></div>
            <div><label class="text-sm font-medium">Harga Bulanan</label>
                <x-currency-input name="harga_bulanan" :value="old('harga_bulanan', $kamar->harga_bulanan)" placeholder="1.500.000" /></div>
            <div><label class="text-sm font-medium">Harga Tahunan</label>
                <x-currency-input name="harga_tahunan" :value="old('harga_tahunan', $kamar->harga_tahunan)" placeholder="15.000.000" /></div>
        </div>

        <div>
            <label>Penjaga Bertanggung Jawab</label>
            <select name="penjaga_id" class="mt-1">
                <option value="">- Belum ditentukan -</option>
                @foreach ($penjagaList as $p)
                    <option value="{{ $p->id }}" @selected(old('penjaga_id', $kamar->penjaga_id) == $p->id)>{{ $p->name }}</option>
                @endforeach
            </select>
        </div>

        <div>
            <label>Status</label>
            <select name="status" class="mt-1">
                @foreach (['tersedia'=>'Tersedia','dipesan'=>'Dipesan','terisi'=>'Terisi','maintenance'=>'Maintenance','nonaktif'=>'Tidak Aktif'] as $val => $label)
                    <option value="{{ $val }}" @selected(old('status', $kamar->status ?? 'tersedia') === $val)>{{ $label }}</option>
                @endforeach
            </select>
        </div>

        <div class="pt-2 border-t border-line">
            <label>Galeri Foto Kamar</label>
            <p class="text-xs text-ink/45 -mt-1 mb-3">Disarankan minimal 4 foto (kamar, kamar mandi, sudut lain) agar Customer lebih yakin untuk booking.</p>

            @if ($kamar->exists && $kamar->fotos->count())
                <div class="grid grid-cols-2 sm:grid-cols-4 gap-2 mb-3">
                    @foreach ($kamar->fotos as $foto)
                        <div class="relative group aspect-square rounded-lg overflow-hidden border border-line">
                            <img src="{{ asset('storage/'.$foto->path) }}" class="w-full h-full object-cover">
                            <form method="POST" action="{{ route('owner.kamar.foto.destroy', $foto) }}"
                                  onsubmit="return confirm('Hapus foto ini?')"
                                  class="absolute inset-0 bg-ink/0 group-hover:bg-ink/50 transition-colors flex items-center justify-center">
                                @csrf @method('DELETE')
                                <button class="opacity-0 group-hover:opacity-100 text-white text-xs font-semibold bg-clay/90 rounded-full px-2.5 py-1 transition-opacity">Hapus</button>
                            </form>
                        </div>
                    @endforeach
                </div>
                <p class="text-xs {{ $kamar->fotos->count() < 4 ? 'text-brass' : 'text-sage' }} font-semibold mb-3">
                    {{ $kamar->fotos->count() }} foto terunggah {{ $kamar->fotos->count() < 4 ? '-- tambahkan '.(4 - $kamar->fotos->count()).' foto lagi' : '' }}
                </p>
            @endif

            <p class="text-xs font-semibold text-ink/60 mb-2">Tambah foto baru (klik kotak untuk pilih foto, bisa 4 sekaligus)</p>
            <div class="grid grid-cols-2 sm:grid-cols-4 gap-3">
                @for ($i = 0; $i < 4; $i++)
                    <div class="relative aspect-square rounded-xl border-2 border-dashed border-line hover:border-brass/50 overflow-hidden cursor-pointer transition-colors bg-linen/50"
                         onclick="document.getElementById('foto-slot-{{ $i }}').click()">
                        <input type="file" id="foto-slot-{{ $i }}" name="fotos[]" accept="image/*" class="hidden" onchange="previewFotoSlot(this, {{ $i }})">

                        <div id="foto-slot-{{ $i }}-empty" class="w-full h-full flex flex-col items-center justify-center text-ink/30 gap-1.5">
                            <svg class="w-6 h-6" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"><path d="M12 5v14M5 12h14"/></svg>
                            <span class="text-xs font-medium">Foto {{ $i + 1 }}</span>
                        </div>

                        <img id="foto-slot-{{ $i }}-preview" class="hidden w-full h-full object-cover">

                        <button type="button" id="foto-slot-{{ $i }}-remove"
                                onclick="event.stopPropagation(); clearFotoSlot({{ $i }})"
                                class="hidden absolute top-1.5 right-1.5 w-6 h-6 rounded-full bg-clay text-white text-sm font-bold items-center justify-center hover:bg-clay/80 shadow-sm">
                            &times;
                        </button>
                    </div>
                @endfor
            </div>
        </div>

        <button class="btn-primary">Simpan</button>
    </form>
</x-card>

<script>
    function previewFotoSlot(input, i) {
        const file = input.files[0];
        const emptyEl = document.getElementById('foto-slot-' + i + '-empty');
        const previewEl = document.getElementById('foto-slot-' + i + '-preview');
        const removeEl = document.getElementById('foto-slot-' + i + '-remove');
        if (!file) return;

        const reader = new FileReader();
        reader.onload = function (e) {
            previewEl.src = e.target.result;
            previewEl.classList.remove('hidden');
            emptyEl.classList.add('hidden');
            removeEl.classList.remove('hidden');
            removeEl.classList.add('flex');
        };
        reader.readAsDataURL(file);
    }

    function clearFotoSlot(i) {
        const input = document.getElementById('foto-slot-' + i);
        const emptyEl = document.getElementById('foto-slot-' + i + '-empty');
        const previewEl = document.getElementById('foto-slot-' + i + '-preview');
        const removeEl = document.getElementById('foto-slot-' + i + '-remove');
        input.value = '';
        previewEl.src = '';
        previewEl.classList.add('hidden');
        emptyEl.classList.remove('hidden');
        removeEl.classList.add('hidden');
        removeEl.classList.remove('flex');
    }
</script>
@endsection
