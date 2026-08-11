@extends('layouts.app')
@section('title', 'Beri Tugas')
@section('content')
<h1 class="font-display text-2xl font-medium text-ink mb-6">Beri Tugas ke Penjaga</h1>

<x-card class="max-w-lg">
    <form method="POST" action="{{ route('owner.tasks.store') }}" class="space-y-4">
        @csrf
        <div>
            <label class="text-sm font-medium">Penjaga</label>
            <select name="penjaga_id" required class="mt-1">
                @foreach ($penjagaList as $p)
                    <option value="{{ $p->id }}">{{ $p->name }}</option>
                @endforeach
            </select>
        </div>
        <div>
            <label class="text-sm font-medium">Kamar (opsional)</label>
            <select name="kamar_id" class="mt-1">
                <option value="">- Tidak terkait kamar tertentu -</option>
                @foreach ($kamarList as $k)
                    <option value="{{ $k->id }}">Kamar {{ $k->nomor_kamar }}</option>
                @endforeach
            </select>
        </div>
        <div>
            <label class="text-sm font-medium">Judul Tugas</label>
            <input placeholder="Contoh: Bersihkan kamar sebelum check-in" type="text" name="judul" required class="mt-1">
        </div>
        <div>
            <label class="text-sm font-medium">Catatan</label>
            <textarea placeholder="Tambahkan catatan (opsional)" name="catatan" class="mt-1"></textarea>
        </div>
        <div class="grid grid-cols-2 gap-4">
            <div>
                <label class="text-sm font-medium">Prioritas</label>
                <select name="prioritas" required class="mt-1">
                    <option value="rendah">Rendah</option>
                    <option value="sedang" selected>Sedang</option>
                    <option value="tinggi">Tinggi</option>
                </select>
            </div>
            <div>
                <label class="text-sm font-medium">Deadline</label>
                <input type="date" name="deadline" class="mt-1">
            </div>
        </div>
        <button class="btn-primary">Kirim Tugas</button>
    </form>
</x-card>
@endsection
