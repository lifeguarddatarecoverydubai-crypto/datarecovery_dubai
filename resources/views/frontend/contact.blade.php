@extends('layouts.app')

@section('seo_meta')
    <title>Contact LifeGuard Data Recovery Dubai | 24/7 Lab Hotline & Walk-in Locations</title>
    <meta name="description" content="Contact LifeGuard Data Recovery Dubai. Free diagnostic evaluation and 24/7 emergency support. Visit our Bur Dubai or Al Barsha labs. Hotline: +971 43 933 082.">
    <meta name="keywords" content="contact lifeguard data recovery, data recovery dubai phone number, data recovery bur dubai, data recovery al barsha, emergency data recovery uae">
    <link rel="canonical" href="{{ route('contact') }}">
    <meta property="og:title" content="Contact LifeGuard Data Recovery Dubai | 24/7 Cleanroom Hotline">
    <meta property="og:description" content="Speak directly with a data recovery specialist. Free diagnostics, strict confidentiality, and No Data No Charge guarantee.">
    <meta property="og:url" content="{{ route('contact') }}">
    <meta property="og:type" content="website">

    <!-- Schema.org BreadcrumbList JSON-LD -->
    <script type="application/ld+json">
    {!! json_encode(\App\Services\SchemaService::getBreadcrumbSchema([
        'Home' => '/',
        'Contact Us' => '/contact'
    ]), JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) !!}
    </script>

    <!-- Custom Schema.org JSON-LD (from Dashboard) -->
    <x-schema-json-ld :schema="$page->schema_markup ?? null" />
@endsection

@section('content')
<div class="bg-slate-50 min-h-screen">

    <!-- Header Section -->
    <section class="relative bg-gradient-to-b from-slate-950 via-slate-900 to-slate-950 text-white pt-24 pb-20 overflow-hidden">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 relative z-10 text-center max-w-3xl">
            <span class="inline-flex items-center gap-2 px-3.5 py-1 rounded-full text-xs font-bold bg-cyan-500/20 text-cyan-300 border border-cyan-500/30 mb-6 backdrop-blur-sm">
                <span class="w-2 h-2 rounded-full bg-emerald-400 animate-ping"></span>
                <span>24/7 EMERGENCY DATA RECOVERY HOTLINE & LAB ACCESS</span>
            </span>
            <h1 class="text-3xl sm:text-5xl font-black font-display tracking-tight text-white">
                Contact <span class="text-transparent bg-clip-text bg-gradient-to-r from-cyan-400 to-blue-400">LifeGuard Data Recovery</span>
            </h1>
            <p class="mt-4 text-sm sm:text-base text-slate-300 font-normal leading-relaxed">
                Whether you need urgent emergency recovery for a mission-critical enterprise RAID server or personal photos from a dropped hard drive, our cleanroom engineers in Dubai are ready to evaluate your device immediately.
            </p>
        </div>
    </section>

    <!-- Main Contact Grid -->
    <section class="py-16 max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="grid grid-cols-1 lg:grid-cols-12 gap-10 items-start max-w-6xl mx-auto">
            
            <!-- Contact Info & Locations (5 cols) -->
            <div class="lg:col-span-5 bg-gradient-to-br from-slate-950 via-slate-900 to-slate-950 text-white rounded-3xl p-8 sm:p-10 space-y-8 shadow-2xl border border-slate-800">
                <div>
                    <div class="flex items-center gap-2.5 mb-2">
                        <div class="w-8 h-8 rounded-xl bg-cyan-600 text-white flex items-center justify-center font-black">
                            <i data-lucide="hard-drive-download" class="w-4 h-4 stroke-[2.2]"></i>
                        </div>
                        <span class="text-base font-black tracking-tight text-white">LIFEGUARD <span class="text-cyan-400">DATA LAB</span></span>
                    </div>
                    <p class="text-slate-400 text-xs leading-relaxed">
                        ISO-5 Class 100 Cleanroom lab, military-grade data confidentiality, and over 30 years of combined recovery engineering.
                    </p>
                </div>

                <div class="space-y-6 text-xs text-slate-300">
                    <div class="flex items-start gap-3.5">
                        <div class="w-9 h-9 rounded-xl bg-cyan-500/10 border border-cyan-500/20 text-cyan-400 flex items-center justify-center shrink-0">
                            <i data-lucide="phone-call" class="w-4 h-4"></i>
                        </div>
                        <div>
                            <span class="text-[10px] font-bold uppercase text-slate-400 block">Bur Dubai Main Lab Hotline</span>
                            <a href="tel:+97143933082" class="font-extrabold text-white hover:text-cyan-400 transition text-sm">+971 43 933 082</a>
                            <span class="text-[11px] text-slate-400 block mt-0.5">Mobile / Direct: +971 55 483 8545</span>
                        </div>
                    </div>

                    <div class="flex items-start gap-3.5">
                        <div class="w-9 h-9 rounded-xl bg-emerald-500/10 border border-emerald-500/20 text-emerald-400 flex items-center justify-center shrink-0">
                            <i data-lucide="message-circle" class="w-4 h-4"></i>
                        </div>
                        <div>
                            <span class="text-[10px] font-bold uppercase text-slate-400 block">Instant WhatsApp Support</span>
                            <a href="https://wa.me/971554838545" target="_blank" class="font-bold text-white hover:text-emerald-400 transition text-xs">+971 55 483 8545</a>
                            <span class="text-[11px] text-emerald-400 block mt-0.5">&bull; 24/7 Direct chat with lab technician</span>
                        </div>
                    </div>

                    <div class="flex items-start gap-3.5">
                        <div class="w-9 h-9 rounded-xl bg-cyan-500/10 border border-cyan-500/20 text-cyan-400 flex items-center justify-center shrink-0">
                            <i data-lucide="mail" class="w-4 h-4"></i>
                        </div>
                        <div>
                            <span class="text-[10px] font-bold uppercase text-slate-400 block">Email Inquiries</span>
                            <a href="mailto:info@lifeguarddatarecovery.com" class="font-bold text-white hover:underline text-xs">info@lifeguarddatarecovery.com</a>
                        </div>
                    </div>

                    <div class="flex items-start gap-3.5">
                        <div class="w-9 h-9 rounded-xl bg-cyan-500/10 border border-cyan-500/20 text-cyan-400 flex items-center justify-center shrink-0">
                            <i data-lucide="map-pin" class="w-4 h-4"></i>
                        </div>
                        <div>
                            <span class="text-[10px] font-bold uppercase text-slate-400 block">Dubai (Main Office - Bur Dubai)</span>
                            <span class="font-bold text-white block">Bayat center S25 Computer Street</span>
                            <span class="text-slate-400 block text-[11px]">Bur Dubai - Dubai.</span>
                            <span class="text-slate-300 block text-[11px]">Tel: +971 55 483 8545 | Fax: +971 43 933 082</span>
                            <span class="text-cyan-400 block text-[10px] mt-1 font-semibold">Mon - Sat: 9:00 AM - 9:00 PM (24/7 Lab Access)</span>
                        </div>
                    </div>

                    <div class="flex items-start gap-3.5">
                        <div class="w-9 h-9 rounded-xl bg-blue-500/10 border border-blue-500/20 text-blue-400 flex items-center justify-center shrink-0">
                            <i data-lucide="building-2" class="w-4 h-4"></i>
                        </div>
                        <div>
                            <span class="text-[10px] font-bold uppercase text-slate-400 block">Al Barsha Branch</span>
                            <span class="font-bold text-white block">DasselDorf Business Point, AL Barsha 1</span>
                            <span class="text-slate-400 block text-[11px]">Behind Mall Of Emirates, Dubai, United Arab Emirates.</span>
                            <span class="text-slate-300 block text-[11px]">Phone: +971 56 410 1040</span>
                        </div>
                    </div>

                    <div class="flex items-start gap-3.5">
                        <div class="w-9 h-9 rounded-xl bg-indigo-500/10 border border-indigo-500/20 text-indigo-400 flex items-center justify-center shrink-0">
                            <i data-lucide="map" class="w-4 h-4"></i>
                        </div>
                        <div>
                            <span class="text-[10px] font-bold uppercase text-slate-400 block">Other UAE & Regional Branches</span>
                            <span class="text-slate-300 block text-[11px]">&bull; <strong>Abu Dhabi:</strong> Electra Street, 11 Al Raqqay St (+971 50 217 6793)</span>
                            <span class="text-slate-300 block text-[11px]">&bull; <strong>Sharjah:</strong> Avenue Business Centre, Al Majaz (+971 50 634 3878)</span>
                            <span class="text-slate-300 block text-[11px]">&bull; <strong>Al Wasl:</strong> RAG Biz HQ, Addiyar Bldg, SZR (+971 56 997 8107)</span>
                            <span class="text-slate-300 block text-[11px]">&bull; <strong>India:</strong> CTC Park Ln, Secunderabad (+91 89775 44544)</span>
                            <span class="text-slate-300 block text-[11px]">&bull; <strong>Kenya:</strong> Mirage Tower, Nairobi (+254 748 020 399)</span>
                        </div>
                    </div>
                </div>

                <div class="p-4 rounded-2xl bg-cyan-950/40 border border-cyan-800/40 text-[11px] text-cyan-200 leading-relaxed">
                    <strong>Free Courier Pickup:</strong> Cannot visit in person? We provide free safe courier collection across Dubai, Abu Dhabi, Sharjah, and Northern Emirates with chain-of-custody tracking.
                </div>
            </div>

            <!-- Online Booking / Inquiry Form (7 cols) -->
            <div class="lg:col-span-7 bg-white rounded-3xl p-8 sm:p-10 border border-slate-200/80 shadow-xl space-y-6">
                <div>
                    <h3 class="text-2xl font-black font-display text-slate-900 tracking-tight">Book Free Diagnostic Evaluation</h3>
                    <p class="text-xs sm:text-sm text-slate-500 mt-1">
                        Fill in your storage device details below. An engineer will contact you within 15 minutes.
                    </p>
                </div>

                @if(session('success'))
                    <div class="p-4 rounded-2xl bg-emerald-50 border border-emerald-200 text-emerald-800 text-xs flex items-center gap-2">
                        <i data-lucide="check-circle" class="w-5 h-5 text-emerald-600 shrink-0"></i>
                        <span>{{ session('success') }}</span>
                    </div>
                @endif

                <form action="{{ route('contact.submit') }}" method="POST" class="space-y-4">
                    @csrf
                    <input type="hidden" name="source" value="Contact Page Main Form">

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-xs font-bold text-slate-700 mb-1.5">Full Name *</label>
                            <input type="text" name="name" required placeholder="Your Name" 
                                   class="w-full bg-slate-50 border border-slate-200 rounded-xl px-4 py-3 text-xs text-slate-900 focus:outline-hidden focus:border-cyan-500 focus:bg-white transition">
                        </div>

                        <div>
                            <label class="block text-xs font-bold text-slate-700 mb-1.5">Phone / WhatsApp *</label>
                            <input type="text" name="phone" required placeholder="+971 50 123 4567" 
                                   class="w-full bg-slate-50 border border-slate-200 rounded-xl px-4 py-3 text-xs text-slate-900 focus:outline-hidden focus:border-cyan-500 focus:bg-white transition">
                        </div>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-xs font-bold text-slate-700 mb-1.5">Email Address</label>
                            <input type="email" name="email" placeholder="name@company.com" 
                                   class="w-full bg-slate-50 border border-slate-200 rounded-xl px-4 py-3 text-xs text-slate-900 focus:outline-hidden focus:border-cyan-500 focus:bg-white transition">
                        </div>

                        <div>
                            <label class="block text-xs font-bold text-slate-700 mb-1.5">Device Type *</label>
                            <select name="device_type" class="w-full bg-slate-50 border border-slate-200 rounded-xl px-4 py-3 text-xs text-slate-900 focus:outline-hidden focus:border-cyan-500 focus:bg-white transition">
                                <option value="External Hard Drive">External Hard Drive (WD, Seagate, Toshiba)</option>
                                <option value="Internal Laptop/Desktop Drive">Internal HDD (SATA / IDE)</option>
                                <option value="Solid State Drive">SSD / NVMe / M.2 Flash Drive</option>
                                <option value="RAID Server Array">RAID Array / Server (RAID 0, 1, 5, 6, 10)</option>
                                <option value="NAS Enclosure">NAS Storage (Synology, QNAP, Buffalo)</option>
                                <option value="CCTV DVR / NVR">CCTV Video Surveillance DVR / NVR</option>
                                <option value="Smartphone / iPhone">iPhone / Android Mobile Phone</option>
                                <option value="MacBook Logic Board">MacBook Pro / Air Apple Silicon</option>
                                <option value="USB / Flash Card">USB Flash Drive / Camera SD Card</option>
                                <option value="Other">Other Digital Storage Device</option>
                            </select>
                        </div>
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-slate-700 mb-1.5">Symptom & Data Description</label>
                        <textarea name="message" rows="4" placeholder="Describe what happened: is the drive clicking? not detected? formatted? critical files needed..." 
                                  class="w-full bg-slate-50 border border-slate-200 rounded-xl px-4 py-3 text-xs text-slate-900 focus:outline-hidden focus:border-cyan-500 focus:bg-white transition"></textarea>
                    </div>

                    <div class="pt-2">
                        <button type="submit" 
                                class="w-full py-4 rounded-xl bg-gradient-to-r from-cyan-600 via-blue-600 to-indigo-600 hover:from-cyan-700 hover:to-indigo-700 text-white font-extrabold text-xs shadow-lg shadow-cyan-600/30 transition-all flex items-center justify-center gap-2">
                            <i data-lucide="shield-check" class="w-4 h-4"></i>
                            <span>Submit for Free Diagnostic Evaluation</span>
                        </button>
                    </div>

                    <div class="flex items-center justify-center gap-4 text-[11px] text-slate-400 pt-2">
                        <span class="flex items-center gap-1"><i data-lucide="check" class="w-3 h-3 text-cyan-600"></i> No Data No Charge</span>
                        <span>&bull;</span>
                        <span class="flex items-center gap-1"><i data-lucide="check" class="w-3 h-3 text-cyan-600"></i> Signed NDA Protected</span>
                        <span>&bull;</span>
                        <span class="flex items-center gap-1"><i data-lucide="check" class="w-3 h-3 text-cyan-600"></i> Free Courier Pickup</span>
                    </div>
                </form>
            </div>

        </div>
    </section>

    <!-- Official Verified Regional Offices Grid -->
    <section class="py-16 bg-slate-900 text-white border-t border-slate-800">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center max-w-3xl mx-auto mb-12">
                <span class="text-xs font-bold uppercase tracking-widest text-cyan-400 block">Verified Physical Centers</span>
                <h2 class="text-2xl sm:text-3xl font-black font-display tracking-tight text-white mt-1">
                    LifeGuard Regional Offices & Recovery Labs
                </h2>
                <p class="text-xs sm:text-sm text-slate-400 mt-2">
                    Direct walk-in drop off and lab evaluation across our 7 official branches in UAE, India, and Kenya.
                </p>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                @foreach(\App\Services\LocationService::all() as $loc)
                    <div class="bg-slate-950 rounded-2xl border border-slate-800 p-6 flex flex-col justify-between hover:border-cyan-500/50 transition group">
                        <div class="space-y-4">
                            <div class="flex items-center justify-between gap-3 border-b border-slate-800/80 pb-3">
                                <div class="flex items-center gap-2.5">
                                    <span class="text-2xl select-none">{{ $loc['flag'] }}</span>
                                    <div>
                                        <h3 class="text-base font-bold text-white group-hover:text-cyan-400 transition">
                                            {{ $loc['office_title'] }}
                                        </h3>
                                        <span class="text-[10px] uppercase font-bold text-cyan-500/80 block">{{ $loc['tag'] }}</span>
                                    </div>
                                </div>
                                @if(!empty($loc['is_headquarters']))
                                    <span class="px-2 py-0.5 rounded-full text-[9px] font-black uppercase bg-emerald-500/20 text-emerald-400 border border-emerald-500/30">HQ</span>
                                @endif
                            </div>

                            <div class="space-y-2.5 text-xs text-slate-300">
                                <div class="flex items-start gap-2.5">
                                    <i data-lucide="map-pin" class="w-4 h-4 text-cyan-400 shrink-0 mt-0.5"></i>
                                    <span class="text-slate-300 leading-relaxed">{{ $loc['address'] }}</span>
                                </div>
                                <div class="flex items-center gap-2.5">
                                    <i data-lucide="phone" class="w-4 h-4 text-cyan-400 shrink-0"></i>
                                    <a href="tel:{{ $loc['phone_link'] }}" class="font-bold text-white hover:text-cyan-400 transition">{{ $loc['phone'] }}</a>
                                </div>
                                @if(!empty($loc['fax']))
                                    <div class="flex items-center gap-2.5">
                                        <i data-lucide="send" class="w-4 h-4 text-cyan-400 shrink-0"></i>
                                        <span class="text-slate-400">Fax: <strong class="text-slate-200">{{ $loc['fax'] }}</strong></span>
                                    </div>
                                @endif
                                @if(!empty($loc['website']))
                                    <div class="flex items-center gap-2.5">
                                        <i data-lucide="globe" class="w-4 h-4 text-cyan-400 shrink-0"></i>
                                        <a href="{{ $loc['website_url'] }}" target="_blank" rel="noopener noreferrer" class="font-semibold text-cyan-400 hover:underline">{{ $loc['website'] }}</a>
                                    </div>
                                @endif
                                <div class="flex items-center gap-2.5">
                                    <i data-lucide="mail" class="w-4 h-4 text-cyan-400 shrink-0"></i>
                                    <a href="mailto:{{ $loc['email'] }}" class="text-slate-300 hover:text-white hover:underline truncate">{{ $loc['email'] }}</a>
                                </div>
                            </div>
                        </div>

                        <div class="pt-4 mt-4 border-t border-slate-800/80 flex items-center justify-between">
                            <a href="{{ url($loc['slug']) }}" class="text-xs font-bold text-cyan-400 hover:text-cyan-300 flex items-center gap-1 transition">
                                <span>Branch Details & Services</span>
                                <i data-lucide="arrow-right" class="w-3.5 h-3.5 group-hover:translate-x-1 transition-transform"></i>
                            </a>
                            <a href="tel:{{ $loc['phone_link'] }}" class="text-[11px] font-semibold text-slate-400 hover:text-white transition">
                                Call Now
                            </a>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </section>

</div>
@endsection
