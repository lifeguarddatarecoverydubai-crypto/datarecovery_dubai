@php
    $isEdit = isset($service);
    $actionUrl = $isEdit ? route('admin.services.update', $service) : route('admin.services.store');
    $currentBenchmarks = $benchmarks ?? ($service->effective_benchmarks ?? [
        'standard' => 'ISO-5 Class 100 Cleanroom / PC-3000 Hardware Certified',
        'evaluation' => '100% Free In-Lab Media Diagnostic & Feasibility Report',
        'guarantee' => '100% "No Data, No Charge" Policy - Zero Financial Risk',
        'turnaround' => '24 to 48-Hour Priority Lab Turnaround (Same-Day Emergency Available)',
        'courier' => 'Complimentary Secure Courier Collection Across Dubai & All 7 Emirates',
    ]);
@endphp

<form action="{{ $actionUrl }}" method="POST" id="serviceForm" class="space-y-8" enctype="multipart/form-data">
    @csrf
    @if($isEdit)
        @method('PUT')
    @endif

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        <!-- Main Left Column (2 Cols) -->
        <div class="lg:col-span-2 space-y-6">

            <!-- Title & Permalink & Hero Badge Card -->
            <div class="bg-white p-6 rounded-2xl border border-slate-200/80 shadow-sm space-y-4">
                <div>
                    <label for="serviceTitle" class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1.5">Service Title</label>
                    <input type="text" id="serviceTitle" name="title" required value="{{ old('title', $service->title ?? '') }}" 
                           placeholder="e.g. Hard Drive Data Recovery Dubai"
                           class="w-full px-4 py-3 text-lg font-bold rounded-xl border border-slate-200 focus:outline-none focus:ring-2 focus:ring-brand-500/20 focus:border-brand-600 transition">
                </div>

                <!-- Slug / Permalink Box -->
                <div class="flex items-center gap-2 text-xs text-slate-500 bg-slate-50 p-2.5 rounded-xl border border-slate-200/60" x-data="{ editingSlug: false }">
                    <span class="font-semibold text-slate-600">Permalink:</span>
                    <span class="text-slate-400">{{ url('/service') }}/</span>
                    <span x-show="!editingSlug" id="slugDisplay" class="font-mono text-brand-600 font-bold">{{ old('slug', $service->slug ?? 'your-service-slug') }}</span>
                    <input type="text" id="serviceSlug" name="slug" value="{{ old('slug', $service->slug ?? '') }}" x-show="editingSlug"
                           class="px-2 py-0.5 rounded border border-slate-300 font-mono text-xs focus:outline-none focus:border-brand-600 w-64">
                    <button type="button" @click="editingSlug = !editingSlug" class="ml-auto text-xs text-brand-600 font-semibold hover:underline">
                        <span x-text="editingSlug ? 'Done' : 'Edit'"></span>
                    </button>
                </div>

                <!-- Hero Lab Badge -->
                <div>
                    <div class="flex items-center justify-between mb-1.5">
                        <label for="serviceHeroBadge" class="block text-xs font-bold uppercase tracking-wider text-slate-700">Hero Certification Badge</label>
                        <span class="text-[11px] text-slate-400">Pill badge shown in the service header</span>
                    </div>
                    <input type="text" id="serviceHeroBadge" name="hero_badge" value="{{ old('hero_badge', $service->hero_badge ?? 'ISO-5 Class 100 Cleanroom • No Data, No Charge Guarantee') }}" 
                           placeholder="e.g. ISO-5 Class 100 Cleanroom • No Data, No Charge Guarantee"
                           class="w-full px-3.5 py-2.5 text-xs rounded-xl border border-slate-200 focus:outline-none focus:ring-2 focus:ring-brand-500/20 focus:border-brand-600">
                    <div class="flex flex-wrap gap-1.5 mt-2">
                        <button type="button" onclick="document.getElementById('serviceHeroBadge').value = 'ISO-5 Class 100 Cleanroom • PC-3000 Hardware Diagnostics'" class="text-[10px] px-2 py-0.5 bg-slate-100 hover:bg-slate-200 text-slate-600 rounded">Class 100 Cleanroom</button>
                        <button type="button" onclick="document.getElementById('serviceHeroBadge').value = '100% No Data, No Charge Policy • Zero Financial Risk'" class="text-[10px] px-2 py-0.5 bg-slate-100 hover:bg-slate-200 text-slate-600 rounded">No Data, No Charge</button>
                        <button type="button" onclick="document.getElementById('serviceHeroBadge').value = '24/7 Emergency Lab Triage • Dedicated Cleanroom Priority'" class="text-[10px] px-2 py-0.5 bg-slate-100 hover:bg-slate-200 text-slate-600 rounded">24/7 Lab Triage</button>
                        <button type="button" onclick="document.getElementById('serviceHeroBadge').value = 'Enterprise RAID & Virtual SAN Reconstruction Specialist'" class="text-[10px] px-2 py-0.5 bg-slate-100 hover:bg-slate-200 text-slate-600 rounded">RAID Specialist</button>
                    </div>
                </div>

                <!-- Tagline -->
                <div>
                    <label for="serviceTagline" class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1.5">Tagline / Subheadline</label>
                    <input type="text" id="serviceTagline" name="tagline" value="{{ old('tagline', $service->tagline ?? '') }}" 
                           placeholder="e.g. Certified ISO Class 100 Cleanroom Lab • 98.7% Proven Success Rate • No Data, No Charge"
                           class="w-full px-3.5 py-2.5 text-xs rounded-xl border border-slate-200 focus:outline-none focus:ring-2 focus:ring-brand-500/20 focus:border-brand-600">
                </div>
            </div>

            <!-- Cleanroom Lab Standards & SLA Benchmarks Card -->
            <div class="bg-white p-6 rounded-2xl border border-slate-200/80 shadow-sm space-y-4">
                <div class="flex items-center justify-between border-b border-slate-100 pb-3">
                    <div>
                        <h3 class="text-xs font-bold uppercase tracking-wider text-slate-800 flex items-center gap-2">
                            <i data-lucide="shield-check" class="w-4 h-4 text-emerald-600"></i>
                            <span>Cleanroom Lab Standards & SLA Benchmarks</span>
                        </h3>
                        <p class="text-[11px] text-slate-400 mt-0.5">Displayed in the verified engineering metrics box on the frontend</p>
                    </div>
                    <span class="text-[10px] font-bold px-2 py-0.5 rounded-full bg-emerald-50 text-emerald-700 border border-emerald-200">Live on Service Hero</span>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-[11px] font-bold text-slate-600 mb-1">Engineering Standard</label>
                        <input type="text" name="benchmark_standard" value="{{ old('benchmark_standard', $currentBenchmarks['standard'] ?? 'ISO-5 Class 100 Cleanroom / PC-3000 Hardware Certified') }}"
                               class="w-full px-3 py-2 text-xs rounded-xl border border-slate-200 focus:outline-none focus:ring-2 focus:ring-brand-500/20">
                    </div>

                    <div>
                        <label class="block text-[11px] font-bold text-slate-600 mb-1">Diagnostic Evaluation Policy</label>
                        <input type="text" name="benchmark_evaluation" value="{{ old('benchmark_evaluation', $currentBenchmarks['evaluation'] ?? '100% Free In-Lab Media Diagnostic & Feasibility Report') }}"
                               class="w-full px-3 py-2 text-xs rounded-xl border border-slate-200 focus:outline-none focus:ring-2 focus:ring-brand-500/20">
                    </div>

                    <div>
                        <label class="block text-[11px] font-bold text-slate-600 mb-1">Client Financial Guarantee</label>
                        <input type="text" name="benchmark_guarantee" value="{{ old('benchmark_guarantee', $currentBenchmarks['guarantee'] ?? '100% \"No Data, No Charge\" Policy — Zero Financial Risk') }}"
                               class="w-full px-3 py-2 text-xs rounded-xl border border-slate-200 focus:outline-none focus:ring-2 focus:ring-brand-500/20">
                    </div>

                    <div>
                        <label class="block text-[11px] font-bold text-slate-600 mb-1">Standard Lab Turnaround</label>
                        <input type="text" name="benchmark_turnaround" value="{{ old('benchmark_turnaround', $currentBenchmarks['turnaround'] ?? '24 to 48-Hour Priority Lab Turnaround (Same-Day Emergency Available)') }}"
                                class="w-full px-3 py-2 text-xs rounded-xl border border-slate-200 focus:outline-none focus:ring-2 focus:ring-brand-500/20">
                    </div>

                    <div class="md:col-span-2">
                        <label class="block text-[11px] font-bold text-slate-600 mb-1">UAE Collection & Courier Service</label>
                        <input type="text" name="benchmark_courier" value="{{ old('benchmark_courier', $currentBenchmarks['courier'] ?? 'Complimentary Secure Courier Collection Across Dubai & All 7 Emirates') }}"
                               class="w-full px-3 py-2 text-xs rounded-xl border border-slate-200 focus:outline-none focus:ring-2 focus:ring-brand-500/20">
                    </div>
                </div>
            </div>

            <!-- Ultimate Content Editor (Dual-Mode: Visual / HTML Source / Live Preview) -->
            <div class="bg-white rounded-2xl border border-slate-200/80 shadow-sm overflow-hidden" 
                 x-data="serviceContentEditor({ initial: @js(old('content', $service->content ?? '')) })">
                
                <!-- Editor Header Toolbar & Tabs -->
                <div class="p-4 bg-slate-900 text-white flex flex-wrap items-center justify-between gap-3 border-b border-slate-800">
                    <div class="flex items-center gap-3">
                        <span class="text-xs font-bold uppercase tracking-wider text-slate-300 flex items-center gap-1.5">
                            <i data-lucide="file-text" class="w-4 h-4 text-brand-400"></i>
                            <span>Detailed Service Content</span>
                        </span>
                        
                        <!-- Mode Selector Tabs -->
                        <div class="flex items-center bg-slate-800 p-1 rounded-xl text-xs font-semibold">
                            <button type="button" @click="setTab('code')" 
                                    :class="activeTab === 'code' ? 'bg-brand-600 text-white shadow-sm' : 'text-slate-300 hover:text-white'"
                                    class="px-3 py-1.5 rounded-lg transition flex items-center gap-1.5">
                                <i data-lucide="code" class="w-3.5 h-3.5"></i>
                                <span>HTML Source Code</span>
                            </button>
                            <button type="button" @click="setTab('visual')" 
                                    :class="activeTab === 'visual' ? 'bg-brand-600 text-white shadow-sm' : 'text-slate-300 hover:text-white'"
                                    class="px-3 py-1.5 rounded-lg transition flex items-center gap-1.5">
                                <i data-lucide="eye" class="w-3.5 h-3.5"></i>
                                <span>Visual WYSIWYG</span>
                            </button>
                            <button type="button" @click="setTab('preview')" 
                                    :class="activeTab === 'preview' ? 'bg-brand-600 text-white shadow-sm' : 'text-slate-300 hover:text-white'"
                                    class="px-3 py-1.5 rounded-lg transition flex items-center gap-1.5">
                                <i data-lucide="monitor" class="w-3.5 h-3.5"></i>
                                <span>Live Preview</span>
                            </button>
                        </div>
                    </div>

                    <!-- Quick Snippet Inserters -->
                    <div class="flex items-center gap-2">
                        <div class="relative" x-data="{ openSnippets: false }">
                            <button type="button" @click="openSnippets = !openSnippets" 
                                    class="px-2.5 py-1.5 rounded-lg bg-slate-800 hover:bg-slate-700 text-slate-200 text-xs font-semibold flex items-center gap-1.5 border border-slate-700 transition">
                                <i data-lucide="plus-circle" class="w-3.5 h-3.5 text-brand-400"></i>
                                <span>Insert Section</span>
                                <i data-lucide="chevron-down" class="w-3 h-3 text-slate-400"></i>
                            </button>
                            <div x-show="openSnippets" x-cloak @click.outside="openSnippets = false" 
                                 class="absolute right-0 mt-1 w-56 rounded-xl bg-slate-800 text-slate-200 border border-slate-700 shadow-xl py-1 text-xs z-30">
                                <button type="button" @click="insertSnippet('faq'); openSnippets = false" class="w-full text-left px-3 py-2 hover:bg-slate-700 flex items-center gap-2">
                                    <i data-lucide="help-circle" class="w-3.5 h-3.5 text-brand-400"></i> FAQ Item Accordion
                                </button>
                                <button type="button" @click="insertSnippet('table'); openSnippets = false" class="w-full text-left px-3 py-2 hover:bg-slate-700 flex items-center gap-2">
                                    <i data-lucide="table" class="w-3.5 h-3.5 text-emerald-400"></i> Diagnostic Table
                                </button>
                                <button type="button" @click="insertSnippet('callout'); openSnippets = false" class="w-full text-left px-3 py-2 hover:bg-slate-700 flex items-center gap-2">
                                    <i data-lucide="shield" class="w-3.5 h-3.5 text-amber-400"></i> Cleanroom Callout Box
                                </button>
                                <button type="button" @click="insertSnippet('grid3'); openSnippets = false" class="w-full text-left px-3 py-2 hover:bg-slate-700 flex items-center gap-2">
                                    <i data-lucide="grid" class="w-3.5 h-3.5 text-sky-400"></i> 3-Column Card Grid
                                </button>
                            </div>
                        </div>

                        <button type="button" onclick="openMediaModal(function(item) { window.insertMediaIntoEditor(item.url, item.name); })" 
                                class="px-2.5 py-1.5 rounded-lg bg-slate-800 hover:bg-slate-700 text-slate-200 text-xs font-semibold flex items-center gap-1.5 border border-slate-700 transition">
                            <i data-lucide="image" class="w-3.5 h-3.5 text-brand-400"></i>
                            <span>Add Media</span>
                        </button>
                    </div>
                </div>

                <!-- Hidden Input Bound to Form Submission -->
                <textarea name="content" id="serviceContentInput" class="hidden" x-model="content"></textarea>

                <!-- TAB 1: HTML Source Code View (Monospace, Indented, 100% Preservation) -->
                <div x-show="activeTab === 'code'" class="p-4 bg-slate-950 font-mono text-xs">
                    <div class="flex items-center justify-between text-[11px] text-slate-400 mb-2 pb-2 border-b border-slate-800">
                        <span>Raw HTML editor &bull; Edit headings, diagnostic grids, Tailwind classes, and tables directly.</span>
                        <span x-text="content.length + ' characters'"></span>
                    </div>
                    <textarea id="codeTextarea" x-model="content" @input="syncFromCode()" rows="24"
                              class="w-full bg-slate-900 text-emerald-300 p-4 rounded-xl border border-slate-800 focus:outline-none focus:ring-2 focus:ring-brand-500 font-mono text-xs leading-relaxed"></textarea>
                </div>

                <!-- TAB 2: Visual WYSIWYG Editor (ContentEditable with Rich Formatting, Zero Tag Stripping) -->
                <div x-show="activeTab === 'visual'" class="bg-white">
                    <div class="p-2.5 bg-slate-100 border-b border-slate-200 flex flex-wrap items-center gap-1.5 text-xs text-slate-700">
                        <button type="button" @click="execCommand('bold')" class="p-1.5 hover:bg-slate-200 rounded font-bold" title="Bold"><b>B</b></button>
                        <button type="button" @click="execCommand('italic')" class="p-1.5 hover:bg-slate-200 rounded italic" title="Italic"><i>I</i></button>
                        <button type="button" @click="execCommand('underline')" class="p-1.5 hover:bg-slate-200 rounded underline" title="Underline"><u>U</u></button>
                        <span class="text-slate-300">|</span>
                        <button type="button" @click="execFormat('h2')" class="px-2 py-1 hover:bg-slate-200 rounded font-bold text-xs" title="Heading 2">H2</button>
                        <button type="button" @click="execFormat('h3')" class="px-2 py-1 hover:bg-slate-200 rounded font-bold text-xs" title="Heading 3">H3</button>
                        <button type="button" @click="execFormat('h4')" class="px-2 py-1 hover:bg-slate-200 rounded font-bold text-xs" title="Heading 4">H4</button>
                        <button type="button" @click="execFormat('p')" class="px-2 py-1 hover:bg-slate-200 rounded text-xs" title="Paragraph">P</button>
                        <span class="text-slate-300">|</span>
                        <button type="button" @click="execCommand('insertUnorderedList')" class="p-1.5 hover:bg-slate-200 rounded" title="Bullet List">&bull; List</button>
                        <button type="button" @click="execCommand('insertOrderedList')" class="p-1.5 hover:bg-slate-200 rounded" title="Numbered List">1. List</button>
                        <span class="text-slate-300">|</span>
                        <button type="button" @click="execPromptLink()" class="px-2 py-1 hover:bg-slate-200 rounded text-xs" title="Insert Link">🔗 Link</button>
                        <button type="button" @click="execCommand('removeFormat')" class="px-2 py-1 hover:bg-slate-200 rounded text-xs text-slate-500" title="Clear Formatting">Clear</button>
                    </div>

                    <div id="visualEditorContainer" contenteditable="true" @input="syncFromVisual()" 
                         class="p-6 min-h-[460px] focus:outline-none prose prose-slate max-w-none text-slate-800 text-sm leading-relaxed"
                         >{!! old('content', $service->content ?? '') !!}</div>
                </div>

                <!-- TAB 3: Full Frontend Live Preview -->
                <div x-show="activeTab === 'preview'" class="p-8 bg-slate-50 min-h-[460px]">
                    <div class="bg-white rounded-3xl p-8 border border-slate-200 shadow-sm max-w-4xl mx-auto">
                        <div class="mb-4 pb-3 border-b border-slate-100 flex items-center justify-between text-xs text-slate-400">
                            <span>Frontend Rendering Simulation</span>
                            <span class="font-bold text-emerald-600">Live Rendered View</span>
                        </div>
                        <div class="prose prose-slate max-w-none text-slate-800 text-sm sm:text-base leading-relaxed" x-html="content"></div>
                    </div>
                </div>
            </div>

            <!-- Short Summary Card -->
            <div class="bg-white p-6 rounded-2xl border border-slate-200/80 shadow-sm space-y-2">
                <label for="serviceSummary" class="block text-xs font-bold uppercase tracking-wider text-slate-700">Short Summary</label>
                <p class="text-xs text-slate-400">Concise 1-2 sentence description displayed on homepage and service index cards.</p>
                <textarea id="serviceSummary" name="summary" rows="3" placeholder="Brief summary of what this cleanroom service offers..."
                          class="w-full px-4 py-2.5 text-sm rounded-xl border border-slate-200 focus:outline-none focus:ring-2 focus:ring-brand-500/20 focus:border-brand-600 transition">{{ old('summary', $service->summary ?? '') }}</textarea>
            </div>

            <!-- Features & Benefits Grids -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Features Card -->
                <div class="bg-white p-6 rounded-2xl border border-slate-200/80 shadow-sm space-y-2">
                    <label for="featuresInput" class="block text-xs font-bold uppercase tracking-wider text-slate-700">Core Technical Capabilities</label>
                    <p class="text-[11px] text-slate-400">Enter one feature per line. Rendered as technical checkmark badges.</p>
                    <textarea id="featuresInput" name="features_input" rows="6" 
                              placeholder="Class 100 Cleanroom Platter Swaps & Head Assembly Replacement&#10;PC-3000 Ultimate & Express AceLab Hardware Diagnostics&#10;NAND Flash Chip-off Reader & Monolithic Pinout Decoding&#10;Proprietary Virtual RAID Rebuilder for RAID 0, 1, 5, 6, 10, 50, 60"
                              class="w-full px-3.5 py-2.5 text-xs font-mono rounded-xl border border-slate-200 focus:outline-none focus:ring-2 focus:ring-brand-500/20 focus:border-brand-600">{{ old('features_input', $featuresString ?? '') }}</textarea>
                </div>

                <!-- Benefits / Impact Card -->
                <div class="bg-white p-6 rounded-2xl border border-slate-200/80 shadow-sm space-y-2">
                    <label for="benefitsInput" class="block text-xs font-bold uppercase tracking-wider text-slate-700">Why Choose LifeGuard Data Recovery</label>
                    <p class="text-[11px] text-slate-400">Enter one benefit per line. Rendered in the verified cleanroom advantage box.</p>
                    <textarea id="benefitsInput" name="benefits_input" rows="6" 
                              placeholder="ISO-5 Class 100 Cleanroom: Dust-free certified laboratory environment for platter swaps&#10;No Data, No Charge Guarantee: Zero evaluation fees, you pay only upon successful file verification&#10;PC-3000 Hardware Certified: Industry-leading AceLab diagnostic controllers&#10;Strict Confidentiality & Non-Disclosure: Enterprise data security and NDA guaranteed"
                              class="w-full px-3.5 py-2.5 text-xs font-mono rounded-xl border border-slate-200 focus:outline-none focus:ring-2 focus:ring-brand-500/20 focus:border-brand-600">{{ old('benefits_input', $benefitsString ?? '') }}</textarea>
                </div>
            </div>

            <!-- Yoast SEO Box -->
            @include('admin.partials.yoast-seo-box', [
                'model' => $service ?? null,
                'titleInputId' => 'serviceTitle',
                'slugInputId' => 'serviceSlug',
                'contentInputId' => 'serviceContentInput',
                'urlPrefix' => url('/service'),
                'type' => 'service'
            ])

        </div>

        <!-- Right Sidebar (1 Col) -->
        <div class="space-y-6">

            <!-- Publish Settings Card -->
            <div class="bg-white p-6 rounded-2xl border border-slate-200/80 shadow-sm space-y-4">
                <h3 class="font-bold text-slate-900 text-sm border-b border-slate-100 pb-3 flex items-center justify-between">
                    <span>Publish Settings</span>
                    @if($isEdit)
                        <a href="{{ url('/'.$service->slug) }}" target="_blank" class="text-xs font-semibold text-brand-600 hover:underline">Preview ↗</a>
                    @endif
                </h3>

                <!-- Status Selector -->
                <div>
                    <label for="serviceStatus" class="block text-xs font-semibold text-slate-700 mb-1.5">Status</label>
                    <select id="serviceStatus" name="status" class="w-full text-xs rounded-xl border border-slate-200 p-2.5 focus:outline-none focus:ring-2 focus:ring-brand-500/20">
                        <option value="published" {{ old('status', $service->status ?? 'published') === 'published' ? 'selected' : '' }}>Published (Active on site)</option>
                        <option value="draft" {{ old('status', $service->status ?? '') === 'draft' ? 'selected' : '' }}>Draft (Hidden)</option>
                    </select>
                </div>

                <!-- Display Order -->
                <div>
                    <label for="serviceOrder" class="block text-xs font-semibold text-slate-700 mb-1.5">Display Order</label>
                    <input type="number" id="serviceOrder" name="order" value="{{ old('order', $service->order ?? 0) }}"
                           class="w-full text-xs rounded-xl border border-slate-200 p-2.5 focus:outline-none focus:ring-2 focus:ring-brand-500/20">
                    <p class="text-[11px] text-slate-400 mt-1">Lower numbers appear first on the frontend.</p>
                </div>

                <!-- Featured Toggle -->
                <div class="pt-2">
                    <label class="flex items-center gap-2 cursor-pointer">
                        <input type="checkbox" name="is_featured" value="1" {{ old('is_featured', $service->is_featured ?? true) ? 'checked' : '' }}
                               class="rounded border-slate-300 text-brand-600 focus:ring-brand-500 h-4 w-4">
                        <span class="text-xs font-semibold text-slate-700">Feature on Homepage</span>
                    </label>
                </div>

                <!-- Action Buttons -->
                <div class="pt-4 flex flex-col gap-2 border-t border-slate-100">
                    <button type="submit" 
                            class="w-full py-2.5 px-4 bg-brand-600 hover:bg-brand-700 text-white rounded-xl text-xs font-bold shadow-md shadow-brand-500/20 transition flex items-center justify-center gap-2">
                        <i data-lucide="check" class="w-4 h-4"></i>
                        <span>{{ $isEdit ? 'Update Service' : 'Save & Publish Service' }}</span>
                    </button>
                    @if($isEdit)
                        <button type="button" onclick="if(confirm('Are you sure you want to delete this service?')) { document.getElementById('deleteServiceForm').submit(); }" 
                                class="w-full py-2 text-xs font-semibold text-rose-600 hover:bg-rose-50 rounded-xl transition">
                            Delete Service
                        </button>
                    @endif
                </div>
            </div>

            <!-- Icon Picker Card -->
            <div class="bg-white p-6 rounded-2xl border border-slate-200/80 shadow-sm space-y-3" x-data="{ selectedIcon: '{{ old('icon', $service->icon ?? 'hard-drive') }}' }">
                <h3 class="font-bold text-slate-900 text-xs uppercase tracking-wider border-b border-slate-100 pb-2">Lucide Icon</h3>
                <p class="text-[11px] text-slate-400">Select an icon for cards and navigation:</p>
                
                <input type="text" id="iconInput" name="icon" x-model="selectedIcon"
                       placeholder="e.g. hard-drive, database, cpu, shield-check"
                       class="w-full px-3 py-2 text-xs font-mono rounded-xl border border-slate-200 focus:outline-none focus:ring-2 focus:ring-brand-500/20">

                <div class="grid grid-cols-4 gap-2 pt-1">
                    @php
                        $presetIcons = ['hard-drive', 'database', 'cpu', 'shield-check', 'smartphone', 'server', 'video', 'usb', 'lock', 'activity', 'sparkles', 'check-circle'];
                    @endphp
                    @foreach($presetIcons as $ico)
                        <button type="button" @click="selectedIcon = '{{ $ico }}'"
                                :class="selectedIcon === '{{ $ico }}' ? 'border-brand-600 bg-brand-50 text-brand-700' : 'border-slate-200 hover:border-slate-300 text-slate-600'"
                                class="p-2 border rounded-xl flex items-center justify-center transition" title="{{ $ico }}">
                            <i data-lucide="{{ $ico }}" class="w-4 h-4"></i>
                        </button>
                    @endforeach
                </div>
            </div>

            <!-- Featured Image / Thumbnail Card -->
            <div class="bg-white p-6 rounded-2xl border border-slate-200/80 shadow-sm space-y-3">
                <h3 class="font-bold text-slate-900 text-xs uppercase tracking-wider border-b border-slate-100 pb-2">Featured Image</h3>
                <p class="text-[11px] text-slate-400">Image URL or select from Media Library.</p>
                
                <div class="space-y-2">
                    <div class="flex items-center gap-2">
                        <input type="text" id="featuredImageInput" name="featured_image" value="{{ old('featured_image', $service->featured_image ?? '') }}"
                               placeholder="https://... or image path"
                               class="flex-1 px-3 py-2 text-xs rounded-xl border border-slate-200 focus:outline-none focus:ring-2 focus:ring-brand-500/20">
                        <button type="button" onclick="openMediaModal(function(item) { document.getElementById('featuredImageInput').value = item.url; document.getElementById('featuredImagePreviewImg').src = item.url; document.getElementById('featuredImagePreviewWrap').classList.remove('hidden'); })"
                                class="px-3 py-2 bg-slate-100 hover:bg-slate-200 text-slate-700 text-xs font-semibold rounded-xl transition shrink-0 flex items-center gap-1">
                            <i data-lucide="image" class="w-3.5 h-3.5"></i>
                            <span>Select</span>
                        </button>
                        <button type="button" onclick="document.getElementById('serviceFeaturedImageFileInput').click()"
                                class="px-3 py-2 bg-brand-50 hover:bg-brand-100 text-brand-700 text-xs font-semibold rounded-xl transition shrink-0 flex items-center gap-1">
                            <i data-lucide="upload" class="w-3.5 h-3.5"></i>
                            <span>Upload</span>
                        </button>
                        <input type="file" id="serviceFeaturedImageFileInput" name="featured_image_file" accept="image/*" class="hidden" onchange="uploadServiceFeaturedImageDirectly(this.files[0])">
                    </div>
                    <div id="serviceFeaturedImageUploadStatus" class="hidden text-center pt-1"></div>
                </div>

                @php $featUrl = $service->featured_image_url ?? old('featured_image'); @endphp
                <div id="featuredImagePreviewWrap" class="rounded-xl overflow-hidden border border-slate-200 aspect-video {{ $featUrl ? '' : 'hidden' }}">
                    <img id="featuredImagePreviewImg" src="{{ $featUrl ?: '' }}" alt="{{ $service->title ?? 'Preview' }}" class="w-full h-full object-cover">
                </div>
            </div>

        </div>
    </div>
</form>

@if($isEdit)
    <form id="deleteServiceForm" action="{{ route('admin.services.destroy', $service) }}" method="POST" class="hidden">
        @csrf
        @method('DELETE')
    </form>
@endif

@push('scripts')
<script>
function serviceContentEditor(config) {
    return {
        activeTab: 'code', // Default to Code mode so nothing gets stripped
        content: config.initial || '',
        setTab(tab) {
            if (this.activeTab === 'visual') {
                const vis = document.getElementById('visualEditorContainer');
                if (vis) {
                    this.content = vis.innerHTML;
                }
            }
            this.activeTab = tab;
            if (tab === 'visual') {
                this.$nextTick(() => {
                    const vis = document.getElementById('visualEditorContainer');
                    if (vis) {
                        vis.innerHTML = this.content;
                    }
                });
            }
        },
        syncFromCode() {
            document.getElementById('serviceContentInput').value = this.content;
        },
        syncFromVisual() {
            const vis = document.getElementById('visualEditorContainer');
            if (vis) {
                this.content = vis.innerHTML;
                document.getElementById('serviceContentInput').value = this.content;
            }
        },
        execCommand(cmd, val = null) {
            document.execCommand(cmd, false, val);
            this.syncFromVisual();
        },
        execFormat(tag) {
            document.execCommand('formatBlock', false, tag);
            this.syncFromVisual();
        },
        execPromptLink() {
            const url = prompt('Enter link URL:');
            if (url) {
                document.execCommand('createLink', false, url);
                this.syncFromVisual();
            }
        },
        insertSnippet(type) {
            let snippet = '';
            if (type === 'faq') {
                snippet = `\n<details class="p-5 rounded-2xl bg-white border border-slate-200/90 shadow-sm group">\n    <summary class="font-bold text-slate-900 cursor-pointer flex items-center justify-between text-sm sm:text-base">\n        <span>Can data be recovered from a clicking hard drive or liquid-damaged device?</span>\n        <span class="text-cyan-600 font-bold group-open:rotate-180 transition-transform">▼</span>\n    </summary>\n    <div class="mt-3 text-xs sm:text-sm text-slate-600 leading-relaxed pt-2 border-t border-slate-100">\n        Yes. Our certified forensic engineers perform cleanroom mechanical head transplants and electronic micro-soldering inside an ISO-5 Class 100 cleanroom laboratory. Never attempt DIY software tools on clicking drives, as this can permanently scratch magnetic platters. Evaluation is 100% free with No Data, No Charge guarantee.\n    </div>\n</details>\n`;
            } else if (type === 'table') {
                snippet = `\n<div class="overflow-x-auto rounded-2xl border border-slate-200 my-6 shadow-sm">\n    <table class="w-full text-left text-xs sm:text-sm">\n        <thead class="bg-slate-900 text-white font-bold uppercase text-[11px] tracking-wider">\n            <tr>\n                <th class="py-3 px-4">Storage Media</th>\n                <th class="py-3 px-4">Common Failure Mode</th>\n                <th class="py-3 px-4">Lab Hardware Equipment</th>\n                <th class="py-3 px-4">Success Rate</th>\n            </tr>\n        </thead>\n        <tbody class="divide-y divide-slate-100">\n            <tr class="hover:bg-slate-50">\n                <td class="py-3 px-4 font-bold text-slate-900">Mechanical Hard Drive (HDD)</td>\n                <td class="py-3 px-4 text-slate-600">Head crash, motor seizure, burnt PCB</td>\n                <td class="py-3 px-4 text-slate-600">PC-3000 Express / ISO Class 100 Bench</td>\n                <td class="py-3 px-4 font-semibold text-emerald-600">98.7% Proven Rate</td>\n            </tr>\n            <tr class="hover:bg-slate-50">\n                <td class="py-3 px-4 font-bold text-slate-900">Solid State Drive (SSD / NVMe)</td>\n                <td class="py-3 px-4 text-slate-600">Controller dead, firmware lock, 0GB read</td>\n                <td class="py-3 px-4 text-slate-600">PC-3000 Flash / Chip-Off NAND Reader</td>\n                <td class="py-3 px-4 font-semibold text-emerald-600">96.4% Proven Rate</td>\n            </tr>\n        </tbody>\n    </table>\n</div>\n`;
            } else if (type === 'callout') {
                snippet = `\n<div class="p-6 rounded-2xl bg-gradient-to-r from-slate-950 via-slate-900 to-cyan-950 text-white border border-cyan-500/30 my-6 space-y-2">\n    <div class="flex items-center gap-2 text-xs font-bold text-cyan-400 uppercase tracking-wider">\n        <span>ISO-5 Class 100 Certified Dust-Free Laboratory</span>\n    </div>\n    <h4 class="text-lg font-black text-white">100% "No Data, No Charge" Recovery Guarantee</h4>\n    <p class="text-xs sm:text-sm text-slate-300 leading-relaxed">\n        Every drive is evaluated in our certified Al Barsha cleanroom lab with written feasibility reports. If we cannot safely recover your critical data, you do not pay a single Dirham.\n    </p>\n</div>\n`;
            } else if (type === 'grid3') {
                snippet = `\n<div class="grid grid-cols-1 md:grid-cols-3 gap-4 my-6">\n    <div class="p-5 rounded-2xl bg-slate-50 border border-slate-200/80 space-y-2">\n        <h4 class="font-bold text-slate-900 text-sm">Step 1: Free In-Lab Diagnostic</h4>\n        <p class="text-xs text-slate-600 leading-relaxed">Free inspection with hardware diagnostics and transparent written fixed-price quotation.</p>\n    </div>\n    <div class="p-5 rounded-2xl bg-slate-50 border border-slate-200/80 space-y-2">\n        <h4 class="font-bold text-slate-900 text-sm">Step 2: Cleanroom Recovery</h4>\n        <p class="text-xs text-slate-600 leading-relaxed">Micro-soldering, head assembly transplants, and proprietary firmware reconstruction in ISO Class 100 cleanroom.</p>\n    </div>\n    <div class="p-5 rounded-2xl bg-slate-50 border border-slate-200/80 space-y-2">\n        <h4 class="font-bold text-slate-900 text-sm">Step 3: Verified Data Handover</h4>\n        <p class="text-xs text-slate-600 leading-relaxed">Client verifies folder tree before payment. Encrypted return drive delivered across UAE.</p>\n    </div>\n</div>\n`;
            }
            this.content += snippet;
            document.getElementById('serviceContentInput').value = this.content;
            if (this.activeTab === 'visual') {
                const vis = document.getElementById('visualEditorContainer');
                if (vis) vis.innerHTML = this.content;
            }
        }
    };
}

// Global media helper
window.insertMediaIntoEditor = function(url, name) {
    const imgHtml = `\n<img src="${url}" alt="${name || 'LifeGuard Data Recovery'}" class="rounded-2xl max-w-full h-auto my-4 shadow-sm" />\n`;
    const textarea = document.getElementById('codeTextarea');
    const vis = document.getElementById('visualEditorContainer');
    const hiddenInput = document.getElementById('serviceContentInput');

    if (textarea) {
        textarea.value += imgHtml;
        textarea.dispatchEvent(new Event('input', { bubbles: true }));
    }
    if (vis) {
        vis.innerHTML += imgHtml;
        vis.dispatchEvent(new Event('input', { bubbles: true }));
    }
    if (hiddenInput) {
        hiddenInput.value += imgHtml;
        hiddenInput.dispatchEvent(new Event('input', { bubbles: true }));
    }
};

document.addEventListener('DOMContentLoaded', function () {
    const titleInput = document.getElementById('serviceTitle');
    const slugInput = document.getElementById('serviceSlug');
    const slugDisplay = document.getElementById('slugDisplay');

    // Auto-generate slug from title if new
    @if(! $isEdit)
    if (titleInput && slugInput && slugDisplay) {
        titleInput.addEventListener('input', function() {
            const raw = titleInput.value.toLowerCase()
                .replace(/[^\w\s-]/g, '')
                .replace(/[\s_-]+/g, '-')
                .replace(/^-+|-+$/g, '');
            slugInput.value = raw;
            slugDisplay.textContent = raw || 'your-service-slug';
        });
    }
    @endif
});

// Direct Featured Image Upload with Automated WebP Conversion
function uploadServiceFeaturedImageDirectly(file) {
    if (!file) return;
    const statusEl = document.getElementById('serviceFeaturedImageUploadStatus');
    if (statusEl) {
        statusEl.classList.remove('hidden');
        statusEl.innerHTML = '<span class="inline-flex items-center gap-1.5 text-[11px] text-brand-600 font-semibold"><span class="animate-spin w-3 h-3 border-2 border-brand-600 border-t-transparent rounded-full"></span> Converting to WebP & compressing...</span>';
    }

    const formData = new FormData();
    formData.append('file', file);
    const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

    fetch('{{ route("admin.media.store") }}', {
        method: 'POST',
        body: formData,
        headers: {
            'X-CSRF-TOKEN': csrfToken,
            'Accept': 'application/json'
        }
    })
    .then(res => res.json())
    .then(data => {
        if (data.media && data.media.length > 0) {
            const item = data.media[0];
            document.getElementById('featuredImageInput').value = item.url;
            document.getElementById('featuredImagePreviewImg').src = item.url;
            document.getElementById('featuredImagePreviewWrap').classList.remove('hidden');
            if (statusEl) {
                statusEl.innerHTML = '<span class="text-[11px] text-emerald-600 font-bold">✓ Converted to WebP (' + (item.dimensions || item.human_size) + ')</span>';
            }
        }
    })
    .catch(err => {
        if (statusEl) {
            statusEl.innerHTML = '<span class="text-[11px] text-amber-600 font-semibold">Saved for automatic WebP conversion on submit.</span>';
        }
    });
}

// Visual Editor Paste Image to WebP Handler
document.addEventListener('DOMContentLoaded', function () {
    const visualEditor = document.getElementById('visualEditorContainer');
    if (visualEditor) {
        visualEditor.addEventListener('paste', function (e) {
            const items = (e.clipboardData || e.originalEvent.clipboardData).items;
            for (let i in items) {
                const item = items[i];
                if (item.kind === 'file' && item.type.startsWith('image/')) {
                    e.preventDefault();
                    const blob = item.getAsFile();
                    const formData = new FormData();
                    formData.append('file', blob);
                    const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

                    fetch('{{ route("admin.media.store") }}', {
                        method: 'POST',
                        body: formData,
                        headers: {
                            'X-CSRF-TOKEN': csrfToken,
                            'Accept': 'application/json'
                        }
                    })
                    .then(r => r.json())
                    .then(data => {
                        if (data.media && data.media.length > 0) {
                            document.execCommand('insertImage', false, data.media[0].url);
                        }
                    });
                    break;
                }
            }
        });
    }
});
</script>
@endpush
