<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="h-full bg-slate-50">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Sign In - {{ config('app.name', 'LifeGuard Data Recovery') }}</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <script src="https://cdn.tailwindcss.com"></script>
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
    <div class="max-w-md w-full space-y-8 bg-white p-8 sm:p-10 rounded-3xl shadow-xl shadow-slate-200/50 border border-slate-100">
        <!-- Logo & Title -->
        <div class="text-center">
            <a href="{{ route('home') }}" class="inline-block mb-4">
                <img src="{{ asset('images/logo.png') }}" alt="LifeGuard Data Recovery" class="h-12 w-auto mx-auto object-contain">
            </a>
            <h2 class="text-2xl font-extrabold text-slate-900 tracking-tight">Welcome back</h2>
            <p class="mt-2 text-sm text-slate-500">Sign in to LifeGuard Data Recovery CMS to manage operations</p>
        </div>

        @if(session('success'))
            <div class="rounded-xl bg-emerald-50 border border-emerald-200 p-3 text-xs text-emerald-800">
                {{ session('success') }}
            </div>
        @endif

        @if($errors->any())
            <div class="rounded-xl bg-rose-50 border border-rose-200 p-3 text-xs text-rose-800">
                <ul class="list-disc list-inside space-y-1">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <!-- Login Form -->
        <form class="mt-8 space-y-5" action="{{ route('login') }}" method="POST">
            @csrf

            <div>
                <label for="email" class="block text-xs font-semibold uppercase tracking-wider text-slate-700 mb-1.5">Email address</label>
                <div class="relative">
                    <input id="email" name="email" type="email" autocomplete="email" required value="{{ old('email') }}"
                           class="w-full px-4 py-2.5 pl-10 text-sm rounded-xl border border-slate-200 focus:outline-none focus:ring-2 focus:ring-brand-500/20 focus:border-brand-500 transition"
                           placeholder="you@example.com">
                    <i data-lucide="mail" class="w-4 h-4 text-slate-400 absolute left-3.5 top-3"></i>
                </div>
            </div>

            <div>
                <label for="password" class="block text-xs font-semibold uppercase tracking-wider text-slate-700 mb-1.5">Password</label>
                <div class="relative">
                    <input id="password" name="password" type="password" autocomplete="current-password" required
                           class="w-full px-4 py-2.5 pl-10 text-sm rounded-xl border border-slate-200 focus:outline-none focus:ring-2 focus:ring-brand-500/20 focus:border-brand-500 transition"
                           placeholder="••••••••">
                    <i data-lucide="lock" class="w-4 h-4 text-slate-400 absolute left-3.5 top-3"></i>
                </div>
            </div>

            <div class="flex items-center justify-between">
                <div class="flex items-center">
                    <input id="remember" name="remember" type="checkbox"
                           class="h-4 w-4 text-brand-600 focus:ring-brand-500 border-slate-300 rounded">
                    <label for="remember" class="ml-2 block text-xs text-slate-600">Remember me</label>
                </div>
            </div>

            <div>
                <button type="submit" 
                        class="w-full flex justify-center items-center gap-2 py-3 px-4 border border-transparent rounded-xl text-sm font-bold text-white bg-brand-600 hover:bg-brand-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-brand-500 shadow-md shadow-brand-500/30 transition">
                    <i data-lucide="log-in" class="w-4 h-4"></i>
                    Sign In
                </button>
            </div>
        </form>

        <div class="pt-4 border-t border-slate-100 text-center text-xs text-slate-500">
            <p><a href="{{ route('home') }}" class="text-slate-400 hover:text-slate-600 inline-flex items-center gap-1">← Return to website</a></p>
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
