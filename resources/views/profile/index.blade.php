@extends(auth()->user()->isSuperAdmin() || auth()->user()->isAdmin() ? 'layouts.admin' : 'layouts.member')
@section('title', 'Profil')
@section('header', 'Profil')
@section('content')

@php
    $isAdmin = auth()->user()->isSuperAdmin() || auth()->user()->isAdmin();
    
    // Design system tokens mapped per visual system
    $containerClass = $isAdmin ? 'max-w-4xl mx-auto space-y-6' : 'max-w-md mx-auto space-y-6';
    
    $cardClass = $isAdmin 
        ? 'bg-admin-surface border border-admin-border rounded-admin-lg p-6' 
        : 'bg-member-surface rounded-member-xl p-6 shadow-member-card border-0';
        
    $headingClass = $isAdmin
        ? 'text-base font-semibold text-admin-ink'
        : 'text-base font-bold text-member-ink';
        
    $labelClass = $isAdmin
        ? 'block text-xs font-medium uppercase tracking-wider text-admin-slate mb-1'
        : 'block text-xs font-bold uppercase tracking-wider text-member-slate mb-1';
        
    $inputClass = $isAdmin
        ? 'w-full px-3 py-2 bg-admin-surface text-admin-ink border border-admin-border rounded-admin-md focus:outline-none focus:border-admin-indigo focus:ring-1 focus:ring-admin-indigo text-sm placeholder:text-admin-mist'
        : 'w-full px-4 py-3 bg-member-canvas text-member-ink border-0 rounded-member-lg focus:outline-none focus:ring-2 focus:ring-member-blue/30 text-sm placeholder:text-member-mist';
        
    $btnPrimaryClass = $isAdmin
        ? 'w-full py-2.5 bg-admin-indigo hover:bg-admin-indigo-deep text-white font-semibold rounded-admin-md transition-colors text-sm shadow-none cursor-pointer flex items-center justify-center'
        : 'w-full py-3.5 bg-member-blue hover:bg-member-blue-deep text-white font-semibold rounded-member-full shadow-member-primary hover:shadow-lg transition-all duration-150 text-sm cursor-pointer flex items-center justify-center';

    $btnDangerClass = $isAdmin
        ? 'w-full py-2.5 bg-admin-danger hover:bg-red-700 text-white font-semibold rounded-admin-md transition-colors text-sm shadow-none cursor-pointer flex items-center justify-center'
        : 'w-full py-3.5 bg-status-alpha hover:bg-red-700 text-white font-semibold rounded-member-full shadow-member-primary hover:shadow-lg transition-all duration-150 text-sm cursor-pointer flex items-center justify-center';

    $roleBadgeClass = $isAdmin
        ? 'bg-admin-indigo-tint text-admin-indigo text-xs font-semibold px-2.5 py-1 rounded-admin-full'
        : 'bg-member-blue-tint text-member-blue text-xs font-bold px-2.5 py-1 rounded-member-full';
        
    $avatarClass = $isAdmin
        ? 'w-24 h-24 rounded-admin-lg mx-auto object-cover border border-admin-border'
        : 'w-24 h-24 rounded-member-lg mx-auto object-cover shadow-member-card';
        
    $captionClass = $isAdmin
        ? 'text-xs text-admin-slate'
        : 'text-xs text-member-slate';
        
    $errorClass = $isAdmin
        ? 'text-xs text-admin-danger mt-1'
        : 'text-xs text-status-alpha mt-1';
@endphp

<div class="{{ $containerClass }} animate-fade-in-up">
    <!-- Profile Card -->
    <div class="{{ $cardClass }} text-center flex flex-col items-center justify-center">
        <div class="relative group">
            <img src="{{ $user->foto_url }}" class="{{ $avatarClass }}" alt="Avatar">
        </div>
        <h2 class="text-lg font-bold mt-4 {{ $isAdmin ? 'text-admin-ink' : 'text-member-ink' }}">{{ $user->name }}</h2>
        <p class="text-sm {{ $isAdmin ? 'text-admin-slate' : 'text-member-slate' }} mb-3">{{ $user->email }}</p>
        <span class="inline-block {{ $roleBadgeClass }}">{{ $user->role_label }}</span>
    </div>

    <!-- Edit Profile -->
    <div class="{{ $cardClass }}">
        <div class="flex items-center gap-2 mb-5">
            <i data-lucide="user-cog" class="w-5 h-5 {{ $isAdmin ? 'text-admin-indigo' : 'text-member-blue' }}"></i>
            <h3 class="{{ $headingClass }}">Edit Profil</h3>
        </div>
        <form action="{{ route('profile.update') }}" method="POST" enctype="multipart/form-data" class="space-y-4">
            @csrf @method('PUT')
            <div>
                <label class="{{ $labelClass }}">Nama Lengkap</label>
                <input type="text" name="name" value="{{ $user->name }}" required class="{{ $inputClass }}" placeholder="Nama Lengkap">
                @error('name') <p class="{{ $errorClass }}">{{ $message }}</p> @enderror
            </div>
            <div>
                <label class="{{ $labelClass }}">No Handphone</label>
                <input type="text" name="phone" value="{{ $user->phone }}" class="{{ $inputClass }}" placeholder="Contoh: 08123456789">
                @error('phone') <p class="{{ $errorClass }}">{{ $message }}</p> @enderror
            </div>
            <div>
                <label class="{{ $labelClass }}">Foto Profil</label>
                <input type="file" name="foto" accept="image/*" class="{{ $inputClass }}">
                <p class="{{ $captionClass }} mt-1">Format: JPG, PNG. Max: 2MB</p>
                @error('foto') <p class="{{ $errorClass }}">{{ $message }}</p> @enderror
            </div>
            <div class="pt-2">
                <button type="submit" class="{{ $btnPrimaryClass }}">
                    <i data-lucide="save" class="w-4 h-4 mr-2"></i>
                    Simpan Perubahan
                </button>
            </div>
        </form>
    </div>

    <!-- Change Password -->
    <div class="{{ $cardClass }}">
        <div class="flex items-center gap-2 mb-5">
            <i data-lucide="lock" class="w-5 h-5 {{ $isAdmin ? 'text-admin-indigo' : 'text-member-blue' }}"></i>
            <h3 class="{{ $headingClass }}">Ganti Password</h3>
        </div>
        <form action="{{ route('profile.password') }}" method="POST" class="space-y-4">
            @csrf @method('PUT')
            <div>
                <label class="{{ $labelClass }}">Password Lama</label>
                <input type="password" name="current_password" required class="{{ $inputClass }}" placeholder="Masukkan password saat ini">
                @error('current_password') <p class="{{ $errorClass }}">{{ $message }}</p> @enderror
            </div>
            <div>
                <label class="{{ $labelClass }}">Password Baru</label>
                <input type="password" name="password" required class="{{ $inputClass }}" placeholder="Password baru minimal 8 karakter">
                @error('password') <p class="{{ $errorClass }}">{{ $message }}</p> @enderror
            </div>
            <div>
                <label class="{{ $labelClass }}">Konfirmasi Password Baru</label>
                <input type="password" name="password_confirmation" required class="{{ $inputClass }}" placeholder="Ulangi password baru">
            </div>
            <div class="pt-2">
                <button type="submit" class="{{ $btnDangerClass }}">
                    <i data-lucide="key-round" class="w-4 h-4 mr-2"></i>
                    Ganti Password
                </button>
            </div>
        </form>
    </div>

    @if(!$isAdmin)
    <!-- Logout Card for Member -->
    <div class="{{ $cardClass }}">
        <form action="{{ route('logout') }}" method="POST">
            @csrf
            <button type="submit" class="w-full py-3.5 bg-status-alpha/10 text-status-alpha hover:bg-status-alpha/20 font-semibold rounded-member-full transition-colors duration-150 text-sm cursor-pointer flex items-center justify-center gap-2">
                <i data-lucide="log-out" class="w-4 h-4"></i>
                Keluar dari Akun
            </button>
        </form>
    </div>
    @endif
</div>
@endsection

