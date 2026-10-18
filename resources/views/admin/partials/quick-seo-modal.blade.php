<!-- Reusable Yoast SEO & Content Signals Modal Component -->
<div x-data="yoastQuickSeoModal()" 
     x-cloak
     @open-quick-seo.window="openModal($event.detail)"
     @keydown.escape.window="closeModal()">

    <!-- Modal Backdrop -->
    <div x-show="isOpen" 
         x-transition:enter="transition ease-out duration-200"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         x-transition:leave="transition ease-in duration-150"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0"
         class="fixed inset-0 bg-slate-900/60 backdrop-blur-xs z-50 flex items-center justify-center p-4 overflow-y-auto">

        <!-- Modal Dialog -->
        <div x-show="isOpen"
             x-transition:enter="transition ease-out duration-200"
             x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
             x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
             x-transition:leave="transition ease-in duration-150"
             x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100"
             x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
             @click.outside="closeModal()"
             class="bg-white rounded-2xl border border-slate-200/80 shadow-2xl w-full max-w-3xl max-h-[92vh] flex flex-col overflow-hidden my-6">

            <!-- Header with Yoast Badges (matches screenshot) -->
            <div class="px-6 py-4 bg-slate-50 border-b border-slate-200 flex flex-wrap items-center justify-between gap-3 shrink-0">
                <div class="flex items-center gap-3">
                    <div class="w-9 h-9 rounded-xl bg-purple-600 text-white flex items-center justify-center font-bold text-sm shadow-md shadow-purple-500/20">
                        <i data-lucide="sparkles" class="w-4 h-4"></i>
                    </div>
                    <div>
                        <div class="flex items-center gap-2">
                            <h3 class="font-bold text-slate-800 text-sm">Yoast SEO & Content Signals</h3>
                            <span class="text-[10px] font-bold uppercase tracking-wider px-2 py-0.5 rounded-full bg-purple-100 text-purple-700">Live AI Assistant</span>
                        </div>
                        <p class="text-xs text-slate-500">Optimize search rankings, social preview cards & keyword density in real-time.</p>
                    </div>
                </div>

                <!-- Live Traffic Light Badges & Close -->
                <div class="flex items-center gap-3">
                    <!-- SEO Score -->
                    <div class="flex items-center gap-1.5 px-3 py-1 rounded-xl border text-xs font-semibold"
                         :class="{
                             'bg-emerald-50 text-emerald-700 border-emerald-200': seoScoreRating === 'good',
                             'bg-amber-50 text-amber-700 border-amber-200': seoScoreRating === 'ok',
                             'bg-rose-50 text-rose-700 border-rose-200': seoScoreRating === 'poor'
                         }">
                        <span class="w-2.5 h-2.5 rounded-full"
                              :class="{
                                  'bg-emerald-500 animate-pulse': seoScoreRating === 'good',
                                  'bg-amber-500': seoScoreRating === 'ok',
                                  'bg-rose-500': seoScoreRating === 'poor'
                              }"></span>
                        <span>SEO: <strong x-text="computedSeoScore + '/100'"></strong></span>
                        <span class="capitalize text-[11px] opacity-80" x-text="'(' + seoScoreRating + ')'"></span>
                    </div>

                    <!-- Readability Score -->
                    <div class="flex items-center gap-1.5 px-3 py-1 rounded-xl border text-xs font-semibold bg-emerald-50 text-emerald-700 border-emerald-200">
                        <span class="w-2.5 h-2.5 rounded-full bg-emerald-500"></span>
                        <span>Readability: <strong>100/100</strong></span>
                    </div>

                    <button type="button" @click="closeModal()" class="p-1.5 text-slate-400 hover:text-slate-700 hover:bg-slate-100 rounded-lg transition ml-1">
                        <i data-lucide="x" class="w-5 h-5"></i>
                    </button>
                </div>
            </div>

            <!-- Tabs Navigation -->
            <div class="px-6 pt-2.5 border-b border-slate-200 flex items-center justify-between bg-white shrink-0">
                <div class="flex items-center gap-1 text-xs font-bold">
                    <button type="button" @click="activeTab = 'general'"
                            :class="activeTab === 'general' ? 'border-purple-600 text-purple-600 bg-purple-50/50' : 'border-transparent text-slate-500 hover:text-slate-800'"
                            class="px-4 py-2 border-b-2 rounded-t-lg transition flex items-center gap-2">
                        <i data-lucide="search" class="w-3.5 h-3.5"></i>
                        <span>General SEO</span>
                    </button>
                    <button type="button" @click="activeTab = 'analysis'"
                            :class="activeTab === 'analysis' ? 'border-purple-600 text-purple-600 bg-purple-50/50' : 'border-transparent text-slate-500 hover:text-slate-800'"
                            class="px-4 py-2 border-b-2 rounded-t-lg transition flex items-center gap-2">
                        <i data-lucide="check-circle-2" class="w-3.5 h-3.5"></i>
                        <span>Yoast Signals & Audit</span>
                        <span class="text-[10px] px-1.5 py-0.5 rounded-full bg-purple-100 text-purple-700" x-text="passedChecksCount + '/7'"></span>
                    </button>
                    <button type="button" @click="activeTab = 'schema'"
                            :class="activeTab === 'schema' ? 'border-purple-600 text-purple-600 bg-purple-50/50' : 'border-transparent text-slate-500 hover:text-slate-800'"
                            class="px-4 py-2 border-b-2 rounded-t-lg transition flex items-center gap-2">
                        <i data-lucide="code-2" class="w-3.5 h-3.5"></i>
                        <span>Schema (Structured Data)</span>
                        <span class="w-2 h-2 rounded-full" :class="form.schema_markup && form.schema_markup.trim().length > 0 ? 'bg-emerald-500' : 'bg-slate-300'"></span>
                    </button>
                </div>

                <!-- Preview Mode Toggle (Desktop / Mobile) -->
                <div class="flex items-center gap-1 bg-slate-100 p-1 rounded-lg text-xs" x-show="activeTab === 'general'">
                    <button type="button" @click="previewMode = 'desktop'"
                            :class="previewMode === 'desktop' ? 'bg-white shadow-xs text-slate-800 font-bold' : 'text-slate-500 hover:text-slate-800'"
                            class="px-2.5 py-1 rounded transition flex items-center gap-1.5 text-[11px]">
                        <i data-lucide="monitor" class="w-3 h-3"></i>
                        <span>Desktop</span>
                    </button>
                    <button type="button" @click="previewMode = 'mobile'"
                            :class="previewMode === 'mobile' ? 'bg-white shadow-xs text-slate-800 font-bold' : 'text-slate-500 hover:text-slate-800'"
                            class="px-2.5 py-1 rounded transition flex items-center gap-1.5 text-[11px]">
                        <i data-lucide="smartphone" class="w-3 h-3"></i>
                        <span>Mobile</span>
                    </button>
                </div>
            </div>

            <!-- Modal Body Scrollable Area -->
            <div class="p-6 overflow-y-auto flex-1 space-y-6">

                <!-- ================= TAB 1: GENERAL SEO ================= -->
                <div x-show="activeTab === 'general'" class="space-y-6">

                    <!-- Focus Keyphrase Input (Purple Box from Screenshot) -->
                    <div class="p-4 rounded-xl border border-purple-200 bg-purple-50/40 space-y-2">
                        <div class="flex items-center justify-between">
                            <label class="text-xs font-bold uppercase tracking-wider text-purple-900 flex items-center gap-1.5">
                                <i data-lucide="key" class="w-3.5 h-3.5 text-purple-600"></i>
                                <span>FOCUS KEYPHRASE</span>
                            </label>
                            <span class="text-[11px] text-purple-700 font-medium">The main phrase you want to rank for</span>
                        </div>
                        <input type="text" x-model="form.focus_keyword" @input="recalc()"
                               placeholder="e.g. generator rental in uae, used portacabin buy in dubai, generator amc dubai"
                               class="w-full px-3.5 py-2.5 text-xs font-semibold rounded-xl border border-purple-200 bg-white focus:outline-none focus:ring-2 focus:ring-purple-500/20 focus:border-purple-600 transition">
                    </div>

                    <!-- Live Google Search Snippet Preview (Exact SERP from Screenshot) -->
                    <div class="space-y-2">
                        <div class="flex items-center justify-between text-xs font-bold text-slate-700">
                            <span class="flex items-center gap-1.5">
                                <i data-lucide="globe" class="w-3.5 h-3.5 text-purple-600"></i> Google Search Snippet Preview
                            </span>
                            <span class="text-[11px] font-normal text-slate-400" x-text="previewMode === 'desktop' ? 'Simulating Desktop SERP' : 'Simulating Mobile SERP'"></span>
                        </div>

                        <div class="rounded-xl border border-slate-200 bg-slate-50/70 p-4 transition-all"
                             :class="previewMode === 'mobile' ? 'max-w-md mx-auto shadow-md' : 'w-full shadow-xs'">
                            <div class="bg-white p-4 rounded-xl border border-slate-200/80 space-y-1.5 font-sans">
                                <!-- URL Bar -->
                                <div class="flex items-center gap-2 text-xs text-slate-600">
                                    <span class="w-4 h-4 rounded-full bg-purple-600 text-white flex items-center justify-center text-[10px] font-bold">G</span>
                                    <div class="flex items-center gap-1 truncate text-[11px]">
                                        <span class="font-medium text-slate-900">{{ config('app.name', 'LifeGuard Data Recovery') }}</span>
                                        <span class="text-slate-400">&rsaquo;</span>
                                        <span class="text-slate-500 truncate" x-text="getFullUrl()"></span>
                                    </div>
                                </div>

                                <!-- Snippet Title -->
                                <h4 class="text-blue-700 font-medium hover:underline cursor-pointer leading-snug"
                                    :class="previewMode === 'mobile' ? 'text-sm' : 'text-base font-medium'"
                                    x-text="form.meta_title || form.title || 'Page Title Placeholder'"></h4>

                                <!-- Snippet Description -->
                                <p class="text-xs text-slate-600 line-clamp-2 leading-relaxed"
                                   x-text="form.meta_description || 'Please provide a meta description by editing the snippet below. If you don’t, Google will pick relevant text from your page.'"></p>
                            </div>
                        </div>
                    </div>

                    <!-- SEO Title Field with Length Meter -->
                    <div class="space-y-1.5">
                        <div class="flex items-center justify-between">
                            <label class="text-xs font-bold text-slate-700">SEO Title</label>
                            <span class="text-[11px] font-mono font-semibold" 
                                  :class="titleLength >= 40 && titleLength <= 60 ? 'text-emerald-600' : (titleLength > 60 ? 'text-rose-600' : 'text-amber-600')"
                                  x-text="titleLength + ' / 60 characters'"></span>
                        </div>
                        <input type="text" x-model="form.meta_title" @input="recalc()"
                               placeholder="e.g. Generator Rental in UAE | Diesel Generator for Rent 20-2500 kVA"
                               class="w-full px-3.5 py-2.5 text-xs rounded-xl border border-slate-200 focus:outline-none focus:ring-2 focus:ring-purple-500/20 focus:border-purple-600">
                        <!-- Title Meter Bar -->
                        <div class="w-full h-1.5 bg-slate-100 rounded-full overflow-hidden">
                            <div class="h-full transition-all duration-300"
                                 :class="titleLength >= 40 && titleLength <= 60 ? 'bg-emerald-500' : (titleLength > 60 ? 'bg-rose-500' : 'bg-amber-400')"
                                 :style="'width: ' + Math.min(100, (titleLength / 60) * 100) + '%'"></div>
                        </div>
                        <p class="text-[11px] text-slate-400">Aim for 40 to 60 characters to avoid truncation on Google search pages.</p>
                    </div>

                    <!-- Meta Description Field with Length Meter -->
                    <div class="space-y-1.5">
                        <div class="flex items-center justify-between">
                            <label class="text-xs font-bold text-slate-700">Meta Description</label>
                            <span class="text-[11px] font-mono font-semibold"
                                  :class="descLength >= 120 && descLength <= 160 ? 'text-emerald-600' : (descLength > 160 ? 'text-rose-600' : 'text-amber-600')"
                                  x-text="descLength + ' / 160 characters'"></span>
                        </div>
                        <textarea x-model="form.meta_description" @input="recalc()" rows="3"
                                  placeholder="e.g. Leading diesel generator rental in Dubai, Abu Dhabi & UAE. 20 kVA to 2500 kVA Cummins & Perkins sets, 24/7 on-site maintenance, 60-min emergency mobilization."
                                  class="w-full px-3.5 py-2.5 text-xs rounded-xl border border-slate-200 focus:outline-none focus:ring-2 focus:ring-purple-500/20 focus:border-purple-600"></textarea>
                        <!-- Desc Meter Bar -->
                        <div class="w-full h-1.5 bg-slate-100 rounded-full overflow-hidden">
                            <div class="h-full transition-all duration-300"
                                 :class="descLength >= 120 && descLength <= 160 ? 'bg-emerald-500' : (descLength > 160 ? 'bg-rose-500' : 'bg-amber-400')"
                                 :style="'width: ' + Math.min(100, (descLength / 160) * 100) + '%'"></div>
                        </div>
                        <p class="text-[11px] text-slate-400">Aim for 120 to 160 characters. A strong meta description boosts search engine click-through rates.</p>
                    </div>
                </div>

                <!-- ================= TAB 2: AUDIT & SIGNALS ================= -->
                <div x-show="activeTab === 'analysis'" class="space-y-4">
                    <div class="p-4 rounded-xl bg-purple-50/50 border border-purple-100 flex items-center justify-between">
                        <div>
                            <h4 class="text-xs font-bold text-purple-900">Yoast Content SEO Checklist</h4>
                            <p class="text-[11px] text-purple-700">Live signals calculated from keyword placement and density.</p>
                        </div>
                        <span class="text-xs font-bold px-2.5 py-1 rounded-full bg-purple-200 text-purple-800" x-text="passedChecksCount + ' / 7 Passed'"></span>
                    </div>

                    <div class="space-y-2">
                        <!-- Check 1: Focus Keyphrase exists -->
                        <div class="flex items-center gap-3 p-3 rounded-xl border border-slate-100 hover:bg-slate-50 transition">
                            <span class="w-2.5 h-2.5 rounded-full" :class="form.focus_keyword ? 'bg-emerald-500' : 'bg-rose-500'"></span>
                            <div class="flex-1 text-xs">
                                <span class="font-bold text-slate-800">Focus keyphrase specified:</span>
                                <span class="text-slate-500" x-text="form.focus_keyword ? 'Targeting: &quot;' + form.focus_keyword + '&quot;' : 'No focus keyphrase specified yet.'"></span>
                            </div>
                        </div>

                        <!-- Check 2: Keyphrase in SEO Title -->
                        <div class="flex items-center gap-3 p-3 rounded-xl border border-slate-100 hover:bg-slate-50 transition">
                            <span class="w-2.5 h-2.5 rounded-full" :class="hasKwInTitle ? 'bg-emerald-500' : 'bg-rose-500'"></span>
                            <div class="flex-1 text-xs">
                                <span class="font-bold text-slate-800">Keyphrase in SEO Title:</span>
                                <span class="text-slate-500" x-text="hasKwInTitle ? 'Keyphrase appears prominently in your SEO title.' : 'Keyphrase does not appear in the SEO title.'"></span>
                            </div>
                        </div>

                        <!-- Check 3: SEO Title Length -->
                        <div class="flex items-center gap-3 p-3 rounded-xl border border-slate-100 hover:bg-slate-50 transition">
                            <span class="w-2.5 h-2.5 rounded-full" :class="titleLength >= 40 && titleLength <= 60 ? 'bg-emerald-500' : 'bg-amber-500'"></span>
                            <div class="flex-1 text-xs">
                                <span class="font-bold text-slate-800">SEO Title Width:</span>
                                <span class="text-slate-500" x-text="titleLength >= 40 && titleLength <= 60 ? 'Optimal width (' + titleLength + ' chars).' : 'Current width is ' + titleLength + ' chars (ideal is 40-60).'"></span>
                            </div>
                        </div>

                        <!-- Check 4: Keyphrase in Meta Description -->
                        <div class="flex items-center gap-3 p-3 rounded-xl border border-slate-100 hover:bg-slate-50 transition">
                            <span class="w-2.5 h-2.5 rounded-full" :class="hasKwInDesc ? 'bg-emerald-500' : 'bg-rose-500'"></span>
                            <div class="flex-1 text-xs">
                                <span class="font-bold text-slate-800">Keyphrase in Meta Description:</span>
                                <span class="text-slate-500" x-text="hasKwInDesc ? 'Keyphrase appears in the meta description snippet.' : 'Keyphrase is missing from meta description.'"></span>
                            </div>
                        </div>

                        <!-- Check 5: Meta Description Length -->
                        <div class="flex items-center gap-3 p-3 rounded-xl border border-slate-100 hover:bg-slate-50 transition">
                            <span class="w-2.5 h-2.5 rounded-full" :class="descLength >= 120 && descLength <= 160 ? 'bg-emerald-500' : 'bg-amber-500'"></span>
                            <div class="flex-1 text-xs">
                                <span class="font-bold text-slate-800">Meta Description Length:</span>
                                <span class="text-slate-500" x-text="descLength >= 120 && descLength <= 160 ? 'Optimal length (' + descLength + ' chars).' : 'Current length is ' + descLength + ' chars (ideal is 120-160).'"></span>
                            </div>
                        </div>

                        <!-- Check 6: Keyphrase in Slug -->
                        <div class="flex items-center gap-3 p-3 rounded-xl border border-slate-100 hover:bg-slate-50 transition">
                            <span class="w-2.5 h-2.5 rounded-full" :class="hasKwInSlug ? 'bg-emerald-500' : 'bg-amber-500'"></span>
                            <div class="flex-1 text-xs">
                                <span class="font-bold text-slate-800">Keyphrase in URL Slug:</span>
                                <span class="text-slate-500" x-text="hasKwInSlug ? 'URL slug contains target keyword tokens.' : 'Target keyword tokens not detected in URL slug.'"></span>
                            </div>
                        </div>

                        <!-- Check 7: Schema Structured Data -->
                        <div class="flex items-center gap-3 p-3 rounded-xl border border-slate-100 hover:bg-slate-50 transition">
                            <span class="w-2.5 h-2.5 rounded-full bg-emerald-500"></span>
                            <div class="flex-1 text-xs">
                                <span class="font-bold text-slate-800">Google Rich Snippet Schema:</span>
                                <span class="text-slate-500" x-text="form.schema_markup && form.schema_markup.trim().length > 0 ? 'Custom Schema JSON-LD active.' : 'Auto-generated Schema.org structured data active.'"></span>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- ================= TAB 3: SCHEMA.ORG ================= -->
                <div x-show="activeTab === 'schema'" class="space-y-4">
                    <div class="flex items-center justify-between pb-2 border-b border-slate-100">
                        <div>
                            <label class="block text-xs font-bold uppercase tracking-wider text-slate-800 flex items-center gap-1.5">
                                <i data-lucide="code-2" class="w-3.5 h-3.5 text-purple-600"></i>
                                <span>Custom Schema.org JSON-LD Code</span>
                            </label>
                            <p class="text-[11px] text-slate-500 mt-0.5">Paste custom JSON-LD structured data code or leave blank to automatically generate Service / Article / WebPage schema.</p>
                        </div>
                        <button type="button" @click="insertDefaultSchema()" class="px-2.5 py-1 text-[11px] font-semibold text-purple-600 bg-purple-50 hover:bg-purple-100 rounded-lg transition border border-purple-200">
                            + Insert Template
                        </button>
                    </div>

                    <textarea x-model="form.schema_markup" rows="7"
                              placeholder='Paste custom schema JSON-LD here or leave blank for automatic schema generation...'
                              class="w-full px-3.5 py-2.5 text-xs font-mono rounded-xl border border-slate-200 focus:outline-none focus:ring-2 focus:ring-purple-500/20 focus:border-purple-600 bg-slate-50"></textarea>

                    <div class="p-3 rounded-xl bg-slate-50 border border-slate-200 text-slate-600 text-[11px] flex items-center justify-between">
                        <span class="flex items-center gap-1.5">
                            <i data-lucide="info" class="w-3.5 h-3.5 text-brand-600"></i>
                            <span x-text="form.schema_markup && form.schema_markup.trim().length > 0 ? 'Custom schema code will be injected in <head>.' : 'Automatic Schema (BreadcrumbList + Service/Article + FAQs) is active.'"></span>
                        </span>
                        <button type="button" x-show="form.schema_markup" @click="form.schema_markup = ''" class="text-rose-600 hover:underline">
                            Clear (Revert to Auto)
                        </button>
                    </div>
                </div>

            </div>

            <!-- Modal Footer Actions -->
            <div class="px-6 py-4 bg-slate-50 border-t border-slate-200 flex items-center justify-between shrink-0">
                <button type="button" @click="closeModal()" class="px-4 py-2 text-xs font-semibold text-slate-600 hover:text-slate-800 rounded-xl transition">
                    Cancel
                </button>
                <div class="flex items-center gap-3">
                    <span x-show="saveSuccess" x-transition class="text-xs font-bold text-emerald-600 flex items-center gap-1">
                        <i data-lucide="check" class="w-4 h-4"></i> Saved!
                    </span>
                    <button type="button" @click="saveSeo()" :disabled="isSaving"
                            class="px-5 py-2.5 rounded-xl bg-purple-600 hover:bg-purple-700 text-white text-xs font-bold shadow-md shadow-purple-500/20 transition flex items-center gap-1.5 disabled:opacity-50">
                        <i data-lucide="check" class="w-4 h-4" x-show="!isSaving"></i>
                        <svg class="animate-spin w-4 h-4 text-white" x-show="isSaving" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8H4z"></path>
                        </svg>
                        <span x-text="isSaving ? 'Saving...' : 'Save SEO & Schema Settings'"></span>
                    </button>
                </div>
            </div>

        </div>
    </div>
</div>

<script>
function yoastQuickSeoModal() {
    return {
        isOpen: false,
        activeTab: 'general',
        previewMode: 'desktop',
        isSaving: false,
        saveSuccess: false,
        itemType: 'service', // service | post | page
        endpoint: '',
        form: {
            id: null,
            title: '',
            slug: '',
            focus_keyword: '',
            meta_title: '',
            meta_description: '',
            schema_markup: '',
            seo_score: 0,
        },

        openModal(data) {
            this.itemType = data.type || 'service';
            this.endpoint = data.endpoint || '';
            this.form.id = data.id || null;
            this.form.title = data.title || '';
            this.form.slug = data.slug || '';
            this.form.focus_keyword = data.focus_keyword || '';
            this.form.meta_title = data.meta_title || '';
            this.form.meta_description = data.meta_description || '';
            this.form.schema_markup = data.schema_markup || '';
            this.form.seo_score = data.seo_score || 0;
            this.activeTab = 'general';
            this.previewMode = 'desktop';
            this.saveSuccess = false;
            this.isOpen = true;
            this.$nextTick(() => {
                if (window.lucide) window.lucide.createIcons();
            });
        },

        closeModal() {
            this.isOpen = false;
        },

        get titleLength() {
            return (this.form.meta_title || this.form.title || '').length;
        },

        get descLength() {
            return (this.form.meta_description || '').length;
        },

        get hasKwInTitle() {
            const kw = (this.form.focus_keyword || '').toLowerCase().trim();
            if (!kw) return false;
            const t = (this.form.meta_title || this.form.title || '').toLowerCase();
            return t.includes(kw);
        },

        get hasKwInDesc() {
            const kw = (this.form.focus_keyword || '').toLowerCase().trim();
            if (!kw) return false;
            const d = (this.form.meta_description || '').toLowerCase();
            return d.includes(kw);
        },

        get hasKwInSlug() {
            const kw = (this.form.focus_keyword || '').toLowerCase().trim();
            if (!kw) return false;
            const slug = (this.form.slug || '').toLowerCase().replace(/-/g, ' ');
            const kwWords = kw.split(/\s+/).filter(w => w.length > 2);
            if (!kwWords.length) return false;
            return kwWords.some(w => slug.includes(w));
        },

        get passedChecksCount() {
            let passed = 1; // Schema active is always passed
            if (this.form.focus_keyword && this.form.focus_keyword.trim().length > 0) passed++;
            if (this.hasKwInTitle) passed++;
            if (this.titleLength >= 40 && this.titleLength <= 60) passed++;
            if (this.hasKwInDesc) passed++;
            if (this.descLength >= 120 && this.descLength <= 160) passed++;
            if (this.hasKwInSlug) passed++;
            return passed;
        },

        get computedSeoScore() {
            let score = 30;
            if (this.form.focus_keyword && this.form.focus_keyword.trim().length > 0) score += 20;
            if (this.hasKwInTitle) score += 15;
            if (this.titleLength >= 40 && this.titleLength <= 60) score += 10;
            if (this.hasKwInDesc) score += 15;
            if (this.descLength >= 120 && this.descLength <= 160) score += 10;
            return Math.min(100, Math.max(0, score));
        },

        get seoScoreRating() {
            const s = this.computedSeoScore;
            if (s >= 80) return 'good';
            if (s >= 50) return 'ok';
            return 'poor';
        },

        getFullUrl() {
            const prefix = this.itemType === 'service' ? 'service' : (this.itemType === 'post' ? 'post' : 'page');
            return 'https://lifeguarddatarecovery.com/' + prefix + '/' + (this.form.slug || '');
        },

        recalc() {
            // Triggers reactivity
        },

        insertDefaultSchema() {
            const name = this.form.title || 'LifeGuard Data Recovery Dubai';
            const schema = {
                name: name,
                description: this.form.meta_description || 'Professional ISO-5 cleanroom data recovery services in Dubai & across UAE.',
                provider: {
                    name: 'LifeGuard Data Recovery',
                    url: 'https://lifeguarddatarecovery.com'
                }
            };
            schema['@' + 'context'] = 'https://schema.org';
            schema['@' + 'type'] = this.itemType === 'service' ? 'Service' : (this.itemType === 'post' ? 'Article' : 'WebPage');
            schema.provider['@' + 'type'] = 'Organization';
            this.form.schema_markup = JSON.stringify(schema, null, 2);
        },

        async saveSeo() {
            if (!this.endpoint) {
                alert('Missing save endpoint.');
                return;
            }

            this.isSaving = true;
            this.saveSuccess = false;

            try {
                const token = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '';
                const payload = {
                    focus_keyword: this.form.focus_keyword,
                    meta_title: this.form.meta_title,
                    meta_description: this.form.meta_description,
                    schema_markup: this.form.schema_markup,
                    seo_score: this.computedSeoScore,
                };

                const res = await fetch(this.endpoint, {
                    method: 'PATCH',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': token,
                    },
                    body: JSON.stringify(payload),
                });

                if (res.ok) {
                    const data = await res.json();
                    this.saveSuccess = true;
                    this.isSaving = false;

                    // Update corresponding DOM element if present
                    const rowId = `seo-row-${this.itemType}-${this.form.id}`;
                    const row = document.getElementById(rowId);
                    if (row) {
                        const kwEl = row.querySelector('.seo-kw-cell');
                        if (kwEl) {
                            if (this.form.focus_keyword) {
                                kwEl.innerHTML = `<span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-[11px] font-bold bg-purple-50 text-purple-700 border border-purple-200"><i data-lucide="tag" class="w-3 h-3 text-purple-500"></i><span>${this.form.focus_keyword}</span></span>`;
                            } else {
                                kwEl.innerHTML = `<span class="text-[10px] font-bold text-rose-500 bg-rose-50 px-2 py-0.5 rounded-full border border-dashed border-rose-200">+ Add Keyword</span>`;
                            }
                        }

                        const sigEl = row.querySelector('.seo-sig-cell');
                        if (sigEl) {
                            const rating = this.seoScoreRating;
                            const score = this.computedSeoScore;
                            const cls = rating === 'good' ? 'bg-emerald-50 text-emerald-700 border-emerald-200' : (rating === 'ok' ? 'bg-amber-50 text-amber-700 border-amber-200' : 'bg-rose-50 text-rose-700 border-rose-200');
                            const dotCls = rating === 'good' ? 'bg-emerald-500 animate-pulse' : (rating === 'ok' ? 'bg-amber-500' : 'bg-rose-500');
                            sigEl.innerHTML = `<div class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-[11px] font-bold border ${cls}"><span class="w-2 h-2 rounded-full ${dotCls}"></span><span>${score}/100</span><span class="uppercase text-[9px] opacity-75">(${rating})</span></div>`;
                        }

                        if (window.lucide) window.lucide.createIcons();
                    }

                    setTimeout(() => {
                        this.closeModal();
                    }, 600);
                } else {
                    const err = await res.json();
                    alert(err.message || 'Error updating SEO settings.');
                    this.isSaving = false;
                }
            } catch (e) {
                console.error(e);
                alert('Network error while saving SEO.');
                this.isSaving = false;
            }
        }
    };
}

window.openQuickSeoModal = function(data) {
    window.dispatchEvent(new CustomEvent('open-quick-seo', { detail: data }));
};
</script>
