@extends('layouts.app')

@section('seo_meta')
    @php
        $pageTitle = 'LifeGuard Data Recovery Locations | Dubai, Abu Dhabi, Sharjah, India & Kenya';
        $pageDescription = 'Visit our official LifeGuard Data Recovery cleanroom laboratories and drop-off branches in Dubai (Bur Dubai, Al Barsha, Al Wasl), Abu Dhabi, Sharjah, India, and Kenya. Free diagnostics & No Data No Charge.';
        $canonicalUrl = route('locations.index');
    @endphp
    <title>{{ $pageTitle }}</title>
    <meta name="title" content="{{ $pageTitle }}">
    <meta name="description" content="{{ $pageDescription }}">
    <meta name="keywords" content="data recovery locations, data recovery dubai address, data recovery abu dhabi phone, data recovery sharjah, data recovery al barsha, data recovery india, data recovery kenya">
    <link rel="canonical" href="{{ $canonicalUrl }}">
    <meta name="robots" content="index, follow">

    <!-- Open Graph -->
    <meta property="og:type" content="website">
    <meta property="og:url" content="{{ $canonicalUrl }}">
    <meta property="og:title" content="{{ $pageTitle }}">
    <meta property="og:description" content="{{ $pageDescription }}">
    <meta property="og:site_name" content="LifeGuard Data Recovery">

    <!-- Twitter -->
    <meta name="twitter:card" content="summary_large_image">
    <meta name="twitter:title" content="{{ $pageTitle }}">
    <meta name="twitter:description" content="{{ $pageDescription }}">

    <!-- Schema.org BreadcrumbList JSON-LD -->
    <script type="application/ld+json">
    {!! json_encode(\App\Services\SchemaService::getBreadcrumbSchema([
        'Home' => '/',
        'Locations' => '/locations'
    ]), JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) !!}
    </script>
@endsection

@section('content')
<div class="bg-slate-950 text-slate-100 min-h-screen">

    <!-- Hero Section -->
    <section class="relative pt-24 pb-20 bg-gradient-to-b from-slate-950 via-slate-900 to-slate-950 overflow-hidden border-b border-slate-800/80">
        <div class="absolute inset-0 bg-[radial-gradient(ellipse_80%_80%_at_50%_-20%,rgba(14,165,233,0.15),rgba(255,255,255,0))] pointer-events-none"></div>

        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 relative z-10 text-center">
            <!-- Breadcrumbs -->
            <nav class="flex items-center justify-center gap-2 text-xs font-semibold text-slate-400 mb-6" aria-label="Breadcrumb">
                <a href="{{ route('home') }}" class="hover:text-cyan-400 transition">Home</a>
                <i data-lucide="chevron-right" class="w-3.5 h-3.5 text-slate-600"></i>
                <span class="text-cyan-400 font-bold">Office Locations & Labs</span>
            </nav>

            <span class="inline-flex items-center gap-2 px-3.5 py-1.5 rounded-full text-xs font-bold bg-cyan-500/10 text-cyan-400 border border-cyan-500/30 mb-6 backdrop-blur-sm">
                <span class="w-2 h-2 rounded-full bg-emerald-400 animate-pulse"></span>
                <span>GLOBAL NETWORK &bull; ISO-5 CLASS 100 CLEANROOM LABS</span>
            </span>

            <h1 class="text-3xl sm:text-5xl lg:text-6xl font-black font-display tracking-tight text-white max-w-4xl mx-auto leading-tight">
                Our Office Locations & <span class="text-transparent bg-clip-text bg-gradient-to-r from-cyan-400 via-sky-300 to-blue-500">Recovery Labs</span>
            </h1>

            <p class="mt-6 text-sm sm:text-base text-slate-300 max-w-2xl mx-auto leading-relaxed">
                Connect directly with our certified engineers. Drop off your drive at any of our official branches across Dubai, Abu Dhabi, Sharjah, India, and Kenya, or request free express courier collection with zero upfront diagnostic fees.
            </p>

            <!-- Quick Trust Bar -->
            <div class="mt-10 flex flex-wrap items-center justify-center gap-6 text-xs text-slate-300 font-medium">
                <div class="flex items-center gap-2">
                    <i data-lucide="shield-check" class="w-4 h-4 text-emerald-400"></i>
                    <span>No Data, No Charge Guarantee</span>
                </div>
                <div class="flex items-center gap-2">
                    <i data-lucide="microchip" class="w-4 h-4 text-cyan-400"></i>
                    <span>ISO Class 100 Cleanrooms</span>
                </div>
                <div class="flex items-center gap-2">
                    <i data-lucide="clock" class="w-4 h-4 text-sky-400"></i>
                    <span>Free Evaluation & Diagnostics</span>
                </div>
                <div class="flex items-center gap-2">
                    <i data-lucide="lock" class="w-4 h-4 text-indigo-400"></i>
                    <span>100% Strict NDA Privacy</span>
                </div>
            </div>
        </div>
    </section>

    <!-- Official Verified Branch Cards Section (Transcribed from User Spec) -->
    <section class="py-20 max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center max-w-3xl mx-auto mb-16">
            <span class="text-xs font-bold uppercase tracking-widest text-cyan-400 block">Verified Physical Offices</span>
            <h2 class="text-2xl sm:text-4xl font-black text-white font-display tracking-tight mt-2">
                LifeGuard Official Regional Centers
            </h2>
            <p class="mt-3 text-slate-400 text-xs sm:text-sm">
                Each center provides professional hardware intake, cleanroom diagnostics, secure storage, and direct access to senior data recovery engineers.
            </p>
        </div>

        <!-- The 7 Official Cards Grid matching user screenshot -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            @foreach($locations as $key => $loc)
                <div class="bg-slate-900/90 rounded-2xl border border-slate-800/90 p-6 sm:p-7 shadow-xl hover:border-cyan-500/50 hover:shadow-2xl hover:shadow-cyan-500/10 transition-all duration-300 flex flex-col justify-between group relative overflow-hidden">
                    <!-- Subtle Corner Gradient Glow -->
                    <div class="absolute top-0 right-0 w-32 h-32 bg-cyan-500/5 rounded-full blur-2xl group-hover:bg-cyan-500/15 transition-all"></div>

                    <div class="space-y-5 relative z-10">
                        <!-- Card Header: Flag & Title -->
                        <div class="flex items-start justify-between gap-3 border-b border-slate-800 pb-4">
                            <div class="flex items-center gap-3">
                                <span class="text-2xl shrink-0 select-none" role="img" aria-label="{{ $loc['country'] }}">{{ $loc['flag'] }}</span>
                                <div>
                                    <h3 class="text-lg font-black text-white font-display group-hover:text-cyan-400 transition">
                                        {{ $loc['office_title'] }}
                                    </h3>
                                    <span class="text-[10px] uppercase tracking-wider font-bold text-cyan-500/80 block">
                                        {{ $loc['tag'] }}
                                    </span>
                                </div>
                            </div>
                            @if(!empty($loc['is_headquarters']))
                                <span class="px-2 py-0.5 rounded-full text-[9px] font-black uppercase tracking-wider bg-emerald-500/20 text-emerald-400 border border-emerald-500/30 shrink-0">
                                    HQ LAB
                                </span>
                            @endif
                        </div>

                        <!-- Card Body: Contact Items -->
                        <div class="space-y-3.5 text-xs text-slate-300">
                            <!-- Address -->
                            <div class="flex items-start gap-3">
                                <div class="w-7 h-7 rounded-lg bg-cyan-500/10 text-cyan-400 flex items-center justify-center shrink-0 mt-0.5">
                                    <i data-lucide="map-pin" class="w-3.5 h-3.5"></i>
                                </div>
                                <div class="leading-relaxed">
                                    <span class="text-slate-200">{{ $loc['address'] }}</span>
                                </div>
                            </div>

                            <!-- Phone -->
                            <div class="flex items-center gap-3">
                                <div class="w-7 h-7 rounded-lg bg-cyan-500/10 text-cyan-400 flex items-center justify-center shrink-0">
                                    <i data-lucide="phone" class="w-3.5 h-3.5"></i>
                                </div>
                                <div>
                                    <a href="tel:{{ $loc['phone_link'] }}" class="font-bold text-white hover:text-cyan-400 transition">
                                        {{ $loc['phone'] }}
                                    </a>
                                </div>
                            </div>

                            <!-- Fax (if applicable) -->
                            @if(!empty($loc['fax']))
                                <div class="flex items-center gap-3">
                                    <div class="w-7 h-7 rounded-lg bg-cyan-500/10 text-cyan-400 flex items-center justify-center shrink-0">
                                        <i data-lucide="send" class="w-3.5 h-3.5"></i>
                                    </div>
                                    <div class="text-slate-300">
                                        <span class="text-slate-400 text-[11px]">Fax:</span>
                                        <span class="font-semibold text-slate-200 ml-1">{{ $loc['fax'] }}</span>
                                    </div>
                                </div>
                            @endif

                            <!-- Website (if country specific) -->
                            @if(!empty($loc['website']))
                                <div class="flex items-center gap-3">
                                    <div class="w-7 h-7 rounded-lg bg-cyan-500/10 text-cyan-400 flex items-center justify-center shrink-0">
                                        <i data-lucide="globe" class="w-3.5 h-3.5"></i>
                                    </div>
                                    <div>
                                        <a href="{{ $loc['website_url'] }}" target="_blank" rel="noopener noreferrer" class="font-semibold text-cyan-400 hover:underline">
                                            {{ $loc['website'] }}
                                        </a>
                                    </div>
                                </div>
                            @endif

                            <!-- Email -->
                            <div class="flex items-center gap-3">
                                <div class="w-7 h-7 rounded-lg bg-cyan-500/10 text-cyan-400 flex items-center justify-center shrink-0">
                                    <i data-lucide="mail" class="w-3.5 h-3.5"></i>
                                </div>
                                <div class="truncate">
                                    <a href="mailto:{{ $loc['email'] }}" class="text-slate-300 hover:text-white hover:underline truncate">
                                        {{ $loc['email'] }}
                                    </a>
                                </div>
                            </div>

                            <!-- Hours -->
                            <div class="flex items-center gap-3 pt-1 border-t border-slate-800/60">
                                <div class="w-7 h-7 rounded-lg bg-slate-800 text-slate-400 flex items-center justify-center shrink-0">
                                    <i data-lucide="clock" class="w-3.5 h-3.5"></i>
                                </div>
                                <div class="text-[11px] text-slate-400">
                                    {{ $loc['hours'] }}
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Card Actions -->
                    <div class="pt-6 mt-6 border-t border-slate-800/80 relative z-10 space-y-2.5">
                        <div class="grid grid-cols-2 gap-2">
                            <a href="tel:{{ $loc['phone_link'] }}" class="py-2.5 px-3 rounded-xl text-xs font-bold bg-slate-800 hover:bg-slate-700 text-white text-center transition flex items-center justify-center gap-1.5 border border-slate-700">
                                <i data-lucide="phone-call" class="w-3.5 h-3.5 text-cyan-400"></i>
                                <span>Call Lab</span>
                            </a>
                            <a href="{{ $loc['whatsapp_link'] }}?text={{ urlencode('Hello LifeGuard ' . $loc['name'] . ', I need urgent data recovery assistance.') }}" target="_blank" rel="noopener noreferrer" class="py-2.5 px-3 rounded-xl text-xs font-bold bg-emerald-600/90 hover:bg-emerald-500 text-white text-center transition flex items-center justify-center gap-1.5 shadow-sm">
                                <i data-lucide="message-circle" class="w-3.5 h-3.5"></i>
                                <span>WhatsApp</span>
                            </a>
                        </div>
                        <a href="{{ url($loc['slug']) }}" class="w-full py-2.5 px-4 rounded-xl text-xs font-extrabold bg-gradient-to-r from-cyan-600 to-blue-600 hover:from-cyan-500 hover:to-blue-500 text-white text-center transition flex items-center justify-center gap-2 shadow-lg shadow-cyan-600/20">
                            <span>View Branch Page & All Services</span>
                            <i data-lucide="arrow-right" class="w-3.5 h-3.5 group-hover:translate-x-1 transition-transform"></i>
                        </a>
                    </div>
                </div>
            @endforeach
        </div>
    </section>

    <!-- All 10 Data Recovery Services Connected with Services Pages -->
    <section class="py-20 bg-slate-900 border-t border-b border-slate-800">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center max-w-3xl mx-auto mb-16">
                <span class="text-xs font-bold uppercase tracking-widest text-cyan-400 block">Comprehensive Capabilities</span>
                <h2 class="text-2xl sm:text-4xl font-black text-white font-display tracking-tight mt-2">
                    Data Recovery Services Available at Every Branch
                </h2>
                <p class="mt-3 text-slate-300 text-xs sm:text-sm">
                    No matter which LifeGuard office you visit, your device has direct access to our specialized Class 100 cleanroom equipment, microcode repair rigs, and certified recovery engineers.
                </p>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                @foreach($services as $svc)
                    <div class="bg-slate-950 rounded-2xl border border-slate-800 p-6 flex flex-col justify-between hover:border-cyan-500/40 transition group">
                        <div class="space-y-4">
                            <div class="flex items-center justify-between gap-3">
                                <div class="w-10 h-10 rounded-xl bg-cyan-500/10 text-cyan-400 flex items-center justify-center">
                                    <i data-lucide="{{ $svc['icon'] }}" class="w-5 h-5"></i>
                                </div>
                                <span class="px-2.5 py-1 rounded-full text-[10px] font-bold bg-slate-800 text-slate-300 border border-slate-700">
                                    {{ $svc['category'] }}
                                </span>
                            </div>

                            <div>
                                <h3 class="text-base font-bold text-white group-hover:text-cyan-400 transition">
                                    <a href="{{ $svc['url'] }}">{{ $svc['title'] }}</a>
                                </h3>
                                <p class="text-xs text-slate-400 mt-2 leading-relaxed">
                                    {{ $svc['description'] }}
                                </p>
                            </div>

                            <div class="pt-3 border-t border-slate-900 text-[11px] space-y-1.5">
                                <div class="text-slate-400"><strong class="text-slate-300">Failure Signs:</strong> {{ $svc['symptoms'] }}</div>
                                <div class="text-slate-400"><strong class="text-slate-300">Turnaround:</strong> <span class="text-cyan-400">{{ $svc['turnaround'] }}</span></div>
                            </div>
                        </div>

                        <div class="pt-5 mt-5 border-t border-slate-800/80 flex items-center justify-between">
                            <a href="{{ $svc['url'] }}" class="text-xs font-bold text-cyan-400 hover:text-cyan-300 flex items-center gap-1.5 transition">
                                <span>Full Service Details</span>
                                <i data-lucide="arrow-right" class="w-3.5 h-3.5 group-hover:translate-x-1 transition-transform"></i>
                            </a>
                            <span class="text-[10px] text-emerald-400 font-semibold flex items-center gap-1">
                                <i data-lucide="check-circle-2" class="w-3 h-3"></i>
                                <span>Cleanroom Ready</span>
                            </span>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </section>

    <!-- LifeGuard 4-Step Recovery Process -->
    <section class="py-20 max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center max-w-3xl mx-auto mb-16">
            <span class="text-xs font-bold uppercase tracking-widest text-cyan-400 block">Seamless Workflow</span>
            <h2 class="text-2xl sm:text-4xl font-black text-white font-display tracking-tight mt-2">
                How Our Regional Recovery Works
            </h2>
            <p class="mt-3 text-slate-400 text-xs sm:text-sm">
                Transparent 4-step protocol from drop-off to cleanroom extraction and secure file delivery.
            </p>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
            <div class="bg-slate-900/60 p-6 rounded-2xl border border-slate-800 space-y-3">
                <div class="w-10 h-10 rounded-full bg-cyan-500/20 text-cyan-400 font-black flex items-center justify-center text-sm border border-cyan-500/30">1</div>
                <h3 class="text-sm font-bold text-white">Free Intake & Evaluation</h3>
                <p class="text-xs text-slate-400 leading-relaxed">
                    Drop off at any branch or request free courier collection. We assess hardware damage with zero upfront charge.
                </p>
            </div>

            <div class="bg-slate-900/60 p-6 rounded-2xl border border-slate-800 space-y-3">
                <div class="w-10 h-10 rounded-full bg-cyan-500/20 text-cyan-400 font-black flex items-center justify-center text-sm border border-cyan-500/30">2</div>
                <h3 class="text-sm font-bold text-white">Transparent Quote</h3>
                <p class="text-xs text-slate-400 leading-relaxed">
                    Receive a firm written quote and file list estimate. If you choose not to proceed, device is returned with no fee.
                </p>
            </div>

            <div class="bg-slate-900/60 p-6 rounded-2xl border border-slate-800 space-y-3">
                <div class="w-10 h-10 rounded-full bg-cyan-500/20 text-cyan-400 font-black flex items-center justify-center text-sm border border-cyan-500/30">3</div>
                <h3 class="text-sm font-bold text-white">Cleanroom Extraction</h3>
                <p class="text-xs text-slate-400 leading-relaxed">
                    Performed in ISO-5 Class 100 cleanrooms using PC-3000 systems. Sector-by-sector cloning preserves the original media.
                </p>
            </div>

            <div class="bg-slate-900/60 p-6 rounded-2xl border border-slate-800 space-y-3">
                <div class="w-10 h-10 rounded-full bg-cyan-500/20 text-cyan-400 font-black flex items-center justify-center text-sm border border-cyan-500/30">4</div>
                <h3 class="text-sm font-bold text-white">Verification & Return</h3>
                <p class="text-xs text-slate-400 leading-relaxed">
                    Verify recovered files remotely or in lab. Data is delivered on an encrypted external drive. No Data, No Charge!
                </p>
            </div>
        </div>
    </section>

    <!-- Full UAE Emirate & Area Internal Linking Strip -->
    <section class="py-16 bg-slate-900/40 border-t border-slate-800/80">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-6">
            <div class="text-center sm:text-left">
                <h3 class="text-xs font-bold uppercase tracking-widest text-slate-400">
                    Additional UAE Emirate & District Recovery Pages:
                </h3>
            </div>
            <div class="flex flex-wrap gap-2.5">
                @foreach($uaeAreas as $area)
                    <a href="{{ $area['url'] }}" class="px-3 py-1.5 rounded-xl bg-slate-800/90 hover:bg-cyan-950 hover:text-cyan-300 hover:border-cyan-500/40 border border-slate-700/80 text-xs font-medium text-slate-300 transition">
                        &bull; {{ $area['title'] }}
                    </a>
                @endforeach
            </div>
        </div>
    </section>

    <!-- Emergency Dispatch CTA Strip -->
    <section class="py-16 bg-gradient-to-r from-cyan-950 via-slate-900 to-slate-950 border-t border-cyan-500/20">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex flex-col lg:flex-row items-center justify-between gap-8 text-center lg:text-left">
                <div class="space-y-2 max-w-2xl">
                    <span class="text-xs font-bold uppercase tracking-widest text-cyan-400 block">Emergency Response Hotline</span>
                    <h3 class="text-2xl sm:text-3xl font-black text-white font-display">
                        Need Immediate Data Recovery Assistance?
                    </h3>
                    <p class="text-xs sm:text-sm text-slate-300">
                        Our emergency cleanroom lab is on call 24 hours a day, 7 days a week. Call our central hotline or message our on-duty engineer on WhatsApp.
                    </p>
                </div>
                <div class="flex flex-wrap items-center justify-center gap-3 shrink-0">
                    <a href="tel:+97143933082" class="px-6 py-3 rounded-full text-xs font-bold bg-cyan-500 hover:bg-cyan-400 text-slate-950 transition flex items-center gap-2 shadow-lg shadow-cyan-500/20">
                        <i data-lucide="phone-call" class="w-4 h-4"></i>
                        <span>Call +971 43 933 082</span>
                    </a>
                    <a href="https://wa.me/971554838545?text=Hello%20LifeGuard%20Data%20Recovery%2C%20I%20need%20urgent%20assistance." target="_blank" rel="noopener noreferrer" class="px-6 py-3 rounded-full text-xs font-bold bg-emerald-600 hover:bg-emerald-500 text-white transition flex items-center gap-2">
                        <i data-lucide="message-circle" class="w-4 h-4"></i>
                        <span>WhatsApp 24/7 Specialist</span>
                    </a>
                </div>
            </div>
        </div>
    </section>

</div>
@endsection
