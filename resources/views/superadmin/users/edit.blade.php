@extends('layouts.app')
@section('title', 'Edit Akun')
@section('content')
<h1 class="font-display text-2xl font-medium text-ink mb-6">Edit Akun &middot; {{ $userAccount->name }}</h1>

<div class="grid lg:grid-cols-3 gap-6">
    <x-card class="lg:col-span-2">
        <form method="POST" action="{{ route('superadmin.users.update', $userAccount) }}" class="space-y-4">
            @csrf @method('PUT')
            <div>
                <label>Role</label>
                <select name="role" required class="mt-1" onchange="toggleRoleFields(this.value)">
                    @foreach (['superadmin'=>'Superadmin','owner'=>'Owner','penjaga'=>'Penjaga Kos','customer'=>'Customer'] as $val=>$label)
                        <option value="{{ $val }}" @selected(old('role', $userAccount->role) === $val)>{{ $label }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label>Nama Lengkap</label>
                <input placeholder="Contoh: Budi Santoso" type="text" name="name" value="{{ old('name', $userAccount->name) }}" required class="mt-1">
            </div>
            <div>
                <label>Email</label>
                <input placeholder="nama@email.com" type="email" name="email" value="{{ old('email', $userAccount->email) }}" required class="mt-1">
            </div>
            <div>
                <label>Nomor WhatsApp</label>
                <input id="phone-field" placeholder="08xxxxxxxxxx" type="text" name="phone" value="{{ old('phone', $userAccount->phone) }}" class="mt-1"
                    {{ in_array(old('role', $userAccount->role), ['owner','penjaga']) ? 'required' : '' }}>
                <p id="phone-hint" class="text-xs text-brass mt-1.5" style="display: {{ in_array(old('role', $userAccount->role), ['owner','penjaga']) ? 'block' : 'none' }}">
                    Wajib diisi untuk Owner/Penjaga -- dipakai mengirim link WhatsApp pengingat pembayaran ke Customer.
                </p>
            </div>
            <div>
                <label>Password Baru (kosongkan jika tidak diubah)</label>
                <input placeholder="Minimal 8 karakter" type="password" name="password" minlength="8" class="mt-1">
            </div>

            @if ($kosList->isEmpty())
                <div id="kos-section" class="pt-3 border-t border-line" style="display: {{ old('role', $userAccount->role)==='owner' ? 'block' : 'none' }}">
                    <label class="!mb-0.5">Tambah Kos Pertama (opsional)</label>
                    <p class="text-xs text-ink/45 mb-3">Owner ini belum punya kos. Isi di sini agar ia langsung punya kos yang siap dikelola, atau biarkan kosong dan Owner bisa menambahkan sendiri.</p>
                    <div class="space-y-3">
                        <div>
                            <label class="text-xs font-medium text-ink/60">Nama Kos</label>
                            <input placeholder="Contoh: Kos Melati Asri" type="text" name="nama_kos" value="{{ old('nama_kos') }}" class="mt-1">
                        </div>
                        <div>
                            <label class="text-xs font-medium text-ink/60">Alamat Kos</label>
                            <textarea placeholder="Contoh: Jl. Melati No. 10, Yogyakarta" name="alamat_kos" class="mt-1">{{ old('alamat_kos') }}</textarea>
                        </div>
                    </div>
                </div>
            @endif

            <button class="btn-primary">Simpan Perubahan</button>
        </form>
    </x-card>

    <div class="space-y-4">
        @if ($userAccount->role === 'owner')
            <x-card>
                <h2 class="font-display text-base font-medium text-ink mb-3">Kos Milik Owner Ini</h2>
                @forelse ($kosList as $kos)
                    <div class="border-b border-line/70 last:border-0 py-2.5">
                        <p class="font-semibold text-ink text-sm">{{ $kos->nama }}</p>
                        <p class="text-xs text-ink/40 mt-0.5">{{ $kos->alamat }}</p>
                        <p class="text-xs text-ink/35 mt-0.5">{{ $kos->kamars_count }} kamar &middot; #{{ $kos->id }}</p>
                    </div>
                @empty
                    <p class="text-sm text-brass font-medium">Belum punya kos.</p>
                    <p class="text-xs text-ink/40 mt-1">Isi form "Tambah Kos Pertama" di sebelah kiri, atau Owner bisa membuat sendiri setelah login.</p>
                @endforelse
            </x-card>
        @endif
    </div>
</div>

<script>
    function toggleRoleFields(role) {
        const isOwnerOrPenjaga = role === 'owner' || role === 'penjaga';
        document.getElementById('phone-hint').style.display = isOwnerOrPenjaga ? 'block' : 'none';
        document.getElementById('phone-field').required = isOwnerOrPenjaga;
        const kosSection = document.getElementById('kos-section');
        if (kosSection) kosSection.style.display = role === 'owner' ? 'block' : 'none';
    }
</script>
@endsection
