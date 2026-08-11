@extends('layouts.app')
@section('title', 'Sewa Langsung')
@section('content')
<h1 class="font-display text-2xl font-medium text-ink mb-6">Sewa Langsung (Modul 9)</h1>
<p class="text-sm text-ink/45 mb-4">Untuk customer yang datang langsung ke kos tanpa membuat akun website.</p>

<x-card class="max-w-xl">
    <form method="POST" action="{{ route('owner.sewa-langsung.store') }}" enctype="multipart/form-data" class="space-y-4">
        @csrf

        <div>
            <label class="text-sm font-medium">Kamar</label>
            <select name="kamar_id" required class="mt-1">
                <option value="">- Pilih kamar tersedia -</option>
                @foreach ($kamarTersedia as $k)
                    <option value="{{ $k->id }}">{{ $k->kos->nama }} - Kamar {{ $k->nomor_kamar }}</option>
                @endforeach
            </select>
        </div>

        <div class="grid grid-cols-2 gap-4">
            <div><label class="text-sm font-medium">Nama Lengkap</label>
                <input placeholder="Nama lengkap penyewa" type="text" name="nama" required class="mt-1"></div>
            <div><label class="text-sm font-medium">Nomor WhatsApp</label>
                <input placeholder="08xxxxxxxxxx" type="text" name="whatsapp" required class="mt-1"></div>
        </div>
        <div class="grid grid-cols-2 gap-4">
            <div><label class="text-sm font-medium">Nomor KTP</label>
                <input placeholder="Sesuai KTP" type="text" name="no_ktp" class="mt-1"></div>
            <div><label class="text-sm font-medium">Foto KTP</label>
                <input type="file" name="foto_ktp" accept="image/*" class="mt-1">
                <p class="text-xs text-ink/35 mt-1">Disimpan privat sesuai BR05.</p></div>
        </div>
        <div><label class="text-sm font-medium">Alamat</label>
            <textarea placeholder="Alamat asal penyewa (opsional)" name="alamat" class="mt-1"></textarea></div>

        <div class="grid grid-cols-2 gap-4">
            <div><label class="text-sm font-medium">Jenis Sewa</label>
                <select name="jenis_durasi" required class="mt-1">
                    <option value="harian">Harian</option>
                    <option value="mingguan">Mingguan</option>
                    <option value="bulanan" selected>Bulanan</option>
                    <option value="tahunan">Tahunan</option>
                </select></div>
            <div><label class="text-sm font-medium">Tanggal Mulai</label>
                <input type="date" name="tanggal_mulai" required value="{{ date('Y-m-d') }}" class="mt-1"></div>
        </div>
        <div><label class="text-sm font-medium">Deposit (opsional)</label>
            <x-currency-input name="deposit" placeholder="200.000 (kosongkan jika tidak ada)" /></div>
        <div><label class="text-sm font-medium">Catatan</label>
            <textarea placeholder="Tambahkan catatan (opsional)" name="catatan" class="mt-1"></textarea></div>

        <button class="btn-primary">Simpan & Aktifkan Sewa</button>
    </form>
</x-card>
@endsection
