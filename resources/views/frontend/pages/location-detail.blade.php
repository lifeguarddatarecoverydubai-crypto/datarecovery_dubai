@extends('layouts.app')

@section('seo_meta')
    @php
        $pageTitle = $page->meta_title ?: 'Data Recovery Services in ' . $location['name'] . ' | LifeGuard Lab';
        $pageDescription = $page->meta_description ?: 'Professional Data Recovery in ' . $location['name'] . '. ISO-5 Class 100 Cleanroom Lab. HDD, SSD, RAID, NAS & Mobile. Office: ' . $location['address'] . '. Call ' . $location['phone'] . '. No Data, No Charge.';
        $canonicalUrl = url($page->slug);
        $locationSchema = \App\Services\LocationService::getSchema($location);
    @endphp
    <title>{{ \App\Support\SeoHelper::formatTitle($pageTitle, true) }}</title>
    <meta name="title" content="{{ \App\Support\SeoHelper::formatTitle($pageTitle, true) }}">
    <meta name="description" content="{{ $pageDescription }}">
    <meta name="keywords" content="data recovery {{ strtolower($location['name']) }}, hard drive recovery {{ strtolower($location['name']) }}, raid data recovery, ssd recovery {{ strtolower($location['city']) }}, cleanroom lab, lifeguard data recovery">
    <link rel="canonical" href="{{ $canonicalUrl }}">
    <meta name="robots" content="index, follow">

    <!-- Open Graph -->
    <meta property="og:type" content="website">
    <meta property="og:url" content="{{ $canonicalUrl }}">
    <meta property="og:title" content="{{ $pageTitle }}">
    <meta property="og:description" content="{{ $pageDescription }}">
    <meta property="og:image" content="{{ url('/images/logo.png') }}">
    <meta property="og:site_name" content="LifeGuard Data Recovery">

    <!-- Twitter -->
    <meta name="twitter:card" content="summary_large_image">
    <meta name="twitter:title" content="{{ $pageTitle }}">
    <meta name="twitter:description" content="{{ $pageDescription }}">

    <!-- Schema.org BreadcrumbList JSON-LD -->
    <script type="application/ld+json">
    {!! json_encode(\App\Services\SchemaService::getBreadcrumbSchema([
        'Home' => '/',
        'Locations' => '/locations',
        $location['name'] => '/' . $location['slug']
    ]), JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) !!}
    </script>

    <!-- Schema.org ProfessionalService / LocalBusiness JSON-LD -->
    <script type="application/ld+json">
    {!! json_encode($locationSchema, JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) !!}
    </script>
@endsection

@section('content')
<div class="bg-white text-slate-800">

    <!-- Top Hero Section (Dark Tech Aesthetics) -->
    <section class="relative bg-slate-950 text-white pt-24 pb-20 overflow-hidden border-b border-slate-800">
        <div class="absolute inset-0 bg-[radial-gradient(ellipse_80%_80%_at_50%_-20%,rgba(14,165,233,0.18),rgba(255,255,255,0))] pointer-events-none"></div>

        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 relative z-10">
            <!-- Breadcrumbs -->
            <nav class="flex items-center gap-2 text-xs font-semibold text-slate-400 mb-6 flex-wrap" aria-label="Breadcrumb">
                <a href="{{ route('home') }}" class="hover:text-cyan-400 transition">Home</a>
                <i data-lucide="chevron-right" class="w-3.5 h-3.5 text-slate-600"></i>
                <a href="{{ route('locations.index') }}" class="hover:text-cyan-400 transition">Locations</a>
                <i data-lucide="chevron-right" class="w-3.5 h-3.5 text-slate-600"></i>
                <span class="text-cyan-400 font-bold truncate">{{ $location['office_title'] }}</span>
            </nav>

            <div class="grid grid-cols-1 lg:grid-cols-12 gap-10 items-center">
                <!-- Left: Headline & Introduction -->
                <div class="lg:col-span-7 space-y-6">
                    <div class="inline-flex items-center gap-2 px-3.5 py-1.5 rounded-full text-xs font-bold bg-cyan-500/10 text-cyan-300 border border-cyan-500/30 backdrop-blur-sm">
                        <span class="w-2 h-2 rounded-full bg-emerald-400 animate-pulse"></span>
                        <span class="text-base select-none">{{ $location['flag'] }}</span>
                        <span>CERTIFIED LAB &bull; {{ strtoupper($location['office_title']) }}</span>
                    </div>

                    <h1 class="text-3xl sm:text-5xl font-black font-display tracking-tight text-white leading-tight">
                        Data Recovery Services in <span class="text-transparent bg-clip-text bg-gradient-to-r from-cyan-400 via-sky-300 to-blue-400">{{ $location['name'] }}</span>
                    </h1>

                    <p class="text-sm sm:text-base text-slate-300 leading-relaxed font-normal">
                        Professional Data Recovery {{ $location['city'] ?? 'Dubai' }}. {{ $location['headline'] }}. Equipped with state-of-the-art ISO-5 Class 100 laminar cleanrooms, hardware PC-3000 extraction workstations, and proprietary micro-soldering benches. We provide free initial diagnostic triage, guaranteed fixed-fee quotes, and our binding <strong class="text-white">"No Data, No Recovery Fee"</strong> policy.
                    </p>

                    <!-- Trust Metrics Grid -->
                    <div class="grid grid-cols-2 sm:grid-cols-4 gap-4 pt-4 border-t border-slate-800/80 text-xs text-slate-300">
                        <div>
                            <div class="text-2xl font-black text-cyan-400 font-display">96.8%</div>
                            <div class="text-[11px] text-slate-400 mt-0.5">Audited Success Rate</div>
                        </div>
                        <div>
                            <div class="text-2xl font-black text-emerald-400 font-display">ISO-5</div>
                            <div class="text-[11px] text-slate-400 mt-0.5">Class 100 Cleanroom</div>
                        </div>
                        <div>
                            <div class="text-2xl font-black text-sky-400 font-display">24 - 48h</div>
                            <div class="text-[11px] text-slate-400 mt-0.5">Standard Turnaround</div>
                        </div>
                        <div>
                            <div class="text-2xl font-black text-amber-400 font-display">100%</div>
                            <div class="text-[11px] text-slate-400 mt-0.5">Strict NDA Privacy</div>
                        </div>
                    </div>

                    <!-- Quick Action Buttons -->
                    <div class="flex flex-wrap items-center gap-3 pt-2">
                        <a href="tel:{{ $location['phone_link'] }}" class="px-6 py-3.5 rounded-xl font-bold text-xs bg-gradient-to-r from-cyan-500 to-blue-600 hover:from-cyan-400 hover:to-blue-500 text-slate-950 hover:text-black shadow-lg shadow-cyan-500/20 transition flex items-center gap-2">
                            <i data-lucide="phone-call" class="w-4 h-4"></i>
                            <span>Call Lab: {{ $location['phone'] }}</span>
                        </a>
                        <a href="{{ $location['whatsapp_link'] }}?text={{ urlencode('Hello LifeGuard ' . $location['name'] . ', I need urgent data recovery assistance.') }}" target="_blank" rel="noopener noreferrer" class="px-6 py-3.5 rounded-xl font-bold text-xs bg-emerald-600 hover:bg-emerald-500 text-white transition flex items-center gap-2 shadow-sm">
                            <i data-lucide="message-circle" class="w-4 h-4"></i>
                            <span>WhatsApp Senior Tech</span>
                        </a>
                        <a href="#services-section" class="px-5 py-3.5 rounded-xl font-bold text-xs bg-slate-900 hover:bg-slate-800 text-slate-300 hover:text-white border border-slate-700 transition flex items-center gap-1.5">
                            <span>Explore 10 Services</span>
                            <i data-lucide="arrow-down" class="w-3.5 h-3.5"></i>
                        </a>
                    </div>
                </div>

                <!-- Right: Official Branch Office Card (Matching User Screenshot Spec) -->
                <div class="lg:col-span-5">
                    <div class="bg-slate-900/95 rounded-3xl border border-slate-700/80 p-7 shadow-2xl space-y-6 relative overflow-hidden group">
                        <div class="absolute -top-12 -right-12 w-44 h-44 bg-cyan-500/10 rounded-full blur-3xl pointer-events-none"></div>

                        <!-- Card Header -->
                        <div class="flex items-start justify-between gap-3 border-b border-slate-800 pb-5">
                            <div class="flex items-center gap-3">
                                <span class="text-3xl shrink-0 select-none">{{ $location['flag'] }}</span>
                                <div>
                                    <h2 class="text-xl font-black text-white font-display">
                                        {{ $location['office_title'] }}
                                    </h2>
                                    <span class="text-[11px] font-bold text-cyan-400 block mt-0.5">
                                        {{ $location['badge'] }}
                                    </span>
                                </div>
                            </div>
                            <span class="px-2.5 py-1 rounded-full text-[9px] font-black uppercase tracking-wider bg-cyan-500/20 text-cyan-300 border border-cyan-500/30 shrink-0">
                                Official Center
                            </span>
                        </div>

                        <!-- Card Details -->
                        <div class="space-y-4 text-xs text-slate-300">
                            <!-- Address -->
                            <div class="flex items-start gap-3.5">
                                <div class="w-8 h-8 rounded-xl bg-cyan-500/10 text-cyan-400 flex items-center justify-center shrink-0 mt-0.5">
                                    <i data-lucide="map-pin" class="w-4 h-4"></i>
                                </div>
                                <div>
                                    <span class="text-[10px] font-bold uppercase tracking-wider text-slate-400 block">Physical Address</span>
                                    <p class="font-medium text-slate-100 leading-relaxed mt-0.5">{{ $location['address'] }}</p>
                                    @if(!empty($location['landmark']))
                                        <span class="text-[11px] text-cyan-400/90 block mt-1">Landmark: {{ $location['landmark'] }}</span>
                                    @endif
                                </div>
                            </div>

                            <!-- Phone -->
                            <div class="flex items-start gap-3.5">
                                <div class="w-8 h-8 rounded-xl bg-cyan-500/10 text-cyan-400 flex items-center justify-center shrink-0">
                                    <i data-lucide="phone" class="w-4 h-4"></i>
                                </div>
                                <div>
                                    <span class="text-[10px] font-bold uppercase tracking-wider text-slate-400 block">Direct Telephone Hotline</span>
                                    <a href="tel:{{ $location['phone_link'] }}" class="font-bold text-white hover:text-cyan-400 text-sm transition">
                                        {{ $location['phone'] }}
                                    </a>
                                    @if(!empty($location['hotline']) && $location['hotline'] !== $location['phone'])
                                        <span class="text-[11px] text-slate-400 block mt-0.5">24/7 Central Dispatch: {{ $location['hotline'] }}</span>
                                    @endif
                                </div>
                            </div>

                            <!-- Fax (if present) -->
                            @if(!empty($location['fax']))
                                <div class="flex items-center gap-3.5">
                                    <div class="w-8 h-8 rounded-xl bg-cyan-500/10 text-cyan-400 flex items-center justify-center shrink-0">
                                        <i data-lucide="send" class="w-4 h-4"></i>
                                    </div>
                                    <div>
                                        <span class="text-[10px] font-bold uppercase tracking-wider text-slate-400 block">Official Fax Line</span>
                                        <span class="font-semibold text-slate-200">{{ $location['fax'] }}</span>
                                    </div>
                                </div>
                            @endif

                            <!-- Website (if present) -->
                            @if(!empty($location['website']))
                                <div class="flex items-center gap-3.5">
                                    <div class="w-8 h-8 rounded-xl bg-cyan-500/10 text-cyan-400 flex items-center justify-center shrink-0">
                                        <i data-lucide="globe" class="w-4 h-4"></i>
                                    </div>
                                    <div>
                                        <span class="text-[10px] font-bold uppercase tracking-wider text-slate-400 block">Regional Domain</span>
                                        <a href="{{ $location['website_url'] }}" target="_blank" rel="noopener noreferrer" class="font-bold text-cyan-400 hover:underline">
                                            {{ $location['website'] }}
                                        </a>
                                    </div>
                                </div>
                            @endif

                            <!-- Email -->
                            <div class="flex items-center gap-3.5">
                                <div class="w-8 h-8 rounded-xl bg-cyan-500/10 text-cyan-400 flex items-center justify-center shrink-0">
                                    <i data-lucide="mail" class="w-4 h-4"></i>
                                </div>
                                <div class="truncate">
                                    <span class="text-[10px] font-bold uppercase tracking-wider text-slate-400 block">Lab Inquiries Email</span>
                                    <a href="mailto:{{ $location['email'] }}" class="font-semibold text-slate-200 hover:text-white hover:underline truncate">
                                        {{ $location['email'] }}
                                    </a>
                                </div>
                            </div>

                            <!-- Working Hours -->
                            <div class="flex items-center gap-3.5 pt-2 border-t border-slate-800">
                                <div class="w-8 h-8 rounded-xl bg-slate-800 text-slate-400 flex items-center justify-center shrink-0">
                                    <i data-lucide="clock" class="w-4 h-4"></i>
                                </div>
                                <div>
                                    <span class="text-[10px] font-bold uppercase tracking-wider text-slate-400 block">Working Hours</span>
                                    <span class="font-medium text-slate-300 text-[11px]">{{ $location['hours'] }}</span>
                                </div>
                            </div>
                        </div>

                        <!-- Courier Alert Banner -->
                        <div class="p-4 rounded-2xl bg-cyan-950/40 border border-cyan-800/40 text-[11px] text-cyan-200 space-y-1">
                            <div class="flex items-center gap-1.5 font-bold text-cyan-300">
                                <i data-lucide="truck" class="w-4 h-4"></i>
                                <span>Free Express Courier Collection</span>
                            </div>
                            <p class="text-slate-300 text-[10px] leading-relaxed">
                                Can't visit our lab in person? We provide free express courier pickup across {{ $location['name'] }} with anti-static secure transit boxes.
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- 3 Convenient Intake Methods -->
    <section class="py-14 bg-slate-50 border-b border-slate-200">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <!-- Method 1: Drop Off -->
                <div class="bg-white p-6 rounded-2xl border border-slate-200 shadow-sm space-y-3">
                    <div class="w-10 h-10 rounded-xl bg-cyan-100 text-cyan-700 flex items-center justify-center font-bold">
                        <i data-lucide="building-2" class="w-5 h-5"></i>
                    </div>
                    <h3 class="text-sm font-bold text-slate-900">1. Direct Lab Drop-Off</h3>
                    <p class="text-xs text-slate-600 leading-relaxed">
                        Bring your damaged device directly to our {{ $location['name'] }} reception counter. Our on-duty technician will log your device and perform immediate initial intake inspection.
                    </p>
                </div>

                <!-- Method 2: Free Courier Pickup -->
                <div class="bg-white p-6 rounded-2xl border border-slate-200 shadow-sm space-y-3">
                    <div class="w-10 h-10 rounded-xl bg-emerald-100 text-emerald-700 flex items-center justify-center font-bold">
                        <i data-lucide="truck" class="w-5 h-5"></i>
                    </div>
                    <h3 class="text-sm font-bold text-slate-900">2. Free Courier Pickup</h3>
                    <p class="text-xs text-slate-600 leading-relaxed">
                        Book a secure collection from your home or office in {{ $location['city'] }}. Our driver arrives with protective shock-proof cases and provides a formal transfer receipt.
                    </p>
                </div>

                <!-- Method 3: Enterprise On-Site Priority -->
                <div class="bg-white p-6 rounded-2xl border border-slate-200 shadow-sm space-y-3">
                    <div class="w-10 h-10 rounded-xl bg-indigo-100 text-indigo-700 flex items-center justify-center font-bold">
                        <i data-lucide="server" class="w-5 h-5"></i>
                    </div>
                    <h3 class="text-sm font-bold text-slate-900">3. Emergency Enterprise Team</h3>
                    <p class="text-xs text-slate-600 leading-relaxed">
                        For mission-critical datacenter, government, or hospital server arrays where drives cannot leave the building, our senior engineers deploy on-site triage kits.
                    </p>
                </div>
            </div>
        </div>
    </section>

    <!-- Comprehensive All-Services Section (Deep Internal Linking to Every Service Page) -->
    <section id="services-section" class="py-20 max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center max-w-3xl mx-auto mb-16 space-y-3">
            <span class="text-xs font-bold uppercase tracking-widest text-cyan-600 block">Class 100 Cleanroom Certified Lab</span>
            <h2 class="text-2xl sm:text-4xl font-black text-slate-900 font-display tracking-tight">
                Specialized Data Recovery Services at this Location
            </h2>
            <p class="text-slate-600 text-xs sm:text-sm leading-relaxed">
                Our {{ $location['name'] }} facility is equipped to handle physical, electronic, and logical failures across all data storage categories. Click any service below for full technical details, case studies, and recovery procedures.
            </p>
        </div>

        <!-- 10 Comprehensive Service Blocks with Deep Internal Linking -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
            @foreach($services as $index => $svc)
                <div class="bg-slate-50 rounded-3xl border border-slate-200/90 p-7 hover:border-cyan-500/50 hover:bg-white hover:shadow-xl transition-all duration-300 flex flex-col justify-between group">
                    <div class="space-y-5">
                        <!-- Service Header -->
                        <div class="flex items-start justify-between gap-4">
                            <div class="flex items-center gap-3.5">
                                <div class="w-12 h-12 rounded-2xl bg-cyan-600/10 text-cyan-600 group-hover:bg-cyan-600 group-hover:text-white transition-colors duration-200 flex items-center justify-center shrink-0">
                                    <i data-lucide="{{ $svc['icon'] }}" class="w-6 h-6 stroke-[2.2]"></i>
                                </div>
                                <div>
                                    <span class="text-[10px] font-bold uppercase tracking-wider text-slate-400 block">{{ $svc['category'] }}</span>
                                    <h3 class="text-lg font-black text-slate-900 group-hover:text-cyan-700 transition">
                                        <a href="{{ $svc['url'] }}">{{ $svc['title'] }}</a>
                                    </h3>
                                </div>
                            </div>
                            <span class="px-2.5 py-1 rounded-full text-[10px] font-bold bg-slate-200 text-slate-700 shrink-0">
                                Service #{{ $index + 1 }}
                            </span>
                        </div>

                        <!-- Description -->
                        <p class="text-xs text-slate-600 leading-relaxed">
                            {{ $svc['description'] }}
                        </p>

                        <!-- Technical Specs Grid -->
                        <div class="bg-white rounded-2xl p-4 border border-slate-200/80 space-y-2 text-xs">
                            <div class="flex items-start gap-2">
                                <span class="text-slate-400 font-semibold shrink-0">Failure Signs:</span>
                                <span class="text-slate-700">{{ $svc['symptoms'] }}</span>
                            </div>
                            <div class="flex items-start gap-2">
                                <span class="text-slate-400 font-semibold shrink-0">Supported Brands:</span>
                                <span class="text-slate-600">{{ $svc['brands'] }}</span>
                            </div>
                            <div class="flex items-center justify-between pt-2 border-t border-slate-100 text-[11px]">
                                <span class="text-slate-400 font-medium">Turnaround Time:</span>
                                <span class="font-bold text-cyan-700">{{ $svc['turnaround'] }}</span>
                            </div>
                        </div>
                    </div>

                    <!-- Action Link (Connected to Official Service Page) -->
                    <div class="pt-6 mt-6 border-t border-slate-200/80 flex flex-wrap items-center justify-between gap-3">
                        <a href="{{ $svc['url'] }}" class="inline-flex items-center gap-1.5 text-xs font-black text-cyan-600 hover:text-cyan-700 transition group-hover:translate-x-0.5">
                            <span>Explore {{ $svc['title'] }} Details</span>
                            <i data-lucide="arrow-right" class="w-4 h-4"></i>
                        </a>

                        <a href="{{ $location['whatsapp_link'] }}?text={{ urlencode('Hello LifeGuard ' . $location['name'] . ', I need assistance with ' . $svc['title']) }}" target="_blank" rel="noopener noreferrer" class="inline-flex items-center gap-1 text-[11px] font-bold text-emerald-600 hover:text-emerald-700 transition">
                            <i data-lucide="message-circle" class="w-3.5 h-3.5"></i>
                            <span>Inquire for this Service</span>
                        </a>
                    </div>
                </div>
            @endforeach
        </div>
    </section>

    <!-- Technical SLA & Device Comparison Matrix -->
    <section class="py-16 bg-slate-900 text-white border-t border-b border-slate-800">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-10">
            <div class="text-center max-w-3xl mx-auto">
                <span class="text-xs font-bold uppercase tracking-widest text-cyan-400 block">Service Capability Matrix</span>
                <h2 class="text-2xl sm:text-3xl font-black font-display tracking-tight text-white mt-1">
                    Turnaround SLAs & Cleanroom Standards in {{ $location['name'] }}
                </h2>
                <p class="text-xs sm:text-sm text-slate-300 mt-2">
                    Every incoming media is isolated in our hardware write-blocking bays before cleanroom sector imaging.
                </p>
            </div>

            <div class="overflow-x-auto rounded-2xl border border-slate-800 shadow-xl">
                <table class="w-full text-left text-xs text-slate-300">
                    <thead class="bg-slate-950 text-slate-400 uppercase text-[10px] tracking-wider border-b border-slate-800">
                        <tr>
                            <th class="py-3.5 px-4 font-bold">Storage Category</th>
                            <th class="py-3.5 px-4 font-bold">Common Failure Modes</th>
                            <th class="py-3.5 px-4 font-bold">Lab Technology Applied</th>
                            <th class="py-3.5 px-4 font-bold">Turnaround</th>
                            <th class="py-3.5 px-4 font-bold text-right">Service Link</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-800/80 bg-slate-900/60">
                        @foreach($services as $svc)
                            <tr class="hover:bg-slate-800/50 transition">
                                <td class="py-3 px-4 font-bold text-white flex items-center gap-2">
                                    <i data-lucide="{{ $svc['icon'] }}" class="w-3.5 h-3.5 text-cyan-400 shrink-0"></i>
                                    <span>{{ $svc['title'] }}</span>
                                </td>
                                <td class="py-3 px-4 text-slate-300 max-w-xs truncate">{{ $svc['symptoms'] }}</td>
                                <td class="py-3 px-4 text-slate-400">Class 100 / PC-3000</td>
                                <td class="py-3 px-4 font-semibold text-cyan-400">{{ $svc['turnaround'] }}</td>
                                <td class="py-3 px-4 text-right">
                                    <a href="{{ $svc['url'] }}" class="text-cyan-400 hover:text-cyan-300 font-bold hover:underline inline-flex items-center gap-1">
                                        <span>View Page</span>
                                        <i data-lucide="chevron-right" class="w-3 h-3"></i>
                                    </a>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </section>

    <!-- Why Choose LifeGuard in this Location -->
    <section class="py-20 max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center max-w-3xl mx-auto mb-16 space-y-3">
            <span class="text-xs font-bold uppercase tracking-widest text-cyan-600 block">The LifeGuard Advantage</span>
            <h2 class="text-2xl sm:text-4xl font-black text-slate-900 font-display tracking-tight">
                Why Choose LifeGuard in {{ $location['name'] }}?
            </h2>
            <p class="text-slate-600 text-xs sm:text-sm">
                We combine industry-leading cleanroom facilities with clear, risk-free terms to protect your most critical corporate databases and personal records.
            </p>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
            <div class="p-6 rounded-2xl bg-slate-50 border border-slate-200 space-y-3">
                <div class="w-10 h-10 rounded-xl bg-cyan-100 text-cyan-600 flex items-center justify-center font-bold">
                    <i data-lucide="shield-check" class="w-5 h-5"></i>
                </div>
                <h3 class="text-base font-bold text-slate-900">No Data, No Fee Guarantee</h3>
                <p class="text-xs text-slate-600 leading-relaxed">
                    If our engineers are unable to recover your verified required files, there is zero recovery charge. You inspect the recovered file listing before any payment is made.
                </p>
            </div>

            <div class="p-6 rounded-2xl bg-slate-50 border border-slate-200 space-y-3">
                <div class="w-10 h-10 rounded-xl bg-cyan-100 text-cyan-600 flex items-center justify-center font-bold">
                    <i data-lucide="microchip" class="w-5 h-5"></i>
                </div>
                <h3 class="text-base font-bold text-slate-900">ISO-5 Class 100 Cleanroom</h3>
                <p class="text-xs text-slate-600 leading-relaxed">
                    Mechanical hard drive platter swaps and delicate read/write head transplants require an environment with under 100 airborne dust particles per cubic foot to prevent head crashes.
                </p>
            </div>

            <div class="p-6 rounded-2xl bg-slate-50 border border-slate-200 space-y-3">
                <div class="w-10 h-10 rounded-xl bg-cyan-100 text-cyan-600 flex items-center justify-center font-bold">
                    <i data-lucide="lock" class="w-5 h-5"></i>
                </div>
                <h3 class="text-base font-bold text-slate-900">100% Strict NDA Protection</h3>
                <p class="text-xs text-slate-600 leading-relaxed">
                    All data is treated with military-grade confidentiality. We sign formal Non-Disclosure Agreements for legal firms, medical records, financial institutions, and government bodies.
                </p>
            </div>

            <div class="p-6 rounded-2xl bg-slate-50 border border-slate-200 space-y-3">
                <div class="w-10 h-10 rounded-xl bg-cyan-100 text-cyan-600 flex items-center justify-center font-bold">
                    <i data-lucide="cpu" class="w-5 h-5"></i>
                </div>
                <h3 class="text-base font-bold text-slate-900">Hardware PC-3000 Systems</h3>
                <p class="text-xs text-slate-600 leading-relaxed">
                    We utilize authorized ACE Lab PC-3000 Express, PC-3000 Flash, PC-3000 SAS, and Rusolut visual NAND readers for low-level firmware microcode manipulation.
                </p>
            </div>

            <div class="p-6 rounded-2xl bg-slate-50 border border-slate-200 space-y-3">
                <div class="w-10 h-10 rounded-xl bg-cyan-100 text-cyan-600 flex items-center justify-center font-bold">
                    <i data-lucide="clock" class="w-5 h-5"></i>
                </div>
                <h3 class="text-base font-bold text-slate-900">Free Diagnostic Assessment</h3>
                <p class="text-xs text-slate-600 leading-relaxed">
                    Drop off your device or request courier pickup. Our engineers provide a clear diagnosis report detailing the failure type, recovery probability, and a fixed quotation.
                </p>
            </div>

            <div class="p-6 rounded-2xl bg-slate-50 border border-slate-200 space-y-3">
                <div class="w-10 h-10 rounded-xl bg-cyan-100 text-cyan-600 flex items-center justify-center font-bold">
                    <i data-lucide="users" class="w-5 h-5"></i>
                </div>
                <h3 class="text-base font-bold text-slate-900">15+ to 30+ Years Experience</h3>
                <p class="text-xs text-slate-600 leading-relaxed">
                    Our engineering team brings decades of combined recovery expertise, having solved over 25,000 complex data loss incidents with an audited 96.8% success rate.
                </p>
            </div>
        </div>
    </section>

    <!-- Local Areas Served Grid -->
    <section class="py-16 bg-slate-50 border-t border-slate-200">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-6">
            <div class="max-w-3xl">
                <span class="text-xs font-bold uppercase tracking-widest text-cyan-600 block">Regional Logistics Coverage</span>
                <h2 class="text-xl sm:text-2xl font-black text-slate-900 font-display mt-1">
                    Areas Served by Our {{ $location['name'] }} Branch
                </h2>
                <p class="text-xs text-slate-600 mt-1">
                    We provide free express courier pickup, same-day delivery, and drop-off access across the following districts and business hubs:
                </p>
            </div>

            <div class="flex flex-wrap gap-2 text-xs">
                @foreach($location['areas'] as $area)
                    <span class="px-3 py-1.5 rounded-xl bg-white border border-slate-200 font-semibold text-slate-700 flex items-center gap-1.5 shadow-sm">
                        <i data-lucide="check" class="w-3.5 h-3.5 text-emerald-500"></i>
                        <span>{{ $area }}</span>
                    </span>
                @endforeach
            </div>
        </div>
    </section>

    <!-- Frequently Asked Questions (FAQ) Section -->
    <section class="py-20 max-w-5xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center max-w-2xl mx-auto mb-14 space-y-2">
            <span class="text-xs font-bold uppercase tracking-widest text-cyan-600 block">Got Questions?</span>
            <h2 class="text-2xl sm:text-3xl font-black text-slate-900 font-display tracking-tight">
                Frequently Asked Questions for {{ $location['name'] }}
            </h2>
        </div>

        <div class="space-y-4" x-data="{ active: null }">
            <div class="bg-slate-50 rounded-2xl border border-slate-200 overflow-hidden">
                <button @click="active = (active === 1 ? null : 1)" class="w-full p-5 text-left font-bold text-sm text-slate-900 flex items-center justify-between gap-4">
                    <span>How much does data recovery cost at your {{ $location['name'] }} office?</span>
                    <i data-lucide="chevron-down" class="w-4 h-4 text-slate-500 transition-transform" :class="active === 1 ? 'rotate-180' : ''"></i>
                </button>
                <div x-show="active === 1" x-cloak class="px-5 pb-5 text-xs text-slate-600 leading-relaxed border-t border-slate-200/60 pt-3">
                    Evaluation at our lab is completely free with zero obligation. Pricing depends strictly on the physical condition of the device (logical file system damage, firmware failure, or mechanical cleanroom head replacement). After evaluation, we provide a guaranteed, fixed quotation. If we cannot retrieve your critical data, our No Data, No Charge guarantee applies!
                </div>
            </div>

            <div class="bg-slate-50 rounded-2xl border border-slate-200 overflow-hidden">
                <button @click="active = (active === 2 ? null : 2)" class="w-full p-5 text-left font-bold text-sm text-slate-900 flex items-center justify-between gap-4">
                    <span>How long will it take to recover my data?</span>
                    <i data-lucide="chevron-down" class="w-4 h-4 text-slate-500 transition-transform" :class="active === 2 ? 'rotate-180' : ''"></i>
                </button>
                <div x-show="active === 2" x-cloak class="px-5 pb-5 text-xs text-slate-600 leading-relaxed border-t border-slate-200/60 pt-3">
                    Our standard turnaround time is typically 24 to 48 hours. For mission-critical emergencies (e.g. corporate RAID servers, business accounting databases), we offer 24/7 priority emergency service with turnarounds as fast as 12 to 24 hours.
                </div>
            </div>

            <div class="bg-slate-50 rounded-2xl border border-slate-200 overflow-hidden">
                <button @click="active = (active === 3 ? null : 3)" class="w-full p-5 text-left font-bold text-sm text-slate-900 flex items-center justify-between gap-4">
                    <span>Can I walk into your {{ $location['name'] }} branch without an appointment?</span>
                    <i data-lucide="chevron-down" class="w-4 h-4 text-slate-500 transition-transform" :class="active === 3 ? 'rotate-180' : ''"></i>
                </button>
                <div x-show="active === 3" x-cloak class="px-5 pb-5 text-xs text-slate-600 leading-relaxed border-t border-slate-200/60 pt-3">
                    Yes! Walk-ins are always welcome during working hours ({{ $location['hours'] }}). Our customer intake counter at {{ $location['address'] }} will immediately receive your drive, assign a secure tracking barcode, and issue an official custody slip.
                </div>
            </div>

            <div class="bg-slate-50 rounded-2xl border border-slate-200 overflow-hidden">
                <button @click="active = (active === 4 ? null : 4)" class="w-full p-5 text-left font-bold text-sm text-slate-900 flex items-center justify-between gap-4">
                    <span>What should I do if my hard drive is clicking or making noise?</span>
                    <i data-lucide="chevron-down" class="w-4 h-4 text-slate-500 transition-transform" :class="active === 4 ? 'rotate-180' : ''"></i>
                </button>
                <div x-show="active === 4" x-cloak class="px-5 pb-5 text-xs text-slate-600 leading-relaxed border-t border-slate-200/60 pt-3">
                    Immediately power off the computer or disconnect the external cable! A clicking sound indicates mechanical failure of the read/write heads. Continuing to supply power causes the microscopic heads to scrape against the magnetic platters, which can cause permanent data loss. Never attempt DIY recovery software on a clicking drive.
                </div>
            </div>

            <div class="bg-slate-50 rounded-2xl border border-slate-200 overflow-hidden">
                <button @click="active = (active === 5 ? null : 5)" class="w-full p-5 text-left font-bold text-sm text-slate-900 flex items-center justify-between gap-4">
                    <span>Is my confidential corporate or personal data protected?</span>
                    <i data-lucide="chevron-down" class="w-4 h-4 text-slate-500 transition-transform" :class="active === 5 ? 'rotate-180' : ''"></i>
                </button>
                <div x-show="active === 5" x-cloak class="px-5 pb-5 text-xs text-slate-600 leading-relaxed border-t border-slate-200/60 pt-3">
                    Absolutely. All data recovery is performed in-house within our biometric-secured facility. We sign formal Non-Disclosure Agreements (NDA) before initiating diagnosis. Your recovered files are delivered on an encrypted external drive, and temporary lab clones are cryptographically wiped after customer confirmation.
                </div>
            </div>
        </div>
    </section>

    <!-- Inter-Branch Cross-Linking Network (Connecting All 7 Locations) -->
    <section class="py-20 bg-slate-950 text-white border-t border-slate-800">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-12">
            <div class="text-center max-w-3xl mx-auto space-y-2">
                <span class="text-xs font-bold uppercase tracking-widest text-cyan-400 block">LifeGuard Global Network</span>
                <h2 class="text-2xl sm:text-3xl font-black font-display tracking-tight text-white">
                    Explore Other LifeGuard Office Locations
                </h2>
                <p class="text-xs sm:text-sm text-slate-400">
                    Need data recovery support in another city or country? Connect with our other regional centers:
                </p>
            </div>

            <!-- Grid of Other 6 Branches -->
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                @foreach($otherLocations as $other)
                    <div class="bg-slate-900 rounded-2xl border border-slate-800 p-6 flex flex-col justify-between hover:border-cyan-500/50 transition group">
                        <div class="space-y-4">
                            <div class="flex items-center justify-between gap-3 border-b border-slate-800/80 pb-3">
                                <div class="flex items-center gap-2.5">
                                    <span class="text-xl select-none">{{ $other['flag'] }}</span>
                                    <h3 class="text-base font-bold text-white group-hover:text-cyan-400 transition">
                                        {{ $other['office_title'] }}
                                    </h3>
                                </div>
                                <span class="text-[9px] uppercase font-bold px-2 py-0.5 rounded-full bg-slate-800 text-slate-300">
                                    {{ $other['tag'] }}
                                </span>
                            </div>

                            <p class="text-xs text-slate-300 line-clamp-2">
                                <strong class="text-slate-400">Address:</strong> {{ $other['address'] }}
                            </p>

                            <div class="text-xs text-slate-400 space-y-1">
                                <div><strong class="text-slate-300">Phone:</strong> {{ $other['phone'] }}</div>
                                <div><strong class="text-slate-300">Email:</strong> {{ $other['email'] }}</div>
                            </div>
                        </div>

                        <div class="pt-4 mt-4 border-t border-slate-800/80 flex items-center justify-between">
                            <a href="{{ url($other['slug']) }}" class="text-xs font-bold text-cyan-400 hover:text-cyan-300 flex items-center gap-1.5 transition">
                                <span>Visit Branch Page</span>
                                <i data-lucide="arrow-right" class="w-3.5 h-3.5 group-hover:translate-x-1 transition-transform"></i>
                            </a>
                            <a href="tel:{{ $other['phone_link'] }}" class="text-[11px] font-semibold text-slate-400 hover:text-white transition">
                                Direct Call
                            </a>
                        </div>
                    </div>
                @endforeach
            </div>

            <!-- Inter-Emirate Sub-Hubs Strip -->
            <div class="pt-8 border-t border-slate-900 space-y-4">
                <span class="text-xs font-bold uppercase tracking-wider text-slate-400 block text-center sm:text-left">
                    All LifeGuard Regional & UAE District Portals:
                </span>
                <div class="flex flex-wrap gap-2 text-xs">
                    @foreach($uaeAreas as $area)
                        <a href="{{ $area['url'] }}" class="px-3 py-1.5 rounded-xl bg-slate-900 hover:bg-cyan-950 hover:text-cyan-300 border border-slate-800 text-slate-300 transition">
                            &bull; {{ $area['title'] }}
                        </a>
                    @endforeach
                </div>
            </div>
        </div>
    </section>

    <!-- Emergency Hotline Banner -->
    <section class="py-16 bg-gradient-to-r from-cyan-900 via-slate-900 to-slate-950 text-white">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex flex-col lg:flex-row items-center justify-between gap-8 text-center lg:text-left">
                <div class="space-y-2 max-w-2xl">
                    <span class="text-xs font-bold uppercase tracking-widest text-cyan-300 block">Emergency 24/7 Lab Dispatch</span>
                    <h3 class="text-2xl sm:text-3xl font-black font-display">
                        Need Immediate Data Recovery in {{ $location['name'] }}?
                    </h3>
                    <p class="text-xs sm:text-sm text-slate-300">
                        Don't risk permanent data loss by rebooting failed drives. Speak directly with our on-duty cleanroom engineer now.
                    </p>
                </div>
                <div class="flex flex-wrap items-center justify-center gap-3 shrink-0">
                    <a href="tel:{{ $location['phone_link'] }}" class="px-6 py-3.5 rounded-full text-xs font-bold bg-cyan-400 hover:bg-cyan-300 text-slate-950 transition flex items-center gap-2 shadow-lg shadow-cyan-500/20">
                        <i data-lucide="phone-call" class="w-4 h-4"></i>
                        <span>Call {{ $location['phone'] }}</span>
                    </a>
                    <a href="{{ $location['whatsapp_link'] }}?text={{ urlencode('Hello LifeGuard ' . $location['name'] . ', I have an urgent data recovery emergency.') }}" target="_blank" rel="noopener noreferrer" class="px-6 py-3.5 rounded-full text-xs font-bold bg-emerald-600 hover:bg-emerald-500 text-white transition flex items-center gap-2">
                        <i data-lucide="message-circle" class="w-4 h-4"></i>
                        <span>WhatsApp 24/7 Specialist</span>
                    </a>
                </div>
            </div>
        </div>
    </section>

</div>
@endsection
