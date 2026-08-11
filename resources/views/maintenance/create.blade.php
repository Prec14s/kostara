@extends('layouts.app')
@section('title', 'Lapor Maintenance')
@section('content')
<h1 class="font-display text-2xl font-medium text-ink mb-6">Lapor Maintenance</h1>

@if (!$bukaLayanan)
    <x-card class="max-w-lg">
        <h2 class="font-display text-lg font-medium text-ink">Layanan Maintenance Tidak Tersedia</h2>
        <p class="text-sm text-ink/45 mt-2">Layanan maintenance tersedia pukul <strong>07.00 - 17.00</strong>.</p>
        <p class="text-sm text-ink/45 mt-2">Jika masalah Anda bersifat urgent, silakan hubungi Penjaga Kos melalui WhatsApp.</p>

        @php $sewa = $kamarAktif->first(); @endphp
        @if ($sewa && $sewa->kamar->penjaga)
            <a href="{{ $sewa->kamar->penjaga->waLink("Halo Pak/Bu ".$sewa->kamar->penjaga->name.",\n\nSaya penghuni kamar {$sewa->kamar->nomor_kamar}. Saya membutuhkan bantuan karena ada masalah yang urgent.\n\nTerima kasih.") }}"
               target="_blank" class="mt-4 inline-block bg-green-600 text-white rounded-lg px-4 py-2 text-sm font-medium hover:bg-green-700">
               💬 Hubungi via WhatsApp
            </a>
        @endif
    </x-card>
@else
    <x-card class="max-w-lg">
        <form method="POST" action="{{ route('maintenance.store') }}" enctype="multipart/form-data" class="space-y-4">
            @csrf
            <div>
                <label class="text-sm font-medium">Kamar</label>
                <select name="kamar_id" required class="mt-1">
                    @foreach ($kamarAktif as $s)
                        <option value="{{ $s->kamar->id }}">Kamar {{ $s->kamar->nomor_kamar }} - {{ $s->kamar->kos->nama }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="text-sm font-medium">Jenis Masalah</label>
                <select name="jenis_masalah" required class="mt-1">
                    <option value="ac_rusak">AC rusak</option>
                    <option value="lampu_rusak">Lampu rusak</option>
                    <option value="keran_toilet">Keran/toilet bermasalah</option>
                    <option value="ganti_sprei">Ganti sprei</option>
                    <option value="bersihkan_kamar">Bersihkan kamar</option>
                    <option value="pintu_kunci">Pintu/kunci bermasalah</option>
                    <option value="listrik">Listrik bermasalah</option>
                    <option value="wifi">WiFi bermasalah</option>
                    <option value="perabot_rusak">Perabot rusak</option>
                    <option value="hama_serangga">Hama/serangga</option>
                    <option value="lainnya">Lainnya</option>
                </select>
            </div>
            <div>
                <label class="text-sm font-medium">Deskripsi</label>
                <textarea placeholder="Jelaskan masalah yang Anda alami secara singkat..." name="deskripsi" class="mt-1"></textarea>
            </div>
            <div>
                <label class="text-sm font-medium">Foto (opsional)</label>
                <input type="file" name="foto" accept="image/*" class="mt-1">
            </div>
            <div>
                <label class="text-sm font-medium">Prioritas</label>
                <select name="prioritas" required class="mt-1">
                    <option value="rendah">Rendah</option>
                    <option value="sedang" selected>Sedang</option>
                    <option value="tinggi">Tinggi</option>
                </select>
            </div>
            <button class="btn-primary">Kirim Laporan</button>
        </form>
    </x-card>
@endif
@endsection
