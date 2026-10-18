@extends('layouts.app')

@section('seo_meta')
    <title>{{ \App\Support\SeoHelper::formatTitle($service->seo_title ?: $service->title, true) }}</title>
    <meta name="description" content="{{ $service->seo_description ?: $service->summary }}">
    @if($service->meta_keywords)
        <meta name="keywords" content="{{ $service->meta_keywords }}">
    @endif
    <link rel="canonical" href="{{ \App\Support\SeoHelper::canonicalFor($service) }}">
    <meta name="robots" content="{{ $service->is_indexable ? 'index, follow' : 'noindex, nofollow' }}">
    <meta property="og:title" content="{{ $service->og_title ?: $service->title }}">
    <meta property="og:description" content="{{ $service->og_description ?: $service->summary }}">
    <meta property="og:type" content="service">
    <meta property="og:url" content="{{ url($service->slug) }}">
    <meta property="og:image" content="{{ $service->featured_image_url ?: 'https://lifeguarddatarecovery.com/wp-content/uploads/2022/03/aboutus.jpg' }}">
    <meta name="twitter:card" content="summary_large_image">

    <!-- Schema.org Service JSON-LD -->
    <script type="application/ld+json">
    {!! json_encode(\App\Services\SchemaService::getServiceSchema($service), JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) !!}
    </script>

    <!-- BreadcrumbList Schema -->
    <script type="application/ld+json">
    {!! json_encode(\App\Services\SchemaService::getBreadcrumbSchema([
        'Home' => '/',
        'Services' => '/services',
        $service->title => '/' . $service->slug
    ]), JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) !!}
    </script>

    @php
        $extractedFaqs = \App\Services\SchemaService::extractFaqsFromHtml($service->content);
    @endphp
    @if(count($extractedFaqs) > 0)
    <!-- FAQPage Schema -->
    <script type="application/ld+json">
    {!! json_encode(\App\Services\SchemaService::getFaqPageSchema($extractedFaqs), JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) !!}
    </script>
    @endif

    <!-- Custom Schema.org JSON-LD (from Dashboard) -->
    <x-schema-json-ld :schema="$service->schema_markup" />
@endsection

@section('content')
<div class="bg-slate-50 min-h-screen">

    <!-- Hero Section -->
    <section class="relative bg-gradient-to-b from-slate-950 via-slate-900 to-slate-900 text-white pt-24 pb-20 overflow-hidden">
        <div class="absolute -top-32 right-10 w-96 h-96 bg-cyan-500/10 rounded-full blur-3xl pointer-events-none"></div>

        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 relative z-10">
            <!-- Breadcrumbs -->
            <nav class="flex items-center gap-2 text-xs text-slate-400 mb-8" aria-label="Breadcrumb">
                <a href="{{ route('home') }}" class="hover:text-cyan-400 transition">Home</a>
                <i data-lucide="chevron-right" class="w-3.5 h-3.5 text-slate-600"></i>
                <a href="{{ route('services.index') }}" class="hover:text-cyan-400 transition">Services</a>
                <i data-lucide="chevron-right" class="w-3.5 h-3.5 text-slate-600"></i>
                <span class="text-cyan-400 font-semibold">{{ $service->title }}</span>
            </nav>

            <div class="grid grid-cols-1 lg:grid-cols-12 gap-12 items-center">
                <div class="lg:col-span-8 space-y-6">
                    <div class="inline-flex items-center gap-2 px-3.5 py-1.5 rounded-full text-xs font-semibold bg-cyan-500/15 text-cyan-400 border border-cyan-500/30 backdrop-blur-sm">
                        <span class="w-2 h-2 rounded-full bg-cyan-400 animate-pulse"></span>
                        <span>ISO-5 CLASS 100 CLEANROOM • NO DATA NO CHARGE</span>
                    </div>

                    <h1 class="text-3xl sm:text-4xl lg:text-5xl font-black font-display tracking-tight leading-tight">
                        {{ $service->title }}
                    </h1>

                    @if($service->tagline)
                        <p class="text-base sm:text-lg text-cyan-300/90 font-medium font-sans">
                            {{ $service->tagline }}
                        </p>
                    @endif

                    <p class="text-sm sm:text-base text-slate-300 leading-relaxed font-light max-w-3xl">
                        {{ $service->summary }}
                    </p>

                    <!-- 4 Value Proposition Badges -->
                    <div class="grid grid-cols-2 sm:grid-cols-4 gap-3 pt-2">
                        <div class="p-3.5 rounded-2xl bg-slate-800/80 border border-slate-700/80 backdrop-blur-sm">
                            <div class="w-7 h-7 rounded-lg bg-cyan-500/20 text-cyan-400 flex items-center justify-center mb-2">
                                <i data-lucide="shield-check" class="w-4 h-4"></i>
                            </div>
                            <div class="text-xs font-bold text-white">No Data, No Charge</div>
                            <div class="text-[11px] text-slate-400 mt-0.5">Zero financial risk</div>
                        </div>
                        <div class="p-3.5 rounded-2xl bg-slate-800/80 border border-slate-700/80 backdrop-blur-sm">
                            <div class="w-7 h-7 rounded-lg bg-cyan-500/20 text-cyan-400 flex items-center justify-center mb-2">
                                <i data-lucide="sparkles" class="w-4 h-4"></i>
                            </div>
                            <div class="text-xs font-bold text-white">Class 100 Lab</div>
                            <div class="text-[11px] text-slate-400 mt-0.5">Laminar flow bench</div>
                        </div>
                        <div class="p-3.5 rounded-2xl bg-slate-800/80 border border-slate-700/80 backdrop-blur-sm">
                            <div class="w-7 h-7 rounded-lg bg-cyan-500/20 text-cyan-400 flex items-center justify-center mb-2">
                                <i data-lucide="cpu" class="w-4 h-4"></i>
                            </div>
                            <div class="text-xs font-bold text-white">PC-3000 Hardware</div>
                            <div class="text-[11px] text-slate-400 mt-0.5">Firmware repair tools</div>
                        </div>
                        <div class="p-3.5 rounded-2xl bg-slate-800/80 border border-slate-700/80 backdrop-blur-sm">
                            <div class="w-7 h-7 rounded-lg bg-cyan-500/20 text-cyan-400 flex items-center justify-center mb-2">
                                <i data-lucide="clock" class="w-4 h-4"></i>
                            </div>
                            <div class="text-xs font-bold text-white">24/7 Hotline</div>
                            <div class="text-[11px] text-slate-400 mt-0.5">Emergency response</div>
                        </div>
                    </div>

                    <div class="pt-4 flex flex-wrap items-center gap-4">
                        <a href="#quote-box" class="px-7 py-3.5 rounded-full font-bold text-xs sm:text-sm bg-gradient-to-r from-cyan-500 to-blue-600 hover:from-cyan-400 hover:to-blue-500 text-white shadow-lg shadow-cyan-500/25 transition flex items-center gap-2">
                            <span>Book Free Lab Diagnostic</span>
                            <i data-lucide="arrow-down" class="w-4 h-4"></i>
                        </a>
                        <a href="tel:+97143933082" class="px-6 py-3.5 rounded-full font-bold text-xs sm:text-sm bg-slate-800 hover:bg-slate-700 text-white border border-slate-700 transition flex items-center gap-2">
                            <i data-lucide="phone-call" class="w-4 h-4 text-cyan-400"></i>
                            <span>+971 43 933 082</span>
                        </a>
                        <a href="https://wa.me/971554838545?text={{ urlencode('Hello LifeGuard Data Recovery, I need urgent assistance with ' . $service->title) }}" target="_blank" class="px-5 py-3.5 rounded-full font-bold text-xs sm:text-sm bg-emerald-600/30 hover:bg-emerald-600/50 text-emerald-300 border border-emerald-500/40 transition flex items-center gap-2">
                            <i data-lucide="message-circle" class="w-4 h-4"></i>
                            <span>WhatsApp</span>
                        </a>
                    </div>
                </div>

                <div class="lg:col-span-4">
                    <div class="rounded-3xl overflow-hidden border border-slate-800 shadow-2xl bg-slate-900 aspect-[4/3] relative">
                        <img src="{{ $service->featured_image_url ?: 'https://lifeguarddatarecovery.com/wp-content/uploads/2022/03/aboutus.jpg' }}" 
                             alt="{{ $service->title }} Dubai" 
                             class="w-full h-full object-cover">
                        <div class="absolute bottom-3 left-3 right-3 p-3 rounded-2xl bg-slate-950/80 backdrop-blur-md border border-white/10 text-xs text-white flex items-center justify-between">
                            <span class="font-bold">LifeGuard Cleanroom Lab</span>
                            <span class="text-cyan-400 font-semibold">98.6% Proven Rate</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Main Content & Diagnostics Form Grid -->
    <section class="py-16 max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="grid grid-cols-1 lg:grid-cols-12 gap-12 items-start">
            
            <!-- Left 8 cols: Service Details & Technical Capabilities -->
            <div class="lg:col-span-8 space-y-12">
                <!-- Features & Capabilities -->
                @if(!empty($service->features) && is_array($service->features))
                    <div class="bg-white rounded-3xl p-8 border border-slate-200/80 shadow-sm space-y-4">
                        <h2 class="text-xl font-black text-slate-900">What We Recover & Repair</h2>
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 pt-2">
                            @foreach($service->features as $feat)
                                <div class="flex items-start gap-2.5 p-3 rounded-xl bg-slate-50 border border-slate-100">
                                    <i data-lucide="check-circle" class="w-4 h-4 text-cyan-600 shrink-0 mt-0.5"></i>
                                    <span class="text-xs font-semibold text-slate-800 leading-snug">{{ $feat }}</span>
                                </div>
                            @endforeach
                        </div>
                    </div>
                @endif

                <!-- Benefits & Service Guarantees -->
                @if(!empty($service->benefits) && is_array($service->benefits))
                    <div class="bg-white rounded-3xl p-8 border border-slate-200/80 shadow-sm space-y-4">
                        <h2 class="text-xl font-black text-slate-900">The LifeGuard Guarantee</h2>
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 pt-2">
                            @foreach($service->benefits as $ben)
                                <div class="flex items-start gap-2.5 p-3 rounded-xl bg-cyan-50/60 border border-cyan-100">
                                    <i data-lucide="shield-check" class="w-4 h-4 text-cyan-700 shrink-0 mt-0.5"></i>
                                    <span class="text-xs font-bold text-cyan-950 leading-snug">{{ $ben }}</span>
                                </div>
                            @endforeach
                        </div>
                    </div>
                @endif

                <!-- Service WYSIWYG Content if available -->
                @if($service->content)
                    <div class="bg-white rounded-3xl p-8 border border-slate-200/80 shadow-sm prose prose-slate max-w-none">
                        {!! $service->content !!}
                    </div>
                @endif

                <!-- Related Technical Guides (from 396+ posts) -->
                @if(isset($relatedPosts) && $relatedPosts->isNotEmpty())
                    <div class="space-y-6 pt-4">
                        <div class="flex items-center justify-between">
                            <h3 class="text-xl font-black text-slate-900">Related Case Studies & Technical Guides</h3>
                            <a href="{{ route('blog.index') }}" class="text-xs font-bold text-cyan-600 hover:underline">View All &rarr;</a>
                        </div>

                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
                            @foreach($relatedPosts as $rPost)
                                <a href="{{ url($rPost->slug) }}" class="p-5 rounded-2xl bg-white border border-slate-200 hover:border-cyan-500 shadow-xs hover:shadow-md transition flex flex-col justify-between group">
                                    <div>
                                        <span class="text-[10px] font-bold uppercase text-cyan-600 block mb-1">
                                            {{ $rPost->categories->first()?->name ?? 'Data Recovery Guide' }}
                                        </span>
                                        <h4 class="text-sm font-bold text-slate-900 group-hover:text-cyan-700 transition leading-snug line-clamp-2">
                                            {{ $rPost->title }}
                                        </h4>
                                        <p class="text-xs text-slate-500 mt-2 line-clamp-2">
                                            {{ $rPost->excerpt }}
                                        </p>
                                    </div>
                                    <div class="text-[11px] font-semibold text-cyan-600 mt-4 flex items-center gap-1">
                                        <span>Read Guide</span>
                                        <i data-lucide="arrow-right" class="w-3.5 h-3.5"></i>
                                    </div>
                                </a>
                            @endforeach
                        </div>
                    </div>
                @endif

                <!-- UAE Service Hubs & Local Cleanroom Labs (Contextual Geo Linking) -->
                <div class="space-y-4 pt-6 border-t border-slate-200">
                    <div class="flex items-center justify-between">
                        <div>
                            <span class="text-[10px] font-bold uppercase tracking-widest text-cyan-600 block">Fast In-Lab Triage & Free Express Courier</span>
                            <h3 class="text-lg font-black text-slate-900">Available Across All UAE Emirates & Regional Hubs</h3>
                        </div>
                    </div>
                    <div class="grid grid-cols-2 sm:grid-cols-4 gap-2.5 text-xs">
                        <a href="/data-recovery-dubai" class="p-3 rounded-xl bg-slate-50 hover:bg-cyan-50/70 border border-slate-200/80 hover:border-cyan-400 transition group flex flex-col justify-between">
                            <span class="font-bold text-slate-800 group-hover:text-cyan-700">Bur Dubai Lab</span>
                            <span class="text-[10px] text-slate-400 mt-1">Main Cleanroom HQ</span>
                        </a>
                        <a href="/data-recovery-al-barsha" class="p-3 rounded-xl bg-slate-50 hover:bg-cyan-50/70 border border-slate-200/80 hover:border-cyan-400 transition group flex flex-col justify-between">
                            <span class="font-bold text-slate-800 group-hover:text-cyan-700">Al Barsha Branch</span>
                            <span class="text-[10px] text-slate-400 mt-1">MOE / Media City</span>
                        </a>
                        <a href="/data-recovery-in-business-bay" class="p-3 rounded-xl bg-slate-50 hover:bg-cyan-50/70 border border-slate-200/80 hover:border-cyan-400 transition group flex flex-col justify-between">
                            <span class="font-bold text-slate-800 group-hover:text-cyan-700">Business Bay</span>
                            <span class="text-[10px] text-slate-400 mt-1">Downtown & DIFC</span>
                        </a>
                        <a href="/data-recovery-dubai-marina" class="p-3 rounded-xl bg-slate-50 hover:bg-cyan-50/70 border border-slate-200/80 hover:border-cyan-400 transition group flex flex-col justify-between">
                            <span class="font-bold text-slate-800 group-hover:text-cyan-700">Dubai Marina</span>
                            <span class="text-[10px] text-slate-400 mt-1">JLT & Palm Jumeirah</span>
                        </a>
                        <a href="/data-recovery-abu-dhabi" class="p-3 rounded-xl bg-slate-50 hover:bg-cyan-50/70 border border-slate-200/80 hover:border-cyan-400 transition group flex flex-col justify-between">
                            <span class="font-bold text-slate-800 group-hover:text-cyan-700">Abu Dhabi</span>
                            <span class="text-[10px] text-slate-400 mt-1">Capital City Hub</span>
                        </a>
                        <a href="/data-recovery-sharjah" class="p-3 rounded-xl bg-slate-50 hover:bg-cyan-50/70 border border-slate-200/80 hover:border-cyan-400 transition group flex flex-col justify-between">
                            <span class="font-bold text-slate-800 group-hover:text-cyan-700">Sharjah</span>
                            <span class="text-[10px] text-slate-400 mt-1">Industrial & City</span>
                        </a>
                        <a href="/data-recovery-ajman" class="p-3 rounded-xl bg-slate-50 hover:bg-cyan-50/70 border border-slate-200/80 hover:border-cyan-400 transition group flex flex-col justify-between">
                            <span class="font-bold text-slate-800 group-hover:text-cyan-700">Ajman</span>
                            <span class="text-[10px] text-slate-400 mt-1">Northern Hub</span>
                        </a>
                        <a href="/contact" class="p-3 rounded-xl bg-cyan-600 text-white hover:bg-cyan-500 transition group flex flex-col justify-between shadow-xs">
                            <span class="font-bold">Free Express Courier</span>
                            <span class="text-[10px] text-cyan-100 mt-1">Across 7 Emirates</span>
                        </a>
                    </div>
                </div>
            </div>

            <!-- Right 4 cols: Sticky Free Diagnostic Quote Box -->
            <div class="lg:col-span-4" id="quote-box">
                <div class="bg-slate-950 text-white rounded-3xl p-6 sm:p-8 border border-slate-800 shadow-2xl sticky top-28 space-y-6">
                    <div>
                        <div class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-md bg-cyan-500/20 text-cyan-300 text-[10px] font-bold mb-2">
                            <span class="w-1.5 h-1.5 rounded-full bg-cyan-400 animate-pulse"></span>
                            <span>FREE DIAGNOSTIC</span>
                        </div>
                        <h3 class="text-xl font-black text-white">Book Lab Evaluation</h3>
                        <p class="text-xs text-slate-400 mt-1">
                            Same-day inspection in our Bur Dubai or Al Barsha cleanrooms.
                        </p>
                    </div>

                    @if(session('success'))
                        <div class="p-3.5 rounded-xl bg-emerald-950/80 border border-emerald-500 text-emerald-200 text-xs">
                            {{ session('success') }}
                        </div>
                    @endif

                    <form action="{{ route('contact.submit') }}" method="POST" class="space-y-4">
                        @csrf
                        <input type="hidden" name="source" value="Service Page: {{ $service->title }}">

                        <div>
                            <label class="block text-[11px] font-semibold text-slate-300 mb-1">Your Full Name *</label>
                            <input type="text" name="name" required placeholder="Name" 
                                   class="w-full bg-slate-900 border border-slate-700 rounded-xl px-3.5 py-2.5 text-xs text-white focus:outline-hidden focus:border-cyan-500">
                        </div>

                        <div>
                            <label class="block text-[11px] font-semibold text-slate-300 mb-1">Phone / WhatsApp *</label>
                            <input type="text" name="phone" required placeholder="+971 50 123 4567" 
                                   class="w-full bg-slate-900 border border-slate-700 rounded-xl px-3.5 py-2.5 text-xs text-white focus:outline-hidden focus:border-cyan-500">
                        </div>

                        <div>
                            <label class="block text-[11px] font-semibold text-slate-300 mb-1">Device Details / Issue</label>
                            <textarea name="message" rows="3" placeholder="Hard drive model, capacity, problem description..." 
                                      class="w-full bg-slate-900 border border-slate-700 rounded-xl px-3.5 py-2.5 text-xs text-white focus:outline-hidden focus:border-cyan-500"></textarea>
                        </div>

                        <button type="submit" 
                                class="w-full py-3.5 rounded-xl bg-gradient-to-r from-cyan-500 to-blue-600 hover:from-cyan-400 hover:to-blue-500 text-white font-extrabold text-xs shadow-lg shadow-cyan-600/30 transition-all flex items-center justify-center gap-2">
                            <i data-lucide="send" class="w-3.5 h-3.5"></i>
                            <span>Submit for Priority Evaluation</span>
                        </button>
                    </form>

                    <div class="pt-4 border-t border-slate-800 text-xs text-slate-400 space-y-2">
                        <div class="flex items-center gap-2">
                            <i data-lucide="phone-call" class="w-4 h-4 text-cyan-400"></i>
                            <a href="tel:+97143933082" class="hover:text-white font-bold text-white">+971 43 933 082</a>
                        </div>
                        <div class="flex items-center gap-2">
                            <i data-lucide="message-circle" class="w-4 h-4 text-emerald-400"></i>
                            <a href="https://wa.me/971554838545" target="_blank" class="hover:text-white">WhatsApp: +971 55 483 8545</a>
                        </div>
                        <div class="text-[11px] text-slate-500 pt-1">
                            Free courier collection across Dubai, Abu Dhabi & Sharjah.
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </section>

</div>
@endsection
