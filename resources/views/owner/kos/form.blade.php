@extends('layouts.app')
@section('title', $kos->exists ? 'Edit Kos' : 'Tambah Kos')
@section('content')
<h1 class="font-display text-2xl font-medium text-ink mb-6">{{ $kos->exists ? 'Edit Kos' : 'Tambah Kos' }}</h1>

<x-card class="max-w-xl">
    <form method="POST" action="{{ $kos->exists ? route('owner.kos.update', $kos) : route('owner.kos.store') }}" enctype="multipart/form-data" class="space-y-4">
        @csrf
        @if ($kos->exists) @method('PUT') @endif

        <div>
            <label class="text-sm font-medium">Nama Kos</label>
            <input placeholder="Contoh: Kos Melati Asri" type="text" name="nama" value="{{ old('nama', $kos->nama) }}" required class="mt-1">
        </div>
        <div>
            <label class="text-sm font-medium">Alamat</label>
            <textarea placeholder="Contoh: Jl. Melati No. 10, Yogyakarta" name="alamat" required class="mt-1">{{ old('alamat', $kos->alamat) }}</textarea>
        </div>
        <div>
            <label class="text-sm font-medium">Deskripsi</label>
            <textarea placeholder="Ceritakan sedikit tentang kos ini..." name="deskripsi" class="mt-1">{{ old('deskripsi', $kos->deskripsi) }}</textarea>
        </div>
        <div>
            <label class="text-sm font-medium">Fasilitas Umum</label>
            <textarea placeholder="Contoh: WiFi, dapur bersama, area parkir, laundry" name="fasilitas" class="mt-1">{{ old('fasilitas', $kos->fasilitas) }}</textarea>
        </div>
        <div>
            <label class="text-sm font-medium">Peraturan Kos</label>
            <textarea placeholder="Contoh: Jam malam 22.00, dilarang membawa tamu menginap" name="peraturan" class="mt-1">{{ old('peraturan', $kos->peraturan) }}</textarea>
        </div>
        <div class="grid grid-cols-2 gap-4">
            <div>
                <label class="text-sm font-medium">Jam Operasional</label>
                <input placeholder="Contoh: 07.00 - 21.00" type="text" name="jam_operasional" value="{{ old('jam_operasional', $kos->jam_operasional) }}" class="mt-1">
            </div>
            <div>
                <label class="text-sm font-medium">Kontak</label>
                <input placeholder="Nomor telepon/WhatsApp yang bisa dihubungi" type="text" name="kontak" value="{{ old('kontak', $kos->kontak) }}" class="mt-1">
            </div>
        </div>
        <div>
            <label class="text-sm font-medium">Foto Kos</label>
            <input type="file" name="foto" accept="image/*" class="mt-1">
        </div>
        <label class="flex items-center gap-2 text-sm">
            <input type="checkbox" name="is_active" value="1" @checked(old('is_active', $kos->is_active ?? true)) class="">
            Aktif (tampil di landing page)
        </label>

        <button class="btn-primary">Simpan</button>
    </form>
</x-card>
@endsection
