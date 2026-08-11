@extends('layouts.app')
@section('title', 'Buat Pengumuman')
@section('content')
<h1 class="font-display text-2xl font-medium text-ink mb-6">Buat Pengumuman</h1>

<x-card class="max-w-lg">
    <form method="POST" action="{{ route('owner.announcements.store') }}" class="space-y-4">
        @csrf
        <div>
            <label class="text-sm font-medium">Kos</label>
            <select name="kos_id" required class="mt-1">
                @foreach ($kosList as $k)
                    <option value="{{ $k->id }}">{{ $k->nama }}</option>
                @endforeach
            </select>
        </div>
        <div>
            <label class="text-sm font-medium">Judul</label>
            <input placeholder="Contoh: Pemadaman listrik terjadwal" type="text" name="judul" required class="mt-1">
        </div>
        <div>
            <label class="text-sm font-medium">Isi Pengumuman</label>
            <textarea placeholder="Tulis isi pengumuman untuk penghuni kos..." name="isi" required rows="4" class="mt-1"></textarea>
        </div>
        <button class="btn-primary">Publikasikan</button>
    </form>
</x-card>
@endsection
