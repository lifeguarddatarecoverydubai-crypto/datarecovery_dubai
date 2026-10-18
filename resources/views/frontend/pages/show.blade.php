@extends('layouts.app')

@section('seo_meta')
    <!-- Primary Meta Tags -->
    @php
        $fullTitle = \App\Support\SeoHelper::formatTitle($page->seo_title, true);
        $canonicalUrl = \App\Support\SeoHelper::canonicalFor($page);
    @endphp
    <title>{{ $fullTitle }}</title>
    <meta name="title" content="{{ $fullTitle }}">
    <meta name="description" content="{{ $page->seo_description }}">
    @if($page->meta_keywords)
        <meta name="keywords" content="{{ $page->meta_keywords }}">
    @endif
    <link rel="canonical" href="{{ $canonicalUrl }}">
    <meta name="robots" content="{{ $page->is_indexable ? 'index, follow' : 'noindex, nofollow' }}">

    <!-- Open Graph -->
    <meta property="og:type" content="website">
    <meta property="og:url" content="{{ url()->current() }}">
    <meta property="og:title" content="{{ $page->og_title ?: $page->seo_title }}">
    <meta property="og:description" content="{{ $page->og_description ?: $page->seo_description }}">
    @if($page->seo_image)
        <meta property="og:image" content="{{ $page->seo_image }}">
    @endif
    <meta property="og:site_name" content="{{ config('app.name', 'LifeGuard Data Recovery') }}">

    <!-- Twitter -->
    <meta name="twitter:card" content="summary_large_image">
    <meta name="twitter:title" content="{{ $page->og_title ?: $page->seo_title }}">
    <meta name="twitter:description" content="{{ $page->og_description ?: $page->seo_description }}">
    @if($page->seo_image)
        <meta name="twitter:image" content="{{ $page->seo_image }}">
    @endif

    <!-- Custom Schema.org JSON-LD (from Dashboard or Yoast) -->
    <x-schema-json-ld :schema="$page->schema_markup" />
@endsection

@section('content')
<div class="py-12 bg-white">
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 space-y-10">
        <!-- Breadcrumbs -->
        <nav class="flex items-center gap-2 text-xs font-semibold text-slate-500 flex-wrap" aria-label="Breadcrumb">
            <a href="{{ route('home') }}" class="hover:text-brand-600 transition">Home</a>
            <i data-lucide="chevron-right" class="w-3.5 h-3.5 text-slate-400"></i>
            <span class="text-slate-900 font-bold truncate">{{ $page->title }}</span>
        </nav>

        <!-- Page Header -->
        <header class="space-y-4 border-b border-slate-100 pb-8">
            <h1 class="text-3xl sm:text-4xl lg:text-5xl font-black text-slate-900 tracking-tight leading-tight">
                {{ $page->title }}
            </h1>
            @if($page->updated_at)
                <div class="flex items-center gap-2 text-xs text-slate-400">
                    <i data-lucide="calendar" class="w-3.5 h-3.5"></i>
                    <span>Last updated on {{ $page->updated_at->format('F d, Y') }}</span>
                </div>
            @endif
        </header>

        <!-- Featured Image -->
        @if($page->featured_image_url)
            <div class="rounded-3xl overflow-hidden aspect-[21/9] shadow-md bg-slate-100">
                <img src="{{ $page->featured_image_url }}" 
                     alt="{{ $page->title }}" 
                     width="1200" 
                     height="514" 
                     loading="eager" 
                     fetchpriority="high" 
                     class="w-full h-full object-cover">
            </div>
        @endif

        <!-- Page WYSIWYG Content Body -->
        <div class="prose prose-slate prose-lg max-w-none leading-relaxed">
            {!! $page->content !!}
        </div>

        @php
            $isLocationPage = str_contains($page->slug, 'data-recovery-') || in_array($page->slug, [
                'data-recovery-dubai', 'data-recovery-al-barsha', 'data-recovery-in-business-bay',
                'data-recovery-dubai-marina', 'data-recovery-abu-dhabi', 'data-recovery-sharjah',
                'data-recovery-ajman', 'data-recovery-deira', 'data-recovery-jumeirah-lake-tower'
            ], true);
        @endphp

        @if($isLocationPage)
            <!-- Location Page Contextual Services Grid -->
            <div class="space-y-6 pt-6 border-t border-slate-200">
                <div>
                    <span class="text-[10px] font-bold uppercase tracking-widest text-sky-600 block">Class 100 Cleanroom Certified Lab</span>
                    <h2 class="text-xl font-black text-slate-900 mt-1">Specialized Data Recovery Services at this Location</h2>
                </div>
                <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-4 text-xs">
                    <a href="/hard-drive-data-recovery-dubai" class="p-4 rounded-2xl bg-slate-50 hover:bg-sky-50 border border-slate-200/80 hover:border-sky-400 transition group flex flex-col justify-between">
                        <span class="font-bold text-slate-900 group-hover:text-sky-700">Hard Drive Recovery</span>
                        <span class="text-[11px] text-slate-500 mt-1">Mechanical, clicking, dropped HDDs</span>
                    </a>
                    <a href="/ssd-drive-data-recovery-in-dubai" class="p-4 rounded-2xl bg-slate-50 hover:bg-sky-50 border border-slate-200/80 hover:border-sky-400 transition group flex flex-col justify-between">
                        <span class="font-bold text-slate-900 group-hover:text-sky-700">SSD & NVMe Recovery</span>
                        <span class="text-[11px] text-slate-500 mt-1">Firmware brick, dead controller, NAND flash</span>
                    </a>
                    <a href="/raid-data-recovery-dubai" class="p-4 rounded-2xl bg-slate-50 hover:bg-sky-50 border border-slate-200/80 hover:border-sky-400 transition group flex flex-col justify-between">
                        <span class="font-bold text-slate-900 group-hover:text-sky-700">RAID & Server Recovery</span>
                        <span class="text-[11px] text-slate-500 mt-1">RAID 0, 1, 5, 6, 10, 50 virtual reconstruction</span>
                    </a>
                    <a href="/nas-data-recovery-dubai" class="p-4 rounded-2xl bg-slate-50 hover:bg-sky-50 border border-slate-200/80 hover:border-sky-400 transition group flex flex-col justify-between">
                        <span class="font-bold text-slate-900 group-hover:text-sky-700">NAS Storage Recovery</span>
                        <span class="text-[11px] text-slate-500 mt-1">Synology, QNAP, Buffalo, TerraMaster</span>
                    </a>
                    <a href="/cctv-footage-recovery-dubai" class="p-4 rounded-2xl bg-slate-50 hover:bg-sky-50 border border-slate-200/80 hover:border-sky-400 transition group flex flex-col justify-between">
                        <span class="font-bold text-slate-900 group-hover:text-sky-700">CCTV Footage Recovery</span>
                        <span class="text-[11px] text-slate-500 mt-1">DVR / NVR overwrite & formatted forensic analysis</span>
                    </a>
                    <a href="/mac-data-recovery" class="p-4 rounded-2xl bg-slate-50 hover:bg-sky-50 border border-slate-200/80 hover:border-sky-400 transition group flex flex-col justify-between">
                        <span class="font-bold text-slate-900 group-hover:text-sky-700">Apple Mac Recovery</span>
                        <span class="text-[11px] text-slate-500 mt-1">MacBook Pro, Air, iMac M1/M2/M3 encrypted chips</span>
                    </a>
                </div>

                <!-- Inter-Emirate Branch Links -->
                <div class="pt-4 border-t border-slate-100 space-y-3">
                    <span class="text-xs font-bold uppercase tracking-wider text-slate-400 block">Other LifeGuard UAE Service Hubs:</span>
                    <div class="flex flex-wrap gap-2 text-xs">
                        <a href="/data-recovery-dubai" class="px-3 py-1.5 rounded-xl bg-slate-100 hover:bg-sky-100 hover:text-sky-700 transition font-medium text-slate-700">Bur Dubai HQ</a>
                        <a href="/data-recovery-al-barsha" class="px-3 py-1.5 rounded-xl bg-slate-100 hover:bg-sky-100 hover:text-sky-700 transition font-medium text-slate-700">Al Barsha & MOE</a>
                        <a href="/data-recovery-in-business-bay" class="px-3 py-1.5 rounded-xl bg-slate-100 hover:bg-sky-100 hover:text-sky-700 transition font-medium text-slate-700">Business Bay</a>
                        <a href="/data-recovery-dubai-marina" class="px-3 py-1.5 rounded-xl bg-slate-100 hover:bg-sky-100 hover:text-sky-700 transition font-medium text-slate-700">Dubai Marina & JLT</a>
                        <a href="/data-recovery-abu-dhabi" class="px-3 py-1.5 rounded-xl bg-slate-100 hover:bg-sky-100 hover:text-sky-700 transition font-medium text-slate-700">Abu Dhabi</a>
                        <a href="/data-recovery-sharjah" class="px-3 py-1.5 rounded-xl bg-slate-100 hover:bg-sky-100 hover:text-sky-700 transition font-medium text-slate-700">Sharjah</a>
                        <a href="/data-recovery-ajman" class="px-3 py-1.5 rounded-xl bg-slate-100 hover:bg-sky-100 hover:text-sky-700 transition font-medium text-slate-700">Ajman & Northern Emirates</a>
                    </div>
                </div>
            </div>
        @endif

        <!-- Cleanroom Lab Emergency CTA Box -->
        <div class="p-8 rounded-3xl bg-gradient-to-br from-slate-900 via-slate-950 to-brand-950 text-white shadow-xl border border-sky-500/20 space-y-6">
            <div class="flex flex-wrap items-center justify-between gap-3 border-b border-white/10 pb-4">
                <div class="flex items-center gap-2">
                    <span class="w-2 h-2 rounded-full bg-emerald-400 animate-ping"></span>
                    <span class="text-xs uppercase tracking-widest font-black text-sky-400">Emergency Cleanroom Lab Assistance</span>
                </div>
                <span class="text-xs text-slate-300 font-medium">Class 100 Cleanroom &bull; PC-3000 Certified</span>
            </div>
            <div class="grid grid-cols-1 md:grid-cols-12 gap-6 items-center">
                <div class="md:col-span-8 space-y-2">
                    <h3 class="text-xl sm:text-2xl font-black font-display text-white">Need Urgent Data Recovery in Dubai?</h3>
                    <p class="text-xs sm:text-sm text-slate-300 leading-relaxed">
                        Visit our cleanroom lab in Bur Dubai or Al Barsha, or request a free express courier pickup across the UAE. Free evaluation and no data, no charge guarantee.
                    </p>
                </div>
                <div class="md:col-span-4 flex flex-col gap-2.5">
                    <a href="tel:+97143933082" class="w-full py-3 px-4 rounded-xl font-bold text-xs bg-sky-500 hover:bg-sky-400 text-slate-950 text-center shadow-lg shadow-sky-500/20 transition flex items-center justify-center gap-2">
                        <i data-lucide="phone-call" class="w-4 h-4"></i>
                        <span>Call +971 43 933 082</span>
                    </a>
                    <a href="https://wa.me/971554838545?text={{ urlencode('Hello LifeGuard Data Recovery, I need assistance with ' . $page->title) }}" target="_blank" class="w-full py-2.5 px-4 rounded-xl font-bold text-xs bg-emerald-600 hover:bg-emerald-500 text-white text-center transition flex items-center justify-center gap-2">
                        <i data-lucide="message-circle" class="w-4 h-4"></i>
                        <span>WhatsApp Senior Tech</span>
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
