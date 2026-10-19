@extends('layouts.app')

@section('seo_meta')
    <title>Certified Data Recovery Services in Dubai & UAE | LifeGuard</title>
    <meta name="description" content="Explore LifeGuard certified data recovery services in Dubai. Hard drive recovery, SSD & NVMe flash, RAID server reconstruction, NAS, CCTV footage, and iPhone recovery in an ISO-5 Class 100 Cleanroom.">
    <meta name="keywords" content="data recovery services dubai, hard drive recovery dubai, ssd data recovery, raid recovery dubai, nas recovery uae, cctv footage recovery">
    <link rel="canonical" href="{{ route('services.index') }}">
    <meta property="og:title" content="Certified Data Recovery Services in Dubai & UAE | LifeGuard">
    <meta property="og:description" content="Class 100 cleanroom data recovery for HDD, SSD, RAID, NAS, and mobile devices in Dubai. No Data, No Charge.">
    <meta property="og:url" content="{{ route('services.index') }}">
    <meta property="og:type" content="website">

    <!-- Schema.org ItemList JSON-LD -->
    <script type="application/ld+json">
    {!! json_encode([
      '@context' => 'https://schema.org',
      '@type' => 'ItemList',
      'name' => 'Data Recovery Services in Dubai & UAE',
      'itemListElement' => $services->map(function($s, $idx) {
          return [
              '@type' => 'ListItem',
              'position' => $idx + 1,
              'name' => $s->title,
              'url' => url($s->slug),
              'description' => $s->summary ?: $s->meta_description,
          ];
      })->values()->all(),
    ], JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) !!}
    </script>
@endsection

@section('content')
<div class="bg-slate-50 min-h-screen">

    <!-- Hero Section -->
    <section class="relative bg-gradient-to-b from-slate-950 via-slate-900 to-slate-900 text-white pt-24 pb-20 overflow-hidden">
        <div class="absolute -top-36 right-0 w-96 h-96 bg-cyan-500/10 rounded-full blur-3xl pointer-events-none"></div>

        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 relative z-10">
            <!-- Breadcrumbs -->
            <nav class="flex items-center gap-2 text-xs text-slate-400 mb-8" aria-label="Breadcrumb">
                <a href="{{ route('home') }}" class="hover:text-cyan-400 transition">Home</a>
                <i data-lucide="chevron-right" class="w-3.5 h-3.5 text-slate-600"></i>
                <span class="text-cyan-400 font-semibold">Services</span>
            </nav>

            <div class="max-w-3xl">
                <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full text-xs font-semibold bg-cyan-500/15 text-cyan-400 border border-cyan-500/30 mb-6 backdrop-blur-sm">
                    <span class="w-2 h-2 rounded-full bg-cyan-400 animate-pulse"></span>
                    <span>ISO-5 CLASS 100 CLEANROOM DATA LAB &bull; UAE WIDE</span>
                </div>
                <h1 class="text-4xl sm:text-5xl lg:text-6xl font-black font-display tracking-tight leading-tight">
                    Professional Data Recovery <span class="bg-gradient-to-r from-cyan-400 via-sky-300 to-blue-400 bg-clip-text text-transparent">Services Dubai</span>
                </h1>
                <p class="mt-6 text-lg sm:text-xl text-slate-300 leading-relaxed font-light">
                    From clicking mechanical desktop drives and water-damaged laptops to crashed enterprise multi-disk RAID arrays, CCTV forensic recovery, and flash memory chip-off extraction across all 7 Emirates.
                </p>
                <div class="mt-8 flex flex-wrap items-center gap-4">
                    <a href="#services-grid" class="px-6 py-3.5 rounded-xl font-bold text-sm bg-gradient-to-r from-cyan-500 to-blue-600 hover:from-cyan-400 hover:to-blue-500 text-white shadow-lg shadow-cyan-500/20 transition transform hover:-translate-y-0.5 flex items-center gap-2">
                        <span>Explore Recovery Services</span>
                        <i data-lucide="arrow-down" class="w-4 h-4"></i>
                    </a>
                    <a href="tel:+97143933082" class="px-6 py-3.5 rounded-xl font-bold text-sm bg-slate-800 hover:bg-slate-700 text-white border border-slate-700 transition flex items-center gap-2">
                        <i data-lucide="phone-call" class="w-4 h-4 text-cyan-400"></i>
                        <span>24/7 Hotline: +971 43 933 082</span>
                    </a>
                </div>
            </div>

            <!-- Stats Bar -->
            <div class="mt-16 pt-10 border-t border-slate-800 grid grid-cols-2 md:grid-cols-4 gap-6 text-slate-300">
                <div>
                    <div class="text-3xl font-black text-white font-display">98.6%</div>
                    <div class="text-xs text-slate-400 mt-1">Verified Success Rate</div>
                </div>
                <div>
                    <div class="text-3xl font-black text-white font-display">Class 100</div>
                    <div class="text-xs text-slate-400 mt-1">ISO-5 Certified Cleanroom</div>
                </div>
                <div>
                    <div class="text-3xl font-black text-white font-display">10,000+</div>
                    <div class="text-xs text-slate-400 mt-1">Donor Parts in Dubai Stock</div>
                </div>
                <div>
                    <div class="text-3xl font-black text-emerald-400 font-display">100%</div>
                    <div class="text-xs text-slate-400 mt-1">No Data, No Charge Policy</div>
                </div>
            </div>
        </div>
    </section>

    <!-- Services Grid Section -->
    <section id="services-grid" class="py-20 max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center max-w-3xl mx-auto mb-16">
            <span class="text-xs font-bold uppercase tracking-widest text-cyan-600">Core Lab Capabilities</span>
            <h2 class="text-3xl sm:text-4xl font-black text-slate-900 tracking-tight mt-2 font-display">
                Engineered for Physical & Logical Disasters
            </h2>
            <p class="text-sm text-slate-600 mt-3">
                Select your storage technology to explore technical details, symptoms repaired, and cleanroom methodologies.
            </p>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
            @php
                $serviceCardImages = [
                    'hard-drive-recovery' => 'media-bundle/media/services/hard-drive-recovery.webp',
                    'ssd-recovery-dubai' => 'media-bundle/media/services/ssd-data-recovery-dubai.webp',
                    'nvme-ssd-recovery-dubai' => 'media-bundle/media/2026/01/emergency-ssd-recovery-serv.webp',
                    'raid-recovery-dubai' => 'media-bundle/media/services/raid-data-recovery-dubai.webp',
                    'nas-recovery-dubai' => 'media-bundle/media/services/ts-412-hardware-specs-removebg-preview.webp',
                    'cctv-footage-recovery-dubai' => 'media-bundle/media/services/cctv-footage-recovery-dubai.webp',
                    'usb-data-recovery-dubai' => 'media-bundle/media/2022/09/usb-flash-drive-data-recovery-in-sharjah-768x768.webp',
                    'iphone-data-recovery-dubai' => 'media-bundle/media/services/iphone-data-recovery.webp',
                    'ransomware-data-recovery-dubai' => 'media-bundle/media/services/ransomware.webp',
                ];
            @endphp

            @foreach($services as $service)
                @php
                    $serviceCardImage = isset($serviceCardImages[$service->slug])
                        ? asset($serviceCardImages[$service->slug])
                        : $service->featured_image_url;
                @endphp

                <article class="group flex h-full flex-col overflow-hidden rounded-3xl border border-slate-200/80 bg-white shadow-sm transition-all duration-300 hover:-translate-y-1 hover:border-cyan-500/50 hover:shadow-xl">
                    @if($serviceCardImage)
                        <div class="relative aspect-[16/9] overflow-hidden bg-slate-100">
                            <img src="{{ $serviceCardImage }}"
                                 alt="{{ $service->title }} service in Dubai"
                                 width="800"
                                 height="450"
                                 loading="lazy"
                                 decoding="async"
                                 class="h-full w-full object-cover transition-transform duration-500 group-hover:scale-105">
                            <div class="pointer-events-none absolute inset-0 bg-gradient-to-t from-slate-950/20 via-transparent to-transparent"></div>
                        </div>
                    @endif

                    <div class="flex flex-1 flex-col justify-between p-6 sm:p-7">
                        <div class="space-y-4">
                            <div class="flex items-center justify-between">
                                <div class="flex h-12 w-12 items-center justify-center rounded-2xl bg-cyan-50 text-cyan-600 shadow-sm transition-all duration-300 group-hover:bg-gradient-to-tr group-hover:from-cyan-600 group-hover:to-blue-600 group-hover:text-white">
                                    <i data-lucide="{{ $service->icon ?: 'hard-drive' }}" class="h-6 w-6 stroke-[2]"></i>
                                </div>
                                <span class="rounded-full bg-slate-100 px-2.5 py-1 text-[10px] font-black uppercase text-slate-600">
                                    98.6% SUCCESS
                                </span>
                            </div>

                            <h3 class="text-xl font-black text-slate-900 transition-colors group-hover:text-cyan-700">
                                {{ $service->title }}
                            </h3>

                            <p class="line-clamp-3 text-xs leading-relaxed text-slate-600 sm:text-sm">
                                {{ $service->summary }}
                            </p>

                            @if(!empty($service->features) && is_array($service->features))
                                <ul class="space-y-1.5 border-t border-slate-100 pt-3">
                                    @foreach(array_slice($service->features, 0, 3) as $feat)
                                        <li class="flex items-center gap-2 text-xs font-medium text-slate-700">
                                            <i data-lucide="check" class="h-3.5 w-3.5 shrink-0 text-cyan-600"></i>
                                            <span class="truncate">{{ $feat }}</span>
                                        </li>
                                    @endforeach
                                </ul>
                            @endif
                        </div>

                        <div class="mt-6 flex items-center justify-between border-t border-slate-100 pt-5">
                            <a href="{{ url($service->slug) }}" class="inline-flex items-center gap-1.5 text-xs font-bold text-cyan-600 transition group-hover:text-cyan-700">
                                <span>View Technical Specifications</span>
                                <i data-lucide="arrow-right" class="h-3.5 w-3.5 transition-transform group-hover:translate-x-1"></i>
                            </a>
                            <a href="{{ route('contact') }}" class="text-[11px] font-semibold text-slate-400 hover:text-slate-800">
                                Free Evaluation &rarr;
                            </a>
                        </div>
                    </div>
                </article>
            @endforeach
        </div>
    </section>

</div>
@endsection
