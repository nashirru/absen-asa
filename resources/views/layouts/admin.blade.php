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
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
    @stack('styles')
</head>
<body class="bg-admin-canvas font-admin text-admin-ink min-h-screen antialiased flex" x-data="{ sidebarOpen: true }">

    <!-- Sidebar - normal flow, full height ikut konten -->
    <aside class="bg-admin-surface border-r border-admin-border flex flex-col transition-all duration-300 z-30 shrink-0"
           :class="sidebarOpen ? 'w-[240px]' : 'w-[72px]'">

        <!-- Logo area -->
        <div class="h-[72px] min-h-[72px] px-6 border-b border-admin-border flex items-center gap-3 shrink-0">
            <div class="w-9 h-9 min-w-[36px] bg-admin-indigo rounded-admin-md flex items-center justify-center text-white font-bold text-lg shrink-0">
                M
            </div>
            <div class="transition-opacity duration-300 truncate" :class="sidebarOpen ? 'opacity-100' : 'opacity-0 w-0 overflow-hidden'">
                <h1 class="font-bold text-sm leading-none text-admin-ink whitespace-nowrap">MeBoX</h1>
                <p class="text-[10px] text-admin-slate mt-0.5 whitespace-nowrap">Admin Console</p>
            </div>
        </div>

        @php
            $user = auth()->user();
            $isSuperAdmin = $user->isSuperAdmin() || $user->isAdmin();
        @endphp

        <!-- Navigation -->
        <nav class="flex-1 p-3 space-y-0.5">
            @if($isSuperAdmin)
                {{-- HOME --}}
                @php $isHome = request()->routeIs('dashboard') && !request('mode'); @endphp
                <a href="{{ route('dashboard') }}"
                   class="flex items-center gap-3 px-4 py-[10px] rounded-admin-md transition-all duration-150 {{ $isHome ? 'bg-admin-indigo-tint text-admin-indigo font-semibold' : 'text-admin-slate hover:bg-admin-canvas hover:text-admin-ink' }}">
                    <i data-lucide="home" class="w-5 h-5 min-w-[20px] shrink-0"></i>
                    <span class="text-sm transition-opacity duration-300 truncate" :class="sidebarOpen ? 'opacity-100' : 'opacity-0 w-0 overflow-hidden'">Home</span>
                </a>

                {{-- MASTER DATA --}}
                @php
                    $isMasterActive = request()->routeIs('karyawan.*') || request()->routeIs('siswa.*') || request()->routeIs('sensei.*') || request()->routeIs('kelas.*');
                    $masterItems = [
                        ['route' => 'karyawan.index', 'icon' => 'briefcase', 'label' => 'Karyawan'],
                        ['route' => 'siswa.index',    'icon' => 'graduation-cap', 'label' => 'Siswa'],
                        ['route' => 'sensei.index',   'icon' => 'award', 'label' => 'Sensei'],
                    ];
                @endphp
                <div x-data="{ open: {{ $isMasterActive ? 'true' : 'false' }} }">
                    <button @click="open = !open"
                            class="w-full flex items-center justify-between px-4 py-[10px] rounded-admin-md transition-all duration-150 {{ $isMasterActive ? 'text-admin-indigo font-semibold bg-admin-indigo-tint/30' : 'text-admin-slate hover:bg-admin-canvas hover:text-admin-ink' }}">
                        <div class="flex items-center gap-3 min-w-0">
                            <i data-lucide="database" class="w-5 h-5 min-w-[20px] shrink-0"></i>
                            <span class="text-sm transition-opacity duration-300 truncate" :class="sidebarOpen ? 'opacity-100' : 'opacity-0 w-0 overflow-hidden'">Master Data</span>
                        </div>
                        <i data-lucide="chevron-down" class="w-4 h-4 shrink-0 transition-transform duration-200" :class="open ? 'rotate-180' : ''" x-show="sidebarOpen"></i>
                    </button>
                    <div x-show="open && sidebarOpen" x-collapse class="pl-4 mt-1 space-y-1">
                        @foreach($masterItems as $item)
                            @php $isActive = request()->routeIs($item['route']) || request()->routeIs($item['route'] . '.*'); @endphp
                            <a href="{{ route($item['route']) }}"
                               class="flex items-center gap-3 px-4 py-2 rounded-admin-md transition-all duration-150 {{ $isActive ? 'bg-admin-indigo-tint text-admin-indigo font-semibold' : 'text-admin-slate hover:bg-admin-canvas hover:text-admin-ink' }}">
                                <i data-lucide="{{ $item['icon'] }}" class="w-4 h-4 min-w-[16px] shrink-0"></i>
                                <span class="text-sm truncate">{{ $item['label'] }}</span>
                            </a>
                        @endforeach
                    </div>
                </div>

                {{-- ABSENSI & JADWAL --}}
                @php
                    $isAbsensiActive = request()->routeIs('absensi.*') || request()->routeIs('locations.*') || request()->routeIs('holidays.*') || request()->routeIs('akumulasi-jam');
                    $absensiItems = [
                        ['route' => 'absensi.index',   'icon' => 'calendar-check', 'label' => 'Absensi'],
                        ['route' => 'locations.index', 'icon' => 'map-pin',       'label' => 'Lokasi'],
                        ['route' => 'holidays.index',  'icon' => 'calendar-off',  'label' => 'Hari Libur'],
                        ['route' => 'akumulasi-jam',   'icon' => 'clock',         'label' => 'Jam Kerja'],
                    ];
                @endphp
                <div x-data="{ open: {{ $isAbsensiActive ? 'true' : 'false' }} }">
                    <button @click="open = !open"
                            class="w-full flex items-center justify-between px-4 py-[10px] rounded-admin-md transition-all duration-150 {{ $isAbsensiActive ? 'text-admin-indigo font-semibold bg-admin-indigo-tint/30' : 'text-admin-slate hover:bg-admin-canvas hover:text-admin-ink' }}">
                        <div class="flex items-center gap-3 min-w-0">
                            <i data-lucide="calendar" class="w-5 h-5 min-w-[20px] shrink-0"></i>
                            <span class="text-sm transition-opacity duration-300 truncate" :class="sidebarOpen ? 'opacity-100' : 'opacity-0 w-0 overflow-hidden'">Absensi & Jadwal</span>
                        </div>
                        <i data-lucide="chevron-down" class="w-4 h-4 shrink-0 transition-transform duration-200" :class="open ? 'rotate-180' : ''" x-show="sidebarOpen"></i>
                    </button>
                    <div x-show="open && sidebarOpen" x-collapse class="pl-4 mt-1 space-y-1">
                        @foreach($absensiItems as $item)
                            @php $isActive = request()->routeIs($item['route']) || request()->routeIs($item['route'] . '.*'); @endphp
                            <a href="{{ route($item['route']) }}"
                               class="flex items-center gap-3 px-4 py-2 rounded-admin-md transition-all duration-150 {{ $isActive ? 'bg-admin-indigo-tint text-admin-indigo font-semibold' : 'text-admin-slate hover:bg-admin-canvas hover:text-admin-ink' }}">
                                <i data-lucide="{{ $item['icon'] }}" class="w-4 h-4 min-w-[16px] shrink-0"></i>
                                <span class="text-sm truncate">{{ $item['label'] }}</span>
                            </a>
                        @endforeach
                    </div>
                </div>

                {{-- LAPORAN --}}
                @php
                    $isLaporanActive = request()->routeIs('rekap.*') || request()->routeIs('report.*');
                    $laporanItems = [
                        ['route' => 'rekap.absensi', 'icon' => 'table',            'label' => 'Rekap Absensi'],
                        ['route' => 'report.index',  'icon' => 'file-spreadsheet',  'label' => 'Report'],
                    ];
                @endphp
                <div x-data="{ open: {{ $isLaporanActive ? 'true' : 'false' }} }">
                    <button @click="open = !open"
                            class="w-full flex items-center justify-between px-4 py-[10px] rounded-admin-md transition-all duration-150 {{ $isLaporanActive ? 'text-admin-indigo font-semibold bg-admin-indigo-tint/30' : 'text-admin-slate hover:bg-admin-canvas hover:text-admin-ink' }}">
                        <div class="flex items-center gap-3 min-w-0">
                            <i data-lucide="bar-chart-3" class="w-5 h-5 min-w-[20px] shrink-0"></i>
                            <span class="text-sm transition-opacity duration-300 truncate" :class="sidebarOpen ? 'opacity-100' : 'opacity-0 w-0 overflow-hidden'">Laporan</span>
                        </div>
                        <i data-lucide="chevron-down" class="w-4 h-4 shrink-0 transition-transform duration-200" :class="open ? 'rotate-180' : ''" x-show="sidebarOpen"></i>
                    </button>
                    <div x-show="open && sidebarOpen" x-collapse class="pl-4 mt-1 space-y-1">
                        @foreach($laporanItems as $item)
                            @php $isActive = request()->routeIs($item['route']) || request()->routeIs($item['route'] . '.*'); @endphp
                            <a href="{{ route($item['route']) }}"
                               class="flex items-center gap-3 px-4 py-2 rounded-admin-md transition-all duration-150 {{ $isActive ? 'bg-admin-indigo-tint text-admin-indigo font-semibold' : 'text-admin-slate hover:bg-admin-canvas hover:text-admin-ink' }}">
                                <i data-lucide="{{ $item['icon'] }}" class="w-4 h-4 min-w-[16px] shrink-0"></i>
                                <span class="text-sm truncate">{{ $item['label'] }}</span>
                            </a>
                        @endforeach
                    </div>
                </div>

                {{-- KEUANGAN --}}
                @php
                    $isKeuanganActive = request()->routeIs('finance.*') || request()->routeIs('payroll.slip') || request()->routeIs('reports.print') || request()->routeIs('dashboard.pdf') || (request()->routeIs('dashboard') && request('mode') === 'keuangan');
                    $financeRoutes = [
                        ['route' => 'dashboard', 'params' => ['mode' => 'keuangan'], 'icon' => 'layout-dashboard', 'label' => 'Dasbor Keuangan'],
                        ['route' => 'finance.accounts.index', 'icon' => 'landmark', 'label' => 'Akun Rekening'],
                        ['route' => 'finance.categories.index', 'icon' => 'tag', 'label' => 'Kategori'],
                        ['route' => 'finance.transactions.index', 'icon' => 'arrow-left-right', 'label' => 'Transaksi'],
                        ['route' => 'finance.fund-transfers.index', 'icon' => 'repeat', 'label' => 'Transfer Dana'],
                        ['route' => 'finance.salary-components.index', 'icon' => 'receipt', 'label' => 'Komponen Gaji'],
                        ['route' => 'finance.payroll-periods.index', 'icon' => 'wallet', 'label' => 'Penggajian'],
                    ];
                @endphp
                <div x-data="{ open: {{ $isKeuanganActive ? 'true' : 'false' }} }">
                    <button @click="open = !open"
                            class="w-full flex items-center justify-between px-4 py-[10px] rounded-admin-md transition-all duration-150 {{ $isKeuanganActive ? 'text-admin-indigo font-semibold bg-admin-indigo-tint/30' : 'text-admin-slate hover:bg-admin-canvas hover:text-admin-ink' }}">
                        <div class="flex items-center gap-3 min-w-0">
                            <i data-lucide="wallet" class="w-5 h-5 min-w-[20px] shrink-0"></i>
                            <span class="text-sm transition-opacity duration-300 truncate" :class="sidebarOpen ? 'opacity-100' : 'opacity-0 w-0 overflow-hidden'">Keuangan</span>
                        </div>
                        <i data-lucide="chevron-down" class="w-4 h-4 shrink-0 transition-transform duration-200" :class="open ? 'rotate-180' : ''" x-show="sidebarOpen"></i>
                    </button>
                    <div x-show="open && sidebarOpen" x-collapse class="pl-4 mt-1 space-y-1">
                        @foreach($financeRoutes as $item)
                            @php
                                $routeParams = $item['params'] ?? [];
                                $isActive = request()->routeIs($item['route']) || (isset($item['route']) && request()->routeIs($item['route'] . '.*'));
                            @endphp
                            <a href="{{ route($item['route'], $routeParams) }}"
                               class="flex items-center gap-3 px-4 py-2 rounded-admin-md transition-all duration-150 {{ $isActive ? 'bg-admin-indigo-tint text-admin-indigo font-semibold' : 'text-admin-slate hover:bg-admin-canvas hover:text-admin-ink' }}">
                                <i data-lucide="{{ $item['icon'] }}" class="w-4 h-4 min-w-[16px] shrink-0"></i>
                                <span class="text-sm truncate">{{ $item['label'] }}</span>
                            </a>
                        @endforeach
                    </div>
                </div>

                {{-- USER (standalone, di atas Pengaturan) --}}
                @php $isUserActive = request()->routeIs('users.*'); @endphp
                <div class="pt-2 mt-2 border-t border-admin-border/50">
                    <a href="{{ route('users.index') }}"
                       class="flex items-center gap-3 px-4 py-[10px] rounded-admin-md transition-all duration-150 {{ $isUserActive ? 'bg-admin-indigo-tint text-admin-indigo font-semibold' : 'text-admin-slate hover:bg-admin-canvas hover:text-admin-ink' }}">
                        <i data-lucide="users" class="w-5 h-5 min-w-[20px] shrink-0"></i>
                        <span class="text-sm transition-opacity duration-300 truncate" :class="sidebarOpen ? 'opacity-100' : 'opacity-0 w-0 overflow-hidden'">User</span>
                    </a>
                </div>

                {{-- PENGATURAN (Super Admin only) --}}
                @if($user->isSuperAdmin())
                    @php $isSettingActive = request()->routeIs('settings.*'); @endphp
                    <div class="pt-2 mt-2 border-t border-admin-border/50">
                        <a href="{{ route('settings.index') }}"
                           class="flex items-center gap-3 px-4 py-[10px] rounded-admin-md transition-all duration-150 {{ $isSettingActive ? 'bg-admin-indigo-tint text-admin-indigo font-semibold' : 'text-admin-slate hover:bg-admin-canvas hover:text-admin-ink' }}">
                            <i data-lucide="settings" class="w-5 h-5 min-w-[20px] shrink-0"></i>
                            <span class="text-sm transition-opacity duration-300 truncate" :class="sidebarOpen ? 'opacity-100' : 'opacity-0 w-0 overflow-hidden'">Pengaturan</span>
                        </a>
                    </div>
                @endif

            @elseif($user->isSensei())
                @php $senseiItems = [
                    ['route' => 'dashboard', 'icon' => 'home', 'label' => 'Home'],
                    ['route' => 'jadwal.index', 'icon' => 'calendar', 'label' => 'Kelola Jadwal'],
                ]; @endphp
                @foreach($senseiItems as $item)
                    @php $isActive = request()->routeIs($item['route']) || request()->routeIs(explode('.', $item['route'])[0] . '.*'); @endphp
                    <a href="{{ route($item['route']) }}"
                       class="flex items-center gap-3 px-4 py-[10px] rounded-admin-md transition-all duration-150 {{ $isActive ? 'bg-admin-indigo-tint text-admin-indigo font-semibold' : 'text-admin-slate hover:bg-admin-canvas hover:text-admin-ink' }}">
                        <i data-lucide="{{ $item['icon'] }}" class="w-5 h-5 min-w-[20px] shrink-0"></i>
                        <span class="text-sm transition-opacity duration-300 truncate" :class="sidebarOpen ? 'opacity-100' : 'opacity-0 w-0 overflow-hidden'">{{ $item['label'] }}</span>
                    </a>
                @endforeach
            @endif
        </nav>
    </aside>

    <!-- Main Content Area -->
    <div class="flex-1 flex flex-col min-w-0">
        <!-- Top Bar -->
        <header class="h-[72px] bg-admin-surface border-b border-admin-border flex items-center justify-between px-8 z-20 shrink-0">
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

                <!-- Profile Dropdown -->
                <div class="relative" x-data="{ open: false }">
                    <button @click="open = !open" @click.outside="open = false"
                            class="flex items-center gap-3 px-3 py-1.5 rounded-admin-md hover:bg-admin-canvas transition-all duration-150 cursor-pointer">
                        <img src="{{ auth()->user()->foto_url }}"
                             class="w-8 h-8 rounded-admin-full object-cover border border-admin-border shrink-0" alt="">
                        <div class="text-left hidden md:block">
                            <p class="text-sm font-semibold text-admin-ink leading-tight truncate max-w-[140px]">{{ auth()->user()->name }}</p>
                            <p class="text-[10px] text-admin-slate leading-tight">{{ auth()->user()->role_label }}</p>
                        </div>
                        <i data-lucide="chevron-down" class="w-4 h-4 text-admin-slate shrink-0 transition-transform duration-200" :class="open ? 'rotate-180' : ''"></i>
                    </button>

                    <!-- Dropdown Menu -->
                    <div x-show="open" x-cloak
                         @click.outside="open = false"
                         x-transition:enter="transition ease-out duration-150"
                         x-transition:enter-start="opacity-0 scale-95 -translate-y-1"
                         x-transition:enter-end="opacity-100 scale-100 translate-y-0"
                         x-transition:leave="transition ease-in duration-100"
                         x-transition:leave-start="opacity-100"
                         x-transition:leave-end="opacity-0"
                         class="absolute right-0 top-full mt-2 w-56 bg-admin-surface border border-admin-border rounded-admin-lg shadow-admin-float overflow-hidden z-50">

                        <!-- User Info -->
                        <div class="px-4 py-3 border-b border-admin-border">
                            <p class="text-sm font-semibold text-admin-ink truncate">{{ auth()->user()->name }}</p>
                            <p class="text-xs text-admin-slate truncate mt-0.5">{{ auth()->user()->email }}</p>
                            <span class="inline-flex items-center px-2 py-0.5 mt-1.5 rounded-admin-full text-xs font-medium bg-admin-indigo-tint text-admin-indigo">
                                {{ auth()->user()->role_label }}
                            </span>
                        </div>

                        <!-- Menu Items -->
                        <div class="p-1">
                            <a href="{{ route('profile.index') }}"
                               class="flex items-center gap-3 px-4 py-2.5 rounded-admin-md text-sm text-admin-slate hover:bg-admin-canvas hover:text-admin-ink transition-all duration-150">
                                <i data-lucide="user" class="w-4 h-4"></i>
                                Profil Saya
                            </a>
                        </div>

                        <!-- Logout -->
                        <div class="border-t border-admin-border p-1">
                            <form action="{{ route('logout') }}" method="POST">
                                @csrf
                                <button type="submit"
                                        class="w-full flex items-center gap-3 px-4 py-2.5 rounded-admin-md text-sm text-admin-danger hover:bg-admin-danger-tint transition-all duration-150 cursor-pointer">
                                    <i data-lucide="log-out" class="w-4 h-4"></i>
                                    Keluar
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </header>

        <!-- Main Content -->
        <main class="flex-1 p-8">
            @yield('content')
        </main>
    </div>

    <!-- Session Flash Messages (SweetAlert2) -->
    @if(session('success'))
        <script>
            document.addEventListener('DOMContentLoaded', () => {
                Swal.fire({
                    icon: 'success',
                    title: 'Berhasil!',
                    text: @json(session('success')),
                    toast: true,
                    position: 'top-end',
                    showConfirmButton: false,
                    timer: 4000,
                    timerProgressBar: true,
                });
            });
        </script>
    @endif
    @if(session('error'))
        <script>
            document.addEventListener('DOMContentLoaded', () => {
                Swal.fire({
                    icon: 'error',
                    title: 'Gagal!',
                    text: @json(session('error')),
                    toast: true,
                    position: 'top-end',
                    showConfirmButton: false,
                    timer: 4000,
                    timerProgressBar: true,
                });
            });
        </script>
    @endif

    @stack('scripts')

    <script>
        document.addEventListener('DOMContentLoaded', () => {
            lucide.createIcons();
        });

        // SweetAlert2 Confirm Delete helper
        function confirmDelete(event, message = 'Data ini akan dihapus permanen.') {
            event.preventDefault();
            const form = event.target.closest('form');
            Swal.fire({
                title: 'Yakin hapus?',
                text: message,
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#DC2626',
                cancelButtonColor: '#6F6C84',
                confirmButtonText: 'Ya, Hapus!',
                cancelButtonText: 'Batal',
                reverseButtons: true,
            }).then((result) => {
                if (result.isConfirmed) {
                    form.submit();
                }
            });
        }

        // Toast event listener for AJAX notifications using SweetAlert2
        window.addEventListener('toast', (e) => {
            Swal.fire({
                icon: e.detail.type || 'success',
                title: e.detail.type === 'success' ? 'Berhasil!' : 'Gagal!',
                text: e.detail.message,
                toast: true,
                position: 'top-end',
                showConfirmButton: false,
                timer: 4000,
                timerProgressBar: true,
            });
        });

        // Alpine.js component: AJAX Form Handler
        // Usage: <form x-data="ajaxForm({ action: '...', method: 'POST', callback: fn })" @submit.prevent="submit">
        document.addEventListener('alpine:init', () => {
            Alpine.data('ajaxForm', (config = {}) => ({
                loading: false,
                errors: {},

                async submit(event) {
                    this.loading = true;
                    this.errors = {};
                    const form = event.target;
                    const formData = new FormData(form);
                    const method = config.method || form.method || (form.querySelector('input[name="_method"], input[name="_token"]') ? 'POST' : 'GET');
                    const action = config.action || form.action;

                    // Handle PUT/PATCH/DELETE via _method field
                    if (form.querySelector('input[name="_method"]')) {
                        formData.set('_method', form.querySelector('input[name="_method"]').value);
                    }

                    try {
                        const response = await fetch(action, {
                            method: method,
                            headers: {
                                'X-Requested-With': 'XMLHttpRequest',
                                'Accept': 'application/json',
                                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                            },
                            body: formData,
                        });

                        const data = await response.json();

                        if (!response.ok) {
                            if (response.status === 422 && data.errors) {
                                // Validation errors
                                this.errors = data.errors;
                                const firstError = Object.values(data.errors)[0][0];
                                this.showToast(firstError, 'error');
                                // Focus first error field
                                const firstKey = Object.keys(data.errors)[0];
                                const firstField = form.querySelector(`[name="${firstKey}"]`);
                                if (firstField) firstField.focus();
                                return;
                            }
                            this.showToast(data.message || 'Terjadi kesalahan', 'error');
                            return;
                        }

                        // Success
                        this.showToast(data.message || 'Berhasil disimpan!', 'success');

                        // Reset form if it's a create form
                        if (!form.querySelector('input[name="_method"]')) {
                            form.reset();
                        }

                        // Call optional callback
                        if (config.callback) {
                            config.callback(data);
                        }

                        // Update UI: if response has table HTML, swap it
                        if (data.table_html) {
                            const tableContainer = document.getElementById(data.table_target || 'table-container');
                            if (tableContainer) tableContainer.innerHTML = data.table_html;
                        }

                        // Close modal if response has modal flag
                        if (data.close_modal) {
                            const modal = document.getElementById(data.close_modal);
                            if (modal) modal.classList.add('hidden');
                        }

                    } catch (error) {
                        this.showToast('Gagal terhubung ke server', 'error');
                    } finally {
                        this.loading = false;
                        // Re-init Lucide icons after DOM update
                        setTimeout(() => { lucide.createIcons(); }, 50);
                    }
                },

                showToast(message, type = 'success') {
                    window.dispatchEvent(new CustomEvent('toast', {
                        detail: { message, type }
                    }));
                }
            }));
        });
    </script>
</body>
</html>
