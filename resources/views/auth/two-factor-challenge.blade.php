<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="h-full bg-slate-50">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Two-Factor Authentication - {{ config('app.name', 'LifeGuard Data Recovery') }}</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <script src="https://cdn.tailwindcss.com"></script>
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    fontFamily: { sans: ['"Plus Jakarta Sans"', 'sans-serif'] },
                    colors: {
                        brand: { 500: '#0ea5e9', 600: '#0284c7', 700: '#0369a1' }
                    }
                }
            }
        }
    </script>
    <script src="https://unpkg.com/lucide@latest"></script>
</head>
<body class="h-full flex items-center justify-center py-12 px-4 sm:px-6 lg:px-8 font-sans">
    <div class="max-w-md w-full space-y-6 bg-white p-8 sm:p-10 rounded-3xl shadow-xl shadow-slate-200/50 border border-slate-100"
         x-data="{ useRecovery: {{ $errors->has('recovery_code') ? 'true' : 'false' }} }">
        
        <!-- Header -->
        <div class="text-center">
            <a href="{{ route('home') }}" class="inline-block mb-3">
                <img src="{{ asset('images/logo.png') }}" alt="LifeGuard Data Recovery" class="h-12 w-auto mx-auto object-contain">
            </a>

            <div class="inline-flex items-center justify-center w-14 h-14 rounded-2xl bg-brand-50 text-brand-600 mb-3 border border-brand-100 shadow-sm">
                <i data-lucide="shield-check" class="w-7 h-7"></i>
            </div>

            <h2 class="text-2xl font-extrabold text-slate-900 tracking-tight">Two-Factor Authentication</h2>
            
            <p class="mt-2 text-xs text-slate-500 leading-relaxed" x-show="!useRecovery">
                Please enter the 6-digit verification code from your <strong class="text-slate-700">Google Authenticator</strong> app for <span class="text-brand-600 font-semibold">{{ $user->email }}</span>.
            </p>
            <p class="mt-2 text-xs text-slate-500 leading-relaxed" x-show="useRecovery" x-cloak>
                Please enter one of your single-use emergency recovery codes to authenticate.
            </p>
        </div>

        @if(session('success'))
            <div class="rounded-xl bg-emerald-50 border border-emerald-200 p-3 text-xs text-emerald-800 flex items-center gap-2">
                <i data-lucide="check-circle" class="w-4 h-4 shrink-0 text-emerald-600"></i>
                <span>{{ session('success') }}</span>
            </div>
        @endif

        @if($errors->any())
            <div class="rounded-xl bg-rose-50 border border-rose-200 p-3.5 text-xs text-rose-800 space-y-1">
                @foreach($errors->all() as $error)
                    <div class="flex items-center gap-2">
                        <i data-lucide="alert-circle" class="w-4 h-4 shrink-0 text-rose-600"></i>
                        <span>{{ $error }}</span>
                    </div>
                @endforeach
            </div>
        @endif

        <!-- 2FA Form -->
        <form class="space-y-5" action="{{ route('login.two-factor') }}" method="POST">
            @csrf

            <!-- Standard 6-Digit TOTP Code -->
            <div x-show="!useRecovery">
                <label for="code" class="block text-xs font-bold uppercase tracking-wider text-slate-700 text-center mb-2">
                    6-Digit Google Authenticator Code
                </label>
                <div class="relative">
                    <input id="code" name="code" type="text" inputmode="numeric" pattern="[0-9]*" autocomplete="one-time-code" maxlength="7" autofocus
                           class="w-full px-4 py-3 text-center font-mono font-black text-2xl tracking-[0.35em] rounded-xl border border-slate-200 focus:outline-none focus:ring-2 focus:ring-brand-500/30 focus:border-brand-500 transition shadow-sm placeholder:tracking-normal placeholder:font-normal placeholder:text-sm"
                           placeholder="123 456">
                </div>
                <p class="text-[11px] text-slate-400 text-center mt-2">
                    Codes update automatically every 30 seconds
                </p>
            </div>

            <!-- Emergency Recovery Code -->
            <div x-show="useRecovery" x-cloak>
                <label for="recovery_code" class="block text-xs font-bold uppercase tracking-wider text-slate-700 text-center mb-2">
                    Emergency Recovery Code
                </label>
                <div class="relative">
                    <input id="recovery_code" name="recovery_code" type="text" autocomplete="off"
                           class="w-full px-4 py-3 text-center font-mono font-bold text-base tracking-widest rounded-xl border border-slate-200 focus:outline-none focus:ring-2 focus:ring-brand-500/30 focus:border-brand-500 transition shadow-sm"
                           placeholder="xxxx-xxxx">
                </div>
                <p class="text-[11px] text-slate-400 text-center mt-2">
                    Each recovery code can only be used once
                </p>
            </div>

            <div>
                <button type="submit"
                        class="w-full flex justify-center items-center gap-2 py-3 px-4 border border-transparent rounded-xl text-sm font-bold text-white bg-brand-600 hover:bg-brand-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-brand-500 shadow-md shadow-brand-500/30 transition">
                    <i data-lucide="lock" class="w-4 h-4"></i>
                    <span>Verify & Continue</span>
                </button>
            </div>
        </form>

        <!-- Toggle Recovery / Switcher -->
        <div class="pt-2 text-center text-xs space-y-3">
            <button type="button" @click="useRecovery = !useRecovery" class="text-brand-600 hover:text-brand-700 font-semibold inline-flex items-center gap-1.5 transition">
                <i data-lucide="key" class="w-3.5 h-3.5"></i>
                <span x-text="useRecovery ? 'Use Google Authenticator 6-digit code' : 'Lost your phone? Use an emergency recovery code'"></span>
            </button>

            <form action="{{ route('login.two-factor.cancel') }}" method="POST" class="pt-2 border-t border-slate-100">
                @csrf
                <button type="submit" class="text-slate-400 hover:text-slate-600 text-xs inline-flex items-center gap-1 transition">
                    <span>← Cancel and return to sign in</span>
                </button>
            </form>
        </div>

    </div>

    <script>
        if (typeof lucide !== 'undefined' && typeof lucide.createIcons === 'function') {
            lucide.createIcons();
        } else if (window.lucide && typeof window.lucide.createIcons === 'function') {
            window.lucide.createIcons();
        }
    </script>
</body>
</html>
