@extends('layouts.app')
@section('title', 'Perpanjang Sewa')
@section('content')

<a href="{{ route('customer.sewa.index') }}" class="text-sm link-accent">&larr; Sewa Saya</a>

<div class="max-w-lg mt-4">
    <h1 class="font-display text-2xl font-medium text-ink mb-1">Perpanjang Sewa</h1>
    <p class="text-sm text-ink/45 mb-6">Kamar {{ $sewa->kamar->nomor_kamar }} &middot; {{ $sewa->kamar->kos->nama }}</p>

    <x-card>
        <div class="flex items-start gap-2.5 bg-linen border border-line rounded-xl p-4 text-sm text-ink/60 mb-5">
            <span class="w-1.5 h-1.5 rounded-full bg-brass mt-1.5 shrink-0"></span>
            Pengajuan ini akan dikirim ke Owner untuk disetujui dulu. Setelah disetujui, tagihan pembayaran baru akan muncul di menu Pembayaran, dan masa sewa baru bertambah setelah Owner memvalidasi pembayarannya.
        </div>

        <form method="POST" action="{{ route('sewa.perpanjang.store', $sewa) }}" class="space-y-4">
            @csrf
            <div>
                <label>Pilih Durasi Perpanjangan</label>
                <div class="grid grid-cols-2 gap-3 mt-2">
                    @foreach (['harian' => 'Harian', 'mingguan' => 'Mingguan', 'bulanan' => 'Bulanan', 'tahunan' => 'Tahunan'] as $val => $label)
                        @php $harga = $sewa->kamar->hargaUntuk($val); @endphp
                        <label class="relative flex flex-col gap-1 border border-line rounded-xl p-3.5 cursor-pointer hover:border-brass/50 transition-colors has-[:checked]:border-brass has-[:checked]:bg-brass-50">
                            <input type="radio" name="jenis_durasi" value="{{ $val }}" class="absolute top-3 right-3 !w-auto" {{ $val === $sewa->jenis_durasi ? 'checked' : '' }} required>
                            <span class="font-semibold text-ink text-sm">{{ $label }}</span>
                            <span class="text-xs text-ink/45">{{ $harga ? 'Rp'.number_format($harga,2,',','.') : 'Harga belum diatur' }}</span>
                        </label>
                    @endforeach
                </div>
            </div>

            <button class="btn-primary w-full py-3">Ajukan Perpanjangan</button>
        </form>
    </x-card>
</div>
@endsection
