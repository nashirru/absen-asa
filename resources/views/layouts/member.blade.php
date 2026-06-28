<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no, viewport-fit=cover">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'LPK Asa Hikari Mulya')</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <script src="https://unpkg.com/lucide@latest"></script>
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
    @stack('styles')
</head>
<body class="bg-member-canvas font-member text-member-ink min-h-dvh flex justify-center antialiased" x-data="{ toastShow: false, toastMessage: '', toastType: 'info' }" x-init="initToast()">

    <!-- Fixed Mobile Container (Max Width 480px, Centered) -->
    <div class="w-full max-w-[480px] min-h-dvh bg-member-canvas flex flex-col relative shadow-xl shadow-gray-200/50">
        
        <!-- Header Bar -->
        @auth
        <header class="h-14 bg-member-surface px-4 flex items-center justify-between sticky top-0 z-40 border-b border-member-border/30">
            <div class="flex items-center gap-2">
                <span class="font-bold text-base tracking-tight text-member-ink flex items-center gap-1">
                    LPK Asa Hikari
                    <i data-lucide="chevron-right" class="w-4 h-4 text-member-slate"></i>
                </span>
            </div>

            <div class="flex items-center gap-3">
                <span class="text-xs font-semibold px-2.5 py-1 rounded-member-full bg-member-blue-tint text-member-blue">
                    {{ auth()->user()->role_label }}
                </span>
                <form action="{{ route('logout') }}" method="POST" class="flex items-center">
                    @csrf
                    <button type="submit" class="text-member-slate hover:text-status-alpha transition-colors p-1 flex items-center justify-center" title="Keluar">
                        <i data-lucide="log-out" class="w-4 h-4"></i>
                    </button>
                </form>
            </div>
        </header>
        @endauth

        <!-- Main Scrollable Content -->
        <main class="flex-1 px-4 py-6" style="padding-bottom: calc(7rem + env(safe-area-inset-bottom, 0px))">
            @yield('content')
        </main>

        <!-- Bottom Navigation Bar -->
        @auth
        @php $role = auth()->user()->role; @endphp
        <nav class="fixed bottom-0 left-0 right-0 lg:left-auto lg:right-auto lg:w-[480px] bg-member-surface border-t border-member-border/40 z-40 flex items-center justify-around ios-bottom-nav">
            @if($role === 'siswa')
                <x-bottom-nav-item route="dashboard" icon="home" label="Home" />
                <x-bottom-nav-item route="jadwal.my-schedule" icon="calendar" label="Jadwal" />
                <x-bottom-nav-item route="absensi.check-in" icon="check-square" label="Absensi" />
                <x-bottom-nav-item route="absensi.riwayat" icon="history" label="Riwayat" />
                <x-bottom-nav-item route="profile.index" icon="user" label="Profile" />
            @elseif($role === 'karyawan')
                <x-bottom-nav-item route="dashboard" icon="home" label="Home" />
                <x-bottom-nav-item route="absensi.check-in" icon="check-square" label="Absensi" />
                <x-bottom-nav-item route="absensi.riwayat" icon="history" label="Riwayat" />
                <x-bottom-nav-item route="jadwal.my-schedule" icon="calendar" label="Jadwal" />
                <x-bottom-nav-item route="profile.index" icon="user" label="Profile" />
            @elseif($role === 'sensei')
                <x-bottom-nav-item route="dashboard" icon="home" label="Home" />
                <x-bottom-nav-item route="jadwal.my-schedule" icon="calendar" label="Jadwal" />
                <x-bottom-nav-item route="kelas.saya" icon="clipboard" label="Kelas" />
                <x-bottom-nav-item route="absensi.riwayat" icon="history" label="Riwayat" />
                <x-bottom-nav-item route="profile.index" icon="user" label="Profile" />
            @endif
        </nav>
        @endauth
    </div>

    <!-- Toast Notification (Floating Layer - Has Shadow) -->
    <div x-show="toastShow" x-cloak
         x-transition:enter="transition ease-out duration-200"
         x-transition:enter-start="opacity-0 translate-y-2"
         x-transition:enter-end="opacity-100 translate-y-0"
         x-transition:leave="transition ease-in duration-150"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0"
         class="fixed bottom-20 left-4 right-4 z-50 bg-member-surface rounded-member-xl p-4 shadow-member-card flex items-start gap-3 border border-member-border/20 max-w-[448px] mx-auto">
        
        <div class="p-1 rounded-member-full"
             :class="toastType === 'success' ? 'bg-status-hadir/10 text-status-hadir' : toastType === 'error' ? 'bg-status-alpha/10 text-status-alpha' : 'bg-member-blue/10 text-member-blue'">
            <i :data-lucide="toastType === 'success' ? 'check-circle' : toastType === 'error' ? 'alert-triangle' : 'info'" class="w-5 h-5"></i>
        </div>
        <div class="flex-1 min-w-0">
            <p class="text-xs font-bold text-member-ink" x-text="toastType.toUpperCase()"></p>
            <p class="text-xs text-member-slate mt-0.5" x-text="toastMessage"></p>
        </div>
        <button @click="toastShow = false" class="text-member-mist hover:text-member-slate transition-colors">
            <i data-lucide="x" class="w-4 h-4"></i>
        </button>
    </div>

    <!-- Session Flash Messages -->
    @if(session('success'))
        <div x-data x-init="setTimeout(() => { $dispatch('toast', { message: @json(session('success')), type: 'success' }) }, 100)"></div>
    @endif
    @if(session('error'))
        <div x-data x-init="setTimeout(() => { $dispatch('toast', { message: @json(session('error')), type: 'error' }) }, 100)"></div>
    @endif

    @stack('scripts')

    <script>
        document.addEventListener('DOMContentLoaded', () => {
            lucide.createIcons();
        });

        function initToast() {
            window.addEventListener('toast', (e) => {
                const alpine = document.body.__x;
                alpine.$data.toastMessage = e.detail.message;
                alpine.$data.toastType = e.detail.type || 'info';
                alpine.$data.toastShow = true;
                setTimeout(() => {
                    lucide.createIcons();
                }, 10);
                setTimeout(() => {
                    alpine.$data.toastShow = false;
                }, 4000);
            });
        }
    </script>
</body>
</html>
