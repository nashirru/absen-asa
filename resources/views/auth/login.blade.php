@extends('layouts.member')
@section('title', 'Login - LPK Asa Hikari Mulya')

@section('content')
<div class="min-h-[85vh] flex items-center justify-center px-2">
    <div class="w-full max-w-sm animate-fade-in-up">

        <!-- Logo + Branding -->
        <div class="text-center mb-8">
            <div class="w-14 h-14 bg-member-blue rounded-xl flex items-center justify-center text-white font-bold text-2xl mx-auto shadow-member-primary">
                A
            </div>
            <h1 class="mt-4 text-xl font-bold tracking-tight text-member-ink">LPK Asa Hikari Mulya</h1>
            <p class="mt-1 text-sm text-member-slate">Sistem Absensi Digital</p>
        </div>

        <!-- Card -->
        <div class="bg-member-surface rounded-2xl border border-member-border p-6 shadow-sm">
            <div class="mb-5">
                <h2 class="text-base font-semibold text-member-ink">Masuk ke akun Anda</h2>
                <p class="text-sm text-member-slate mt-0.5">Masukkan email dan password untuk melanjutkan</p>
            </div>

            <form action="{{ route('login') }}" method="POST" class="space-y-4">
                @csrf

                <!-- Email -->
                <div class="space-y-1.5">
                    <label for="email" class="text-sm font-medium text-member-ink">Email</label>
                    <input
                        id="email"
                        type="email"
                        name="email"
                        value="{{ old('email') }}"
                        required
                        autocomplete="email"
                        placeholder="nama@email.com"
                        class="w-full h-10 px-3 text-sm rounded-lg border border-member-border bg-transparent text-member-ink placeholder:text-member-mist outline-none transition-all duration-150 focus:border-member-blue focus:ring-2 focus:ring-member-blue/20 @error('email') border-status-alpha focus:border-status-alpha focus:ring-status-alpha/20 @enderror"
                    >
                    @error('email')
                        <p class="text-xs text-status-alpha mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Password -->
                <div class="space-y-1.5">
                    <label for="password" class="text-sm font-medium text-member-ink">Password</label>
                    <input
                        id="password"
                        type="password"
                        name="password"
                        required
                        autocomplete="current-password"
                        placeholder="••••••••"
                        class="w-full h-10 px-3 text-sm rounded-lg border border-member-border bg-transparent text-member-ink placeholder:text-member-mist outline-none transition-all duration-150 focus:border-member-blue focus:ring-2 focus:ring-member-blue/20"
                    >
                </div>

                <!-- Remember Me -->
                <div class="flex items-center">
                    <label class="flex items-center gap-2 cursor-pointer select-none">
                        <input
                            type="checkbox"
                            name="remember"
                            class="w-4 h-4 rounded border-member-border text-member-blue accent-member-blue cursor-pointer"
                        >
                        <span class="text-sm text-member-slate">Ingat saya</span>
                    </label>
                </div>

                <!-- Submit -->
                <button
                    type="submit"
                    class="w-full h-10 bg-member-blue hover:bg-member-blue-deep text-white text-sm font-medium rounded-lg shadow-member-primary transition-all duration-150 active:scale-[0.98] focus:outline-none focus:ring-2 focus:ring-member-blue/40 focus:ring-offset-2"
                >
                    Masuk
                </button>
            </form>
        </div>

        <!-- Footer -->
        <p class="text-center text-xs text-member-mist mt-6">
            &copy; {{ date('Y') }} LPK Asa Hikari Mulya
        </p>
    </div>
</div>
@endsection
