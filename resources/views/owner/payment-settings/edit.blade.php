@extends('layouts.app')
@section('title', 'Metode Pembayaran')
@section('content')
<h1 class="font-display text-2xl font-medium text-ink mb-1">Metode Pembayaran</h1>
<p class="text-sm text-ink/45 mb-6">Isi rekening bank dan/atau QRIS. Boleh isi keduanya sekaligus -- Customer akan melihat semua metode yang Anda aktifkan saat membayar.</p>

<div class="grid lg:grid-cols-3 gap-6">
    <x-card class="lg:col-span-2">
        <form method="POST" action="{{ route('owner.payment-settings.update') }}" enctype="multipart/form-data" class="space-y-5">
            @csrf @method('PUT')

            <div>
                <div class="flex items-center gap-2 mb-3">
                    <span class="w-1.5 h-1.5 rounded-full {{ $owner->bank_account_number ? 'bg-sage' : 'bg-ink/20' }}"></span>
                    <h2 class="font-display text-base font-medium text-ink">Transfer Bank</h2>
                </div>
                <div class="grid grid-cols-2 gap-4">
                    <div><label>Nama Bank</label>
                        <input type="text" name="bank_name" value="{{ old('bank_name', $owner->bank_name) }}" placeholder="Contoh: BCA, Mandiri, BNI" class="mt-1"></div>
                    <div><label>Nomor Rekening</label>
                        <input placeholder="Nomor rekening tanpa spasi" type="text" name="bank_account_number" value="{{ old('bank_account_number', $owner->bank_account_number) }}" class="mt-1"></div>
                </div>
                <div class="mt-4"><label>Atas Nama</label>
                    <input placeholder="Nama sesuai buku tabungan" type="text" name="bank_account_holder" value="{{ old('bank_account_holder', $owner->bank_account_holder) }}" class="mt-1"></div>
            </div>

            <div class="pt-4 border-t border-line">
                <div class="flex items-center gap-2 mb-3">
                    <span class="w-1.5 h-1.5 rounded-full {{ $owner->qris_image ? 'bg-sage' : 'bg-ink/20' }}"></span>
                    <h2 class="font-display text-base font-medium text-ink">QRIS</h2>
                </div>
                @if ($owner->qris_image)
                    <img src="{{ asset('storage/'.$owner->qris_image) }}" class="w-32 h-32 object-contain border border-line rounded-xl mb-3">
                @endif
                <input type="file" name="qris_image" accept="image/*" class="mt-1">
                <p class="text-xs text-ink/40 mt-1.5">Unggah gambar/screenshot QRIS statis kos Anda.</p>
            </div>

            <div class="pt-4 border-t border-line">
                <div class="flex items-center gap-2 mb-3">
                    <span class="w-1.5 h-1.5 rounded-full {{ $owner->terima_tunai ? 'bg-sage' : 'bg-ink/20' }}"></span>
                    <h2 class="font-display text-base font-medium text-ink">Tunai (Cash)</h2>
                </div>
                <label class="flex items-center gap-2.5 !mb-0 font-normal cursor-pointer">
                    <input type="checkbox" name="terima_tunai" value="1" @checked(old('terima_tunai', $owner->terima_tunai))>
                    <span class="text-sm text-ink/70">Terima pembayaran tunai langsung (serah terima tanpa transfer)</span>
                </label>
                <p class="text-xs text-ink/40 mt-1.5 ml-6">Jika aktif, Customer bisa menandai "Bayar Tunai" tanpa perlu upload bukti transfer -- Anda tetap perlu konfirmasi manual setelah uang diterima.</p>
            </div>

            <button class="btn-primary">Simpan Metode Pembayaran</button>
        </form>
    </x-card>

    <x-card>
        <h2 class="font-display text-base font-medium text-ink mb-3">Pratinjau untuk Customer</h2>
        <p class="text-xs text-ink/45 mb-4">Begini tampilan yang dilihat Customer saat akan membayar tagihan.</p>

        @if ($owner->bank_account_number)
            <div class="bg-linen rounded-xl p-3.5 text-sm border border-line mb-2.5">
                <p class="font-semibold text-ink">Transfer Bank</p>
                <p class="text-ink/70">{{ $owner->bank_name }} {{ $owner->bank_account_number }}</p>
                <p class="text-ink/45 text-xs">a.n. {{ $owner->bank_account_holder ?: '-' }}</p>
            </div>
        @endif

        @if ($owner->qris_image)
            <div class="bg-linen rounded-xl p-3.5 text-sm border border-line mb-2.5">
                <p class="font-semibold text-ink mb-2">QRIS</p>
                <img src="{{ asset('storage/'.$owner->qris_image) }}" class="w-24 h-24 object-contain border border-line rounded-lg">
            </div>
        @endif

        @if ($owner->terima_tunai)
            <div class="bg-linen rounded-xl p-3.5 text-sm border border-line mb-2.5">
                <p class="font-semibold text-ink">Tunai (Cash)</p>
                <p class="text-ink/60 text-xs mt-0.5">Bayar langsung ke Owner</p>
            </div>
        @endif

        @if (! $owner->punyaMetodePembayaran())
            <x-empty title="Belum ada metode aktif" subtitle="Isi salah satu form di samping agar Customer bisa membayar." />
        @endif
    </x-card>
</div>
@endsection
