<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Admin Console - MeBoX')</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <script src="https://unpkg.com/lucide@latest"></script>
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
    @stack('styles')
</head>
<body class="bg-admin-canvas font-admin text-admin-ink min-h-screen antialiased flex" x-data="{ sidebarOpen: true, toastShow: false, toastMessage: '', toastType: 'info' }" x-init="initToast()">

    <!-- Sidebar -->
    <aside class="bg-admin-surface border-r border-admin-border flex flex-col justify-between transition-all duration-300 z-30"
           :class="sidebarOpen ? 'w-[240px]' : 'w-[72px] lg:w-[72px]'">
        <div>
            <!-- Logo area -->
            <div class="h-[72px] px-6 border-b border-admin-border flex items-center gap-3 overflow-hidden">
                <div class="w-9 h-9 min-w-[36px] bg-admin-indigo rounded-admin-md flex items-center justify-center text-white font-bold text-lg">
                    M
                </div>
                <div class="transition-opacity duration-300" :class="sidebarOpen ? 'opacity-100' : 'opacity-0 w-0 overflow-hidden'">
                    <h1 class="font-bold text-sm leading-none text-admin-ink whitespace-nowrap">MeBoX</h1>
                    <p class="text-[10px] text-admin-slate mt-0.5 whitespace-nowrap">Admin Console</p>
                </div>
            </div>

            <!-- Navigation Links -->
            <nav class="p-3 space-y-1 overflow-y-auto max-h-[calc(100vh-200px)]">
                @php
                    $user = auth()->user();
                    $absensiNavItems = [];
                    $senseiNavItems = [];

                    if ($user->isSuperAdmin() || $user->isAdmin()) {
                        $absensiNavItems = [
                            ['route' => 'users.index',      'icon' => 'users',          'label' => 'User'],
                            ['route' => 'karyawan.index',   'icon' => 'briefcase',      'label' => 'Karyawan'],
                            ['route' => 'absensi.index',    'icon' => 'calendar',       'label' => 'Absensi'],
                            ['route' => 'rekap.absensi',    'icon' => 'table',          'label' => 'Rekap'],
                            ['route' => 'locations.index',  'icon' => 'map-pin',        'label' => 'Lokasi'],
                            ['route' => 'holidays.index',   'icon' => 'calendar-off',   'label' => 'Hari Libur'],
                            ['route' => 'akumulasi-jam',    'icon' => 'clock',          'label' => 'Jam Kerja'],
                            ['route' => 'report.index',     'icon' => 'file-spreadsheet','label' => 'Report'],
                            ['route' => 'settings.index',   'icon' => 'settings',       'label' => 'Setting'],
                        ];
                    } elseif ($user->isSensei()) {
                        $senseiNavItems = [
                            ['route' => 'dashboard',        'icon' => 'home',           'label' => 'Home'],
                            ['route' => 'jadwal.index',     'icon' => 'calendar',       'label' => 'Kelola Jadwal'],
                        ];
                    }
                @endphp

                @if($user->isSuperAdmin() || $user->isAdmin())
                    <!-- Home Link -->
                    @php
                        $isHomeActive = request()->routeIs('dashboard') || request()->is('/') || request()->is('dashboard');
                    @endphp
                    <a href="{{ route('dashboard') }}"
                       class="flex items-center gap-3 px-4 py-[10px] rounded-admin-md transition-all duration-150 {{ $isHomeActive ? 'bg-admin-indigo-tint text-admin-indigo font-semibold' : 'text-admin-slate hover:bg-admin-canvas hover:text-admin-ink' }}">
                        <i data-lucide="home" class="w-5 h-5 min-w-[20px]"></i>
                        <span class="text-sm transition-opacity duration-300" :class="sidebarOpen ? 'opacity-100' : 'opacity-0 w-0 overflow-hidden'">
                            Home
                        </span>
                    </a>

                    <!-- Dropdown Absensi -->
                    @php
                        $isAbsensiActive = request()->routeIs('users.*', 'absensi.*', 'rekap.*', 'locations.*', 'holidays.*', 'akumulasi-jam', 'report.*', 'settings.*');
                    @endphp
                    <div x-data="{ open: {{ $isAbsensiActive ? 'true' : 'false' }} }">
                        <button @click="open = !open"
                                class="w-full flex items-center justify-between px-4 py-[10px] rounded-admin-md transition-all duration-150 {{ $isAbsensiActive ? 'text-admin-indigo font-semibold bg-admin-indigo-tint/30' : 'text-admin-slate hover:bg-admin-canvas hover:text-admin-ink' }}">
                            <div class="flex items-center gap-3">
                                <i data-lucide="calendar" class="w-5 h-5 min-w-[20px]"></i>
                                <span class="text-sm transition-opacity duration-300" :class="sidebarOpen ? 'opacity-100' : 'opacity-0 w-0 overflow-hidden'">
                                    Absensi
                                </span>
                            </div>
                            <i data-lucide="chevron-down" class="w-4 h-4 transition-transform duration-200" :class="open ? 'transform rotate-180' : ''" x-show="sidebarOpen"></i>
                        </button>
                        
                        <div x-show="open" x-cloak x-collapse class="pl-4 mt-1 space-y-1" :class="sidebarOpen ? '' : 'hidden'">
                            @foreach($absensiNavItems as $item)
                                @php
                                    $isActive = request()->routeIs($item['route']) || request()->routeIs(explode('.', $item['route'])[0] . '.*');
                                @endphp
                                <a href="{{ route($item['route']) }}"
                                   class="flex items-center gap-3 px-4 py-[8px] rounded-admin-md transition-all duration-150 {{ $isActive ? 'bg-admin-indigo-tint text-admin-indigo font-semibold' : 'text-admin-slate hover:bg-admin-canvas hover:text-admin-ink' }}">
                                    <i data-lucide="{{ $item['icon'] }}" class="w-4 h-4 min-w-[16px]"></i>
                                    <span class="text-xs">
                                        {{ $item['label'] }}
                                    </span>
                                </a>
                            @endforeach
                        </div>
                    </div>

                    <!-- Dropdown Keuangan -->
                    @php
                        $isKeuanganActive = request()->routeIs('finance.*', 'payroll.slip', 'reports.print', 'dashboard.pdf');
                        $financeRoutes = [
                            ['route' => 'dashboard', 'params' => ['mode' => 'keuangan'], 'icon' => 'layout-dashboard', 'label' => 'Dasbor Keuangan'],
                            ['route' => 'finance.accounts.index', 'icon' => 'landmark', 'label' => 'Akun Rekening'],
                            ['route' => 'finance.categories.index', 'icon' => 'tag', 'label' => 'Kategori'],
                            ['route' => 'finance.salary-components.index', 'icon' => 'calculator', 'label' => 'Komponen Gaji'],
                            ['route' => 'finance.transactions.index', 'icon' => 'arrow-left-right', 'label' => 'Transaksi'],
                            ['route' => 'finance.fund-transfers.index', 'icon' => 'repeat', 'label' => 'Transfer Dana'],
                            ['route' => 'finance.payroll-periods.index', 'icon' => 'calendar', 'label' => 'Periode Gaji'],
                        ];
                    @endphp
                    <div x-data="{ open: {{ $isKeuanganActive ? 'true' : 'false' }} }">
                        <button @click="open = !open"
                                class="w-full flex items-center justify-between px-4 py-[10px] rounded-admin-md transition-all duration-150 {{ $isKeuanganActive ? 'text-admin-indigo font-semibold bg-admin-indigo-tint/30' : 'text-admin-slate hover:bg-admin-canvas hover:text-admin-ink' }}">
                            <div class="flex items-center gap-3">
                                <i data-lucide="wallet" class="w-5 h-5 min-w-[20px]"></i>
                                <span class="text-sm transition-opacity duration-300" :class="sidebarOpen ? 'opacity-100' : 'opacity-0 w-0 overflow-hidden'">
                                    Keuangan
                                </span>
                            </div>
                            <i data-lucide="chevron-down" class="w-4 h-4 transition-transform duration-200" :class="open ? 'transform rotate-180' : ''" x-show="sidebarOpen"></i>
                        </button>

                        <div x-show="open" x-cloak x-collapse class="pl-4 mt-1 space-y-1" :class="sidebarOpen ? '' : 'hidden'">
                            @foreach($financeRoutes as $item)
                                @php
                                    $routeParams = $item['params'] ?? [];
                                    $isActive = request()->routeIs($item['route']) || (isset($item['route']) && request()->routeIs($item['route'] . '.*'));
                                @endphp
                                <a href="{{ route($item['route'], $routeParams) }}"
                                   class="flex items-center gap-3 px-4 py-[8px] rounded-admin-md transition-all duration-150 {{ $isActive ? 'bg-admin-indigo-tint text-admin-indigo font-semibold' : 'text-admin-slate hover:bg-admin-canvas hover:text-admin-ink' }}">
                                    <i data-lucide="{{ $item['icon'] }}" class="w-4 h-4 min-w-[16px]"></i>
                                    <span class="text-xs">{{ $item['label'] }}</span>
                                </a>
                            @endforeach
                        </div>
                    </div>
                @elseif($user->isSensei())
                    @foreach($senseiNavItems as $item)
                        @php
                            $isActive = request()->routeIs($item['route']) || request()->routeIs(explode('.', $item['route'])[0] . '.*');
                        @endphp
                        <a href="{{ route($item['route']) }}"
                           class="flex items-center gap-3 px-4 py-[10px] rounded-admin-md transition-all duration-150 {{ $isActive ? 'bg-admin-indigo-tint text-admin-indigo font-semibold' : 'text-admin-slate hover:bg-admin-canvas hover:text-admin-ink' }}">
                            <i data-lucide="{{ $item['icon'] }}" class="w-5 h-5 min-w-[20px]"></i>
                            <span class="text-sm transition-opacity duration-300" :class="sidebarOpen ? 'opacity-100' : 'opacity-0 w-0 overflow-hidden'">
                                {{ $item['label'] }}
                            </span>
                        </a>
                    @endforeach
                @endif
        </div>

        <!-- User profile at bottom -->
        <div class="p-3 border-t border-admin-border">
            <div class="flex items-center gap-3 p-2 overflow-hidden">
                <img src="{{ auth()->user()->foto_url }}" class="w-9 h-9 min-w-[36px] rounded-admin-full object-cover" alt="">
                <div class="min-w-0 flex-1 transition-opacity duration-300" :class="sidebarOpen ? 'opacity-100' : 'opacity-0 w-0 overflow-hidden'">
                    <p class="text-xs font-semibold text-admin-ink truncate leading-tight">{{ auth()->user()->name }}</p>
                    <p class="text-[10px] text-admin-slate truncate">{{ auth()->user()->role_label }}</p>
                </div>
            </div>
            
            <form action="{{ route('logout') }}" method="POST" class="mt-2">
                @csrf
                <button type="submit" 
                        class="w-full flex items-center gap-3 px-4 py-2.5 rounded-admin-md text-admin-danger hover:bg-admin-danger-tint transition-all duration-150">
                    <i data-lucide="log-out" class="w-5 h-5 min-w-[20px]"></i>
                    <span class="text-sm font-medium transition-opacity duration-300" :class="sidebarOpen ? 'opacity-100' : 'opacity-0 w-0 overflow-hidden'">
                        Keluar
                    </span>
                </button>
            </form>
        </div>
    </aside>

    <!-- Main Content Area -->
    <div class="flex-1 flex flex-col min-w-0">
        <!-- Top Bar -->
        <header class="h-[72px] bg-admin-surface border-b border-admin-border flex items-center justify-between px-8 z-20">
            <div class="flex items-center gap-4">
                <button @click="sidebarOpen = !sidebarOpen" class="p-2 rounded-admin-md hover:bg-admin-canvas text-admin-slate hover:text-admin-ink transition-colors">
                    <i data-lucide="menu" class="w-5 h-5"></i>
                </button>
                <h2 class="text-lg font-semibold text-admin-ink">@yield('header', 'Admin Console')</h2>
            </div>
            
            <div class="flex items-center gap-4">
                <div class="text-right hidden sm:block">
                    <p class="text-xs text-admin-slate font-medium">{{ now()->locale('id')->isoFormat('dddd, D MMMM Y') }}</p>
                </div>
                <div class="h-6 w-px bg-admin-border"></div>
                <div class="flex items-center gap-2">
                    <span class="px-3 py-1 text-xs font-semibold rounded-admin-full bg-admin-indigo-tint text-admin-indigo">
                        {{ auth()->user()->role_label }}
                    </span>
                </div>
            </div>
        </header>

        <!-- Main Content -->
        <main class="flex-1 p-8 overflow-y-auto">
            @yield('content')
        </main>
    </div>

    <!-- Toast Notification (Floating Modal Layer - Has Shadow) -->
    <div x-show="toastShow" x-cloak
         x-transition:enter="transition ease-out duration-200"
         x-transition:enter-start="opacity-0 translate-y-2"
         x-transition:enter-end="opacity-100 translate-y-0"
         x-transition:leave="transition ease-in duration-150"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0"
         class="fixed top-6 right-6 z-50 max-w-sm w-full bg-admin-surface border border-admin-border rounded-admin-md p-4 shadow-admin-float flex items-start gap-3">
        
        <div class="p-1 rounded-admin-full"
             :class="toastType === 'success' ? 'bg-admin-success/10 text-admin-success' : toastType === 'error' ? 'bg-admin-danger/10 text-admin-danger' : 'bg-admin-indigo/10 text-admin-indigo'">
            <i :data-lucide="toastType === 'success' ? 'check-circle' : toastType === 'error' ? 'alert-triangle' : 'info'" class="w-5 h-5"></i>
        </div>
        <div class="flex-1 min-w-0">
            <p class="text-xs font-semibold text-admin-ink" x-text="toastType.toUpperCase()"></p>
            <p class="text-xs text-admin-slate mt-0.5" x-text="toastMessage"></p>
        </div>
        <button @click="toastShow = false" class="text-admin-mist hover:text-admin-slate transition-colors">
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
