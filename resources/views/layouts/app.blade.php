<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Kostara')</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Fraunces:opsz,wght@9..144,400;9..144,500;9..144,600;9..144,700&family=Plus+Jakarta+Sans:wght@400;500;600;700;800&family=JetBrains+Mono:wght@400;500;600&display=swap" rel="stylesheet">
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        ink: '#16323A',
                        teal: { DEFAULT: '#1F4E5C', dark: '#123640', 50: '#EAF2F4' },
                        linen: '#F6F4EE',
                        line: '#E6E1D3',
                        brass: { DEFAULT: '#B8863B', dark: '#96692A', 50: '#FBF3E3' },
                        sage: { DEFAULT: '#6B8F71', 50: '#EEF3EC' },
                        clay: { DEFAULT: '#B6533C', 50: '#FBEEEA' },
                    },
                    fontFamily: {
                        display: ['"Fraunces"', 'serif'],
                        sans: ['"Plus Jakarta Sans"', 'sans-serif'],
                        mono: ['"JetBrains Mono"', 'monospace'],
                    },
                    boxShadow: {
                        soft: '0 1px 2px rgba(22,50,58,0.06), 0 8px 24px -12px rgba(22,50,58,0.12)',
                        lift: '0 4px 8px rgba(22,50,58,0.08), 0 16px 32px -12px rgba(22,50,58,0.18)',
                    },
                    borderRadius: { '2xl': '1.1rem', '3xl': '1.6rem' },
                },
            },
        };
    </script>
    <style type="text/tailwindcss">
        @layer base {
            body { @apply font-sans text-ink bg-linen antialiased; }
            h1, h2, h3 { @apply font-display; }
            input[type=text], input[type=email], input[type=password], input[type=number],
            input[type=date], input[type=search], textarea, select {
                @apply w-full rounded-xl border border-line bg-white px-3.5 py-2.5 text-sm text-ink
                       placeholder:text-ink/35 shadow-sm transition-all duration-150
                       focus:border-brass focus:ring-4 focus:ring-brass/15 focus:outline-none;
            }
            input[type=file] {
                @apply w-full text-sm text-ink/60 rounded-xl border border-dashed border-line bg-white/60 px-3 py-2.5
                       file:mr-3 file:rounded-lg file:border-0 file:bg-teal-50 file:px-3 file:py-1.5
                       file:text-xs file:font-semibold file:text-teal file:cursor-pointer
                       hover:border-brass/50 transition-colors cursor-pointer;
            }
            input[type=checkbox] { @apply rounded border-line text-teal focus:ring-brass/30; }
            input[type=radio] { @apply border-line text-brass focus:ring-brass/30; }
            label { @apply block text-sm font-semibold text-ink/70 mb-1.5; }
            a { @apply transition-colors duration-150; }
            table { @apply w-full; }
        }
        @layer components {
            .btn { @apply inline-flex items-center justify-center gap-1.5 rounded-xl px-4 py-2.5 text-sm font-semibold
                    shadow-sm transition-all duration-150 active:scale-[0.97] disabled:opacity-50 disabled:pointer-events-none; }
            .btn-primary { @apply btn bg-teal text-white hover:bg-teal-dark hover:shadow-lift; }
            .btn-accent { @apply btn bg-brass text-white hover:bg-brass-dark hover:shadow-lift; }
            .btn-ghost { @apply btn bg-white text-ink border border-line hover:border-teal/40 hover:bg-teal-50/60; }
            .btn-danger { @apply btn bg-white text-clay border border-clay/30 hover:bg-clay/5; }
            .surface { @apply bg-white rounded-2xl border border-line/80 shadow-soft; }
            .link-accent { @apply text-teal font-semibold hover:text-brass underline decoration-2 underline-offset-4 decoration-brass/0 hover:decoration-brass/60; }
            .chip { @apply px-2.5 py-1 rounded-full text-xs font-semibold tracking-wide; }
            .row-hover { @apply transition-colors hover:bg-linen/70; }

            /* Sidebar nav links */
            .side-link {
                @apply flex items-center gap-3 px-3.5 py-2.5 rounded-xl text-sm font-medium text-white/55
                       hover:text-white hover:bg-white/5 transition-all duration-150 relative;
            }
            .side-link.is-active {
                @apply text-white bg-white/10;
            }
            .side-link.is-active::before {
                content: '';
                @apply absolute left-0 top-1/2 -translate-y-1/2 -translate-x-3.5 h-5 w-1 rounded-r-full bg-brass;
            }
        }
    </style>
</head>
<body class="min-h-screen bg-linen">

@auth
    @php
        $user = auth()->user();
        // Helper kecil: cek apakah salah satu pola nama route sedang aktif, untuk menandai menu di sidebar.
        $isActive = function (...$patterns) {
            return request()->routeIs($patterns) ? 'is-active' : '';
        };
    @endphp

    <!-- Topbar mobile -->
    <div class="lg:hidden sticky top-0 z-40 bg-ink flex items-center justify-between px-4 h-14 shadow-md">
        <button onclick="document.getElementById('sidebar').classList.remove('-translate-x-full'); document.getElementById('sidebar-backdrop').classList.remove('hidden');"
                class="text-white/80 hover:text-white p-1 -ml-1">
            <x-icon name="menu" />
        </button>
        <a href="{{ route('dashboard') }}" class="flex items-center gap-2 text-white font-display font-semibold">
            <span class="w-7 h-7 rounded-lg bg-brass/90 flex items-center justify-center text-ink text-xs font-sans font-bold">K</span>
            Kostara
        </a>
        <span class="w-8 h-8 rounded-full bg-brass/20 border border-brass/40 flex items-center justify-center text-brass text-xs font-bold uppercase">
            {{ substr($user->name, 0, 1) }}
        </span>
    </div>

    <!-- Backdrop (mobile only) -->
    <div id="sidebar-backdrop" onclick="document.getElementById('sidebar').classList.add('-translate-x-full'); this.classList.add('hidden');"
         class="hidden fixed inset-0 bg-ink/50 z-40 lg:hidden"></div>

    <!-- Sidebar -->
    <aside id="sidebar" class="fixed top-0 left-0 h-screen w-72 bg-ink z-50 flex flex-col
                                -translate-x-full lg:translate-x-0 transition-transform duration-300 ease-out">
        <div class="flex items-center justify-between px-5 h-16 shrink-0">
            <a href="{{ route('dashboard') }}" class="flex items-center gap-2 text-white font-display font-semibold text-lg">
                <span class="w-8 h-8 rounded-lg bg-brass/90 flex items-center justify-center text-ink text-sm font-sans font-bold">K</span>
                Kostara
            </a>
            <button onclick="document.getElementById('sidebar').classList.add('-translate-x-full'); document.getElementById('sidebar-backdrop').classList.add('hidden');"
                    class="lg:hidden text-white/50 hover:text-white">
                <x-icon name="close" class="w-5 h-5" />
            </button>
        </div>

        <nav class="flex-1 overflow-y-auto px-3.5 py-3 space-y-1">
            @if ($user->isSuperadmin())
                <a href="{{ route('dashboard') }}" class="side-link {{ $isActive('dashboard') }}"><x-icon name="dashboard" />Dashboard</a>
                <a href="{{ route('superadmin.users.index') }}" class="side-link {{ $isActive('superadmin.users.*') }}"><x-icon name="users" />Manajemen Akun</a>
                <a href="{{ route('superadmin.kos.index') }}" class="side-link {{ $isActive('superadmin.kos.*') }}"><x-icon name="home" />Semua Kos</a>
            @endif

            @if ($user->isOwner())
                <a href="{{ route('dashboard') }}" class="side-link {{ $isActive('dashboard') }}"><x-icon name="dashboard" />Dashboard</a>
                <a href="{{ route('owner.kos.index') }}" class="side-link {{ $isActive('owner.kos.*') }}"><x-icon name="home" />Kos Saya</a>
                <a href="{{ route('booking.index') }}" class="side-link {{ $isActive('booking.*') }}"><x-icon name="calendar" />Booking</a>
                <a href="{{ route('sewa.index') }}" class="side-link {{ $isActive('sewa.*') }}"><x-icon name="file" />Sewa</a>

                @php $pembayaranAktif = request()->routeIs('owner.pembayaran.*', 'owner.payment-settings.*'); @endphp
                <div>
                    <button type="button"
                            onclick="this.nextElementSibling.classList.toggle('hidden'); this.querySelector('.chevron').classList.toggle('rotate-90')"
                            class="side-link w-full justify-between {{ $pembayaranAktif ? 'is-active' : '' }}">
                        <span class="flex items-center gap-3"><x-icon name="wallet" />Pembayaran</span>
                        <svg class="chevron w-3.5 h-3.5 shrink-0 transition-transform duration-200 {{ $pembayaranAktif ? 'rotate-90' : '' }}"
                             viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M9 6l6 6-6 6"/>
                        </svg>
                    </button>
                    <div class="{{ $pembayaranAktif ? '' : 'hidden' }} pl-8 mt-1 space-y-1">
                        <a href="{{ route('owner.pembayaran.index') }}" class="side-link !py-2 text-[13px] {{ $isActive('owner.pembayaran.*') }}">Validasi Pembayaran</a>
                        <a href="{{ route('owner.payment-settings.edit') }}" class="side-link !py-2 text-[13px] {{ $isActive('owner.payment-settings.*') }}">Metode Pembayaran</a>
                    </div>
                </div>

                <a href="{{ route('maintenance.index') }}" class="side-link {{ $isActive('maintenance.*') }}"><x-icon name="wrench" />Maintenance</a>
                <a href="{{ route('tasks.index') }}" class="side-link {{ $isActive('tasks.*', 'owner.tasks.*') }}"><x-icon name="check" />Tugas</a>
                <a href="{{ route('announcements.index') }}" class="side-link {{ $isActive('announcements.*', 'owner.announcements.*') }}"><x-icon name="megaphone" />Pengumuman</a>
                <a href="{{ route('owner.laporan.index') }}" class="side-link {{ $isActive('owner.laporan.*') }}"><x-icon name="chart" />Laporan</a>
            @endif

            @if ($user->isPenjaga())
                <a href="{{ route('dashboard') }}" class="side-link {{ $isActive('dashboard') }}"><x-icon name="dashboard" />Dashboard</a>
                <a href="{{ route('booking.index') }}" class="side-link {{ $isActive('booking.*') }}"><x-icon name="calendar" />Booking</a>
                <a href="{{ route('sewa.index') }}" class="side-link {{ $isActive('sewa.*') }}"><x-icon name="file" />Jatuh Tempo</a>
                <a href="{{ route('maintenance.index') }}" class="side-link {{ $isActive('maintenance.*') }}"><x-icon name="wrench" />Maintenance</a>
                <a href="{{ route('tasks.index') }}" class="side-link {{ $isActive('tasks.*') }}"><x-icon name="check" />Tugas</a>
            @endif

            @if ($user->isCustomer())
                <a href="{{ route('dashboard') }}" class="side-link {{ $isActive('dashboard') }}"><x-icon name="dashboard" />Dashboard</a>
                <a href="{{ route('landing') }}" class="side-link {{ $isActive('landing', 'guest.kos.*') }}"><x-icon name="search" />Kamar Kosong</a>
                <a href="{{ route('customer.sewa.index') }}" class="side-link {{ $isActive('customer.sewa.*') }}"><x-icon name="file" />Sewa Saya</a>
                <a href="{{ route('customer.pembayaran.index') }}" class="side-link {{ $isActive('customer.pembayaran.*') }}"><x-icon name="wallet" />Pembayaran</a>
                <a href="{{ route('maintenance.create') }}" class="side-link {{ $isActive('maintenance.create', 'maintenance.store', 'customer.maintenance.*') }}"><x-icon name="wrench" />Maintenance</a>
                <a href="{{ route('announcements.index') }}" class="side-link {{ $isActive('announcements.index') }}"><x-icon name="megaphone" />Pengumuman</a>
            @endif
        </nav>

        <div class="px-3.5 py-4 border-t border-white/10 shrink-0">
            <a href="{{ route('profile.edit') }}" class="side-link {{ $isActive('profile.edit') }} !mb-1">
                <span class="w-6 h-6 rounded-full bg-brass/20 border border-brass/40 flex items-center justify-center text-brass text-[10px] font-bold uppercase shrink-0">
                    {{ substr($user->name, 0, 1) }}
                </span>
                <span class="truncate">{{ $user->name }}</span>
            </a>
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button class="side-link w-full text-white/40 hover:text-clay hover:bg-clay/10"><x-icon name="logout" />Keluar</button>
            </form>
        </div>
    </aside>
@endauth

<div class="{{ auth()->check() ? 'lg:pl-72' : '' }}">
    <main class="max-w-6xl mx-auto w-full px-4 sm:px-6 py-6 sm:py-8">
        @if (session('status'))
            <div class="mb-5 flex items-center gap-2 rounded-xl bg-sage-50 border border-sage/30 text-sage px-4 py-3 text-sm font-medium">
                <span class="w-1.5 h-1.5 rounded-full bg-sage shrink-0"></span> {{ session('status') }}
            </div>
        @endif
        @if (session('error'))
            <div class="mb-5 flex items-center gap-2 rounded-xl bg-clay-50 border border-clay/30 text-clay px-4 py-3 text-sm font-medium">
                <span class="w-1.5 h-1.5 rounded-full bg-clay shrink-0"></span> {{ session('error') }}
            </div>
        @endif
        @if ($errors->any())
            <div class="mb-5 rounded-xl bg-clay-50 border border-clay/30 text-clay px-4 py-3 text-sm">
                <ul class="list-disc list-inside space-y-0.5">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        @yield('content')
    </main>

    <footer class="text-center text-xs text-ink/40 py-6 border-t border-line mt-8">Kostara &copy; {{ date('Y') }} &middot; Kelola kos, secepat kirim WhatsApp.</footer>
</div>

<script>
    // Format input harga jadi "100.000" selagi diketik (Modul Kamar).
    function formatRupiah(el) {
        const cursorFromEnd = el.value.length - el.selectionStart;
        const raw = el.value.replace(/\D/g, '');
        el.value = raw === '' ? '' : raw.replace(/\B(?=(\d{3})+(?!\d))/g, '.');
        const pos = Math.max(el.value.length - cursorFromEnd, 0);
        el.setSelectionRange(pos, pos);
    }

    // Sebelum form dikirim, kembalikan field .rupiah-input ke angka polos (tanpa titik) agar tervalidasi benar di server.
    document.addEventListener('submit', function (e) {
        e.target.querySelectorAll('.rupiah-input').forEach(function (el) {
            el.value = el.value.replace(/\./g, '');
        });
    }, true);
</script>

</body>
</html>
