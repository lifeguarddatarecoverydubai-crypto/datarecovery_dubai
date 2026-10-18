@extends('layouts.app')

@section('seo_meta')
    <title>#1 Data Recovery Dubai - Trusted Data Recovery Services Dubai | LifeGuard</title>
    <meta name="description" content="LifeGuard is the best Data Recovery service provider in Dubai with 30 years of experience. Certified Class 100 cleanroom lab for HDD, SSD, RAID, NAS, CCTV, and mobile devices.">
    <meta name="keywords" content="data recovery dubai, hard drive recovery dubai, ssd data recovery dubai, raid recovery dubai, nas recovery uae, cctv footage recovery dubai, macbook data recovery dubai, data recovery cost dubai">
    <link rel="canonical" href="{{ route('home') }}">
    <meta property="og:title" content="#1 Data Recovery Dubai - Trusted Data Recovery Services Dubai">
    <meta property="og:description" content="LifeGuard is the best Data Recovery service provider in Dubai with 30 years of experience. Experts in recovering data from HDD, SSD, RAID, NAS, CCTV, and mobile devices.">
    <meta property="og:type" content="website">
    <meta property="og:url" content="{{ route('home') }}">
    <meta property="og:site_name" content="LifeGuard Data Recovery">
    <meta property="og:image" content="{{ asset('media-bundle/media/pages/aboutus.webp') }}">
    <meta name="twitter:card" content="summary_large_image">

    <!-- Schema.org FAQPage JSON-LD -->
    <script type="application/ld+json">
    {!! json_encode(\App\Services\SchemaService::getFaqPageSchema([
        [
            'question' => 'What is the cost of data recovery in Dubai?',
            'answer' => 'Data recovery costs in Dubai depend on the storage device type (HDD, SSD, RAID, NAS, Flash drive) and the underlying failure mechanism (logical deletion vs mechanical cleanroom head replacement). At LifeGuard Data Recovery, we offer a 100% Free Diagnostic Evaluation and operate strictly on a No Data – No Charge policy with upfront itemized pricing before work commences.',
        ],
        [
            'question' => 'What should I do immediately if my hard drive is clicking or making strange noises?',
            'answer' => 'Power down your computer or external hard drive immediately and unplug the cable. A clicking noise indicates that the physical read/write heads are failing or contacting the magnetic platters. Continuing to run the drive can scrape off the magnetic layer and cause permanent, irrecoverable data loss. Bring it to an ISO-5 Class 100 cleanroom lab for safe head replacement.',
        ],
        [
            'question' => 'Can data be recovered from dead or unreadable SSDs and NVMe drives?',
            'answer' => 'Yes. Even if an SSD is completely dead, not detected by the BIOS, or showing 0GB / RAW partition, our flash recovery engineers use hardware PC-3000 Flash systems to desolder NAND flash memory chips, read the raw flash dumps directly, and reconstruct the proprietary translation table without relying on the damaged controller.',
        ],
        [
            'question' => 'How long does a data recovery process take?',
            'answer' => 'Standard evaluations are completed within 2 to 4 hours. Standard recovery typically takes 1 to 3 business days. For emergency mission-critical enterprise RAID arrays, database servers, and corporate cases, we provide a 24/7 Priority Emergency Service with around-the-clock cleanroom engineering.',
        ],
        [
            'question' => 'Is my data kept confidential and secure during recovery?',
            'answer' => 'Yes. LifeGuard Data Recovery maintains rigorous military-grade security protocols, signed Non-Disclosure Agreements (NDAs), encrypted clone storage, and isolated offline forensic workstations fully compliant with UAE data protection and privacy regulations.',
        ],
    ]), JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) !!}
    </script>

    <!-- Custom Schema.org JSON-LD (from Dashboard) -->
    <x-schema-json-ld :schema="$page->schema_markup ?? null" />
@endsection

@section('content')
<div class="space-y-20 sm:space-y-28 overflow-hidden">

    <!-- ================= 1. HERO SECTION ================= -->
    <section class="relative overflow-hidden pt-12 pb-20 lg:pt-20 lg:pb-32 bg-gradient-to-b from-slate-950 via-slate-900 to-slate-950 text-white">
        <!-- Ambient High-Tech Glows -->
        <div class="absolute top-1/4 left-1/2 -translate-x-1/2 w-[700px] h-[350px] bg-cyan-600/15 blur-[140px] rounded-full pointer-events-none"></div>
        <div class="absolute top-10 left-10 w-72 h-72 bg-blue-600/10 blur-[100px] rounded-full pointer-events-none"></div>

        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 relative z-10">
            <div class="grid grid-cols-1 lg:grid-cols-12 gap-12 items-center">
                
                <!-- Left Column: Copy & Actions -->
                <div class="lg:col-span-7 space-y-6 text-left">
                    <!-- Cleanroom Badge -->
                    <div class="inline-flex items-center gap-2 px-3.5 py-1.5 rounded-full bg-cyan-500/10 text-cyan-400 border border-cyan-500/30 text-xs font-bold tracking-wide">
                        <span class="w-2 h-2 rounded-full bg-cyan-400 animate-pulse"></span>
                        <span>ISO-5 CLASS 100 CLEANROOM • 30+ YEARS EXPERIENCE</span>
                    </div>

                    <!-- Main Headline -->
                    <h1 class="text-4xl sm:text-5xl lg:text-6xl font-black font-display tracking-tight text-white leading-tight">
                        #1 Data Recovery Dubai <br>
                        <span class="text-transparent bg-clip-text bg-gradient-to-r from-cyan-400 via-sky-300 to-blue-400">
                            Trusted Cleanroom Lab
                        </span>
                    </h1>

                    <!-- Subtitle -->
                    <p class="text-base sm:text-lg text-slate-300 max-w-2xl leading-relaxed">
                        Don’t panic and do not power on your damaged drive. LifeGuard recovers critical lost files from failed <strong>Hard Drives, SSDs, RAID Servers, NAS, CCTV, and Mobile Phones</strong> with a proven <strong>98.6% recovery rate</strong> under our strict <strong>No Data – No Charge policy</strong>.
                    </p>

                    <!-- Trust Metric Highlights -->
                    <div class="grid grid-cols-3 gap-4 pt-2 pb-2 border-y border-slate-800/80 max-w-xl">
                        <div>
                            <span class="block text-2xl sm:text-3xl font-black text-cyan-400">98.6%</span>
                            <span class="text-[11px] text-slate-400 uppercase font-semibold">Success Rate</span>
                        </div>
                        <div>
                            <span class="block text-2xl sm:text-3xl font-black text-white">100%</span>
                            <span class="text-[11px] text-slate-400 uppercase font-semibold">No Data, No Charge</span>
                        </div>
                        <div>
                            <span class="block text-2xl sm:text-3xl font-black text-emerald-400">24/7</span>
                            <span class="text-[11px] text-slate-400 uppercase font-semibold">Emergency Support</span>
                        </div>
                    </div>

                    <!-- Action Buttons -->
                    <div class="flex flex-wrap items-center gap-4 pt-2">
                        <a href="{{ route('contact') }}" 
                           class="inline-flex items-center gap-2.5 px-7 py-3.5 rounded-full bg-gradient-to-r from-cyan-500 via-blue-600 to-indigo-600 hover:from-cyan-400 hover:to-indigo-500 text-white font-extrabold text-sm shadow-xl shadow-cyan-500/25 transition-all transform hover:-translate-y-0.5">
                            <i data-lucide="microchip" class="w-4 h-4 stroke-[2.2]"></i>
                            <span>Book Free Evaluation</span>
                        </a>

                        <a href="tel:+97143933082" 
                           class="inline-flex items-center gap-2 px-6 py-3.5 rounded-full bg-slate-900 hover:bg-slate-800 border border-slate-700 text-white font-bold text-sm transition">
                            <i data-lucide="phone-call" class="w-4 h-4 text-cyan-400"></i>
                            <span>+971 43 933 082</span>
                        </a>

                        <a href="https://wa.me/971554838545" target="_blank"
                           class="inline-flex items-center gap-2 px-5 py-3.5 rounded-full bg-emerald-600/20 hover:bg-emerald-600/30 border border-emerald-500/40 text-emerald-300 font-bold text-sm transition">
                            <i data-lucide="message-circle" class="w-4 h-4"></i>
                            <span>WhatsApp</span>
                        </a>
                    </div>

                    <!-- Verified Google Rating -->
                    <div class="flex items-center gap-3 pt-2 text-xs text-slate-400">
                        <div class="flex text-amber-400">
                            ★★★★★
                        </div>
                        <span class="font-semibold text-slate-200">4.9 / 5.0 Rating</span>
                        <span class="text-slate-600">•</span>
                        <span>Over 540+ Verified UAE Client Reviews</span>
                    </div>
                </div>

                <!-- Right Column: Interactive Quick Evaluation Card -->
                <div class="lg:col-span-5">
                    <div class="bg-slate-900/90 border border-slate-800 rounded-3xl p-6 sm:p-8 shadow-2xl backdrop-blur-xl relative overflow-hidden" 
                         x-data="{ deviceType: 'hdd', failureType: 'clicking', name: '', phone: '', submitted: false }">
                        
                        <div class="flex items-center justify-between pb-5 border-b border-slate-800">
                            <div>
                                <h3 class="text-lg font-extrabold text-white">Instant Diagnostic Assessment</h3>
                                <p class="text-xs text-slate-400">Select your device symptom for instant triage</p>
                            </div>
                            <span class="px-2.5 py-1 text-[10px] font-extrabold uppercase rounded-md bg-cyan-500/20 text-cyan-300 border border-cyan-400/30">FREE</span>
                        </div>

                        <form action="{{ route('contact.submit') }}" method="POST" class="mt-6 space-y-4">
                            @csrf
                            <input type="hidden" name="source" value="Homepage Hero Diagnostic Tool">

                            <!-- Device Selector -->
                            <div>
                                <label for="home-device-type" class="block text-xs font-semibold text-slate-300 mb-1.5">Storage Device</label>
                                <select id="home-device-type" name="device_type" x-model="deviceType" class="w-full bg-slate-950 border border-slate-700 rounded-xl px-3.5 py-2.5 text-xs text-white focus:outline-hidden focus:border-cyan-500">
                                    <option value="hdd">External / Desktop Hard Drive (HDD)</option>
                                    <option value="ssd">Solid State Drive (SSD / NVMe / M.2)</option>
                                    <option value="raid">RAID Server Array (0, 1, 5, 6, 10, 50, 60)</option>
                                    <option value="nas">NAS Network Storage (Synology, QNAP, TrueNAS)</option>
                                    <option value="cctv">CCTV DVR / NVR Security Footage</option>
                                    <option value="mobile">iPhone / Android Mobile Phone</option>
                                    <option value="macbook">MacBook / Apple Silicon Logic Board</option>
                                    <option value="usb">USB Flash Drive / Camera SD Card</option>
                                </select>
                            </div>

                            <!-- Failure Symptom Selector -->
                            <div>
                                <label for="home-failure-type" class="block text-xs font-semibold text-slate-300 mb-1.5">Primary Problem / Symptom</label>
                                <select id="home-failure-type" name="failure_type" x-model="failureType" class="w-full bg-slate-950 border border-slate-700 rounded-xl px-3.5 py-2.5 text-xs text-white focus:outline-hidden focus:border-cyan-500">
                                    <option value="clicking">Clicking, Beeping, or Grinding Sounds</option>
                                    <option value="not_detected">Not Detected / Drive Won't Spin Up</option>
                                    <option value="deleted">Formatted Partition or Accidentally Deleted Files</option>
                                    <option value="dropped">Physically Dropped or Liquid / Water Damaged</option>
                                    <option value="burnt">Burnt Circuit Board / Electrical Surge / Smell</option>
                                    <option value="ransomware">Encrypted by Ransomware / Corrupted File System</option>
                                </select>
                            </div>

                            <!-- Contact Inputs -->
                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 pt-1">
                                <div>
                                    <label for="home-name" class="block text-[11px] font-semibold text-slate-400 mb-1">Your Name</label>
                                    <input id="home-name" type="text" name="name" required placeholder="Ahmed Al-Mansoor" class="w-full bg-slate-950 border border-slate-700 rounded-xl px-3 py-2 text-xs text-white focus:outline-hidden focus:border-cyan-500">
                                </div>
                                <div>
                                    <label for="home-phone" class="block text-[11px] font-semibold text-slate-400 mb-1">Phone / WhatsApp</label>
                                    <input id="home-phone" type="text" name="phone" required placeholder="+971 50 123 4567" class="w-full bg-slate-950 border border-slate-700 rounded-xl px-3 py-2 text-xs text-white focus:outline-hidden focus:border-cyan-500">
                                </div>
                            </div>

                            <!-- Submit Button -->
                            <button type="submit" class="w-full py-3.5 rounded-xl bg-gradient-to-r from-cyan-500 via-blue-600 to-indigo-600 hover:from-cyan-400 hover:to-indigo-500 text-white font-extrabold text-xs shadow-lg shadow-cyan-600/30 transition-all flex items-center justify-center gap-2">
                                <i data-lucide="send" class="w-3.5 h-3.5"></i>
                                <span>Request Free Lab Evaluation & Priority Pickup</span>
                            </button>

                            <p class="text-[10px] text-center text-slate-500">
                                Strictly confidential. We sign Non-Disclosure Agreements (NDA). Zero spam guarantee.
                            </p>
                        </form>
                    </div>
                </div>

            </div>
        </div>
    </section>

    <!-- ================= 2. CORE SPECIALIZED SERVICES ================= -->
    <section class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center max-w-3xl mx-auto mb-16 space-y-3">
            <span class="text-xs font-extrabold tracking-widest uppercase text-cyan-600">Enterprise & Personal Recovery</span>
            <h2 class="text-3xl sm:text-4xl font-black font-display text-slate-900 tracking-tight">
                Specialized Data Recovery Services
            </h2>
            <p class="text-slate-600 text-sm sm:text-base leading-relaxed">
                Cleanroom hardware diagnostics and proprietary software extraction across all digital storage media.
            </p>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
            @foreach($services as $service)
                <div class="group bg-white rounded-3xl p-8 border border-slate-200/80 hover:border-cyan-500/50 shadow-sm hover:shadow-xl transition-all duration-300 flex flex-col justify-between">
                    <div class="space-y-4">
                        <!-- Icon & Order Badge -->
                        <div class="flex items-center justify-between">
                            <div class="w-12 h-12 rounded-2xl bg-cyan-50 group-hover:bg-gradient-to-tr group-hover:from-cyan-600 group-hover:to-blue-600 text-cyan-600 group-hover:text-white flex items-center justify-center transition-all duration-300 shadow-sm">
                                <i data-lucide="{{ $service->icon ?: 'hard-drive' }}" class="w-6 h-6 stroke-[2]"></i>
                            </div>
                            <span class="text-[10px] font-black uppercase px-2.5 py-1 rounded-full bg-slate-100 text-slate-600">
                                98.6% SUCCESS
                            </span>
                        </div>

                        <!-- Title -->
                        <h3 class="text-xl font-black text-slate-900 group-hover:text-cyan-700 transition-colors">
                            {{ $service->title }}
                        </h3>

                        <!-- Summary -->
                        <p class="text-xs sm:text-sm text-slate-600 leading-relaxed line-clamp-3">
                            {{ $service->summary }}
                        </p>

                        <!-- Key Features -->
                        @if(!empty($service->features) && is_array($service->features))
                            <ul class="space-y-1.5 pt-2 border-t border-slate-100">
                                @foreach(array_slice($service->features, 0, 3) as $feat)
                                    <li class="flex items-center gap-2 text-xs text-slate-700 font-medium">
                                        <i data-lucide="check" class="w-3.5 h-3.5 text-cyan-600 shrink-0"></i>
                                        <span class="truncate">{{ $feat }}</span>
                                    </li>
                                @endforeach
                            </ul>
                        @endif
                    </div>

                    <!-- Action Link -->
                    <div class="pt-6 mt-6 border-t border-slate-100 flex items-center justify-between">
                        <a href="{{ url($service->slug) }}" class="inline-flex items-center gap-1.5 text-xs font-bold text-cyan-600 group-hover:text-cyan-700 transition">
                            <span>Explore Recovery Details</span>
                            <i data-lucide="arrow-right" class="w-3.5 h-3.5 transition-transform group-hover:translate-x-1"></i>
                        </a>
                        <a href="{{ route('contact') }}" class="text-[11px] font-semibold text-slate-400 hover:text-slate-800">
                            Get Quote &rarr;
                        </a>
                    </div>
                </div>
            @endforeach
        </div>
    </section>

    <!-- ================= 3. OUR 4-STEP PROVEN PROCESS ================= -->
    <section class="bg-slate-900 text-white py-20 relative overflow-hidden">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 relative z-10">
            <div class="text-center max-w-3xl mx-auto mb-16 space-y-3">
                <span class="text-xs font-extrabold tracking-widest uppercase text-cyan-400">Streamlined & Transparent</span>
                <h2 class="text-3xl sm:text-4xl font-black font-display tracking-tight text-white">
                    Our 4-Step Cleanroom Recovery Process
                </h2>
                <p class="text-slate-400 text-sm leading-relaxed">
                    Zero guesswork, strict data confidentiality, and no payment required until your critical files are recovered and verified.
                </p>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-8">
                <!-- Step 1 -->
                <div class="bg-slate-950/70 border border-slate-800 p-6 rounded-2xl space-y-3 relative">
                    <span class="text-3xl font-black text-cyan-500/30">01</span>
                    <h3 class="text-base font-bold text-white">Free Diagnostic Evaluation</h3>
                    <p class="text-xs text-slate-400 leading-relaxed">
                        Drop off your drive or book a complimentary Dubai courier pickup. Our certified engineers inspect the hardware in our lab within 2–4 hours.
                    </p>
                </div>

                <!-- Step 2 -->
                <div class="bg-slate-950/70 border border-slate-800 p-6 rounded-2xl space-y-3 relative">
                    <span class="text-3xl font-black text-cyan-500/30">02</span>
                    <h3 class="text-base font-bold text-white">Fixed Quote & File List</h3>
                    <p class="text-xs text-slate-400 leading-relaxed">
                        We provide an exact diagnostic report with an itemized price quote and expected turnaround. You decide whether to proceed with zero obligation.
                    </p>
                </div>

                <!-- Step 3 -->
                <div class="bg-slate-950/70 border border-slate-800 p-6 rounded-2xl space-y-3 relative">
                    <span class="text-3xl font-black text-cyan-500/30">03</span>
                    <h3 class="text-base font-bold text-white">Class 100 Cleanroom Extraction</h3>
                    <p class="text-xs text-slate-400 leading-relaxed">
                        Mechanical head swaps, PCB micro-soldering, and raw NAND dumps are carried out inside laminar flow benches using DeepSpar & PC-3000 gear.
                    </p>
                </div>

                <!-- Step 4 -->
                <div class="bg-slate-950/70 border border-slate-800 p-6 rounded-2xl space-y-3 relative">
                    <span class="text-3xl font-black text-cyan-500/30">04</span>
                    <h3 class="text-base font-bold text-white">Verification & Safe Delivery</h3>
                    <p class="text-xs text-slate-400 leading-relaxed">
                        You inspect and verify your recovered folders. Once completely satisfied, your restored data is released on an encrypted transfer drive.
                    </p>
                </div>
            </div>
        </div>
    </section>

    <!-- ================= 4. WHY LIFEGUARD (CERTIFIED CLEANROOM LAB) ================= -->
    <section class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="bg-gradient-to-br from-slate-950 via-slate-900 to-slate-950 text-white rounded-3xl p-8 sm:p-12 lg:p-16 border border-slate-800 relative overflow-hidden">
            <div class="grid grid-cols-1 lg:grid-cols-12 gap-12 items-center relative z-10">
                <div class="lg:col-span-7 space-y-6">
                    <span class="text-xs font-extrabold uppercase tracking-widest text-cyan-400">Advanced Cleanroom Facilities</span>
                    <h2 class="text-3xl sm:text-4xl font-black font-display tracking-tight leading-tight">
                        Why Dubai Businesses Trust LifeGuard Data Recovery
                    </h2>
                    <p class="text-slate-300 text-sm leading-relaxed">
                        Hard drive platters operate with microscopic clearance under 3 nanometers. Opening a mechanical disk outside a certified cleanroom immediately ruins it through dust particle contamination. LifeGuard maintains verified ISO-5 Class 100 cleanroom environments right here in Dubai.
                    </p>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 pt-2">
                        <div class="flex items-start gap-3">
                            <div class="w-8 h-8 rounded-lg bg-cyan-500/20 text-cyan-400 flex items-center justify-center shrink-0">
                                <i data-lucide="shield-check" class="w-4 h-4"></i>
                            </div>
                            <div>
                                <h4 class="text-xs font-bold text-white">No Data, No Charge Policy</h4>
                                <p class="text-[11px] text-slate-400 mt-0.5">If we cannot recover your critical files, you pay nothing for the recovery service.</p>
                            </div>
                        </div>

                        <div class="flex items-start gap-3">
                            <div class="w-8 h-8 rounded-lg bg-cyan-500/20 text-cyan-400 flex items-center justify-center shrink-0">
                                <i data-lucide="lock" class="w-4 h-4"></i>
                            </div>
                            <div>
                                <h4 class="text-xs font-bold text-white">Strict Non-Disclosure (NDA)</h4>
                                <p class="text-[11px] text-slate-400 mt-0.5">Full confidentiality compliance for banks, hospitals, legal firms, and corporate databases.</p>
                            </div>
                        </div>

                        <div class="flex items-start gap-3">
                            <div class="w-8 h-8 rounded-lg bg-cyan-500/20 text-cyan-400 flex items-center justify-center shrink-0">
                                <i data-lucide="cpu" class="w-4 h-4"></i>
                            </div>
                            <div>
                                <h4 class="text-xs font-bold text-white">PC-3000 & DeepSpar Lab</h4>
                                <p class="text-[11px] text-slate-400 mt-0.5">Equipped with industry-standard hardware tools for SAS, SCSI, PCIe, and NVMe controller repairs.</p>
                            </div>
                        </div>

                        <div class="flex items-start gap-3">
                            <div class="w-8 h-8 rounded-lg bg-cyan-500/20 text-cyan-400 flex items-center justify-center shrink-0">
                                <i data-lucide="map-pin" class="w-4 h-4"></i>
                            </div>
                            <div>
                                <h4 class="text-xs font-bold text-white">Central Dubai Lab & Branches</h4>
                                <p class="text-[11px] text-slate-400 mt-0.5">Convenient drop-offs in Bur Dubai (Computer St) and Al Barsha (Mall of Emirates).</p>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="lg:col-span-5 space-y-4">
                    <div class="rounded-2xl overflow-hidden border border-slate-700 shadow-2xl bg-slate-900">
                        <img src="{{ asset('media-bundle/media/pages/aboutus.webp') }}"
                             alt="LifeGuard Data Recovery Cleanroom Lab Dubai" 
                             class="w-full h-64 object-cover">
                        <div class="p-5 space-y-2">
                            <div class="flex items-center justify-between text-xs font-semibold text-slate-300">
                                <span>Dubai Lab Status:</span>
                                <span class="text-emerald-400 flex items-center gap-1">
                                    <span class="w-2 h-2 rounded-full bg-emerald-400 animate-pulse"></span>
                                    Operational 24/7
                                </span>
                            </div>
                            <div class="text-xs text-slate-400">
                                Central Lab: Bayat Center S25, Computer Street, Bur Dubai
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- ================= 5. RECENT BLOG & TECHNICAL GUIDES (396+ POSTS) ================= -->
    <section class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex flex-col md:flex-row items-center justify-between gap-4 mb-12">
            <div>
                <span class="text-xs font-extrabold tracking-widest uppercase text-cyan-600">Knowledge Base</span>
                <h2 class="text-3xl font-black font-display text-slate-900 tracking-tight">
                    Data Recovery Articles & Tech Insights
                </h2>
            </div>
            <a href="{{ route('blog.index') }}" class="inline-flex items-center gap-2 text-xs font-extrabold text-cyan-600 hover:text-cyan-800 transition">
                <span>View All Articles ({{ \App\Models\Post::published()->count() }})</span>
                <i data-lucide="arrow-right" class="w-4 h-4"></i>
            </a>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
            @foreach($latestPosts as $post)
                <article class="bg-white rounded-3xl overflow-hidden border border-slate-200/80 hover:border-cyan-500/50 shadow-sm hover:shadow-xl transition-all duration-300 flex flex-col justify-between">
                    <div>
                        <!-- Featured Image -->
                        <a href="{{ url($post->slug) }}" class="block aspect-[16/9] bg-slate-100 overflow-hidden relative">
                            @if($post->featured_image_url)
                                <img src="{{ $post->featured_image_url }}" 
                                     alt="{{ $post->title }}" 
                                     loading="lazy" 
                                     class="w-full h-full object-cover transition-transform duration-500 hover:scale-105">
                            @else
                                <div class="w-full h-full flex items-center justify-center bg-slate-900 text-cyan-400">
                                    <i data-lucide="hard-drive" class="w-8 h-8"></i>
                                </div>
                            @endif

                            @if($post->categories->isNotEmpty())
                                <span class="absolute top-3 left-3 px-2.5 py-1 text-[10px] font-bold rounded-lg bg-slate-900/80 text-white backdrop-blur-sm">
                                    {{ $post->categories->first()->name }}
                                </span>
                            @endif
                        </a>

                        <!-- Content -->
                        <div class="p-6 space-y-3">
                            <div class="text-[11px] text-slate-400 flex items-center gap-2">
                                <span>{{ $post->published_at ? $post->published_at->format('M d, Y') : 'Recent' }}</span>
                                <span>•</span>
                                <span>{{ $post->reading_time ?? 5 }} min read</span>
                            </div>

                            <h3 class="text-base font-bold text-slate-900 hover:text-cyan-700 transition leading-snug line-clamp-2">
                                <a href="{{ url($post->slug) }}">
                                    {{ $post->title }}
                                </a>
                            </h3>

                            <p class="text-xs text-slate-500 line-clamp-3 leading-relaxed">
                                {{ $post->excerpt }}
                            </p>
                        </div>
                    </div>

                    <div class="px-6 pb-6 pt-2 border-t border-slate-100">
                        <a href="{{ url($post->slug) }}" class="inline-flex items-center gap-1.5 text-xs font-bold text-cyan-600 hover:text-cyan-700">
                            <span>Read Full Guide</span>
                            <i data-lucide="arrow-right" class="w-3.5 h-3.5"></i>
                        </a>
                    </div>
                </article>
            @endforeach
        </div>
    </section>

    <!-- ================= 6. FREQUENTLY ASKED QUESTIONS (FAQ ACCORDION) ================= -->
    <section class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8" x-data="{ activeFaq: null }">
        <div class="text-center mb-12 space-y-3">
            <span class="text-xs font-extrabold tracking-widest uppercase text-cyan-600">Client Support</span>
            <h2 class="text-3xl font-black font-display text-slate-900 tracking-tight">
                Frequently Asked Questions
            </h2>
            <p class="text-slate-600 text-sm">
                Common questions about data recovery pricing, cleanroom procedures, and turnaround times in Dubai.
            </p>
        </div>

        <div class="space-y-4">
            @php
                $faqs = [
                    [
                        'q' => 'What is the cost of data recovery in Dubai?',
                        'a' => 'Data recovery costs vary based on the specific media type (internal/external HDD, NVMe SSD, RAID, NAS, USB, CCTV) and failure type (logical corruption vs physical head damage). At LifeGuard Data Recovery, evaluations are 100% free, and we follow a strict No Data – No Charge policy with transparent fixed quotes.',
                    ],
                    [
                        'q' => 'Why is it dangerous to power on a clicking hard drive?',
                        'a' => 'A clicking noise occurs when the read/write slider has failed or mechanical heads cannot find servo sector tracks. Every time the disk spins up, damaged heads can grind directly into the magnetic coating of the platter, causing permanent data destruction. Always leave the drive powered off.',
                    ],
                    [
                        'q' => 'Can files be recovered from water-damaged or burned devices?',
                        'a' => 'Yes. We have high recovery success rates on liquid-damaged phones, submerged hard drives, and fire-damaged storage. Liquid damage requires immediate ultrasonic cleaning and motherboard micro-soldering before corrosion destroys the traces.',
                    ],
                    [
                        'q' => 'How does your "No Data, No Charge" guarantee work?',
                        'a' => 'If your device is assessed and our engineers are unable to recover your specified critical target files, you do not pay any recovery fees. You only pay when your verified data is retrieved and ready for return.',
                    ],
                    [
                        'q' => 'Do you provide emergency 24/7 service for business servers and RAID arrays?',
                        'a' => 'Yes! We offer 24/7 emergency priority dispatch for mission-critical corporate databases, Dell PowerEdge, HP ProLiant, and Synology NAS arrays. Cleanroom technicians work around the clock to minimize business downtime.',
                    ],
                ];
            @endphp

            @foreach($faqs as $index => $faq)
                <div class="bg-white rounded-2xl border border-slate-200 overflow-hidden shadow-xs">
                    <button type="button" 
                            @click="activeFaq = (activeFaq === {{ $index }} ? null : {{ $index }})" 
                            class="w-full px-6 py-5 text-left flex items-center justify-between gap-4 font-bold text-slate-900 hover:text-cyan-700 transition">
                        <span class="text-sm sm:text-base">{{ $faq['q'] }}</span>
                        <i data-lucide="chevron-down" class="w-4 h-4 text-slate-400 transition-transform duration-200 shrink-0" :class="activeFaq === {{ $index }} ? 'rotate-180 text-cyan-600' : ''"></i>
                    </button>
                    <div x-show="activeFaq === {{ $index }}" x-cloak class="px-6 pb-5 pt-1 text-xs sm:text-sm text-slate-600 leading-relaxed border-t border-slate-100">
                        {{ $faq['a'] }}
                    </div>
                </div>
            @endforeach
        </div>
    </section>

</div>
@endsection
