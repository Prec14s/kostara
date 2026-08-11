@extends('layouts.app')
@section('title', 'Tambah Akun')
@section('content')
<h1 class="font-display text-2xl font-medium text-ink mb-1">Tambah Akun Baru</h1>
<p class="text-sm text-ink/45 mb-6">Superadmin dapat membuat akun untuk seluruh role, termasuk Superadmin lain.</p>

<x-card class="max-w-lg">
    <form method="POST" action="{{ route('superadmin.users.store') }}" class="space-y-4">
        @csrf
        <div>
            <label>Role</label>
            <select name="role" required class="mt-1" onchange="toggleRoleFields(this.value)">
                <option value="superadmin" @selected(old('role')==='superadmin')>Superadmin</option>
                <option value="owner" @selected(old('role')==='owner')>Owner</option>
                <option value="penjaga" @selected(old('role')==='penjaga')>Penjaga Kos</option>
                <option value="customer" @selected(old('role','customer')==='customer')>Customer</option>
            </select>
        </div>
        <div>
            <label>Nama Lengkap</label>
            <input placeholder="Contoh: Budi Santoso" type="text" name="name" value="{{ old('name') }}" required class="mt-1">
        </div>
        <div>
            <label>Email</label>
            <input placeholder="nama@email.com" type="email" name="email" value="{{ old('email') }}" required class="mt-1">
        </div>
        <div>
            <label>Nomor WhatsApp</label>
            <input id="phone-field" placeholder="08xxxxxxxxxx" type="text" name="phone" value="{{ old('phone') }}" class="mt-1"
                {{ in_array(old('role'), ['owner','penjaga']) ? 'required' : '' }}>
            <p id="phone-hint" class="text-xs text-brass mt-1.5" style="display: {{ in_array(old('role'), ['owner','penjaga']) ? 'block' : 'none' }}">
                Wajib diisi untuk Owner/Penjaga -- dipakai mengirim link WhatsApp pengingat pembayaran ke Customer.
            </p>
        </div>
        <div>
            <label>Password Awal</label>
            <input placeholder="Minimal 8 karakter" type="password" name="password" required minlength="8" class="mt-1">
        </div>

        <div id="kos-section" class="pt-3 border-t border-line" style="display: {{ old('role')==='owner' ? 'block' : 'none' }}">
            <label class="!mb-0.5">Kos Pertama (opsional)</label>
            <p class="text-xs text-ink/45 mb-3">Isi sekarang agar Owner langsung punya kos yang siap dikelola (edit & tambah kamar) begitu login pertama kali. Bisa juga dikosongkan -- Owner bisa membuat kos sendiri nanti lewat menu "Kos Saya".</p>
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

        <button class="btn-primary">Buat Akun</button>
    </form>
</x-card>

<script>
    function toggleRoleFields(role) {
        const isOwnerOrPenjaga = role === 'owner' || role === 'penjaga';
        document.getElementById('phone-hint').style.display = isOwnerOrPenjaga ? 'block' : 'none';
        document.getElementById('phone-field').required = isOwnerOrPenjaga;
        document.getElementById('kos-section').style.display = role === 'owner' ? 'block' : 'none';
    }
</script>
@endsection
