@extends('layouts.app')

@section('seo_meta')
    <title>About LifeGuard Data Recovery Dubai | Certified Cleanroom Laboratory</title>
    <meta name="description" content="Learn about LifeGuard Data Recovery Dubai. 30+ years of hardware data recovery experience, certified ISO-5 Class 100 cleanroom facilities, and advanced forensic capabilities.">
    <meta name="keywords" content="about lifeguard data recovery, data recovery lab dubai, cleanroom data recovery uae, data recovery experts dubai">
    <link rel="canonical" href="{{ route('about') }}">
    <meta property="og:title" content="About LifeGuard Data Recovery Dubai | Cleanroom Lab & Forensic Specialists">
    <meta property="og:description" content="Over 30 years of specialized data recovery from mechanical hard drives, dead SSDs, and enterprise RAID arrays across Dubai and UAE.">
    <meta property="og:url" content="{{ route('about') }}">
    <meta property="og:type" content="website">

    <!-- Schema.org BreadcrumbList JSON-LD -->
    <script type="application/ld+json">
    {!! json_encode(\App\Services\SchemaService::getBreadcrumbSchema([
        'Home' => '/',
        'About Us' => '/about-us'
    ]), JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) !!}
    </script>

    <!-- Custom Schema.org JSON-LD (from Dashboard) -->
    <x-schema-json-ld :schema="$page->schema_markup ?? null" />
@endsection

@section('content')
<div class="bg-slate-50 min-h-screen space-y-20">

    <!-- Hero Section -->
    <section class="relative bg-gradient-to-b from-slate-950 via-slate-900 to-slate-950 text-white pt-24 pb-20 overflow-hidden">
        <div class="absolute -top-32 right-10 w-96 h-96 bg-cyan-500/15 rounded-full blur-[140px] pointer-events-none"></div>

        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 relative z-10">
            <!-- Breadcrumbs -->
            <nav class="flex items-center gap-2 text-xs text-slate-400 mb-8" aria-label="Breadcrumb">
                <a href="{{ route('home') }}" class="hover:text-white transition">Home</a>
                <i data-lucide="chevron-right" class="w-3.5 h-3.5 text-slate-600"></i>
                <span class="text-cyan-400 font-bold">About Us</span>
            </nav>

            <div class="max-w-3xl space-y-4">
                <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full text-xs font-bold bg-cyan-500/20 text-cyan-300 border border-cyan-500/30">
                    <span class="w-2 h-2 rounded-full bg-cyan-400 animate-pulse"></span>
                    <span>30+ YEARS OF DATA RECOVERY LEADERSHIP IN UAE</span>
                </div>
                <h1 class="text-3xl sm:text-5xl lg:text-6xl font-black font-display tracking-tight leading-tight">
                    Rescuing Critical Data <span class="text-transparent bg-clip-text bg-gradient-to-r from-cyan-400 via-sky-300 to-blue-400">When Failure Isn't an Option</span>
                </h1>
                <p class="text-sm sm:text-base text-slate-300 leading-relaxed font-normal">
                    LifeGuard Data Recovery was founded with a singular commitment: to deliver guaranteed, confidential, and scientifically rigorous data recovery services to individuals, multinational corporations, banks, and government agencies across Dubai, Abu Dhabi, and the entire UAE.
                </p>
                <div class="pt-4 flex flex-wrap items-center gap-4">
                    <a href="{{ route('services.index') }}" class="px-7 py-3.5 rounded-full bg-gradient-to-r from-cyan-500 to-blue-600 hover:from-cyan-400 hover:to-blue-500 text-white font-extrabold text-xs shadow-lg shadow-cyan-500/25 transition flex items-center gap-2">
                        <span>Explore Recovery Services</span>
                        <i data-lucide="arrow-right" class="w-4 h-4"></i>
                    </a>
                    <a href="{{ route('contact') }}" class="px-6 py-3.5 rounded-full bg-white/10 hover:bg-white/20 text-white font-bold text-xs border border-white/20 transition">
                        <span>Contact Laboratory</span>
                    </a>
                </div>
            </div>

            <!-- Stats Bar -->
            <div class="mt-16 pt-10 border-t border-slate-800 grid grid-cols-2 md:grid-cols-4 gap-6 text-slate-300">
                <div>
                    <div class="text-3xl sm:text-4xl font-black text-white font-display">30+ Years</div>
                    <div class="text-xs text-slate-400 mt-1">Combined Technical Experience</div>
                </div>
                <div>
                    <div class="text-3xl sm:text-4xl font-black text-cyan-400 font-display">98.6%</div>
                    <div class="text-xs text-slate-400 mt-1">Verified Recovery Success Rate</div>
                </div>
                <div>
                    <div class="text-3xl sm:text-4xl font-black text-white font-display">Class 100</div>
                    <div class="text-xs text-slate-400 mt-1">ISO-5 Certified Cleanroom</div>
                </div>
                <div>
                    <div class="text-3xl sm:text-4xl font-black text-emerald-400 font-display">100%</div>
                    <div class="text-xs text-slate-400 mt-1">No Data, No Charge Guarantee</div>
                </div>
            </div>
        </div>
    </section>

    <!-- Company Narrative -->
    <section class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="grid grid-cols-1 lg:grid-cols-12 gap-12 items-center">
            <div class="lg:col-span-6 space-y-6">
                <span class="text-xs font-extrabold uppercase tracking-widest text-cyan-600">The LifeGuard Standard</span>
                <h2 class="text-3xl sm:text-4xl font-black text-slate-900 tracking-tight">
                    Dubai’s Most Technologically Advanced Data Recovery Facility
                </h2>
                <p class="text-sm text-slate-600 leading-relaxed">
                    At LifeGuard Data Recovery, we specialize in fast, secure, and reliable data recovery across Dubai, Abu Dhabi, Sharjah, and GCC. When storage drives experience mechanical failure, head crashes, corrupted firmware, or catastrophic water damage, software tools cannot solve the problem—they can permanently destroy the data.
                </p>
                <p class="text-sm text-slate-600 leading-relaxed">
                    Our laboratory is equipped with certified Class 100 cleanroom laminar flow workbenches, DeepSpar Disk Imagers, PC-3000 Flash systems, and an inventory of over 10,000 donor drives. Every case is treated under strict non-disclosure security standards.
                </p>

                <div class="space-y-3 pt-2">
                    <div class="flex items-center gap-3 text-sm font-semibold text-slate-800">
                        <i data-lucide="check-circle" class="w-5 h-5 text-cyan-600 shrink-0"></i>
                        <span>Zero Risk: Free comprehensive diagnostic evaluation before any commitment</span>
                    </div>
                    <div class="flex items-center gap-3 text-sm font-semibold text-slate-800">
                        <i data-lucide="check-circle" class="w-5 h-5 text-cyan-600 shrink-0"></i>
                        <span>Guaranteed Results: You only pay when your verified data is retrieved</span>
                    </div>
                    <div class="flex items-center gap-3 text-sm font-semibold text-slate-800">
                        <i data-lucide="check-circle" class="w-5 h-5 text-cyan-600 shrink-0"></i>
                        <span>24/7 Priority Emergency Support for corporate servers & enterprise RAID</span>
                    </div>
                </div>
            </div>

            <div class="lg:col-span-6">
                <div class="rounded-3xl overflow-hidden shadow-2xl border border-slate-200">
                    <img src="{{ asset('media-bundle/media/pages/aboutus.webp') }}"
                         alt="LifeGuard Data Recovery Laboratory Dubai" 
                         class="w-full h-96 object-cover">
                </div>
            </div>
        </div>
    </section>

    <!-- Lab Locations Overview -->
    <section class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 pb-12">
        <div class="bg-white rounded-3xl p-8 sm:p-12 border border-slate-200 shadow-sm space-y-8">
            <div class="text-center max-w-2xl mx-auto space-y-2">
                <h3 class="text-2xl font-black text-slate-900">Visit Our Dubai Laboratories</h3>
                <p class="text-xs sm:text-sm text-slate-500">Convenient walk-in locations with on-site diagnostics and free parking</p>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                <!-- Location 1 -->
                <div class="p-6 rounded-2xl bg-slate-50 border border-slate-200 space-y-3">
                    <div class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full bg-cyan-100 text-cyan-800 text-xs font-bold">
                        <i data-lucide="map-pin" class="w-3.5 h-3.5"></i>
                        <span>Main Lab & Headquarters</span>
                    </div>
                    <h4 class="text-lg font-black text-slate-900">Bur Dubai Lab (Computer Street)</h4>
                    <p class="text-xs text-slate-600">
                        Bayat Center S25, Computer Street - Bur Dubai, Dubai, United Arab Emirates
                    </p>
                    <div class="text-xs text-slate-700 pt-2 space-y-1">
                        <div><strong>Phone:</strong> +971 43 933 082 / +971 55 483 8545</div>
                        <div><strong>Hours:</strong> Mon - Sat: 9:00 AM - 9:00 PM (24/7 Hotline)</div>
                    </div>
                </div>

                <!-- Location 2 -->
                <div class="p-6 rounded-2xl bg-slate-50 border border-slate-200 space-y-3">
                    <div class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full bg-blue-100 text-blue-800 text-xs font-bold">
                        <i data-lucide="map-pin" class="w-3.5 h-3.5"></i>
                        <span>Branch Office</span>
                    </div>
                    <h4 class="text-lg font-black text-slate-900">Al Barsha Branch (Near Mall of Emirates)</h4>
                    <p class="text-xs text-slate-600">
                        Dusseldorf Business Point, Al Barsha 1, Behind Mall of Emirates, Dubai, UAE
                    </p>
                    <div class="text-xs text-slate-700 pt-2 space-y-1">
                        <div><strong>Phone:</strong> +971 56 410 1040 / +971 50 217 6793</div>
                        <div><strong>Hours:</strong> Mon - Sat: 9:30 AM - 8:30 PM</div>
                    </div>
                </div>
            </div>
        </div>
    </section>

</div>
@endsection
