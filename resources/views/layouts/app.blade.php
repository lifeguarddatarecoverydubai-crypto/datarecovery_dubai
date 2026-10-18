<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="h-full bg-slate-50 text-slate-900 antialiased scroll-smooth">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <!-- Favicon & Icons -->
    <link rel="icon" type="image/x-icon" href="{{ asset('favicon.ico') }}">
    <link rel="icon" type="image/png" sizes="32x32" href="{{ asset('favicon.png') }}">
    <link rel="apple-touch-icon" sizes="180x180" href="{{ asset('apple-touch-icon.png') }}">
    <link rel="icon" type="image/webp" href="{{ asset('storage/media/favicon.webp') }}">

    @if(config('services.google.search_console_verification'))
        <meta name="google-site-verification" content="{{ config('services.google.search_console_verification') }}">
    @endif

    @if(config('services.google.tag_id'))
        <!-- Google tag (gtag.js) -->
        <script async src="https://www.googletagmanager.com/gtag/js?id={{ config('services.google.tag_id') }}"></script>
        <script>
            window.dataLayer = window.dataLayer || [];
            function gtag(){dataLayer.push(arguments);}
            gtag('js', new Date());
            gtag('config', '{{ config('services.google.tag_id') }}');
        </script>
    @endif

    @hasSection('seo_meta')
        @yield('seo_meta')
    @else
        <title>#1 Data Recovery Dubai - Trusted Data Recovery Services Dubai | LifeGuard</title>
        <meta name="description" content="LifeGuard is the best Data Recovery service provider in Dubai with 30 years of experience. Certified Class 100 cleanroom lab for HDD, SSD, RAID, NAS, CCTV, and mobile devices.">
        <meta name="keywords" content="data recovery dubai, hard drive recovery dubai, ssd data recovery dubai, raid recovery dubai, nas recovery uae, cctv footage recovery dubai, macbook data recovery dubai">
        <link rel="canonical" href="{{ url()->current() }}">
        <meta property="og:title" content="#1 Data Recovery Dubai - Trusted Data Recovery Services Dubai">
        <meta property="og:description" content="LifeGuard is the best Data Recovery service provider in Dubai with 30 years of experience. Experts in recovering data from HDD, SSD, RAID, CCTV, and mobile devices.">
        <meta property="og:type" content="website">
        <meta property="og:url" content="{{ url()->current() }}">
        <meta property="og:site_name" content="LifeGuard Data Recovery">
        <meta property="og:image" content="{{ asset('media-bundle/media/pages/aboutus.webp') }}">
        <meta name="twitter:card" content="summary_large_image">
    @endif

    <!-- Global LocalBusiness & ProfessionalService Schema.org JSON-LD -->
    <script type="application/ld+json">
    {!! json_encode(\App\Services\SchemaService::getLocalBusinessSchema(), JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) !!}
    </script>

    <!-- Global WebSite Schema.org JSON-LD (SearchAction & Sitelinks) -->
    <script type="application/ld+json">
    {!! json_encode(\App\Services\SchemaService::getWebSiteSchema(), JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) !!}
    </script>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800;900&family=Red+Hat+Display:wght@600;700;800;900&display=swap" rel="stylesheet">

    <!-- Vite Pipeline (Compiled Tailwind CSS & Bundled Lucide Icons) -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <!-- Alpine.js -->
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.14.8/dist/cdn.min.js"></script>

    <style>
        [x-cloak] { display: none !important; }
        .prose pre { background-color: #0b1329; color: #f8fafc; border-radius: 0.75rem; padding: 1.25rem; overflow-x: auto; }
        .prose img { border-radius: 0.75rem; box-shadow: 0 4px 6px -1px rgb(0 0 0 / 0.1); margin: 2rem auto; }
    </style>
    @stack('styles')
</head>
<body class="min-h-full flex flex-col font-sans selection:bg-cyan-500 selection:text-white bg-slate-50" x-data="{ mobileMenuOpen: false }">

    <!-- Top Hello Bar / 24/7 Cleanroom Status -->
    <div class="bg-gradient-to-r from-slate-950 via-slate-900 to-slate-950 text-white text-xs py-2 px-4 border-b border-cyan-500/20 relative z-50">
        <div class="max-w-7xl mx-auto flex items-center justify-between gap-4">
            <div class="flex items-center gap-2 overflow-hidden truncate">
                <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-extrabold bg-cyan-500/20 text-cyan-300 border border-cyan-400/30 shrink-0">
                    <span class="w-1.5 h-1.5 rounded-full bg-emerald-400 animate-pulse mr-1.5"></span> ISO-5 CLASS 100 CLEANROOM
                </span>
                <span class="text-slate-300 truncate">
                    Dubai's #1 Certified Data Recovery Lab • <strong>No Data, No Charge Policy</strong> • 98.6% Proven Success Rate
                </span>
            </div>
            <div class="hidden sm:flex items-center gap-4 shrink-0 text-slate-300 font-medium">
                <a href="tel:+97143933082" class="flex items-center gap-1.5 hover:text-white transition font-bold text-cyan-400">
                    <i data-lucide="phone-call" class="w-3.5 h-3.5"></i>
                    <span>+971 43 933 082</span>
                </a>
                <span class="text-slate-700">|</span>
                <a href="https://wa.me/971554838545" target="_blank" class="text-emerald-400 hover:text-emerald-300 font-semibold transition flex items-center gap-1">
                    <i data-lucide="message-circle" class="w-3.5 h-3.5"></i>
                    <span>WhatsApp 24/7</span>
                </a>
                <span class="text-slate-700">|</span>
                <a href="{{ route('contact') }}" class="text-cyan-300 hover:text-white font-semibold transition flex items-center gap-1">
                    <span>Free Evaluation</span>
                    <i data-lucide="arrow-right" class="w-3.5 h-3.5"></i>
                </a>
            </div>
        </div>
    </div>

    <!-- Main Navigation Header -->
    <header class="sticky top-0 z-40 bg-white/95 backdrop-blur-md border-b border-slate-200/80 transition-all shadow-xs" x-data="{ servicesOpen: false, locationsOpen: false }">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex items-center justify-between h-20">
                
                <!-- Brand Logo (LifeGuard Data Recovery) -->
                <div class="flex items-center gap-6 xl:gap-8">
                    <a href="{{ route('home') }}" class="flex items-center gap-3 shrink-0 py-1 group" title="LifeGuard Data Recovery Dubai">
                        <picture>
                            <source srcset="{{ asset('images/logo.webp') }}" type="image/webp">
                            <img src="{{ asset('images/logo.png') }}" alt="LifeGuard Data Recovery Dubai" class="h-10 sm:h-12 w-auto object-contain">
                        </picture>
                    </a>

                    <!-- Desktop Nav Links -->
                    <nav class="hidden lg:flex items-center gap-5 xl:gap-6 text-sm font-semibold text-slate-700">
                        @php
                            $navMenus = \App\Models\MenuItem::whereNull('parent_id')
                                ->where('is_active', true)
                                ->with(['activeChildren'])
                                ->orderBy('order')
                                ->get();
                        @endphp

                        @if($navMenus->isNotEmpty())
                            @foreach($navMenus as $item)
                                @if($item->activeChildren->isNotEmpty())
                                    <!-- Dropdown Menu Item -->
                                    <div class="relative" 
                                         x-data="{ open: false }" 
                                         @mouseenter="open = true" 
                                         @mouseleave="open = false">
                                        <button type="button" @click="open = !open" 
                                                class="inline-flex items-center gap-1 transition hover:text-cyan-600 py-2 {{ $item->isActive() ? 'text-cyan-600 font-bold' : '' }}">
                                            <span>{{ $item->title }}</span>
                                            <i data-lucide="chevron-down" class="w-3.5 h-3.5 transition-transform duration-200" :class="open ? 'rotate-180' : ''"></i>
                                        </button>

                                        <!-- Dropdown Panel -->
                                        <div x-show="open" x-cloak 
                                             x-transition:enter="transition ease-out duration-150"
                                             x-transition:enter-start="opacity-0 translate-y-2"
                                             x-transition:enter-end="opacity-100 translate-y-0"
                                             x-transition:leave="transition ease-in duration-100"
                                             x-transition:leave-start="opacity-100 translate-y-0"
                                             x-transition:leave-end="opacity-0 translate-y-2"
                                             class="absolute -left-6 top-full w-72 sm:w-80 bg-white rounded-2xl border border-slate-200 shadow-2xl p-3 space-y-1 z-50">
                                            @foreach($item->activeChildren as $child)
                                                <a href="{{ $child->getUrl() }}" class="flex items-center gap-2.5 p-2 rounded-xl hover:bg-cyan-50/70 transition group">
                                                    <div class="w-7 h-7 rounded-lg bg-slate-100 group-hover:bg-cyan-600 group-hover:text-white text-slate-600 flex items-center justify-center shrink-0 transition-colors">
                                                        <i data-lucide="shield-check" class="w-3.5 h-3.5"></i>
                                                    </div>
                                                    <span class="text-xs font-semibold text-slate-800 group-hover:text-cyan-700 transition truncate">{{ $child->title }}</span>
                                                </a>
                                            @endforeach
                                        </div>
                                    </div>
                                @else
                                    <!-- Standard Menu Link -->
                                    <a href="{{ $item->getUrl() }}" target="{{ $item->target }}" 
                                       class="transition hover:text-cyan-600 py-2 {{ $item->isActive() ? 'text-cyan-600 font-bold' : '' }}">
                                        {{ $item->title }}
                                        @if($item->badge)
                                            <span class="ml-1 text-[9px] font-extrabold px-1.5 py-0.5 rounded-full bg-cyan-100 text-cyan-800">{{ $item->badge }}</span>
                                        @endif
                                    </a>
                                @endif
                            @endforeach
                        @else
                            <a href="{{ route('home') }}" class="transition hover:text-cyan-600 py-2 font-bold">Home</a>
                            <a href="{{ route('about') }}" class="transition hover:text-cyan-600 py-2">About Us</a>
                            <a href="{{ route('services.index') }}" class="transition hover:text-cyan-600 py-2">Services</a>
                            <a href="{{ route('locations.index') }}" class="transition hover:text-cyan-600 py-2">Locations</a>
                            <a href="/data-recovery-rate-calculator" class="transition hover:text-cyan-600 py-2">Rate Calculator</a>
                            <a href="{{ route('blog.index') }}" class="transition hover:text-cyan-600 py-2">Blog</a>
                            <a href="{{ route('contact') }}" class="transition hover:text-cyan-600 py-2">Contact Us</a>
                        @endif
                    </nav>
                </div>

                <!-- Right Side: Emergency Hotline & Free Evaluation CTA -->
                <div class="hidden lg:flex items-center gap-3 xl:gap-4 shrink-0">
                    <!-- Call Hotline -->
                    <a href="tel:+97143933082" class="flex items-center gap-2 text-xs font-bold text-slate-800 hover:text-cyan-600 px-3 py-2 rounded-xl hover:bg-slate-100 transition">
                        <div class="w-9 h-9 rounded-full bg-cyan-500/10 text-cyan-600 flex items-center justify-center">
                            <i data-lucide="phone-call" class="w-4 h-4"></i>
                        </div>
                        <div class="text-left">
                            <span class="block text-[10px] text-slate-400 uppercase font-semibold leading-none">Emergency Hotline</span>
                            <span class="font-extrabold text-sm text-slate-900 leading-tight">+971 43 933 082</span>
                        </div>
                    </a>

                    @auth
                        <div class="flex items-center gap-3 pl-2 border-l border-slate-200">
                            @if(auth()->user()->canManageContent())
                                <a href="{{ route('admin.dashboard') }}" class="inline-flex items-center gap-1.5 px-3.5 py-2 text-xs font-bold rounded-xl bg-slate-950 text-cyan-400 hover:bg-slate-900 transition shadow-sm border border-cyan-500/30">
                                    <i data-lucide="layout-dashboard" class="w-3.5 h-3.5"></i>
                                    Admin CMS
                                </a>
                            @endif
                            <form action="{{ route('logout') }}" method="POST" class="inline">
                                @csrf
                                <button type="submit" class="p-2 text-slate-500 hover:text-rose-600 transition rounded-lg hover:bg-slate-100" title="Logout">
                                    <i data-lucide="log-out" class="w-4 h-4"></i>
                                </button>
                            </form>
                        </div>
                    @else
                        <!-- Action CTA Button -->
                        <a href="{{ route('contact') }}" 
                           class="inline-flex items-center gap-2 px-5 py-2.5 rounded-full bg-gradient-to-r from-cyan-600 via-blue-600 to-indigo-600 hover:from-cyan-700 hover:to-indigo-700 text-white text-xs font-extrabold shadow-md shadow-cyan-600/30 hover:shadow-lg transition-all transform hover:-translate-y-0.5 whitespace-nowrap">
                            <i data-lucide="microchip" class="w-4 h-4 stroke-[2.2]"></i>
                            <span>Free Evaluation & Quote</span>
                        </a>
                    @endif
                </div>

                <!-- Mobile Hamburger Button -->
                <div class="lg:hidden flex items-center gap-2">
                    <a href="tel:+971554838545" class="p-2 text-cyan-600 rounded-lg hover:bg-cyan-50">
                        <i data-lucide="phone-call" class="w-5 h-5"></i>
                    </a>
                    <button type="button" @click="mobileMenuOpen = !mobileMenuOpen" :aria-expanded="mobileMenuOpen.toString()" aria-controls="mobile-navigation" aria-label="Toggle navigation menu" class="p-2 text-slate-700 hover:text-slate-900 rounded-lg hover:bg-slate-100">
                        <i data-lucide="menu" class="w-6 h-6" x-show="!mobileMenuOpen"></i>
                        <i data-lucide="x" class="w-6 h-6" x-show="mobileMenuOpen" x-cloak></i>
                    </button>
                </div>

            </div>
        </div>

        <!-- Mobile Navigation Menu Dropdown -->
        <div id="mobile-navigation" x-show="mobileMenuOpen" x-cloak class="lg:hidden border-t border-slate-200 bg-white px-4 pt-3 pb-6 space-y-3 shadow-xl">
            <div class="flex flex-col space-y-2 text-sm font-semibold text-slate-700">
                @if(isset($navMenus) && $navMenus->isNotEmpty())
                    @foreach($navMenus as $item)
                        @if($item->activeChildren->isNotEmpty())
                            <div x-data="{ open: false }" class="py-1">
                                <button @click="open = !open" class="flex items-center justify-between w-full py-1 text-slate-900 font-bold">
                                    <span>{{ $item->title }}</span>
                                    <i data-lucide="chevron-down" class="w-4 h-4 transition-transform" :class="open ? 'rotate-180' : ''"></i>
                                </button>
                                <div x-show="open" class="pl-4 pt-2 pb-1 space-y-1.5 border-l-2 border-cyan-400 ml-1">
                                    @foreach($item->activeChildren as $child)
                                        <a href="{{ $child->getUrl() }}" class="block py-1 text-xs text-slate-600 hover:text-cyan-600">
                                            {{ $child->title }}
                                        </a>
                                    @endforeach
                                </div>
                            </div>
                        @else
                            <a href="{{ $item->getUrl() }}" class="py-1 hover:text-cyan-600 {{ $item->isActive() ? 'text-cyan-600 font-bold' : '' }}">
                                {{ $item->title }}
                            </a>
                        @endif
                    @endforeach
                @else
                    <a href="{{ route('home') }}" class="py-1 hover:text-cyan-600">Home</a>
                    <a href="{{ route('about') }}" class="py-1 hover:text-cyan-600">About Us</a>
                    <a href="{{ route('service.index') }}" class="py-1 hover:text-cyan-600">Services</a>
                    <a href="/data-recovery-rate-calculator" class="py-1 hover:text-cyan-600">Rate Calculator</a>
                    <a href="{{ route('blog.index') }}" class="py-1 hover:text-cyan-600">Blog</a>
                    <a href="{{ route('contact') }}" class="py-1 hover:text-cyan-600">Contact Us</a>
                @endif
            </div>

            <div class="pt-4 border-t border-slate-100 space-y-2">
                <a href="{{ route('contact') }}" class="block w-full py-3 rounded-xl bg-gradient-to-r from-cyan-600 to-blue-600 text-white text-center font-extrabold text-xs shadow-md">
                    Book Free Evaluation & Quote
                </a>
                <a href="tel:+97143933082" class="block w-full py-3 rounded-xl bg-slate-900 text-white text-center font-bold text-xs">
                    Call Hotline: +971 43 933 082
                </a>
            </div>
        </div>
    </header>

    <!-- Main Content Area -->
    <main class="flex-grow">
        @if(session('success'))
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 mt-6">
                <div class="p-4 rounded-2xl bg-emerald-50 border border-emerald-200 text-emerald-800 text-sm flex items-center justify-between">
                    <div class="flex items-center gap-2">
                        <i data-lucide="check-circle" class="w-5 h-5 text-emerald-600"></i>
                        <span>{{ session('success') }}</span>
                    </div>
                </div>
            </div>
        @endif

        @yield('content')
    </main>

    <!-- Global Footer -->
    <footer class="bg-slate-950 text-white border-t border-slate-800 mt-20">
        <!-- Pre-footer CTA Strip -->
        <div class="border-b border-slate-800 bg-gradient-to-r from-slate-950 via-slate-900 to-slate-950 py-12 px-4">
            <div class="max-w-7xl mx-auto flex flex-col md:flex-row items-center justify-between gap-6 text-center md:text-left">
                <div>
                    <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-cyan-500/20 text-cyan-400 text-xs font-bold mb-3 border border-cyan-500/30">
                        <span class="w-2 h-2 rounded-full bg-emerald-400 animate-pulse"></span>
                        <span>Emergency Data Recovery Lab</span>
                    </div>
                    <h3 class="text-2xl sm:text-3xl font-extrabold font-display text-white">Lost Critical Data? Don't Power On Your Device!</h3>
                    <p class="text-xs sm:text-sm text-slate-300 mt-1 max-w-xl">
                        Immediate assistance for clicking hard drives, dead SSDs, crashed RAID servers, and corrupted CCTV systems in Dubai.
                    </p>
                </div>
                <div class="flex flex-wrap items-center justify-center gap-3">
                    <a href="{{ route('contact') }}" class="px-7 py-3.5 rounded-full bg-gradient-to-r from-cyan-500 to-blue-600 hover:from-cyan-400 hover:to-blue-500 text-white text-xs font-extrabold shadow-lg shadow-cyan-500/25 transition">
                        Get Free Evaluation
                    </a>
                    <a href="tel:+97143933082" class="px-7 py-3.5 rounded-full bg-slate-900 hover:bg-slate-800 border border-slate-700 text-white text-xs font-bold transition flex items-center gap-2">
                        <i data-lucide="phone-call" class="w-4 h-4 text-cyan-400"></i>
                        <span>+971 43 933 082</span>
                    </a>
                </div>
            </div>
        </div>

        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-16">
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-5 gap-10">
                
                <!-- Col 1: Brand & Heritage -->
                <div class="space-y-4 lg:col-span-2">
                    <a href="{{ route('home') }}" class="flex items-center gap-3">
                        <img src="{{ asset('images/logo.png') }}" alt="LifeGuard Data Recovery Dubai" class="h-10 w-auto brightness-0 invert object-contain">
                    </a>
                    <p class="text-xs text-slate-400 leading-relaxed max-w-sm">
                        LifeGuard Data Recovery is Dubai’s leading certified data retrieval lab. Equipped with state-of-the-art ISO-5 Class 100 cleanrooms and proprietary hardware diagnostics to recover data from Hard Drives, SSDs, RAID servers, NAS, and mobile devices under a strict No Data, No Charge guarantee.
                    </p>
                    <div class="space-y-2 text-xs text-slate-300 pt-2">
                        <div class="flex items-center gap-2">
                            <i data-lucide="phone-call" class="w-4 h-4 text-cyan-400"></i>
                            <span class="font-bold text-white">+971 43 933 082 / +971 55 483 8545</span>
                        </div>
                        <div class="flex items-center gap-2">
                            <i data-lucide="mail" class="w-4 h-4 text-cyan-400"></i>
                            <a href="mailto:info@lifeguarddatarecovery.com" class="text-slate-300 hover:underline">info@lifeguarddatarecovery.com</a>
                        </div>
                        <div class="flex items-start gap-2">
                            <i data-lucide="map-pin" class="w-4 h-4 text-cyan-400 shrink-0 mt-0.5"></i>
                            <span class="text-slate-300">Dubai (Main Office): Bayat center S25 Computer Street, Bur Dubai</span>
                        </div>
                        <div class="flex items-start gap-2">
                            <i data-lucide="building-2" class="w-4 h-4 text-cyan-400 shrink-0 mt-0.5"></i>
                            <span class="text-slate-300">Al Barsha: DasselDorf Business Point, Behind Mall Of Emirates, Dubai</span>
                        </div>
                    </div>
                </div>

                <!-- Col 2: Specialized Services -->
                <div>
                    <h4 class="text-xs font-bold text-white uppercase tracking-wider mb-4 border-b border-slate-800 pb-2">Recovery Services</h4>
                    <ul class="space-y-2.5 text-xs text-slate-400">
                        @foreach(\App\Models\Service::published()->ordered()->take(7)->get() as $s)
                            <li>
                                <a href="{{ url($s->slug) }}" class="hover:text-white transition flex items-center gap-1.5">
                                    <span class="text-cyan-400">&rsaquo;</span>
                                    <span>{{ $s->title }}</span>
                                </a>
                            </li>
                        @endforeach
                        <li>
                            <a href="{{ route('service.index') }}" class="text-cyan-400 font-semibold hover:underline block pt-1">
                                View All Services &rarr;
                            </a>
                        </li>
                    </ul>
                </div>

                <!-- Col 3: Key Pages & Calculators -->
                <div>
                    <h4 class="text-xs font-bold text-white uppercase tracking-wider mb-4 border-b border-slate-800 pb-2">Quick Navigation</h4>
                    <ul class="space-y-2.5 text-xs text-slate-400">
                        <li><a href="/data-recovery-rate-calculator" class="hover:text-cyan-400 transition flex items-center gap-1.5"><span class="text-cyan-400">&rsaquo;</span> Cost Rate Calculator</a></li>
                        <li><a href="/about-us" class="hover:text-cyan-400 transition flex items-center gap-1.5"><span class="text-cyan-400">&rsaquo;</span> Cleanroom & Company</a></li>
                        <li><a href="/faq" class="hover:text-cyan-400 transition flex items-center gap-1.5"><span class="text-cyan-400">&rsaquo;</span> Frequently Asked Questions</a></li>
                        <li><a href="/blog" class="hover:text-cyan-400 transition flex items-center gap-1.5"><span class="text-cyan-400">&rsaquo;</span> Data Recovery Knowledge Base</a></li>
                        <li><a href="/video-testimonials-lifeguard" class="hover:text-cyan-400 transition flex items-center gap-1.5"><span class="text-cyan-400">&rsaquo;</span> Video Testimonials</a></li>
                        <li><a href="/contact" class="hover:text-cyan-400 transition flex items-center gap-1.5"><span class="text-cyan-400">&rsaquo;</span> Lab Drop-Off & Pickup</a></li>
                        <li><a href="/terms-conditions" class="hover:text-cyan-400 transition flex items-center gap-1.5"><span class="text-cyan-400">&rsaquo;</span> Privacy & NDA Terms</a></li>
                    </ul>
                </div>

                <!-- Col 4: Regional Offices & Labs -->
                <div>
                    <h4 class="text-xs font-bold text-white uppercase tracking-wider mb-4 border-b border-slate-800 pb-2">Regional Offices & Labs</h4>
                    <ul class="space-y-2 text-xs text-slate-400">
                        <li><a href="{{ route('locations.index') }}" class="text-cyan-400 font-bold hover:underline flex items-center gap-1">&bull; All Locations Overview &rarr;</a></li>
                        <li><a href="/data-recovery-dubai" class="hover:text-cyan-400 transition">&bull; Dubai (Main Office - Bur Dubai)</a></li>
                        <li><a href="/data-recovery-al-barsha" class="hover:text-cyan-400 transition">&bull; Al Barsha, Dubai (Behind MOE)</a></li>
                        <li><a href="/data-recovery-abu-dhabi" class="hover:text-cyan-400 transition">&bull; Abu Dhabi (Electra Street)</a></li>
                        <li><a href="/data-recovery-sharjah" class="hover:text-cyan-400 transition">&bull; Sharjah (Al Majaz Waterfront)</a></li>
                        <li><a href="/data-recovery-al-wasl" class="hover:text-cyan-400 transition">&bull; Al Wasl, Dubai (Sheikh Zayed Rd)</a></li>
                        <li><a href="/data-recovery-india" class="hover:text-cyan-400 transition">&bull; India (CTC Park Ln, Secunderabad)</a></li>
                        <li><a href="/data-recovery-service-in-kenya" class="hover:text-cyan-400 transition">&bull; Kenya (Mirage Tower, Nairobi)</a></li>
                    </ul>
                </div>

            </div>

            <!-- Bottom Legal & Compliance -->
            <div class="pt-10 mt-12 border-t border-slate-900 flex flex-col md:flex-row items-center justify-between text-xs text-slate-500 gap-4">
                <div class="flex items-center gap-4 flex-wrap">
                    <p>&copy; {{ date('Y') }} LifeGuard Data Recovery Dubai. All rights reserved.</p>
                    <span class="text-slate-700 hidden sm:inline">&bull;</span>
                    <span class="text-slate-400">Class 100 Cleanroom Certified | 100% Confidentiality & Non-Disclosure Protection</span>
                </div>
                <div class="flex items-center gap-6">
                    <a href="{{ route('about') }}" class="hover:text-slate-300">About Us</a>
                    <a href="{{ route('services.index') }}" class="hover:text-slate-300">Services</a>
                    <a href="{{ route('contact') }}" class="hover:text-slate-300">Contact Us</a>
                    <a href="{{ route('login') }}" class="text-slate-600 hover:text-slate-400">Staff Portal</a>
                </div>
            </div>
        </div>
    </footer>

    <!-- Floating WhatsApp CTA -->
    <a href="https://wa.me/971554838545?text=Hello%20LifeGuard%20Data%20Recovery%2C%20I%20have%20an%20urgent%20data%20recovery%20inquiry." 
       target="_blank" 
       rel="noopener noreferrer"
       class="fixed bottom-6 right-6 z-50 flex items-center gap-2 px-4 py-3 bg-emerald-500 hover:bg-emerald-600 text-white rounded-full shadow-2xl hover:scale-105 transition-all group font-bold text-xs"
       aria-label="Chat with Data Recovery Specialist on WhatsApp">
        <i data-lucide="message-circle" class="w-5 h-5 fill-current"></i>
        <span class="hidden sm:inline">WhatsApp 24/7 Specialist</span>
    </a>

    <script>
        if (typeof lucide !== 'undefined' && typeof lucide.createIcons === 'function') {
            lucide.createIcons();
        } else if (window.lucide && typeof window.lucide.createIcons === 'function') {
            window.lucide.createIcons();
        }
    </script>
    @stack('scripts')
</body>
</html>
