@php
    $sidebarSubject = $subject ?? (isset($service) ? 'Data Recovery Inquiry: ' . $service->title : 'Emergency Lab Evaluation Request');
    $whatsappText = $whatsappMessage ?? (isset($service) ? 'Hello LifeGuard Data Recovery, I need urgent evaluation for ' . $service->title : 'Hello LifeGuard Data Recovery, I have a failed storage device and need emergency data recovery assistance.');

    // Resolve services list for the navigator
    if (!isset($navServices)) {
        if (isset($otherServices) && $otherServices->isNotEmpty()) {
            $navServices = $otherServices;
        } elseif (isset($services) && $services->isNotEmpty()) {
            $navServices = isset($service) ? $services->where('id', '!=', $service->id) : $services;
        } else {
            $allServices = \App\Models\Service::published()->ordered()->get();
            $navServices = isset($service) ? $allServices->where('id', '!=', $service->id) : $allServices;
        }
    }
@endphp

<div id="quote-box" class="space-y-6">

    <!-- Flash Message Notification -->
    @if(session('success'))
        <div class="p-4 rounded-2xl bg-emerald-600 text-white shadow-lg space-y-1">
            <div class="flex items-center gap-2 font-bold text-xs uppercase tracking-wide">
                <i data-lucide="check-circle" class="w-4 h-4 shrink-0"></i>
                <span>Evaluation Request Received!</span>
            </div>
            <p class="text-xs text-emerald-50 leading-relaxed">{{ session('success') }}</p>
        </div>
    @endif

    <!-- 1. Free Emergency Lab Diagnostic Evaluation Card -->
    <div class="bg-white rounded-3xl p-6 sm:p-7 border border-slate-200/90 shadow-sm space-y-4">
        <div class="flex items-center gap-2 text-xs font-bold uppercase text-sky-600">
            <i data-lucide="shield-check" class="w-4 h-4 text-sky-500"></i>
            <span>ISO-5 Cleanroom Lab Evaluation</span>
        </div>
        <h4 class="text-xl font-black text-slate-900 font-display">Free Diagnostic Quote</h4>
        <p class="text-xs text-slate-500">No Data, No Charge Guarantee. Submit details for an immediate lab case assessment.</p>

        <form action="{{ route('contact.submit') }}" method="POST" class="space-y-3.5 pt-1">
            @csrf
            <input type="hidden" name="subject" value="{{ $sidebarSubject }}">
            <input type="hidden" name="type" value="evaluation">

            <!-- Anti-Bot Silent Honeypot -->
            <div style="display: none !important;" aria-hidden="true">
                <input type="text" name="_hp_website" tabindex="-1" autocomplete="off">
                <input type="text" name="bot_check" tabindex="-1" autocomplete="off">
            </div>

            <div>
                <label class="block text-[11px] font-bold uppercase tracking-wider text-slate-700 mb-1">Full Name</label>
                <input type="text" name="name" required placeholder="e.g. Tariq Al Nuaimi" class="w-full px-3.5 py-2 text-xs rounded-xl border border-slate-200 focus:outline-none focus:ring-2 focus:ring-sky-500/20 focus:border-sky-500">
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-2">
                <div>
                    <label class="block text-[11px] font-bold uppercase tracking-wider text-slate-700 mb-1">Phone / WhatsApp</label>
                    <input type="tel" name="phone" required placeholder="+971 50 123 4567" class="w-full px-3 py-2 text-xs rounded-xl border border-slate-200 focus:outline-none focus:ring-2 focus:ring-sky-500/20 focus:border-sky-500">
                </div>
                <div>
                    <label class="block text-[11px] font-bold uppercase tracking-wider text-slate-700 mb-1">Email</label>
                    <input type="email" name="email" required placeholder="name@company.ae" class="w-full px-3 py-2 text-xs rounded-xl border border-slate-200 focus:outline-none focus:ring-2 focus:ring-sky-500/20 focus:border-sky-500">
                </div>
            </div>

            <div class="grid grid-cols-2 gap-2">
                <div>
                    <label class="block text-[11px] font-bold uppercase tracking-wider text-slate-700 mb-1">Media Type</label>
                    <select name="device_type" class="w-full px-2.5 py-2 text-xs rounded-xl border border-slate-200 focus:outline-none focus:ring-2 focus:ring-sky-500/20 focus:border-sky-500 bg-white">
                        <option value="External Hard Drive" selected>External Hard Drive</option>
                        <option value="Internal HDD / Laptop">Internal HDD / Laptop</option>
                        <option value="NVMe / SSD">SSD / NVMe / M.2</option>
                        <option value="RAID / NAS / Server">RAID / NAS / SAN</option>
                        <option value="CCTV DVR / NVR">CCTV DVR / NVR</option>
                        <option value="MacBook / Apple SSD">MacBook / Apple SSD</option>
                        <option value="USB Flash / SD Card">USB Flash / SD Card</option>
                        <option value="Ransomware Recovery">Ransomware / Encrypted</option>
                        <option value="Smartphone / Mobile">iPhone / Android</option>
                    </select>
                </div>
                <div>
                    <label class="block text-[11px] font-bold uppercase tracking-wider text-slate-700 mb-1">Nearest Lab / Emirate</label>
                    <select name="emirate" class="w-full px-2.5 py-2 text-xs rounded-xl border border-slate-200 focus:outline-none focus:ring-2 focus:ring-sky-500/20 focus:border-sky-500 bg-white">
                        <option value="Bur Dubai Main Lab" selected>Bur Dubai Lab</option>
                        <option value="Al Barsha Branch Lab">Al Barsha Lab</option>
                        <option value="Dubai Free Courier">Dubai (Courier Pickup)</option>
                        <option value="Abu Dhabi Courier">Abu Dhabi (Courier)</option>
                        <option value="Sharjah Courier">Sharjah (Courier)</option>
                        <option value="Ajman Courier">Ajman (Courier)</option>
                        <option value="RAK / Fujairah Courier">RAK / Fujairah</option>
                    </select>
                </div>
            </div>

            <div>
                <label class="block text-[11px] font-bold uppercase tracking-wider text-slate-700 mb-1">Failure Symptoms / Details</label>
                <textarea name="message" rows="2" placeholder="e.g. Clicking noise, dropped on floor, unreadable partition, formatted, dead board..." class="w-full px-3.5 py-2 text-xs rounded-xl border border-slate-200 focus:outline-none focus:ring-2 focus:ring-sky-500/20 focus:border-sky-500"></textarea>
            </div>

            <div class="flex items-center gap-2 p-2.5 rounded-xl bg-sky-50 border border-sky-100">
                <input type="checkbox" id="pickup_sidebar" name="pickup_requested" value="yes" checked class="rounded text-sky-600 focus:ring-sky-500">
                <label for="pickup_sidebar" class="text-[11px] text-slate-700 font-medium">Request <strong>Free Secure Courier Pickup</strong> across UAE</label>
            </div>

            <button type="submit" class="w-full py-3.5 rounded-xl font-bold text-xs bg-gradient-to-r from-sky-600 to-brand-600 hover:from-sky-500 hover:to-brand-500 text-white shadow-lg shadow-sky-600/20 transition transform hover:-translate-y-0.5">
                Book Free Lab Evaluation
            </button>
        </form>

        <div class="pt-2 border-t border-slate-100 flex items-center justify-between text-[11px] text-slate-500">
            <span class="inline-flex items-center gap-1"><i data-lucide="check" class="w-3.5 h-3.5 text-emerald-500"></i> No Data, No Fee</span>
            <span class="inline-flex items-center gap-1"><i data-lucide="lock" class="w-3.5 h-3.5 text-sky-500"></i> Strict Confidentiality</span>
        </div>
    </div>

    <!-- 2. Emergency 24/7 Cleanroom Dispatch Card -->
    <div class="bg-gradient-to-br from-slate-900 via-slate-900 to-slate-950 text-white rounded-3xl p-6 border border-slate-800 shadow-md space-y-4">
        <div class="flex items-center gap-2 text-xs font-bold uppercase text-sky-400">
            <span class="w-2 h-2 rounded-full bg-emerald-400 animate-ping"></span>
            <span>24/7 Critical Data Hotline</span>
        </div>
        <div class="space-y-2">
            <div>
                <span class="text-[10px] uppercase tracking-wider text-slate-400 font-bold block">Bur Dubai Lab & Hotline:</span>
                <a href="tel:+97143933082" class="text-xl font-black text-white hover:text-sky-300 transition block font-mono">
                    +971 43 933 082
                </a>
            </div>
            <div>
                <span class="text-[10px] uppercase tracking-wider text-slate-400 font-bold block">Al Barsha Lab Direct:</span>
                <a href="tel:+971564101040" class="text-base font-bold text-white hover:text-sky-300 transition block font-mono">
                    +971 56 410 1040
                </a>
            </div>
        </div>
        <a href="https://wa.me/971554838545?text={{ urlencode($whatsappText) }}" target="_blank" class="w-full py-2.5 rounded-xl bg-emerald-600 hover:bg-emerald-500 text-white font-bold text-xs flex items-center justify-center gap-2 transition shadow-md">
            <i data-lucide="message-circle" class="w-4 h-4"></i>
            <span>Chat With Senior Lab Engineer</span>
        </a>
        <div class="pt-2 border-t border-slate-800 flex items-center justify-between text-[11px] text-slate-400">
            <span>Cleanroom Lab: <strong>ISO-5 Certified</strong></span>
            <span>All 7 Emirates</span>
        </div>
    </div>

    <!-- 3. Specialized Data Recovery Services Navigator -->
    @if(isset($navServices) && $navServices->isNotEmpty())
        <div class="bg-white rounded-3xl p-6 border border-slate-200/90 shadow-sm space-y-4">
            <h5 class="text-xs font-bold uppercase tracking-wider text-slate-500 flex items-center gap-2">
                <i data-lucide="layers" class="w-4 h-4 text-sky-600"></i>
                <span>Recovery Services</span>
            </h5>
            <div class="space-y-1.5">
                @foreach($navServices as $navSvc)
                    <a href="{{ url('/'.$navSvc->slug) }}" class="flex items-center justify-between p-3 rounded-2xl text-xs font-semibold text-slate-700 hover:bg-sky-50 hover:text-sky-900 transition group border border-transparent hover:border-sky-200">
                        <span class="truncate">{{ $navSvc->title }}</span>
                        <i data-lucide="arrow-right" class="w-3.5 h-3.5 text-slate-400 group-hover:text-sky-600 group-hover:translate-x-0.5 transition shrink-0"></i>
                    </a>
                @endforeach
            </div>
            <div class="pt-2">
                <a href="{{ route('services.index') }}" class="block text-center py-2 text-xs font-bold text-sky-700 hover:text-sky-800 transition">
                    View All Cleanroom Services &rarr;
                </a>
            </div>
        </div>
    @endif

    <!-- 4. Lab Branches & Walk-in Depots -->
    <div class="p-5 rounded-3xl bg-slate-50 border border-slate-200/80 text-xs space-y-3">
        <span class="font-extrabold uppercase text-[10px] text-slate-500 tracking-wider flex items-center gap-1.5">
            <i data-lucide="map-pin" class="w-3.5 h-3.5 text-sky-600"></i>
            <span>Walk-in Lab Branches:</span>
        </span>
        <div class="space-y-2 text-[11px]">
            <div class="p-2.5 rounded-xl bg-white border border-slate-200/70">
                <strong class="text-slate-900 block font-bold">Bur Dubai Main Cleanroom Lab</strong>
                <span class="text-slate-500 block text-[10px]">Bayat Center S25, Computer Street, Bur Dubai</span>
            </div>
            <div class="p-2.5 rounded-xl bg-white border border-slate-200/70">
                <strong class="text-slate-900 block font-bold">Al Barsha Branch Lab</strong>
                <span class="text-slate-500 block text-[10px]">Dusseldorf Business Point, Behind Mall of Emirates</span>
            </div>
        </div>
        <div class="pt-2 border-t border-slate-200">
            <a href="{{ route('contact') }}" class="text-sky-700 hover:underline font-bold flex items-center gap-1 text-[11px]">
                <i data-lucide="navigation" class="w-3 h-3"></i>
                <span>Get Lab Directions & Working Hours &rarr;</span>
            </a>
        </div>
    </div>

</div>
