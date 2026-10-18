@props([
    'model' => null,
    'titleInputId' => 'titleInput',
    'slugInputId' => 'slugInput',
    'contentInputId' => 'contentInput',
    'urlPrefix' => url('/'),
    'type' => 'post'
])

@php
    $focusKeyword = old('focus_keyword', $model->focus_keyword ?? '');
    $metaTitle = old('meta_title', $model->meta_title ?? '');
    $metaDescription = old('meta_description', $model->meta_description ?? '');
    $metaKeywords = old('meta_keywords', $model->meta_keywords ?? '');
    $canonicalUrl = old('canonical_url', $model->canonical_url ?? '');
    $ogTitle = old('og_title', $model->og_title ?? '');
    $ogDescription = old('og_description', $model->og_description ?? '');
    $ogImage = old('og_image', $model->og_image ?? '');
    $isIndexable = old('is_indexable', $model->is_indexable ?? true);
    $seoScore = old('seo_score', $model->seo_score ?? 0);
    $readabilityScore = old('readability_score', $model->readability_score ?? 0);
    $schemaMarkup = old('schema_markup', $model->schema_markup ?? '');
@endphp

<div class="bg-white rounded-2xl border border-slate-200/80 shadow-sm overflow-hidden" 
     x-data="yoastSeoAnalyzer({
        titleInputId: '{{ $titleInputId }}',
        slugInputId: '{{ $slugInputId }}',
        contentInputId: '{{ $contentInputId }}',
        urlPrefix: '{{ $urlPrefix }}',
        initialFocusKeyword: @js($focusKeyword),
        initialTitle: @js($metaTitle),
        initialDescription: @js($metaDescription),
        initialSeoScore: {{ (int) $seoScore }},
        initialReadabilityScore: {{ (int) $readabilityScore }},
        initialSchemaMarkup: @js($schemaMarkup)
     })" 
     x-init="initAnalyzer()">
    
    <!-- Header with Yoast Traffic Light Badges -->
    <div class="px-6 py-4 bg-slate-50 border-b border-slate-200 flex flex-wrap items-center justify-between gap-4">
        <div class="flex items-center gap-3">
            <div class="w-8 h-8 rounded-xl bg-purple-600 text-white flex items-center justify-center font-bold text-xs shadow-md shadow-purple-500/20">
                <i data-lucide="sparkles" class="w-4 h-4"></i>
            </div>
            <div>
                <div class="flex items-center gap-2">
                    <h3 class="font-bold text-slate-800 text-sm">Yoast SEO & Content Signals</h3>
                    <span class="text-[10px] font-bold uppercase tracking-wider px-2 py-0.5 rounded-full bg-purple-100 text-purple-700">Live AI Assistant</span>
                </div>
                <p class="text-xs text-slate-400">Optimize search rankings, social preview cards & keyword density in real-time.</p>
            </div>
        </div>

        <!-- Yoast Traffic Light Badges -->
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
                <span>SEO: <strong x-text="seoScore + '/100'"></strong></span>
                <span class="capitalize text-[11px] opacity-80" x-text="'(' + seoScoreRating + ')'"></span>
            </div>

            <!-- Readability Score -->
            <div class="flex items-center gap-1.5 px-3 py-1 rounded-xl border text-xs font-semibold"
                 :class="{
                     'bg-emerald-50 text-emerald-700 border-emerald-200': readabilityRating === 'good',
                     'bg-amber-50 text-amber-700 border-amber-200': readabilityRating === 'ok',
                     'bg-rose-50 text-rose-700 border-rose-200': readabilityRating === 'poor'
                 }">
                <span class="w-2.5 h-2.5 rounded-full"
                      :class="{
                          'bg-emerald-500': readabilityRating === 'good',
                          'bg-amber-500': readabilityRating === 'ok',
                          'bg-rose-500': readabilityRating === 'poor'
                      }"></span>
                <span>Readability: <strong x-text="readabilityScore + '/100'"></strong></span>
            </div>
        </div>
    </div>

    <!-- Hidden form fields to store calculated scores -->
    <input type="hidden" name="seo_score" :value="seoScore">
    <input type="hidden" name="readability_score" :value="readabilityScore">

    <!-- Navigation Tabs -->
    <div class="px-6 pt-3 border-b border-slate-200 flex items-center justify-between bg-white flex-wrap gap-2">
        <div class="flex items-center gap-1 text-xs font-bold">
            <button type="button" @click="activeTab = 'general'" 
                    :class="activeTab === 'general' ? 'border-purple-600 text-purple-600 bg-purple-50/50' : 'border-transparent text-slate-500 hover:text-slate-800'"
                    class="px-4 py-2.5 border-b-2 rounded-t-lg transition flex items-center gap-2">
                <i data-lucide="search" class="w-3.5 h-3.5"></i>
                <span>General SEO</span>
            </button>
            <button type="button" @click="activeTab = 'analysis'" 
                    :class="activeTab === 'analysis' ? 'border-purple-600 text-purple-600 bg-purple-50/50' : 'border-transparent text-slate-500 hover:text-slate-800'"
                    class="px-4 py-2.5 border-b-2 rounded-t-lg transition flex items-center gap-2">
                <i data-lucide="check-circle-2" class="w-3.5 h-3.5"></i>
                <span>Yoast Signals & Audit</span>
                <span class="text-[10px] px-1.5 py-0.2 rounded-full bg-slate-200 text-slate-700" x-text="passedChecksCount + '/' + totalChecksCount"></span>
            </button>
            <button type="button" @click="activeTab = 'social'" 
                    :class="activeTab === 'social' ? 'border-purple-600 text-purple-600 bg-purple-50/50' : 'border-transparent text-slate-500 hover:text-slate-800'"
                    class="px-4 py-2.5 border-b-2 rounded-t-lg transition flex items-center gap-2">
                <i data-lucide="share-2" class="w-3.5 h-3.5"></i>
                <span>Social (Open Graph)</span>
            </button>
            <button type="button" @click="activeTab = 'advanced'" 
                    :class="activeTab === 'advanced' ? 'border-purple-600 text-purple-600 bg-purple-50/50' : 'border-transparent text-slate-500 hover:text-slate-800'"
                    class="px-4 py-2.5 border-b-2 rounded-t-lg transition flex items-center gap-2">
                <i data-lucide="sliders" class="w-3.5 h-3.5"></i>
                <span>Robots & Indexing</span>
            </button>
            <button type="button" @click="activeTab = 'schema'" 
                    :class="activeTab === 'schema' ? 'border-purple-600 text-purple-600 bg-purple-50/50' : 'border-transparent text-slate-500 hover:text-slate-800'"
                    class="px-4 py-2.5 border-b-2 rounded-t-lg transition flex items-center gap-2">
                <i data-lucide="code-2" class="w-3.5 h-3.5"></i>
                <span>Schema.org JSON-LD</span>
                @if(!empty($schemaMarkup))
                    <span class="w-1.5 h-1.5 rounded-full bg-emerald-500" title="Custom Schema active"></span>
                @endif
            </button>
        </div>

        <!-- Preview Mode Toggle (Desktop / Mobile) -->
        <div class="flex items-center gap-1 bg-slate-100 p-1 rounded-lg text-xs" x-show="activeTab === 'general' || activeTab === 'social'">
            <button type="button" @click="previewMode = 'desktop'" 
                    :class="previewMode === 'desktop' ? 'bg-white shadow-xs text-slate-800 font-bold' : 'text-slate-500 hover:text-slate-800'"
                    class="px-2.5 py-1 rounded transition flex items-center gap-1.5">
                <i data-lucide="monitor" class="w-3 h-3"></i>
                <span>Desktop</span>
            </button>
            <button type="button" @click="previewMode = 'mobile'" 
                    :class="previewMode === 'mobile' ? 'bg-white shadow-xs text-slate-800 font-bold' : 'text-slate-500 hover:text-slate-800'"
                    class="px-2.5 py-1 rounded transition flex items-center gap-1.5">
                <i data-lucide="smartphone" class="w-3 h-3"></i>
                <span>Mobile</span>
            </button>
        </div>
    </div>

    <!-- Body Contents -->
    <div class="p-6 space-y-6">

        <!-- ================= TAB 1: GENERAL SEO ================= -->
        <div x-show="activeTab === 'general'" class="space-y-6">

            <!-- Focus Keyword Input -->
            <div class="p-4 rounded-xl border border-purple-200 bg-purple-50/40 space-y-2">
                <div class="flex items-center justify-between">
                    <label for="focusKeywordInput" class="text-xs font-bold uppercase tracking-wider text-purple-900 flex items-center gap-1.5">
                        <i data-lucide="key" class="w-3.5 h-3.5 text-purple-600"></i>
                        Focus Keyphrase
                    </label>
                    <span class="text-[11px] text-purple-700 font-medium">The main phrase you want to rank for</span>
                </div>
                <input type="text" id="focusKeywordInput" name="focus_keyword" x-model="focusKeyword" @input="reanalyze()"
                       placeholder="e.g. generator rental in uae, used portacabin buy in dubai"
                       class="w-full px-3.5 py-2.5 text-xs font-semibold rounded-xl border border-purple-200 bg-white focus:outline-none focus:ring-2 focus:ring-purple-500/20 focus:border-purple-600 transition">
            </div>

            <!-- Live Google Snippet Preview -->
            <div class="space-y-2">
                <div class="flex items-center justify-between text-xs font-bold text-slate-700">
                    <span class="flex items-center gap-1.5">
                        <i data-lucide="globe" class="w-3.5 h-3.5 text-purple-600"></i> Google Search Snippet Preview
                    </span>
                    <span class="text-[11px] font-normal text-slate-400" x-text="previewMode === 'desktop' ? 'Simulating Desktop SERP' : 'Simulating Mobile SERP'"></span>
                </div>

                <div class="rounded-xl border border-slate-200 bg-slate-50/70 p-4 transition-all"
                     :class="previewMode === 'mobile' ? 'max-w-md mx-auto shadow-md' : 'w-full shadow-xs'">
                    <div class="bg-white p-4 rounded-lg border border-slate-200/80 space-y-1.5 font-sans">
                        <!-- URL Bar -->
                        <div class="flex items-center gap-2 text-xs text-slate-600">
                            <span class="w-4 h-4 rounded-full bg-purple-600 text-white flex items-center justify-center text-[10px] font-bold">G</span>
                            <div class="flex items-center gap-1 truncate text-[11px]">
                                <span class="font-medium text-slate-900">{{ config('app.name', 'LifeGuard Data Recovery') }}</span>
                                <span class="text-slate-400">&rsaquo;</span>
                                <span class="text-slate-500 truncate" x-text="currentSlug ? urlPrefix + '/' + currentSlug : urlPrefix"></span>
                            </div>
                        </div>

                        <!-- Snippet Title -->
                        <h4 class="text-blue-700 font-medium hover:underline cursor-pointer leading-snug"
                            :class="previewMode === 'mobile' ? 'text-sm' : 'text-base font-medium'"
                            x-text="metaTitle || effectiveTitle || 'Title Placeholder'"></h4>

                        <!-- Snippet Description -->
                        <p class="text-xs text-slate-600 line-clamp-2 leading-relaxed"
                           x-text="metaDescription || effectiveDescription || 'Please provide a meta description by editing the snippet below. If you don’t, Google will pick relevant text from your page.'"></p>
                    </div>
                </div>
            </div>

            <!-- Meta Title Field with Length Meter -->
            <div class="space-y-1.5">
                <div class="flex items-center justify-between">
                    <label for="metaTitleInput" class="text-xs font-bold text-slate-700">SEO Title</label>
                    <span class="text-[11px] font-mono" :class="titleLengthClass" x-text="titleLength + ' / 60 characters'"></span>
                </div>
                <input type="text" id="metaTitleInput" name="meta_title" x-model="metaTitle" @input="reanalyze()"
                       placeholder="Leave blank to use main page title"
                       class="w-full px-3.5 py-2.5 text-xs rounded-xl border border-slate-200 focus:outline-none focus:ring-2 focus:ring-purple-500/20 focus:border-purple-600">
                <!-- Title Meter Bar -->
                <div class="w-full h-1.5 bg-slate-100 rounded-full overflow-hidden">
                    <div class="h-full transition-all duration-300" 
                         :class="titleMeterColor"
                         :style="'width: ' + Math.min(100, (titleLength / 60) * 100) + '%'"></div>
                </div>
                <p class="text-[11px] text-slate-400">Aim for 40 to 60 characters to avoid truncation on Google search pages.</p>
            </div>

            <!-- Meta Description Field with Length Meter -->
            <div class="space-y-1.5">
                <div class="flex items-center justify-between">
                    <label for="metaDescriptionInput" class="text-xs font-bold text-slate-700">Meta Description</label>
                    <span class="text-[11px] font-mono" :class="descLengthClass" x-text="descLength + ' / 160 characters'"></span>
                </div>
                <textarea id="metaDescriptionInput" name="meta_description" rows="3" x-model="metaDescription" @input="reanalyze()"
                          placeholder="Write a clear, compelling summary (120-160 characters) with your focus keyword to encourage click-throughs..."
                          class="w-full px-3.5 py-2.5 text-xs rounded-xl border border-slate-200 focus:outline-none focus:ring-2 focus:ring-purple-500/20 focus:border-purple-600"></textarea>
                <!-- Description Meter Bar -->
                <div class="w-full h-1.5 bg-slate-100 rounded-full overflow-hidden">
                    <div class="h-full transition-all duration-300" 
                         :class="descMeterColor"
                         :style="'width: ' + Math.min(100, (descLength / 160) * 100) + '%'"></div>
                </div>
                <p class="text-[11px] text-slate-400">Recommended length: 120 - 160 characters.</p>
            </div>

            <!-- Meta Keywords -->
            <div class="space-y-1.5">
                <label for="metaKeywordsInput" class="block text-xs font-bold text-slate-700">Meta Keywords</label>
                <input type="text" id="metaKeywordsInput" name="meta_keywords" value="{{ $metaKeywords }}"
                       placeholder="separated, by, commas"
                       class="w-full px-3.5 py-2.5 text-xs rounded-xl border border-slate-200 focus:outline-none focus:ring-2 focus:ring-purple-500/20 focus:border-purple-600">
            </div>
        </div>

        <!-- ================= TAB 2: YOAST SIGNALS & AUDIT ================= -->
        <div x-show="activeTab === 'analysis'" class="space-y-6">
            <div class="p-4 bg-slate-50 rounded-xl border border-slate-200 space-y-3">
                <div class="flex items-center justify-between">
                    <div class="flex items-center gap-2">
                        <i data-lucide="shield-check" class="w-4 h-4 text-purple-600"></i>
                        <h4 class="font-bold text-slate-800 text-xs uppercase tracking-wider">Yoast SEO Audit Checklist</h4>
                    </div>
                    <span class="text-xs font-bold" :class="seoScoreRating === 'good' ? 'text-emerald-600' : (seoScoreRating === 'ok' ? 'text-amber-600' : 'text-rose-600')"
                          x-text="passedChecksCount + ' out of ' + totalChecksCount + ' checks passing'"></span>
                </div>
                <p class="text-xs text-slate-500">Live checks evaluate title, meta description, keyword presence, density, content length, and readability.</p>
            </div>

            <!-- Signal List -->
            <div class="space-y-2.5">
                <template x-for="(check, index) in checksList" :key="index">
                    <div class="flex items-start gap-3 p-3 rounded-xl border transition-colors"
                         :class="{
                             'bg-emerald-50/40 border-emerald-200/80': check.status === 'good',
                             'bg-amber-50/40 border-amber-200/80': check.status === 'ok',
                             'bg-rose-50/40 border-rose-200/80': check.status === 'poor'
                         }">
                        <!-- Status Dot -->
                        <span class="mt-0.5 w-3 h-3 rounded-full shrink-0 flex items-center justify-center"
                              :class="{
                                  'bg-emerald-500 text-white': check.status === 'good',
                                  'bg-amber-500 text-white': check.status === 'ok',
                                  'bg-rose-500 text-white': check.status === 'poor'
                              }">
                        </span>
                        <!-- Content -->
                        <div class="flex-1 text-xs">
                            <span class="font-bold block"
                                  :class="{
                                      'text-emerald-900': check.status === 'good',
                                      'text-amber-900': check.status === 'ok',
                                      'text-rose-900': check.status === 'poor'
                                  }" x-text="check.title"></span>
                            <span class="text-slate-600 text-[11px] leading-relaxed" x-text="check.message"></span>
                        </div>
                    </div>
                </template>
            </div>
        </div>

        <!-- ================= TAB 3: SOCIAL OPEN GRAPH ================= -->
        <div x-show="activeTab === 'social'" class="space-y-6">
            <p class="text-xs text-slate-500">Preview and customize how this page looks when shared on LinkedIn, Facebook, WhatsApp, and Twitter.</p>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Fields -->
                <div class="space-y-4">
                    <div>
                        <label class="block text-xs font-bold text-slate-700 mb-1">Social Title</label>
                        <input type="text" name="og_title" x-model="ogTitle"
                               placeholder="Defaults to SEO title if blank"
                               class="w-full px-3.5 py-2.5 text-xs rounded-xl border border-slate-200 focus:outline-none focus:ring-2 focus:ring-purple-500/20 focus:border-purple-600">
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-slate-700 mb-1">Social Description</label>
                        <textarea name="og_description" rows="3" x-model="ogDescription"
                                  placeholder="Defaults to meta description if blank"
                                  class="w-full px-3.5 py-2.5 text-xs rounded-xl border border-slate-200 focus:outline-none focus:ring-2 focus:ring-purple-500/20 focus:border-purple-600"></textarea>
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-slate-700 mb-1">Social Image URL</label>
                        <input type="text" name="og_image" x-model="ogImage"
                               placeholder="https://... or image URL"
                               class="w-full px-3.5 py-2.5 text-xs rounded-xl border border-slate-200 focus:outline-none focus:ring-2 focus:ring-purple-500/20 focus:border-purple-600">
                    </div>
                </div>

                <!-- Live Social Card Preview -->
                <div class="space-y-2">
                    <span class="text-xs font-bold text-slate-700 flex items-center gap-1.5">
                        <i data-lucide="share" class="w-3.5 h-3.5 text-purple-600"></i> Social Card Preview
                    </span>
                    <div class="rounded-xl border border-slate-200 overflow-hidden shadow-xs bg-white">
                        <div class="h-36 bg-slate-100 flex items-center justify-center relative overflow-hidden">
                            <template x-if="ogImage">
                                <img :src="ogImage" alt="Social Preview" class="w-full h-full object-cover">
                            </template>
                            <template x-if="!ogImage">
                                <div class="text-center text-slate-400 p-4">
                                    <i data-lucide="image" class="w-8 h-8 mx-auto mb-1 opacity-50"></i>
                                    <span class="text-[11px] block">No Social Image Selected</span>
                                </div>
                            </template>
                        </div>
                        <div class="p-3.5 space-y-1 bg-slate-50 border-t border-slate-100">
                            <span class="text-[10px] uppercase font-bold text-slate-400 tracking-wider">LIFEGUARDDATARECOVERY.COM</span>
                            <h5 class="text-xs font-bold text-slate-900 truncate" x-text="ogTitle || metaTitle || effectiveTitle || 'Shared Title'"></h5>
                            <p class="text-[11px] text-slate-500 line-clamp-2 leading-relaxed" x-text="ogDescription || metaDescription || effectiveDescription || 'Shared description snippet.'"></p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- ================= TAB 4: ADVANCED ROBOTS & INDEXING ================= -->
        <div x-show="activeTab === 'advanced'" class="space-y-5">
            <div>
                <label for="canonicalUrlInput" class="block text-xs font-bold text-slate-700 mb-1.5">Canonical URL</label>
                <input type="url" id="canonicalUrlInput" name="canonical_url" value="{{ $canonicalUrl }}"
                       placeholder="https://lifeguarddatarecovery.com/original-page"
                       class="w-full px-3.5 py-2.5 text-xs rounded-xl border border-slate-200 focus:outline-none focus:ring-2 focus:ring-purple-500/20 focus:border-purple-600">
                <p class="text-[11px] text-slate-400 mt-1">Leave empty to use this item's default permalink.</p>
            </div>

            <div class="pt-3 border-t border-slate-100">
                <label class="flex items-center gap-2.5 cursor-pointer">
                    <input type="checkbox" name="is_indexable" value="1" {{ $isIndexable ? 'checked' : '' }}
                           class="rounded border-slate-300 text-purple-600 focus:ring-purple-500 h-4 w-4">
                    <div>
                        <span class="text-xs font-bold text-slate-800 block">Allow search engines to show this in search results (Index)</span>
                        <span class="text-[11px] text-slate-400">Uncheck to apply <code class="bg-slate-100 px-1 py-0.5 rounded">noindex, nofollow</code> robot tags.</span>
                    </div>
                </label>
            </div>
        </div>

        <!-- ================= TAB 5: SCHEMA.ORG JSON-LD ================= -->
        <div x-show="activeTab === 'schema'" class="space-y-4">
            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3">
                <div>
                    <h4 class="text-xs font-bold uppercase tracking-wider text-slate-800 flex items-center gap-2">
                        <i data-lucide="code-2" class="w-4 h-4 text-purple-600"></i>
                        Custom Schema.org Structured Data (JSON-LD)
                    </h4>
                    <p class="text-xs text-slate-500 mt-0.5">
                        Define rich structured snippets (FAQPage, Product, Service, Article) to capture Google Rich Snippets & knowledge panels.
                    </p>
                </div>
                <div class="flex items-center gap-2 flex-shrink-0">
                    <span class="text-[11px] px-2.5 py-1 rounded-full font-semibold border transition"
                          :class="schemaStatusClass" x-text="schemaStatusText"></span>
                </div>
            </div>

            <!-- Snippet Quick Starters -->
            <div class="p-3 bg-slate-50 border border-slate-200 rounded-xl flex flex-wrap items-center gap-2 text-xs">
                <span class="font-bold text-slate-600 text-[11px] uppercase tracking-wider flex items-center gap-1 mr-1">
                    <i data-lucide="sparkles" class="w-3.5 h-3.5 text-purple-600"></i> Insert Template:
                </span>
                <button type="button" @click="insertSchemaTemplate('article')" 
                        class="px-2.5 py-1 bg-white border border-slate-200 hover:border-purple-300 hover:text-purple-700 rounded-lg text-slate-700 font-medium transition shadow-2xs">
                    + Article
                </button>
                <button type="button" @click="insertSchemaTemplate('product')" 
                        class="px-2.5 py-1 bg-white border border-slate-200 hover:border-purple-300 hover:text-purple-700 rounded-lg text-slate-700 font-medium transition shadow-2xs">
                    + Product / Fleet
                </button>
                <button type="button" @click="insertSchemaTemplate('service')" 
                        class="px-2.5 py-1 bg-white border border-slate-200 hover:border-purple-300 hover:text-purple-700 rounded-lg text-slate-700 font-medium transition shadow-2xs">
                    + Service
                </button>
                <button type="button" @click="insertSchemaTemplate('faq')" 
                        class="px-2.5 py-1 bg-white border border-slate-200 hover:border-purple-300 hover:text-purple-700 rounded-lg text-slate-700 font-medium transition shadow-2xs">
                    + FAQPage
                </button>
                <button type="button" @click="insertSchemaTemplate('localbusiness')" 
                        class="px-2.5 py-1 bg-white border border-slate-200 hover:border-purple-300 hover:text-purple-700 rounded-lg text-slate-700 font-medium transition shadow-2xs">
                    + LocalBusiness
                </button>
                <button type="button" @click="formatJson()" 
                        class="sm:ml-auto px-3 py-1 bg-purple-50 border border-purple-200 text-purple-700 hover:bg-purple-100 rounded-lg font-semibold transition flex items-center gap-1.5 shadow-2xs">
                    <i data-lucide="check" class="w-3 h-3"></i> Format & Validate JSON
                </button>
            </div>

            <!-- Code Editor Field -->
            <div class="relative">
                <textarea id="schemaMarkupInput" name="schema_markup" rows="12"
                          x-model="schemaContent"
                          @input="validateSchema()"
                          placeholder='{
  "@@context": "https://schema.org",
  "@type": "Article",
  "headline": "...",
  "description": "..."
}'
                          class="w-full font-mono text-xs px-4 py-3 bg-slate-900 text-emerald-400 placeholder:text-slate-600 rounded-xl border border-slate-800 focus:outline-none focus:ring-2 focus:ring-purple-500/30 focus:border-purple-500 leading-relaxed shadow-inner"></textarea>
            </div>

            <div class="flex flex-wrap items-center justify-between gap-2 text-[11px] text-slate-500">
                <span x-text="schemaValidationMessage"></span>
                <div class="flex items-center gap-3">
                    <span class="text-slate-400">Supports pure JSON or wrapped &lt;script type="application/ld+json"&gt;</span>
                    <a href="https://validator.schema.org/" target="_blank" rel="noopener" class="text-purple-600 hover:text-purple-700 font-semibold hover:underline flex items-center gap-1">
                        <span>Schema.org Validator</span>
                        <i data-lucide="external-link" class="w-3 h-3"></i>
                    </a>
                </div>
            </div>
        </div>

    </div>
</div>

<script>
document.addEventListener('alpine:init', () => {
    Alpine.data('yoastSeoAnalyzer', (config) => ({
        activeTab: 'general',
        previewMode: 'desktop',
        titleInputId: config.titleInputId,
        slugInputId: config.slugInputId,
        contentInputId: config.contentInputId,
        urlPrefix: config.urlPrefix,

        focusKeyword: config.initialFocusKeyword || '',
        metaTitle: config.initialTitle || '',
        metaDescription: config.initialDescription || '',
        ogTitle: '',
        ogDescription: '',
        ogImage: '',

        effectiveTitle: '',
        effectiveDescription: '',
        currentSlug: '',
        contentHtml: '',

        seoScore: config.initialSeoScore || 0,
        readabilityScore: config.initialReadabilityScore || 0,
        seoScoreRating: 'poor',
        readabilityRating: 'poor',

        schemaContent: config.initialSchemaMarkup || '',
        schemaStatusText: 'Ready',
        schemaStatusClass: 'bg-slate-100 text-slate-600 border-slate-200',
        schemaValidationMessage: '',

        checksList: [],

        initAnalyzer() {
            // Bind to title input
            const titleEl = document.getElementById(this.titleInputId);
            if (titleEl) {
                this.effectiveTitle = titleEl.value;
                titleEl.addEventListener('input', () => {
                    this.effectiveTitle = titleEl.value;
                    this.reanalyze();
                });
            }

            // Bind to slug input
            const slugEl = document.getElementById(this.slugInputId);
            if (slugEl) {
                this.currentSlug = slugEl.value;
                slugEl.addEventListener('input', () => {
                    this.currentSlug = slugEl.value;
                    this.reanalyze();
                });
            }

            // Bind to Quill or textarea
            const contentEl = document.getElementById(this.contentInputId);
            if (contentEl) {
                this.contentHtml = contentEl.value;
                contentEl.addEventListener('input', () => {
                    this.contentHtml = contentEl.value;
                    this.reanalyze();
                });
            }

            // Listen for Quill text-change if available
            if (window.quill) {
                window.quill.on('text-change', () => {
                    this.contentHtml = window.quill.root.innerHTML;
                    this.reanalyze();
                });
            }

            this.validateSchema();
            this.reanalyze();
        },

        get titleLength() {
            return (this.metaTitle || this.effectiveTitle || '').length;
        },

        get descLength() {
            return (this.metaDescription || '').length;
        },

        get titleLengthClass() {
            const l = this.titleLength;
            if (l >= 40 && l <= 60) return 'text-emerald-600 font-bold';
            if (l > 60) return 'text-rose-600 font-bold';
            return 'text-amber-600 font-medium';
        },

        get titleMeterColor() {
            const l = this.titleLength;
            if (l >= 40 && l <= 60) return 'bg-emerald-500';
            if (l > 60) return 'bg-rose-500';
            return 'bg-amber-400';
        },

        get descLengthClass() {
            const l = this.descLength;
            if (l >= 120 && l <= 160) return 'text-emerald-600 font-bold';
            if (l > 160) return 'text-rose-600 font-bold';
            return 'text-amber-600 font-medium';
        },

        get descMeterColor() {
            const l = this.descLength;
            if (l >= 120 && l <= 160) return 'bg-emerald-500';
            if (l > 160) return 'bg-rose-500';
            return 'bg-amber-400';
        },

        get passedChecksCount() {
            return this.checksList.filter(c => c.status === 'good').length;
        },

        get totalChecksCount() {
            return this.checksList.length;
        },

        reanalyze() {
            const keyword = (this.focusKeyword || '').trim().toLowerCase();
            const fullTitle = (this.metaTitle || this.effectiveTitle || '').trim();
            const fullDesc = (this.metaDescription || '').trim();
            const slug = (this.currentSlug || '').trim();
            
            // Extract raw text from HTML content
            const tempDiv = document.createElement('div');
            tempDiv.innerHTML = this.contentHtml || '';
            const plainContent = tempDiv.textContent || tempDiv.innerText || '';
            const words = plainContent.trim().split(/\s+/).filter(w => w.length > 0);
            const wordCount = words.length;

            const checks = [];
            let earnedSeoPoints = 0;
            let totalSeoPoints = 0;

            let earnedReadPoints = 0;
            let totalReadPoints = 0;

            // 1. Focus Keyword set
            totalSeoPoints += 10;
            if (keyword.length > 0) {
                earnedSeoPoints += 10;
                checks.push({
                    title: 'Focus Keyphrase Defined',
                    status: 'good',
                    message: `Focus keyword "${this.focusKeyword}" is specified.`
                });
            } else {
                checks.push({
                    title: 'Focus Keyphrase Missing',
                    status: 'poor',
                    message: 'Add a focus keyphrase to enable full Yoast SEO ranking audit.'
                });
            }

            // 2. Keyphrase in SEO Title
            totalSeoPoints += 15;
            if (keyword && fullTitle.toLowerCase().includes(keyword)) {
                earnedSeoPoints += 15;
                checks.push({
                    title: 'Keyphrase in SEO Title',
                    status: 'good',
                    message: `The focus keyphrase appears in the SEO title.`
                });
            } else if (keyword) {
                checks.push({
                    title: 'Keyphrase Missing from SEO Title',
                    status: 'poor',
                    message: `Include "${this.focusKeyword}" in your SEO title.`
                });
            }

            // 3. SEO Title Length
            totalSeoPoints += 15;
            if (fullTitle.length >= 40 && fullTitle.length <= 60) {
                earnedSeoPoints += 15;
                checks.push({
                    title: 'SEO Title Length',
                    status: 'good',
                    message: `Title length (${fullTitle.length} characters) is ideal for Google results.`
                });
            } else if (fullTitle.length > 60) {
                earnedSeoPoints += 5;
                checks.push({
                    title: 'SEO Title Too Long',
                    status: 'poor',
                    message: `Title is ${fullTitle.length} characters. Google will likely truncate it beyond 60.`
                });
            } else {
                earnedSeoPoints += 5;
                checks.push({
                    title: 'SEO Title Too Short',
                    status: 'ok',
                    message: `Title is only ${fullTitle.length} characters. Consider adding more descriptive details.`
                });
            }

            // 4. Keyphrase in Meta Description
            totalSeoPoints += 15;
            if (keyword && fullDesc.toLowerCase().includes(keyword)) {
                earnedSeoPoints += 15;
                checks.push({
                    title: 'Keyphrase in Meta Description',
                    status: 'good',
                    message: 'The meta description contains the focus keyphrase.'
                });
            } else if (keyword && fullDesc.length > 0) {
                checks.push({
                    title: 'Keyphrase Missing in Meta Description',
                    status: 'ok',
                    message: `The meta description does not contain the keyphrase "${this.focusKeyword}".`
                });
            } else {
                checks.push({
                    title: 'Meta Description Missing',
                    status: 'poor',
                    message: 'No meta description set. Search engines will generate an arbitrary snippet.'
                });
            }

            // 5. Meta Description Length
            totalSeoPoints += 15;
            if (fullDesc.length >= 120 && fullDesc.length <= 160) {
                earnedSeoPoints += 15;
                checks.push({
                    title: 'Meta Description Length',
                    status: 'good',
                    message: `Description length (${fullDesc.length} characters) is optimal.`
                });
            } else if (fullDesc.length > 160) {
                earnedSeoPoints += 5;
                checks.push({
                    title: 'Meta Description Too Long',
                    status: 'ok',
                    message: `Description is ${fullDesc.length} characters. May be truncated on search results.`
                });
            } else {
                checks.push({
                    title: 'Meta Description Length',
                    status: 'poor',
                    message: `Description is only ${fullDesc.length} characters. Target between 120 and 160.`
                });
            }

            // 6. Keyphrase in Slug / URL
            totalSeoPoints += 15;
            const normalizedKeywordSlug = keyword.replace(/\s+/g, '-');
            if (keyword && slug.toLowerCase().includes(normalizedKeywordSlug)) {
                earnedSeoPoints += 15;
                checks.push({
                    title: 'Keyphrase in URL Slug',
                    status: 'good',
                    message: 'The URL slug contains your focus keyphrase.'
                });
            } else if (keyword) {
                earnedSeoPoints += 5;
                checks.push({
                    title: 'Keyphrase in Slug',
                    status: 'ok',
                    message: 'Consider including the keyphrase in the permalink slug.'
                });
            }

            // 7. Keyword Density & Content Length
            totalSeoPoints += 15;
            if (wordCount >= 250) {
                earnedSeoPoints += 15;
                checks.push({
                    title: 'Text Word Count',
                    status: 'good',
                    message: `Content length is ${wordCount} words (above 250 word minimum threshold).`
                });
            } else {
                earnedSeoPoints += 5;
                checks.push({
                    title: 'Text Word Count',
                    status: 'ok',
                    message: `Content has ${wordCount} words. 250+ words recommended for high search ranking.`
                });
            }

            // Readability Checks
            totalReadPoints += 50;
            const hasHeadings = tempDiv.querySelectorAll('h1, h2, h3, h4').length > 0;
            if (hasHeadings) {
                earnedReadPoints += 50;
                checks.push({
                    title: 'Subheadings Distribution',
                    status: 'good',
                    message: 'Content contains structured subheadings (H2/H3) for easy reading.'
                });
            } else {
                earnedReadPoints += 20;
                checks.push({
                    title: 'Subheadings Missing',
                    status: 'ok',
                    message: 'Add H2 or H3 section titles to make the page easier to scan.'
                });
            }

            totalReadPoints += 50;
            if (wordCount > 50) {
                earnedReadPoints += 50;
                checks.push({
                    title: 'Paragraph Structure',
                    status: 'good',
                    message: 'Content is well-proportioned for reading.'
                });
            } else {
                earnedReadPoints += 20;
                checks.push({
                    title: 'Thin Content',
                    status: 'poor',
                    message: 'Add more informative text to improve reader engagement.'
                });
            }

            // Calculate percentage scores
            this.seoScore = Math.min(100, Math.round((earnedSeoPoints / totalSeoPoints) * 100));
            this.readabilityScore = Math.min(100, Math.round((earnedReadPoints / totalReadPoints) * 100));

            // Ratings
            this.seoScoreRating = this.seoScore >= 80 ? 'good' : (this.seoScore >= 50 ? 'ok' : 'poor');
            this.readabilityRating = this.readabilityScore >= 80 ? 'good' : (this.readabilityScore >= 50 ? 'ok' : 'poor');

            this.checksList = checks;
        },

        validateSchema() {
            if (!this.schemaContent || !this.schemaContent.trim()) {
                this.schemaStatusText = 'Empty (Defaults apply)';
                this.schemaStatusClass = 'bg-slate-100 text-slate-600 border-slate-200';
                this.schemaValidationMessage = 'No custom schema provided. Built-in defaults will apply.';
                return true;
            }
            try {
                let raw = this.schemaContent.trim();
                if (raw.startsWith('<script')) {
                    raw = raw.replace(/^<script[^>]*>/i, '').replace(/<\/script>$/i, '').trim();
                }
                JSON.parse(raw);
                this.schemaStatusText = 'Valid JSON-LD';
                this.schemaStatusClass = 'bg-emerald-50 text-emerald-700 border-emerald-200';
                this.schemaValidationMessage = '✓ Schema is valid JSON.';
                return true;
            } catch (e) {
                this.schemaStatusText = 'Invalid JSON';
                this.schemaStatusClass = 'bg-rose-50 text-rose-700 border-rose-200';
                this.schemaValidationMessage = '⚠ JSON Syntax Error: ' + e.message;
                return false;
            }
        },

        formatJson() {
            if (!this.schemaContent || !this.schemaContent.trim()) return;
            try {
                let raw = this.schemaContent.trim();
                let hadScript = false;
                if (raw.startsWith('<script')) {
                    hadScript = true;
                    raw = raw.replace(/^<script[^>]*>/i, '').replace(/<\/script>$/i, '').trim();
                }
                const parsed = JSON.parse(raw);
                const formatted = JSON.stringify(parsed, null, 2);
                this.schemaContent = hadScript 
                    ? '<script type="application/ld+json">\n' + formatted + '\n</' + 'script>' 
                    : formatted;
                this.validateSchema();
            } catch (e) {
                alert('Could not format: ' + e.message);
            }
        },

        insertSchemaTemplate(type) {
            const currentTitle = this.effectiveTitle || this.metaTitle || 'LifeGuard Data Recovery Dubai';
            const currentDesc = this.effectiveDescription || this.metaDescription || 'Professional ISO-5 Class 100 cleanroom data recovery services in Dubai & across UAE.';
            let tpl = {};

            if (type === 'article') {
                tpl = {
                    ["@" + "context"]: "https://schema.org",
                    "@type": "BlogPosting",
                    "headline": currentTitle,
                    "description": currentDesc,
                    "author": {
                        "@type": "Organization",
                        "name": "LifeGuard Data Recovery Engineering Team",
                        "url": "https://lifeguarddatarecovery.com"
                    },
                    "publisher": {
                        "@type": "Organization",
                        "name": "LifeGuard Data Recovery",
                        "logo": {
                            "@type": "ImageObject",
                            "url": "https://lifeguarddatarecovery.com/assets/images/logo.png"
                        }
                    },
                    "inLanguage": "en-AE"
                };
            } else if (type === 'product') {
                tpl = {
                    ["@" + "context"]: "https://schema.org",
                    "@type": "Product",
                    "name": currentTitle,
                    "description": currentDesc,
                    "brand": {
                        "@type": "Brand",
                        "name": "LifeGuard Cleanroom Diagnostics"
                    },
                    "offers": {
                        "@type": "AggregateOffer",
                        "priceCurrency": "AED",
                        "lowPrice": "500",
                        "highPrice": "4500",
                        "offerCount": "10",
                        "availability": "https://schema.org/InStock",
                        "areaServed": {
                            "@type": "AdministrativeArea",
                            "name": "United Arab Emirates"
                        }
                    }
                };
            } else if (type === 'service') {
                tpl = {
                    ["@" + "context"]: "https://schema.org",
                    "@type": "Service",
                    "name": currentTitle,
                    "serviceType": "Cleanroom Data Recovery & Forensic Investigation",
                    "description": currentDesc,
                    "provider": {
                        "@type": "LocalBusiness",
                        "name": "LifeGuard Data Recovery",
                        "telephone": "+97143933082",
                        "url": "https://lifeguarddatarecovery.com",
                        "address": {
                            "@type": "PostalAddress",
                            "streetAddress": "Bayat Center S25, Computer Street, Bur Dubai",
                            "addressLocality": "Dubai",
                            "addressCountry": "AE"
                        }
                    },
                    "areaServed": "United Arab Emirates"
                };
            } else if (type === 'faq') {
                tpl = {
                    ["@" + "context"]: "https://schema.org",
                    "@type": "FAQPage",
                    "mainEntity": [
                        {
                            "@type": "Question",
                            "name": "What is the No Data, No Charge policy at LifeGuard Data Recovery?",
                            "acceptedAnswer": {
                                "@type": "Answer",
                                "text": "If our cleanroom engineers are unable to recover your critical files, you owe nothing. Diagnostic evaluation is 100% free with no obligation."
                            }
                        },
                        {
                            "@type": "Question",
                            "name": "How fast can you evaluate my failed hard drive or SSD in Dubai?",
                            "acceptedAnswer": {
                                "@type": "Answer",
                                "text": "We provide rapid diagnostic triage within 2 to 4 hours in our ISO-5 Class 100 cleanroom lab in Bur Dubai or Al Barsha."
                            }
                        }
                    ]
                };
            } else if (type === 'localbusiness') {
                tpl = {
                    ["@" + "context"]: "https://schema.org",
                    "@type": "LocalBusiness",
                    "name": "LifeGuard Data Recovery",
                    "image": "https://lifeguarddatarecovery.com/assets/images/cleanroom-lab.jpg",
                    "telephone": "+97143933082",
                    "email": "info@lifeguarddatarecovery.com",
                    "url": "https://lifeguarddatarecovery.com",
                    "address": {
                        "@type": "PostalAddress",
                        "streetAddress": "Bayat Center S25, Computer Street, Bur Dubai",
                        "addressLocality": "Dubai",
                        "addressRegion": "Dubai",
                        "postalCode": "00000",
                        "addressCountry": "AE"
                    },
                    "geo": {
                        "@type": "GeoCoordinates",
                        "latitude": "25.2532",
                        "longitude": "55.2974"
                    },
                    "openingHoursSpecification": {
                        "@type": "OpeningHoursSpecification",
                        "dayOfWeek": ["Monday", "Tuesday", "Wednesday", "Thursday", "Friday", "Saturday"],
                        "opens": "09:00",
                        "closes": "21:00"
                    }
                };
            }

            this.schemaContent = JSON.stringify(tpl, null, 2);
            this.validateSchema();
        }
    }));
});
</script>
