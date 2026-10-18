@extends('layouts.admin')

@section('title', 'Dashboard')

@section('content')
<div class="space-y-8">
    <!-- Welcome Banner (WordPress-style) -->
    <div class="relative overflow-hidden bg-gradient-to-r from-slate-900 via-slate-800 to-brand-900 rounded-3xl p-6 sm:p-8 text-white shadow-xl shadow-slate-900/10">
        <div class="relative z-10 max-w-2xl">
            <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-semibold bg-sky-500/20 text-sky-300 border border-sky-400/30 mb-3">
                <i data-lucide="sparkles" class="w-3.5 h-3.5"></i> LifeGuard Data Recovery &bull; Cleanroom Lab CMS
            </span>
            <h1 class="text-2xl sm:text-3xl font-extrabold tracking-tight">Welcome, {{ auth()->user()->name }}!</h1>
            <p class="mt-2 text-sm text-slate-300 leading-relaxed">
                Directly manage your cleanroom data recovery services, diagnostic lab articles, client device intake inquiries, regional lab landing pages, and search engine redirections.
            </p>
            <div class="mt-6 flex flex-wrap items-center gap-3">
                <a href="{{ route('admin.services.index') }}" class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl bg-brand-600 hover:bg-brand-500 text-white text-xs font-bold shadow-lg shadow-brand-600/30 transition">
                    <i data-lucide="layers" class="w-4 h-4"></i> Manage Services
                </a>
                <a href="{{ route('admin.inquiries.index') }}" class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl bg-emerald-600 hover:bg-emerald-500 text-white text-xs font-bold shadow-lg shadow-emerald-600/30 transition">
                    <i data-lucide="inbox" class="w-4 h-4"></i> View Leads & Inquiries
                </a>
                <a href="{{ route('admin.posts.create') }}" class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl bg-white/10 hover:bg-white/20 text-white text-xs font-semibold backdrop-blur-sm transition">
                    <i data-lucide="plus-circle" class="w-4 h-4"></i> New Blog Article
                </a>
                <a href="{{ route('admin.pages.index') }}" class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl bg-white/10 hover:bg-white/20 text-white text-xs font-semibold backdrop-blur-sm transition">
                    <i data-lucide="file-text" class="w-4 h-4"></i> Website Pages
                </a>
                <a href="{{ route('admin.menus.index') }}" class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl bg-white/10 hover:bg-white/20 text-white text-xs font-semibold backdrop-blur-sm transition">
                    <i data-lucide="menu" class="w-4 h-4"></i> Header Menu
                </a>
                <a href="{{ route('admin.redirects.index') }}" class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl bg-teal-600/30 hover:bg-teal-600/50 text-white text-xs font-semibold backdrop-blur-sm border border-teal-400/30 transition">
                    <i data-lucide="arrow-left-right" class="w-4 h-4"></i> 301 Redirections
                </a>
                <form action="{{ route('admin.cache.clear') }}" method="POST" class="inline">
                    @csrf
                    <input type="hidden" name="type" value="all">
                    <button type="submit" class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl bg-amber-500/20 hover:bg-amber-500/30 text-amber-300 text-xs font-bold backdrop-blur-sm border border-amber-400/30 transition shadow-sm" title="Clear website application and view caches">
                        <i data-lucide="refresh-cw" class="w-3.5 h-3.5 text-amber-400"></i> Clear Cache
                    </button>
                </form>
                <a href="{{ route('home') }}" target="_blank" class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl bg-white/5 hover:bg-white/10 text-slate-300 hover:text-white text-xs font-medium transition">
                    <i data-lucide="external-link" class="w-3.5 h-3.5"></i> Visit Website
                </a>
            </div>
        </div>
        <div class="absolute -right-10 -bottom-10 w-72 h-72 bg-brand-500/10 rounded-full blur-3xl pointer-events-none"></div>
    </div>

    <!-- Stats Cards Grid -->
    <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-6 gap-4">
        <!-- Leads & Inquiries -->
        <a href="{{ route('admin.inquiries.index') }}" class="bg-white p-5 rounded-2xl border border-slate-200/80 shadow-sm flex flex-col justify-between hover:shadow-md transition group sm:col-span-1">
            <div class="flex items-center justify-between">
                <span class="text-[10px] font-bold text-slate-400 uppercase tracking-wider">Leads</span>
                <div class="w-8 h-8 rounded-lg bg-rose-50 text-rose-600 flex items-center justify-center">
                    <i data-lucide="inbox" class="w-4 h-4"></i>
                </div>
            </div>
            <div class="mt-3">
                <div class="flex items-baseline gap-1.5">
                    <span class="text-xl font-black text-slate-900">{{ $stats['total_inquiries'] }}</span>
                    @if($stats['unread_inquiries'] > 0)
                        <span class="text-[10px] text-rose-600 font-extrabold">{{ $stats['unread_inquiries'] }} new</span>
                    @endif
                </div>
                <p class="text-[10px] text-slate-400 mt-0.5">Emergency & Intake Leads</p>
            </div>
        </a>

        <!-- Services -->
        <a href="{{ route('admin.services.index') }}" class="bg-white p-5 rounded-2xl border border-slate-200/80 shadow-sm flex flex-col justify-between hover:shadow-md transition group">
            <div class="flex items-center justify-between">
                <span class="text-[10px] font-bold text-slate-400 uppercase tracking-wider">Services</span>
                <div class="w-8 h-8 rounded-lg bg-sky-50 text-brand-600 flex items-center justify-center">
                    <i data-lucide="layers" class="w-4 h-4"></i>
                </div>
            </div>
            <div class="mt-3">
                <span class="text-xl font-black text-slate-900">{{ $stats['total_services'] }}</span>
                <p class="text-[10px] text-emerald-600 font-semibold mt-0.5">{{ $stats['published_services'] }} active recovery services</p>
            </div>
        </a>

        <!-- Blog Posts -->
        <a href="{{ route('admin.posts.index') }}" class="bg-white p-5 rounded-2xl border border-slate-200/80 shadow-sm flex flex-col justify-between hover:shadow-md transition group">
            <div class="flex items-center justify-between">
                <span class="text-[10px] font-bold text-slate-400 uppercase tracking-wider">Articles</span>
                <div class="w-8 h-8 rounded-lg bg-indigo-50 text-indigo-600 flex items-center justify-center">
                    <i data-lucide="pin" class="w-4 h-4"></i>
                </div>
            </div>
            <div class="mt-3">
                <span class="text-xl font-black text-slate-900">{{ $stats['total_posts'] }}</span>
                <p class="text-[10px] text-slate-500 font-medium mt-0.5">{{ $stats['published_posts'] }} published</p>
            </div>
        </a>

        <!-- Pages -->
        <a href="{{ route('admin.pages.index') }}" class="bg-white p-5 rounded-2xl border border-slate-200/80 shadow-sm flex flex-col justify-between hover:shadow-md transition group">
            <div class="flex items-center justify-between">
                <span class="text-[10px] font-bold text-slate-400 uppercase tracking-wider">Pages</span>
                <div class="w-8 h-8 rounded-lg bg-emerald-50 text-emerald-600 flex items-center justify-center">
                    <i data-lucide="file-text" class="w-4 h-4"></i>
                </div>
            </div>
            <div class="mt-3">
                <span class="text-xl font-black text-slate-900">{{ $stats['total_pages'] }}</span>
                <p class="text-[10px] text-slate-400 mt-0.5">CMS Pages</p>
            </div>
        </a>

        <!-- Navigation Menu -->
        <a href="{{ route('admin.menus.index') }}" class="bg-white p-5 rounded-2xl border border-slate-200/80 shadow-sm flex flex-col justify-between hover:shadow-md transition group">
            <div class="flex items-center justify-between">
                <span class="text-[10px] font-bold text-slate-400 uppercase tracking-wider">Navigation</span>
                <div class="w-8 h-8 rounded-lg bg-amber-50 text-amber-600 flex items-center justify-center">
                    <i data-lucide="menu" class="w-4 h-4"></i>
                </div>
            </div>
            <div class="mt-3">
                <span class="text-xl font-black text-slate-900">{{ $stats['total_menu_items'] }}</span>
                <p class="text-[10px] text-emerald-600 font-semibold mt-0.5">{{ $stats['active_menu_items'] }} active links</p>
            </div>
        </a>

        <!-- 301 Redirections -->
        <a href="{{ route('admin.redirects.index') }}" class="bg-white p-5 rounded-2xl border border-slate-200/80 shadow-sm flex flex-col justify-between hover:shadow-md transition group">
            <div class="flex items-center justify-between">
                <span class="text-[10px] font-bold text-slate-400 uppercase tracking-wider">Redirects</span>
                <div class="w-8 h-8 rounded-lg bg-teal-50 text-teal-600 flex items-center justify-center">
                    <i data-lucide="arrow-left-right" class="w-4 h-4"></i>
                </div>
            </div>
            <div class="mt-3">
                <span class="text-xl font-black text-slate-900">{{ $stats['total_redirects'] }}</span>
                <p class="text-[10px] text-teal-600 font-semibold mt-0.5">{{ $stats['redirect_hits'] }} hits rescued</p>
            </div>
        </a>
    </div>

    <!-- YOAST SEO, FOCUS KEYWORDS & SCHEMA STUDIO COMMAND CENTER ON DASHBOARD -->
    @php
        $studioItems = [];
        foreach ($services as $svc) {
            $studioItems['service-'.$svc->id] = [
                'type' => 'service',
                'id' => $svc->id,
                'title' => $svc->title,
                'slug' => $svc->slug,
                'url' => route('service.show', $svc->slug),
                'editUrl' => route('admin.services.edit', $svc),
                'endpoint' => route('admin.services.quick-seo', $svc),
                'focus_keyword' => $svc->focus_keyword ?? '',
                'meta_title' => $svc->meta_title ?? '',
                'meta_description' => $svc->meta_description ?? '',
                'schema_markup' => $svc->schema_markup ?? '',
                'seo_score' => (int) ($svc->seo_score ?? 0),
            ];
        }
        foreach ($recentPosts as $pst) {
            $studioItems['post-'.$pst->id] = [
                'type' => 'post',
                'id' => $pst->id,
                'title' => $pst->title,
                'slug' => $pst->slug,
                'url' => route('post.show', $pst->slug),
                'editUrl' => route('admin.posts.edit', $pst),
                'endpoint' => route('admin.posts.quick-seo', $pst),
                'focus_keyword' => $pst->focus_keyword ?? '',
                'meta_title' => $pst->meta_title ?? '',
                'meta_description' => $pst->meta_description ?? '',
                'schema_markup' => $pst->schema_markup ?? '',
                'seo_score' => (int) ($pst->seo_score ?? 0),
            ];
        }
        foreach ($pages as $pg) {
            $studioItems['page-'.$pg->id] = [
                'type' => 'page',
                'id' => $pg->id,
                'title' => $pg->title,
                'slug' => $pg->slug,
                'url' => route('page.show', $pg->slug),
                'editUrl' => route('admin.pages.edit', $pg),
                'endpoint' => route('admin.pages.quick-seo', $pg),
                'focus_keyword' => $pg->focus_keyword ?? '',
                'meta_title' => $pg->meta_title ?? '',
                'meta_description' => $pg->meta_description ?? '',
                'schema_markup' => $pg->schema_markup ?? '',
                'seo_score' => (int) ($pg->seo_score ?? 0),
            ];
        }
        $defaultKey = 'service-' . ($services->first()->id ?? 0);
    @endphp

    <div id="seoStudioContainer" 
         class="space-y-6"
         x-data="dashboardSeoStudio({
             items: {{ json_encode($studioItems) }},
             initialKey: '{{ $defaultKey }}'
         })"
         x-init="init()">

        <!-- ========================================================================= -->
        <!-- 1. LIVE DIRECT SEO, META DESCRIPTION & SCHEMA CODE STUDIO CARD           -->
        <!-- ========================================================================= -->
        <div id="seoStudioCard" class="bg-white rounded-3xl border border-purple-200/90 shadow-xl shadow-purple-900/5 overflow-hidden">
            <!-- Studio Top Header -->
            <div class="px-6 py-5 bg-gradient-to-r from-purple-900 via-indigo-900 to-slate-900 text-white flex flex-col md:flex-row md:items-center md:justify-between gap-4">
                <div class="flex items-center gap-3.5">
                    <div class="w-11 h-11 rounded-2xl bg-white/10 backdrop-blur-md text-amber-300 flex items-center justify-center font-bold text-lg shadow-inner border border-white/20 shrink-0">
                        <i data-lucide="sparkles" class="w-6 h-6"></i>
                    </div>
                    <div>
                        <div class="flex items-center gap-2">
                            <h2 class="text-base sm:text-lg font-black tracking-tight">Interactive Focus Keyword, Meta & Schema Code Studio</h2>
                            <span class="text-[10px] font-extrabold uppercase px-2.5 py-0.5 rounded-full bg-emerald-500 text-white tracking-wider shadow-xs">Direct Live Writer</span>
                        </div>
                        <p class="text-xs text-purple-200/80 mt-0.5">Write and save Focus Keywords, Meta Titles, Meta Descriptions, and Schema.org JSON-LD code directly from the dashboard.</p>
                    </div>
                </div>

                <!-- Live SEO Signal Score Badge -->
                <div class="flex items-center gap-3 shrink-0">
                    <div class="flex items-center gap-2 px-3.5 py-1.5 rounded-xl border text-xs font-bold backdrop-blur-md"
                         :class="{
                             'bg-emerald-500/20 text-emerald-200 border-emerald-400/30': seoScoreRating === 'good',
                             'bg-amber-500/20 text-amber-200 border-amber-400/30': seoScoreRating === 'ok',
                             'bg-rose-500/20 text-rose-200 border-rose-400/30': seoScoreRating === 'poor'
                         }">
                        <span class="w-2.5 h-2.5 rounded-full"
                              :class="{
                                  'bg-emerald-400 animate-pulse': seoScoreRating === 'good',
                                  'bg-amber-400': seoScoreRating === 'ok',
                                  'bg-rose-400': seoScoreRating === 'poor'
                              }"></span>
                        <span>Yoast SEO: <strong x-text="computedSeoScore + '/100'"></strong></span>
                        <span class="uppercase text-[10px] opacity-80" x-text="'(' + seoScoreRating + ')'"></span>
                    </div>

                    <a :href="studio.url" target="_blank" class="p-2 rounded-xl bg-white/10 hover:bg-white/20 text-white transition flex items-center gap-1.5 text-xs font-semibold" title="View Frontend Page">
                        <i data-lucide="external-link" class="w-4 h-4"></i>
                        <span class="hidden sm:inline">Visit</span>
                    </a>
                </div>
            </div>

            <!-- Item Selector Toolbar -->
            <div class="px-6 py-4 bg-purple-50/60 border-b border-purple-100 flex flex-col md:flex-row md:items-center justify-between gap-3">
                <div class="flex items-center gap-2 flex-wrap text-xs">
                    <span class="font-bold text-slate-700 text-xs flex items-center gap-1.5 mr-1">
                        <i data-lucide="sliders" class="w-4 h-4 text-purple-600"></i> Select Page to Edit:
                    </span>
                    <button type="button" @click="currentSeoTab = 'services'; loadFirstOfType('service')"
                            :class="currentSeoTab === 'services' ? 'bg-purple-700 text-white shadow-sm' : 'bg-white text-slate-700 hover:bg-slate-100'"
                            class="px-3 py-1.5 rounded-lg font-bold border border-purple-200 transition">
                        ⚡ Services ({{ $services->count() }})
                    </button>
                    <button type="button" @click="currentSeoTab = 'posts'; loadFirstOfType('post')"
                            :class="currentSeoTab === 'posts' ? 'bg-purple-700 text-white shadow-sm' : 'bg-white text-slate-700 hover:bg-slate-100'"
                            class="px-3 py-1.5 rounded-lg font-bold border border-purple-200 transition">
                        📝 Articles ({{ $recentPosts->count() }})
                    </button>
                    <button type="button" @click="currentSeoTab = 'pages'; loadFirstOfType('page')"
                            :class="currentSeoTab === 'pages' ? 'bg-purple-700 text-white shadow-sm' : 'bg-white text-slate-700 hover:bg-slate-100'"
                            class="px-3 py-1.5 rounded-lg font-bold border border-purple-200 transition">
                        📄 Pages ({{ $pages->count() }})
                    </button>
                </div>

                <!-- Dropdown Item Selector -->
                <div class="flex-1 max-w-md">
                    <select x-model="selectedKey" @change="onSelectKeyChange($event.target.value)"
                            class="w-full text-xs font-bold px-3 py-2 rounded-xl border border-purple-200 bg-white text-slate-800 focus:outline-none focus:ring-2 focus:ring-purple-500/20 focus:border-purple-600 transition cursor-pointer shadow-xs">
                        <optgroup label="⚡ Commercial Services">
                            @foreach($services as $svc)
                                <option value="service-{{ $svc->id }}">Service: {{ $svc->title }} [Keyword: {{ $svc->focus_keyword ?: 'None' }}]</option>
                            @endforeach
                        </optgroup>
                        <optgroup label="📝 Blog Posts & Articles">
                            @foreach($recentPosts as $post)
                                <option value="post-{{ $post->id }}">Post: {{ $post->title }} [Keyword: {{ $post->focus_keyword ?: 'None' }}]</option>
                            @endforeach
                        </optgroup>
                        <optgroup label="📄 Website Pages">
                            @foreach($pages as $pg)
                                <option value="page-{{ $pg->id }}">Page: {{ $pg->title }} [Keyword: {{ $pg->focus_keyword ?: 'None' }}]</option>
                            @endforeach
                        </optgroup>
                    </select>
                </div>
            </div>

            <!-- Studio Main 2-Column Body -->
            <div class="p-6 grid grid-cols-1 lg:grid-cols-12 gap-8">
                <!-- ================= LEFT COLUMN: DIRECT INPUTS (7 COLS) ================= -->
                <div class="lg:col-span-7 space-y-5">
                    
                    <!-- Alert Messages -->
                    <div x-show="saveSuccessMsg" x-cloak
                         class="p-4 bg-emerald-50 border border-emerald-300 text-emerald-900 rounded-2xl flex items-center justify-between text-xs font-bold shadow-xs">
                        <div class="flex items-center gap-2.5">
                            <i data-lucide="check-circle-2" class="w-5 h-5 text-emerald-600 shrink-0"></i>
                            <span x-text="saveSuccessMsg"></span>
                        </div>
                        <button type="button" @click="saveSuccessMsg = ''" class="text-emerald-700 hover:text-emerald-900"><i data-lucide="x" class="w-4 h-4"></i></button>
                    </div>

                    <div x-show="saveErrorMsg" x-cloak
                         class="p-4 bg-rose-50 border border-rose-300 text-rose-900 rounded-2xl flex items-center justify-between text-xs font-bold shadow-xs">
                        <div class="flex items-center gap-2.5">
                            <i data-lucide="alert-circle" class="w-5 h-5 text-rose-600 shrink-0"></i>
                            <span x-text="saveErrorMsg"></span>
                        </div>
                        <button type="button" @click="saveErrorMsg = ''" class="text-rose-700 hover:text-rose-900"><i data-lucide="x" class="w-4 h-4"></i></button>
                    </div>

                    <!-- 1. Focus Keyphrase Input (Big & Obvious) -->
                    <div class="p-4 rounded-2xl border-2 border-purple-200 bg-purple-50/40 space-y-2">
                        <div class="flex items-center justify-between">
                            <label for="studioFocusKeywordInput" class="text-xs font-black uppercase tracking-wider text-purple-950 flex items-center gap-2">
                                <i data-lucide="key" class="w-4 h-4 text-purple-600"></i>
                                <span>Focus Keyphrase (Search Target)</span>
                            </label>
                            <span class="text-[10px] font-bold px-2 py-0.5 rounded-full"
                                  :class="studio.focus_keyword && studio.focus_keyword.trim().length > 0 ? 'bg-emerald-100 text-emerald-800' : 'bg-rose-100 text-rose-700'"
                                  x-text="studio.focus_keyword && studio.focus_keyword.trim().length > 0 ? '✓ Keyphrase active' : '⚠ Missing keyword'"></span>
                        </div>
                        <input type="text" id="studioFocusKeywordInput" x-model="studio.focus_keyword"
                               placeholder="e.g. hard drive data recovery dubai, raid recovery uae, ssd data recovery"
                               class="w-full px-4 py-2.5 text-sm font-bold rounded-xl border border-purple-200 bg-white text-slate-900 placeholder:text-slate-400 focus:outline-none focus:ring-2 focus:ring-purple-500/20 focus:border-purple-600 transition shadow-inner">
                        <p class="text-[11px] text-purple-900/70">The exact query your prospective clients search on Google when looking for cleanroom data recovery services.</p>
                    </div>

                    <!-- 2. SEO Meta Title Input with Length Meter -->
                    <div class="p-4 rounded-2xl border border-slate-200 bg-white space-y-2">
                        <div class="flex items-center justify-between">
                            <label class="text-xs font-bold text-slate-800 uppercase tracking-wider flex items-center gap-1.5">
                                <i data-lucide="type" class="w-3.5 h-3.5 text-slate-500"></i>
                                <span>SEO Meta Title</span>
                            </label>
                            <span class="text-[11px] font-mono font-bold"
                                  :class="titleLength >= 40 && titleLength <= 60 ? 'text-emerald-600' : (titleLength > 60 ? 'text-rose-600' : 'text-amber-600')"
                                  x-text="titleLength + ' / 60 characters'"></span>
                        </div>
                        <input type="text" x-model="studio.meta_title"
                               placeholder="e.g. Certified Hard Drive Data Recovery Dubai | ISO-5 Cleanroom Lab"
                               class="w-full px-3.5 py-2.5 text-xs font-semibold rounded-xl border border-slate-200 focus:outline-none focus:ring-2 focus:ring-purple-500/20 focus:border-purple-600 transition">
                        <!-- Title Meter Bar -->
                        <div class="w-full h-1.5 bg-slate-100 rounded-full overflow-hidden">
                            <div class="h-full transition-all duration-300"
                                 :class="titleLength >= 40 && titleLength <= 60 ? 'bg-emerald-500' : (titleLength > 60 ? 'bg-rose-500' : 'bg-amber-400')"
                                 :style="'width: ' + Math.min(100, (titleLength / 60) * 100) + '%'"></div>
                        </div>
                        <p class="text-[11px] text-slate-400">Aim for 40 to 60 characters to prevent title truncation on Google SERP.</p>
                    </div>

                    <!-- 3. Meta Description Input with Length Meter -->
                    <div class="p-4 rounded-2xl border border-slate-200 bg-white space-y-2">
                        <div class="flex items-center justify-between">
                            <label class="text-xs font-bold text-slate-800 uppercase tracking-wider flex items-center gap-1.5">
                                <i data-lucide="align-left" class="w-3.5 h-3.5 text-slate-500"></i>
                                <span>Meta Description</span>
                            </label>
                            <span class="text-[11px] font-mono font-bold"
                                  :class="descLength >= 120 && descLength <= 160 ? 'text-emerald-600' : (descLength > 160 ? 'text-rose-600' : 'text-amber-600')"
                                  x-text="descLength + ' / 160 characters'"></span>
                        </div>
                        <textarea x-model="studio.meta_description" rows="3"
                                  placeholder="Write a clear, compelling 120-160 character description including your focus keyword to maximize search click-throughs..."
                                  class="w-full px-3.5 py-2.5 text-xs rounded-xl border border-slate-200 focus:outline-none focus:ring-2 focus:ring-purple-500/20 focus:border-purple-600 transition"></textarea>
                        <!-- Description Meter Bar -->
                        <div class="w-full h-1.5 bg-slate-100 rounded-full overflow-hidden">
                            <div class="h-full transition-all duration-300"
                                 :class="descLength >= 120 && descLength <= 160 ? 'bg-emerald-500' : (descLength > 160 ? 'bg-rose-500' : 'bg-amber-400')"
                                 :style="'width: ' + Math.min(100, (descLength / 160) * 100) + '%'"></div>
                        </div>
                        <p class="text-[11px] text-slate-400">Recommended snippet length: 120 to 160 characters.</p>
                    </div>

                    <!-- 4. Schema.org JSON-LD Code Editor -->
                    <div class="p-4 rounded-2xl border border-slate-200 bg-white space-y-3">
                        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-2">
                            <div>
                                <label class="text-xs font-bold text-slate-800 uppercase tracking-wider flex items-center gap-2">
                                    <i data-lucide="code-2" class="w-4 h-4 text-purple-600"></i>
                                    <span>Schema.org JSON-LD Structured Data Code</span>
                                </label>
                                <p class="text-[11px] text-slate-400 mt-0.5">Write custom JSON-LD schema or click a template to auto-populate.</p>
                            </div>
                            <span class="text-[10px] font-bold px-2.5 py-1 rounded-full border self-start sm:self-auto"
                                  :class="isSchemaValid ? 'bg-emerald-50 text-emerald-700 border-emerald-200' : (studio.schema_markup && studio.schema_markup.trim().length > 0 ? 'bg-rose-50 text-rose-700 border-rose-200' : 'bg-slate-50 text-slate-500 border-slate-200')"
                                  x-text="isSchemaValid ? '✓ Valid Schema JSON' : (studio.schema_markup && studio.schema_markup.trim().length > 0 ? '⚠ Invalid JSON' : 'No Schema Set')"></span>
                        </div>

                        <!-- Template Insertion Buttons -->
                        <div class="p-2.5 bg-slate-50 rounded-xl border border-slate-200 flex flex-wrap items-center gap-1.5 text-xs">
                            <span class="text-[10px] font-bold text-slate-500 uppercase tracking-wider mr-1">Insert Schema:</span>
                            <button type="button" @click="insertTemplate('service')" class="px-2.5 py-1 bg-white hover:bg-purple-50 text-slate-700 hover:text-purple-700 border border-slate-200 hover:border-purple-300 rounded-lg text-xs font-semibold transition shadow-2xs">
                                + Service
                            </button>
                            <button type="button" @click="insertTemplate('article')" class="px-2.5 py-1 bg-white hover:bg-purple-50 text-slate-700 hover:text-purple-700 border border-slate-200 hover:border-purple-300 rounded-lg text-xs font-semibold transition shadow-2xs">
                                + Article
                            </button>
                            <button type="button" @click="insertTemplate('faq')" class="px-2.5 py-1 bg-white hover:bg-purple-50 text-slate-700 hover:text-purple-700 border border-slate-200 hover:border-purple-300 rounded-lg text-xs font-semibold transition shadow-2xs">
                                + FAQPage
                            </button>
                            <button type="button" @click="insertTemplate('localbusiness')" class="px-2.5 py-1 bg-white hover:bg-purple-50 text-slate-700 hover:text-purple-700 border border-slate-200 hover:border-purple-300 rounded-lg text-xs font-semibold transition shadow-2xs">
                                + LocalBusiness
                            </button>
                            <button type="button" @click="formatJson()" class="sm:ml-auto px-2.5 py-1 bg-purple-50 hover:bg-purple-100 text-purple-700 border border-purple-200 rounded-lg text-xs font-bold transition flex items-center gap-1">
                                <i data-lucide="check" class="w-3 h-3"></i> Format JSON
                            </button>
                        </div>

                        <!-- Textarea Editor -->
                        <textarea x-model="studio.schema_markup" rows="8"
                                  placeholder='{"@@context": "https://schema.org", "@type": "Service", "name": "...", "description": "..."}'
                                  class="w-full font-mono text-xs px-4 py-3 bg-slate-950 text-emerald-400 rounded-xl border border-slate-800 focus:outline-none focus:ring-2 focus:ring-purple-500/20 leading-relaxed shadow-inner"></textarea>
                    </div>

                    <!-- 5. Submit / Save Button Row -->
                    <div class="pt-2 flex flex-col sm:flex-row items-center gap-3">
                        <button type="button" @click="saveStudioSeo()" :disabled="isSaving"
                                class="w-full sm:flex-1 py-3 px-6 bg-purple-600 hover:bg-purple-700 text-white rounded-xl text-xs font-black shadow-lg shadow-purple-600/30 flex items-center justify-center gap-2 transition disabled:opacity-50">
                            <i data-lucide="save" class="w-4 h-4"></i>
                            <span x-text="isSaving ? 'Saving Changes to Database...' : 'Save Focus Keyword, Meta & Schema Code Now'"></span>
                        </button>
                        <a :href="studio.editUrl" 
                           class="w-full sm:w-auto px-4 py-3 bg-slate-100 hover:bg-slate-200 text-slate-700 font-bold text-xs rounded-xl transition flex items-center justify-center gap-1.5 shrink-0">
                            <i data-lucide="edit-3" class="w-4 h-4 text-slate-500"></i>
                            <span>Full Edit Page</span>
                        </a>
                    </div>
                </div>

                <!-- ================= RIGHT COLUMN: LIVE SERP & AUDIT (5 COLS) ================= -->
                <div class="lg:col-span-5 space-y-5">
                    
                    <!-- Google Search Result Snippet Simulation -->
                    <div class="p-5 rounded-2xl border border-slate-200 bg-slate-50/70 space-y-3">
                        <div class="flex items-center justify-between">
                            <h4 class="text-xs font-black uppercase tracking-wider text-slate-700 flex items-center gap-1.5">
                                <i data-lucide="globe" class="w-4 h-4 text-purple-600"></i>
                                <span>Google SERP Preview</span>
                            </h4>
                            <div class="flex items-center gap-1 bg-white p-0.5 rounded-lg border border-slate-200 text-[11px] font-semibold">
                                <button type="button" @click="previewMode = 'desktop'" :class="previewMode === 'desktop' ? 'bg-purple-600 text-white shadow-2xs' : 'text-slate-500 hover:text-slate-800'" class="px-2 py-0.5 rounded-md transition">Desktop</button>
                                <button type="button" @click="previewMode = 'mobile'" :class="previewMode === 'mobile' ? 'bg-purple-600 text-white shadow-2xs' : 'text-slate-500 hover:text-slate-800'" class="px-2 py-0.5 rounded-md transition">Mobile</button>
                            </div>
                        </div>

                        <!-- Snippet Container -->
                        <div class="bg-white p-4 rounded-xl border border-slate-200 shadow-sm space-y-1.5 font-sans"
                             :class="previewMode === 'mobile' ? 'max-w-xs mx-auto border-purple-200' : 'w-full'">
                            <!-- URL Line -->
                            <div class="flex items-center gap-1.5 text-xs text-slate-600 truncate">
                                <div class="w-4 h-4 rounded-full bg-purple-600 text-white flex items-center justify-center text-[10px] font-bold">G</div>
                                <div class="truncate text-[11px] text-slate-500 font-mono">
                                    https://lifeguarddatarecovery.com &rsaquo; <span x-text="studio.type"></span> &rsaquo; <span class="font-bold text-slate-700" x-text="studio.slug"></span>
                                </div>
                            </div>

                            <!-- Title -->
                            <h3 class="text-blue-700 font-medium hover:underline cursor-pointer leading-snug truncate"
                                :class="previewMode === 'mobile' ? 'text-sm' : 'text-base font-medium'"
                                x-text="studio.meta_title || studio.title || 'Page Title Placeholder'"></h3>

                            <!-- Description -->
                            <p class="text-xs text-slate-600 line-clamp-2 leading-relaxed"
                               x-text="studio.meta_description || 'Please write a meta description above to preview how your page will appear on Google search result cards.'"></p>
                        </div>
                    </div>

                    <!-- Live Yoast 7-Point Audit Checklist -->
                    <div class="p-5 rounded-2xl border border-slate-200 bg-white space-y-3">
                        <div class="flex items-center justify-between border-b border-slate-100 pb-2">
                            <h4 class="text-xs font-black uppercase tracking-wider text-slate-800 flex items-center gap-1.5">
                                <i data-lucide="shield-check" class="w-4 h-4 text-purple-600"></i>
                                <span>Yoast Content Signals Audit</span>
                            </h4>
                            <span class="text-xs font-bold"
                                  :class="passedChecksCount >= 5 ? 'text-emerald-600' : 'text-amber-600'"
                                  x-text="passedChecksCount + ' / 7 checks passing'"></span>
                        </div>

                        <div class="space-y-2 text-xs">
                            <!-- 1. Keyword Set -->
                            <div class="flex items-center justify-between p-2 rounded-xl"
                                 :class="studio.focus_keyword && studio.focus_keyword.trim().length > 0 ? 'bg-emerald-50/70 text-emerald-900' : 'bg-rose-50/70 text-rose-900'">
                                <div class="flex items-center gap-2">
                                    <span class="w-2 h-2 rounded-full" :class="studio.focus_keyword && studio.focus_keyword.trim().length > 0 ? 'bg-emerald-500' : 'bg-rose-500'"></span>
                                    <span>Focus Keyphrase configured</span>
                                </div>
                                <span class="text-[10px] font-bold uppercase" x-text="studio.focus_keyword && studio.focus_keyword.trim().length > 0 ? 'Pass' : 'Missing'"></span>
                            </div>

                            <!-- 2. Keyword in Title -->
                            <div class="flex items-center justify-between p-2 rounded-xl"
                                 :class="hasKwInTitle ? 'bg-emerald-50/70 text-emerald-900' : 'bg-rose-50/70 text-rose-900'">
                                <div class="flex items-center gap-2">
                                    <span class="w-2 h-2 rounded-full" :class="hasKwInTitle ? 'bg-emerald-500' : 'bg-rose-500'"></span>
                                    <span>Keyphrase in SEO Title</span>
                                </div>
                                <span class="text-[10px] font-bold uppercase" x-text="hasKwInTitle ? 'Pass' : 'Not found'"></span>
                            </div>

                            <!-- 3. Title Length -->
                            <div class="flex items-center justify-between p-2 rounded-xl"
                                 :class="titleLength >= 40 && titleLength <= 60 ? 'bg-emerald-50/70 text-emerald-900' : 'bg-amber-50/70 text-amber-900'">
                                <div class="flex items-center gap-2">
                                    <span class="w-2 h-2 rounded-full" :class="titleLength >= 40 && titleLength <= 60 ? 'bg-emerald-500' : 'bg-amber-500'"></span>
                                    <span>SEO Title Length (40-60 chars)</span>
                                </div>
                                <span class="text-[10px] font-bold uppercase" x-text="titleLength + ' chars'"></span>
                            </div>

                            <!-- 4. Keyword in Meta Description -->
                            <div class="flex items-center justify-between p-2 rounded-xl"
                                 :class="hasKwInDesc ? 'bg-emerald-50/70 text-emerald-900' : 'bg-rose-50/70 text-rose-900'">
                                <div class="flex items-center gap-2">
                                    <span class="w-2 h-2 rounded-full" :class="hasKwInDesc ? 'bg-emerald-500' : 'bg-rose-500'"></span>
                                    <span>Keyphrase in Meta Description</span>
                                </div>
                                <span class="text-[10px] font-bold uppercase" x-text="hasKwInDesc ? 'Pass' : 'Not found'"></span>
                            </div>

                            <!-- 5. Description Length -->
                            <div class="flex items-center justify-between p-2 rounded-xl"
                                 :class="descLength >= 120 && descLength <= 160 ? 'bg-emerald-50/70 text-emerald-900' : 'bg-amber-50/70 text-amber-900'">
                                <div class="flex items-center gap-2">
                                    <span class="w-2 h-2 rounded-full" :class="descLength >= 120 && descLength <= 160 ? 'bg-emerald-500' : 'bg-amber-500'"></span>
                                    <span>Meta Description Length (120-160 chars)</span>
                                </div>
                                <span class="text-[10px] font-bold uppercase" x-text="descLength + ' chars'"></span>
                            </div>

                            <!-- 6. Keyword in Slug -->
                            <div class="flex items-center justify-between p-2 rounded-xl"
                                 :class="hasKwInSlug ? 'bg-emerald-50/70 text-emerald-900' : 'bg-slate-50 text-slate-700'">
                                <div class="flex items-center gap-2">
                                    <span class="w-2 h-2 rounded-full" :class="hasKwInSlug ? 'bg-emerald-500' : 'bg-slate-400'"></span>
                                    <span>Keyphrase in URL Slug</span>
                                </div>
                                <span class="text-[10px] font-bold uppercase" x-text="hasKwInSlug ? 'Pass' : 'Partial'"></span>
                            </div>

                            <!-- 7. Schema Valid -->
                            <div class="flex items-center justify-between p-2 rounded-xl"
                                 :class="isSchemaValid ? 'bg-emerald-50/70 text-emerald-900' : 'bg-rose-50/70 text-rose-900'">
                                <div class="flex items-center gap-2">
                                    <span class="w-2 h-2 rounded-full" :class="isSchemaValid ? 'bg-emerald-500' : 'bg-rose-500'"></span>
                                    <span>Schema.org JSON-LD Code valid</span>
                                </div>
                                <span class="text-[10px] font-bold uppercase" x-text="isSchemaValid ? 'Valid' : 'Invalid'"></span>
                            </div>
                        </div>
                    </div>

                </div>
            </div>
        </div>

        <!-- ========================================================================= -->
        <!-- 2. DIRECTORY TABLES WITH ONE-CLICK "WRITE IN STUDIO" BUTTONS              -->
        <!-- ========================================================================= -->
        <div class="bg-white rounded-2xl border border-slate-200/80 shadow-sm overflow-hidden">
            <!-- Header with Tab Navigation -->
            <div class="px-6 py-4 border-b border-slate-100 flex flex-col md:flex-row md:items-center md:justify-between gap-4 bg-slate-50/70">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-xl bg-purple-600 text-white flex items-center justify-center font-bold text-sm shadow-md shadow-purple-500/20 shrink-0">
                        <i data-lucide="layers" class="w-5 h-5"></i>
                    </div>
                    <div>
                        <div class="flex items-center gap-2">
                            <h3 class="text-sm font-black text-slate-900">SEO Directory Index</h3>
                            <span class="text-[10px] font-bold px-2 py-0.5 rounded-full bg-purple-100 text-purple-700 uppercase tracking-wider">All Pages & Posts</span>
                        </div>
                        <p class="text-xs text-slate-500">Click any row or "Write in Studio" to edit its Focus Keyword, Meta Description, and Schema code above.</p>
                    </div>
                </div>

                <!-- Tabs: Services | Posts | Pages -->
                <div class="flex items-center bg-slate-200/80 p-1 rounded-xl text-xs font-bold shrink-0">
                    <button type="button" @click="currentSeoTab = 'services'"
                            :class="currentSeoTab === 'services' ? 'bg-white text-purple-700 shadow-sm' : 'text-slate-600 hover:text-slate-900'"
                            class="px-3.5 py-1.5 rounded-lg transition flex items-center gap-1.5">
                        <i data-lucide="zap" class="w-3.5 h-3.5"></i>
                        <span>Services ({{ $services->count() }})</span>
                    </button>
                    <button type="button" @click="currentSeoTab = 'posts'"
                            :class="currentSeoTab === 'posts' ? 'bg-white text-purple-700 shadow-sm' : 'text-slate-600 hover:text-slate-900'"
                            class="px-3.5 py-1.5 rounded-lg transition flex items-center gap-1.5">
                        <i data-lucide="file-text" class="w-3.5 h-3.5"></i>
                        <span>Blog Posts ({{ $recentPosts->count() }})</span>
                    </button>
                    <button type="button" @click="currentSeoTab = 'pages'"
                            :class="currentSeoTab === 'pages' ? 'bg-white text-purple-700 shadow-sm' : 'text-slate-600 hover:text-slate-900'"
                            class="px-3.5 py-1.5 rounded-lg transition flex items-center gap-1.5">
                        <i data-lucide="layout" class="w-3.5 h-3.5"></i>
                        <span>Pages ({{ $pages->count() }})</span>
                    </button>
                </div>
            </div>

            <!-- ================= TAB 1: COMMERCIAL SERVICES ================= -->
            <div x-show="currentSeoTab === 'services'" class="overflow-x-auto">
                <table class="w-full text-left border-collapse text-xs">
                    <thead>
                        <tr class="bg-slate-50 border-b border-slate-200/80 text-slate-500 font-bold uppercase tracking-wider text-[10px]">
                            <th class="py-3 px-6">Service Name & Slug</th>
                            <th class="py-3 px-4">Focus Keyword</th>
                            <th class="py-3 px-4">Yoast Signal</th>
                            <th class="py-3 px-4">Meta Description</th>
                            <th class="py-3 px-4">Schema Code</th>
                            <th class="py-3 px-4">Status</th>
                            <th class="py-3 px-6 text-right">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 text-slate-700">
                    @forelse($services as $svc)
                        @php
                            $score = $svc->seo_score ?? 0;
                            $rating = $score >= 80 ? 'good' : ($score >= 50 ? 'ok' : 'poor');
                            $hasDesc = !empty($svc->meta_description);
                            $hasCustomSchema = !empty($svc->schema_markup);
                        @endphp
                        <tr id="seo-row-service-{{ $svc->id }}" class="hover:bg-slate-50/80 transition group">
                            <!-- Title & Slug -->
                            <td class="py-3.5 px-6 max-w-xs">
                                <div class="font-bold text-slate-900 group-hover:text-brand-600 transition text-xs sm:text-sm flex items-center gap-2">
                                    <a href="{{ route('admin.services.edit', $svc) }}">{{ $svc->title }}</a>
                                    @if($svc->is_featured)
                                        <span class="px-1.5 py-0.5 rounded text-[9px] font-bold bg-amber-100 text-amber-800">Featured</span>
                                    @endif
                                </div>
                                <div class="text-[11px] text-slate-400 mt-0.5 font-mono">/service/{{ $svc->slug }}</div>
                            </td>

                            <!-- Focus Keyword Cell -->
                            <td class="py-3.5 px-4 seo-kw-cell">
                                @if($svc->focus_keyword)
                                    <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-[11px] font-bold bg-purple-50 text-purple-700 border border-purple-200">
                                        <i data-lucide="tag" class="w-3 h-3 text-purple-500"></i>
                                        <span>{{ $svc->focus_keyword }}</span>
                                    </span>
                                @else
                                    <button type="button" 
                                            @click="selectFromRow('service', {{ $svc->id }})" 
                                            class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-[10px] font-bold bg-rose-50 text-rose-700 border border-dashed border-rose-300 hover:bg-rose-100 transition">
                                        <i data-lucide="plus" class="w-3 h-3"></i>
                                        <span>+ Write Keyword</span>
                                    </button>
                                @endif
                            </td>

                            <!-- Yoast SEO Signal Cell -->
                            <td class="py-3.5 px-4 seo-sig-cell">
                                <div class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-[11px] font-bold border {{ $rating === 'good' ? 'bg-emerald-50 text-emerald-700 border-emerald-200 shadow-xs shadow-emerald-500/10' : ($rating === 'ok' ? 'bg-amber-50 text-amber-700 border-amber-200' : 'bg-rose-50 text-rose-700 border-rose-200') }}">
                                    <span class="w-2 h-2 rounded-full {{ $rating === 'good' ? 'bg-emerald-500 animate-pulse' : ($rating === 'ok' ? 'bg-amber-500' : 'bg-rose-500') }}"></span>
                                    <span>{{ $score }}/100</span>
                                    <span class="uppercase text-[9px] opacity-75">({{ $rating }})</span>
                                </div>
                            </td>

                            <!-- Meta Description Status -->
                            <td class="py-3.5 px-4">
                                @if($hasDesc)
                                    <div class="flex items-center gap-1.5 text-emerald-700 text-xs font-semibold" title="{{ $svc->meta_description }}">
                                        <i data-lucide="check-circle" class="w-3.5 h-3.5 text-emerald-600 shrink-0"></i>
                                        <span class="truncate max-w-[150px] text-slate-600 text-[11px] font-normal">{{ $svc->meta_description }}</span>
                                    </div>
                                @else
                                    <span class="inline-flex items-center gap-1 text-[10px] font-bold text-rose-600 bg-rose-50 px-2 py-0.5 rounded-md border border-rose-200">
                                        <i data-lucide="alert-triangle" class="w-3 h-3"></i>
                                        <span>Missing Meta</span>
                                    </span>
                                @endif
                            </td>

                            <!-- Schema Status -->
                            <td class="py-3.5 px-4">
                                @if($hasCustomSchema)
                                    <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-[10px] font-bold bg-indigo-50 text-indigo-700 border border-indigo-200">
                                        <i data-lucide="code-2" class="w-3 h-3 text-indigo-500"></i>
                                        <span>Custom JSON-LD</span>
                                    </span>
                                @else
                                    <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-[10px] font-bold bg-sky-50 text-sky-700 border border-sky-200">
                                        <i data-lucide="sparkles" class="w-3 h-3 text-sky-500"></i>
                                        <span>Service + FAQ</span>
                                    </span>
                                @endif
                            </td>

                            <!-- Status -->
                            <td class="py-3.5 px-4">
                                @if($svc->status === 'published')
                                    <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-[10px] font-bold bg-emerald-100 text-emerald-800">
                                        <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span> Active
                                    </span>
                                @else
                                    <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-[10px] font-bold bg-amber-100 text-amber-800">
                                        <span class="w-1.5 h-1.5 rounded-full bg-amber-500"></span> Draft
                                    </span>
                                @endif
                            </td>

                            <!-- Actions -->
                            <td class="py-3.5 px-6 text-right">
                                    <button type="button" 
                                            @click="selectFromRow('service', {{ $svc->id }})"
                                            class="px-2.5 py-1 rounded-lg text-white bg-purple-600 hover:bg-purple-700 text-[11px] font-bold shadow-xs transition flex items-center gap-1"
                                            title="Write Focus Keyword & Schema in Studio">
                                        <i data-lucide="sparkles" class="w-3 h-3 text-amber-300"></i>
                                        <span>Write SEO</span>
                                    </button>
                                    <button type="button" 
                                            @click="window.openQuickSeoModal({
                                                type: 'service',
                                                endpoint: '{{ route('admin.services.quick-seo', $svc) }}',
                                                id: {{ $svc->id }},
                                                title: @js($svc->title),
                                                slug: @js($svc->slug),
                                                focus_keyword: @js($svc->focus_keyword ?? ''),
                                                meta_title: @js($svc->meta_title ?? ''),
                                                meta_description: @js($svc->meta_description ?? ''),
                                                schema_markup: @js($svc->schema_markup ?? ''),
                                                seo_score: {{ (int) ($svc->seo_score ?? 0) }}
                                            })"
                                            class="px-2 py-1 rounded-lg text-purple-700 hover:text-purple-900 bg-purple-50 hover:bg-purple-100 text-[11px] font-bold border border-purple-200 transition flex items-center gap-1"
                                            title="Open Yoast Modal">
                                        <i data-lucide="maximize-2" class="w-3 h-3 text-purple-600"></i>
                                        <span>Modal</span>
                                    </button>
                                    <a href="{{ route('service.show', $svc->slug) }}" target="_blank" 
                                       class="px-2 py-1 rounded-lg text-slate-500 hover:text-slate-900 bg-slate-100 hover:bg-slate-200 text-[11px] font-semibold transition flex items-center gap-1">
                                        <i data-lucide="external-link" class="w-3 h-3 text-slate-400"></i>
                                        <span>View</span>
                                    </a>
                                    <a href="{{ route('admin.services.edit', $svc) }}" 
                                       class="px-3 py-1 rounded-lg text-white bg-brand-600 hover:bg-brand-500 text-[11px] font-bold shadow-sm transition flex items-center gap-1">
                                        <i data-lucide="edit-3" class="w-3 h-3"></i>
                                        <span>Edit Page</span>
                                    </a>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="py-8 text-center text-slate-400">No services created yet.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- ================= TAB 2: BLOG POSTS ================= -->
        <div x-show="currentSeoTab === 'posts'" class="overflow-x-auto">
            <table class="w-full text-left border-collapse text-xs">
                <thead>
                    <tr class="bg-slate-50 border-b border-slate-200/80 text-slate-500 font-bold uppercase tracking-wider text-[10px]">
                        <th class="py-3 px-6">Blog Post Title & Slug</th>
                        <th class="py-3 px-4">Focus Keyword</th>
                        <th class="py-3 px-4">Yoast Signal</th>
                        <th class="py-3 px-4">Meta Description</th>
                        <th class="py-3 px-4">Category</th>
                        <th class="py-3 px-4">Status</th>
                        <th class="py-3 px-6 text-right">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 text-slate-700">
                    @forelse($recentPosts as $post)
                        @php
                            $score = $post->seo_score ?? 0;
                            $rating = $score >= 80 ? 'good' : ($score >= 50 ? 'ok' : 'poor');
                            $hasDesc = !empty($post->meta_description);
                        @endphp
                        <tr id="seo-row-post-{{ $post->id }}" class="hover:bg-slate-50/80 transition group">
                            <!-- Title & Slug -->
                            <td class="py-3.5 px-6 max-w-sm">
                                <div class="font-bold text-slate-900 group-hover:text-brand-600 transition text-xs sm:text-sm">
                                    <a href="{{ route('admin.posts.edit', $post) }}">{{ $post->title }}</a>
                                </div>
                                <div class="text-[11px] text-slate-400 mt-0.5 font-mono">/{{ $post->slug }}</div>
                            </td>

                            <!-- Focus Keyword Cell -->
                            <td class="py-3.5 px-4 seo-kw-cell">
                                @if($post->focus_keyword)
                                    <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-[11px] font-bold bg-purple-50 text-purple-700 border border-purple-200">
                                        <i data-lucide="tag" class="w-3 h-3 text-purple-500"></i>
                                        <span>{{ $post->focus_keyword }}</span>
                                    </span>
                                @else
                                    <button type="button" 
                                            @click="selectFromRow('post', {{ $post->id }})" 
                                            class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-[10px] font-bold bg-rose-50 text-rose-700 border border-dashed border-rose-300 hover:bg-rose-100 transition">
                                        <i data-lucide="plus" class="w-3 h-3"></i>
                                        <span>+ Write Keyword</span>
                                    </button>
                                @endif
                            </td>

                            <!-- Yoast SEO Signal Cell -->
                            <td class="py-3.5 px-4 seo-sig-cell">
                                <div class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-[11px] font-bold border {{ $rating === 'good' ? 'bg-emerald-50 text-emerald-700 border-emerald-200' : ($rating === 'ok' ? 'bg-amber-50 text-amber-700 border-amber-200' : 'bg-rose-50 text-rose-700 border-rose-200') }}">
                                    <span class="w-2 h-2 rounded-full {{ $rating === 'good' ? 'bg-emerald-500 animate-pulse' : ($rating === 'ok' ? 'bg-amber-500' : 'bg-rose-500') }}"></span>
                                    <span>{{ $score }}/100</span>
                                    <span class="uppercase text-[9px] opacity-75">({{ $rating }})</span>
                                </div>
                            </td>

                            <!-- Meta Description -->
                            <td class="py-3.5 px-4">
                                @if($hasDesc)
                                    <div class="flex items-center gap-1.5 text-emerald-700 text-xs font-semibold" title="{{ $post->meta_description }}">
                                        <i data-lucide="check-circle" class="w-3.5 h-3.5 text-emerald-600 shrink-0"></i>
                                        <span class="truncate max-w-[150px] text-slate-600 text-[11px] font-normal">{{ $post->meta_description }}</span>
                                    </div>
                                @else
                                    <span class="inline-flex items-center gap-1 text-[10px] font-bold text-rose-600 bg-rose-50 px-2 py-0.5 rounded-md border border-rose-200">
                                        <i data-lucide="alert-triangle" class="w-3 h-3"></i>
                                        <span>Missing Meta</span>
                                    </span>
                                @endif
                            </td>

                            <!-- Categories -->
                            <td class="py-3.5 px-4">
                                @if($post->categories->count())
                                    <span class="px-2 py-0.5 rounded-md bg-slate-100 text-slate-600 text-[10px] font-medium">{{ $post->categories->first()->name }}</span>
                                @else
                                    <span class="text-slate-400 italic text-[11px]">—</span>
                                @endif
                            </td>

                            <!-- Status -->
                            <td class="py-3.5 px-4">
                                @if($post->status === 'published')
                                    <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-[10px] font-semibold bg-emerald-50 text-emerald-700 border border-emerald-200">Published</span>
                                @elseif($post->status === 'scheduled')
                                    <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-[10px] font-semibold bg-sky-50 text-sky-700 border border-sky-200">Scheduled</span>
                                @else
                                    <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-[10px] font-semibold bg-amber-50 text-amber-700 border border-amber-200">Draft</span>
                                @endif
                            </td>

                            <!-- Actions -->
                            <td class="py-3.5 px-6 text-right">
                                <div class="flex items-center justify-end gap-1.5">
                                    <button type="button" 
                                            @click="selectFromRow('post', {{ $post->id }})"
                                            class="px-2.5 py-1 rounded-lg text-white bg-purple-600 hover:bg-purple-700 text-[11px] font-bold shadow-xs transition flex items-center gap-1"
                                            title="Write Focus Keyword & Schema in Studio">
                                        <i data-lucide="sparkles" class="w-3 h-3 text-amber-300"></i>
                                        <span>Write SEO</span>
                                    </button>
                                    <button type="button" 
                                            @click="window.openQuickSeoModal({
                                                type: 'post',
                                                endpoint: '{{ route('admin.posts.quick-seo', $post) }}',
                                                id: {{ $post->id }},
                                                title: @js($post->title),
                                                slug: @js($post->slug),
                                                focus_keyword: @js($post->focus_keyword ?? ''),
                                                meta_title: @js($post->meta_title ?? ''),
                                                meta_description: @js($post->meta_description ?? ''),
                                                schema_markup: @js($post->schema_markup ?? ''),
                                                seo_score: {{ (int) ($post->seo_score ?? 0) }}
                                            })"
                                            class="px-2 py-1 rounded-lg text-purple-700 hover:text-purple-900 bg-purple-50 hover:bg-purple-100 text-[11px] font-bold border border-purple-200 transition flex items-center gap-1"
                                            title="Open Yoast Modal">
                                        <i data-lucide="maximize-2" class="w-3 h-3 text-purple-600"></i>
                                        <span>Modal</span>
                                    </button>
                                    <a href="{{ route('post.show', $post->slug) }}" target="_blank" 
                                       class="px-2 py-1 rounded-lg text-slate-500 hover:text-slate-900 bg-slate-100 hover:bg-slate-200 text-[11px] font-semibold transition flex items-center gap-1">
                                        <i data-lucide="external-link" class="w-3 h-3 text-slate-400"></i>
                                        <span>View</span>
                                    </a>
                                    <a href="{{ route('admin.posts.edit', $post) }}" 
                                       class="px-3 py-1 rounded-lg text-white bg-brand-600 hover:bg-brand-500 text-[11px] font-bold shadow-sm transition flex items-center gap-1">
                                        <i data-lucide="edit-3" class="w-3 h-3"></i>
                                        <span>Edit</span>
                                    </a>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="py-8 text-center text-slate-400">No blog posts found.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- ================= TAB 3: WEBSITE PAGES ================= -->
        <div x-show="currentSeoTab === 'pages'" class="overflow-x-auto">
            <table class="w-full text-left border-collapse text-xs">
                <thead>
                    <tr class="bg-slate-50 border-b border-slate-200/80 text-slate-500 font-bold uppercase tracking-wider text-[10px]">
                        <th class="py-3 px-6">Page Title & Slug</th>
                        <th class="py-3 px-4">Focus Keyword</th>
                        <th class="py-3 px-4">Yoast Signal</th>
                        <th class="py-3 px-4">Meta Description</th>
                        <th class="py-3 px-4">Template</th>
                        <th class="py-3 px-4">Status</th>
                        <th class="py-3 px-6 text-right">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 text-slate-700">
                    @forelse($pages as $pg)
                        @php
                            $score = $pg->seo_score ?? 0;
                            $rating = $score >= 80 ? 'good' : ($score >= 50 ? 'ok' : 'poor');
                            $hasDesc = !empty($pg->meta_description);
                        @endphp
                        <tr id="seo-row-page-{{ $pg->id }}" class="hover:bg-slate-50/80 transition group">
                            <!-- Title & Slug -->
                            <td class="py-3.5 px-6 max-w-sm">
                                <div class="font-bold text-slate-900 group-hover:text-brand-600 transition text-xs sm:text-sm">
                                    <a href="{{ route('admin.pages.edit', $pg) }}">{{ $pg->title }}</a>
                                </div>
                                <div class="text-[11px] text-slate-400 mt-0.5 font-mono">/page/{{ $pg->slug }}</div>
                            </td>

                            <!-- Focus Keyword Cell -->
                            <td class="py-3.5 px-4 seo-kw-cell">
                                @if($pg->focus_keyword)
                                    <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-[11px] font-bold bg-purple-50 text-purple-700 border border-purple-200">
                                        <i data-lucide="tag" class="w-3 h-3 text-purple-500"></i>
                                        <span>{{ $pg->focus_keyword }}</span>
                                    </span>
                                @else
                                    <button type="button" 
                                            @click="selectFromRow('page', {{ $pg->id }})" 
                                            class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-[10px] font-bold bg-rose-50 text-rose-700 border border-dashed border-rose-300 hover:bg-rose-100 transition">
                                        <i data-lucide="plus" class="w-3 h-3"></i>
                                        <span>+ Write Keyword</span>
                                    </button>
                                @endif
                            </td>

                            <!-- Yoast SEO Signal Cell -->
                            <td class="py-3.5 px-4 seo-sig-cell">
                                <div class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-[11px] font-bold border {{ $rating === 'good' ? 'bg-emerald-50 text-emerald-700 border-emerald-200' : ($rating === 'ok' ? 'bg-amber-50 text-amber-700 border-amber-200' : 'bg-rose-50 text-rose-700 border-rose-200') }}">
                                    <span class="w-2 h-2 rounded-full {{ $rating === 'good' ? 'bg-emerald-500 animate-pulse' : ($rating === 'ok' ? 'bg-amber-500' : 'bg-rose-500') }}"></span>
                                    <span>{{ $score }}/100</span>
                                    <span class="uppercase text-[9px] opacity-75">({{ $rating }})</span>
                                </div>
                            </td>

                            <!-- Meta Description -->
                            <td class="py-3.5 px-4">
                                @if($hasDesc)
                                    <div class="flex items-center gap-1.5 text-emerald-700 text-xs font-semibold" title="{{ $pg->meta_description }}">
                                        <i data-lucide="check-circle" class="w-3.5 h-3.5 text-emerald-600 shrink-0"></i>
                                        <span class="truncate max-w-[150px] text-slate-600 text-[11px] font-normal">{{ $pg->meta_description }}</span>
                                    </div>
                                @else
                                    <span class="inline-flex items-center gap-1 text-[10px] font-bold text-rose-600 bg-rose-50 px-2 py-0.5 rounded-md border border-rose-200">
                                        <i data-lucide="alert-triangle" class="w-3 h-3"></i>
                                        <span>Missing Meta</span>
                                    </span>
                                @endif
                            </td>

                            <!-- Template -->
                            <td class="py-3.5 px-4">
                                <span class="px-2 py-0.5 rounded-md bg-slate-100 text-slate-700 text-[11px] font-mono capitalize">{{ $pg->template ?? 'default' }}</span>
                            </td>

                            <!-- Status -->
                            <td class="py-3.5 px-4">
                                @if($pg->status === 'published')
                                    <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-[10px] font-semibold bg-emerald-50 text-emerald-700 border border-emerald-200">Published</span>
                                @else
                                    <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-[10px] font-semibold bg-amber-50 text-amber-700 border border-amber-200">Draft</span>
                                @endif
                            </td>

                            <!-- Actions -->
                            <td class="py-3.5 px-6 text-right">
                                <div class="flex items-center justify-end gap-1.5">
                                    <button type="button" 
                                            @click="selectFromRow('page', {{ $pg->id }})"
                                            class="px-2.5 py-1 rounded-lg text-white bg-purple-600 hover:bg-purple-700 text-[11px] font-bold shadow-xs transition flex items-center gap-1"
                                            title="Write Focus Keyword & Schema in Studio">
                                        <i data-lucide="sparkles" class="w-3 h-3 text-amber-300"></i>
                                        <span>Write SEO</span>
                                    </button>
                                    <button type="button" 
                                            @click="window.openQuickSeoModal({
                                                type: 'page',
                                                endpoint: '{{ route('admin.pages.quick-seo', $pg) }}',
                                                id: {{ $pg->id }},
                                                title: @js($pg->title),
                                                slug: @js($pg->slug),
                                                focus_keyword: @js($pg->focus_keyword ?? ''),
                                                meta_title: @js($pg->meta_title ?? ''),
                                                meta_description: @js($pg->meta_description ?? ''),
                                                schema_markup: @js($pg->schema_markup ?? ''),
                                                seo_score: {{ (int) ($pg->seo_score ?? 0) }}
                                            })"
                                            class="px-2 py-1 rounded-lg text-purple-700 hover:text-purple-900 bg-purple-50 hover:bg-purple-100 text-[11px] font-bold border border-purple-200 transition flex items-center gap-1"
                                            title="Open Yoast Modal">
                                        <i data-lucide="maximize-2" class="w-3 h-3 text-purple-600"></i>
                                        <span>Modal</span>
                                    </button>
                                    <a href="{{ route('page.show', $pg->slug) }}" target="_blank" 
                                        class="px-2 py-1 rounded-lg text-slate-500 hover:text-slate-900 bg-slate-100 hover:bg-slate-200 text-[11px] font-semibold transition flex items-center gap-1">
                                        <i data-lucide="external-link" class="w-3 h-3 text-slate-400"></i>
                                        <span>View</span>
                                    </a>
                                    <a href="{{ route('admin.pages.edit', $pg) }}" 
                                        class="px-3 py-1 rounded-lg text-white bg-brand-600 hover:bg-brand-500 text-[11px] font-bold shadow-sm transition flex items-center gap-1">
                                        <i data-lucide="edit-3" class="w-3 h-3"></i>
                                        <span>Edit</span>
                                    </a>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="py-8 text-center text-slate-400">No pages found.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

    </div>
</div>

    @if($recentInquiries->isNotEmpty())
        <!-- Recent Leads & Inquiries Banner -->
        <div class="bg-white rounded-2xl border border-slate-200/80 shadow-sm overflow-hidden">
            <div class="px-6 py-4 border-b border-slate-100 flex items-center justify-between bg-slate-50/50">
                <div class="flex items-center gap-2">
                    <i data-lucide="inbox" class="w-4 h-4 text-rose-600"></i>
                    <h2 class="text-sm font-bold text-slate-800">Recent Leads & Inquiries</h2>
                </div>
                <a href="{{ route('admin.inquiries.index') }}" class="text-xs font-semibold text-brand-600 hover:text-brand-700">View All Leads &rarr;</a>
            </div>
            <div class="divide-y divide-slate-100 text-xs">
                @foreach($recentInquiries as $inq)
                    <div class="p-4 flex items-center justify-between hover:bg-slate-50/60 transition {{ !$inq->is_read ? 'bg-rose-50/20' : '' }}">
                        <div class="flex items-center gap-3">
                            <span class="w-2 h-2 rounded-full {{ !$inq->is_read ? 'bg-rose-500 animate-pulse' : 'bg-slate-300' }}"></span>
                            <div>
                                <div class="font-bold text-slate-900">{{ $inq->name }} &bull; <span class="font-normal text-slate-500">{{ $inq->email }}</span></div>
                                <div class="text-[11px] text-slate-500 font-medium">{{ $inq->subject }} {{ $inq->company ? '('.$inq->company.')' : '' }}</div>
                            </div>
                        </div>
                        <div class="flex items-center gap-3">
                            <span class="text-[10px] text-slate-400">{{ $inq->created_at->diffForHumans() }}</span>
                            <a href="{{ route('admin.inquiries.show', $inq) }}" class="px-2.5 py-1 rounded-lg bg-slate-100 hover:bg-slate-200 text-slate-700 font-semibold text-[11px]">View</a>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    @endif

    <!-- 2-Column Section: Recent Posts (Left) & Pages + Quick Draft (Right) -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        <!-- Left 2 Cols: Recent Posts -->
        <div class="lg:col-span-2 bg-white rounded-2xl border border-slate-200/80 shadow-sm overflow-hidden flex flex-col">
            <div class="px-6 py-4 border-b border-slate-100 flex items-center justify-between bg-slate-50/50">
                <div class="flex items-center gap-2">
                    <i data-lucide="file-check-2" class="w-4 h-4 text-brand-600"></i>
                    <h2 class="text-sm font-bold text-slate-800">Recent Blog Activity</h2>
                </div>
                <a href="{{ route('admin.posts.index') }}" class="text-xs font-semibold text-brand-600 hover:text-brand-700">View All Posts &rarr;</a>
            </div>

            <div class="flex-1 divide-y divide-slate-100">
                @forelse($recentPosts as $post)
                    <div class="p-4 sm:p-5 flex items-center justify-between hover:bg-slate-50/80 transition group">
                        <div class="space-y-1 pr-4 min-w-0">
                            <div class="flex items-center gap-2 flex-wrap">
                                <a href="{{ route('admin.posts.edit', $post) }}" class="text-sm font-bold text-slate-900 group-hover:text-brand-600 transition truncate block">
                                    {{ $post->title }}
                                </a>
                                @if($post->status === 'published')
                                    <span class="inline-flex items-center px-2 py-0.5 rounded text-[10px] font-semibold bg-emerald-50 text-emerald-700 border border-emerald-200">Published</span>
                                @elseif($post->status === 'scheduled')
                                    <span class="inline-flex items-center px-2 py-0.5 rounded text-[10px] font-semibold bg-sky-50 text-sky-700 border border-sky-200">Scheduled</span>
                                @else
                                    <span class="inline-flex items-center px-2 py-0.5 rounded text-[10px] font-semibold bg-amber-50 text-amber-700 border border-amber-200">Draft</span>
                                @endif
                            </div>
                            <div class="flex items-center gap-4 text-xs text-slate-500">
                                <span>{{ $post->created_at->diffForHumans() }}</span>
                                @if($post->categories->count())
                                    <span>•</span>
                                    <span class="text-brand-600 font-medium">{{ $post->categories->pluck('name')->implode(', ') }}</span>
                                @endif
                            </div>
                        </div>

                        <div class="flex items-center gap-1 shrink-0">
                            <a href="{{ route('admin.posts.edit', $post) }}" class="p-2 text-slate-400 hover:text-brand-600 hover:bg-slate-100 rounded-lg transition" title="Edit Post">
                                <i data-lucide="edit-3" class="w-4 h-4"></i>
                            </a>
                            <a href="{{ route('post.show', $post->slug) }}" target="_blank" class="p-2 text-slate-400 hover:text-slate-700 hover:bg-slate-100 rounded-lg transition" title="Preview">
                                <i data-lucide="external-link" class="w-4 h-4"></i>
                            </a>
                        </div>
                    </div>
                @empty
                    <div class="p-12 text-center text-slate-400 text-xs">
                        <i data-lucide="inbox" class="w-8 h-8 mx-auto mb-2 opacity-50"></i>
                        No posts created yet.
                    </div>
                @endforelse
            </div>
        </div>

        <!-- Right 1 Col: Website Pages & Quick Draft -->
        <div class="space-y-6">
            <!-- Website Pages Quick Box -->
            <div class="bg-white rounded-2xl border border-slate-200/80 shadow-sm p-6 space-y-3">
                <div class="flex items-center justify-between pb-3 border-b border-slate-100">
                    <h3 class="text-sm font-bold text-slate-900 flex items-center gap-2">
                        <i data-lucide="file-text" class="w-4 h-4 text-emerald-600"></i>
                        Website Pages
                    </h3>
                    <a href="{{ route('admin.pages.index') }}" class="text-[11px] font-semibold text-brand-600 hover:underline">All Pages &rarr;</a>
                </div>

                <div class="space-y-2 text-xs">
                    @forelse($pages as $pg)
                        <div class="flex items-center justify-between p-2 rounded-xl hover:bg-slate-50 transition group">
                            <div class="min-w-0 pr-2">
                                <a href="{{ route('admin.pages.edit', $pg) }}" class="font-semibold text-slate-800 group-hover:text-brand-600 transition truncate block">
                                    {{ $pg->title }}
                                </a>
                                <span class="text-[10px] text-slate-400 font-mono">/page/{{ $pg->slug }}</span>
                            </div>
                            <a href="{{ route('admin.pages.edit', $pg) }}" class="p-1 rounded text-slate-400 hover:text-brand-600 transition shrink-0" title="Edit">
                                <i data-lucide="edit-3" class="w-3.5 h-3.5"></i>
                            </a>
                        </div>
                    @empty
                        <p class="text-slate-400 text-center py-2">No pages found.</p>
                    @endforelse
                </div>
            </div>

            <!-- Quick Draft Box -->
            <div class="bg-white rounded-2xl border border-slate-200/80 shadow-sm p-6 space-y-4">
                <div class="flex items-center justify-between pb-3 border-b border-slate-100">
                    <h3 class="text-sm font-bold text-slate-900 flex items-center gap-2">
                        <i data-lucide="feather" class="w-4 h-4 text-brand-600"></i>
                        Quick Draft
                    </h3>
                    <span class="text-[10px] bg-slate-100 text-slate-500 px-2 py-0.5 rounded font-medium">Idea jotter</span>
                </div>

                <form action="{{ route('admin.quick-draft') }}" method="POST" class="space-y-3">
                    @csrf
                    <div>
                        <input type="text" name="title" required placeholder="Title of your post idea"
                               class="w-full px-3 py-2 text-xs rounded-xl border border-slate-200 focus:outline-none focus:ring-2 focus:ring-brand-500/20 focus:border-brand-500 transition">
                    </div>
                    <div>
                        <textarea name="content" rows="3" placeholder="What's on your mind?..."
                                  class="w-full px-3 py-2 text-xs rounded-xl border border-slate-200 focus:outline-none focus:ring-2 focus:ring-brand-500/20 focus:border-brand-500 transition"></textarea>
                    </div>
                    <button type="submit" class="w-full py-2 bg-slate-900 hover:bg-slate-800 text-white rounded-xl text-xs font-semibold shadow-sm transition">
                        Save as Draft
                    </button>
                </form>
            </div>

            <!-- Recent Media Thumbnails -->
            <div class="bg-white rounded-2xl border border-slate-200/80 shadow-sm p-6 space-y-4">
                <div class="flex items-center justify-between pb-2 border-b border-slate-100">
                    <h3 class="text-sm font-bold text-slate-900 flex items-center gap-2">
                        <i data-lucide="image" class="w-4 h-4 text-amber-600"></i>
                        Recent Media Uploads
                    </h3>
                    <a href="{{ route('admin.media.index') }}" class="text-[11px] font-semibold text-brand-600 hover:underline">Library &rarr;</a>
                </div>

                @if($recentMedia->count())
                    <div class="grid grid-cols-4 gap-2">
                        @foreach($recentMedia as $med)
                            <div class="aspect-square rounded-lg overflow-hidden bg-slate-100 border border-slate-200">
                                @if($med->is_image)
                                    <img src="{{ $med->url }}" alt="{{ $med->name }}" class="w-full h-full object-cover" loading="lazy" onerror="this.onerror=null; this.src='/storage/media/2026/09/1-y7ja6x.webp';">
                                @else
                                    <div class="w-full h-full flex items-center justify-center text-slate-400">
                                        <i data-lucide="file" class="w-5 h-5"></i>
                                    </div>
                                @endif
                            </div>
                        @endforeach
                    </div>
                @else
                    <p class="text-xs text-slate-400 text-center py-4">No media uploaded yet.</p>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
function dashboardSeoStudio(config) {
    return {
        items: config.items || {},
        selectedKey: config.initialKey || '',
        currentSeoTab: 'services',
        previewMode: 'desktop',
        isSaving: false,
        saveSuccessMsg: '',
        saveErrorMsg: '',
        
        studio: {
            key: '',
            type: 'service',
            id: null,
            title: '',
            slug: '',
            url: '',
            focus_keyword: '',
            meta_title: '',
            meta_description: '',
            schema_markup: '',
            seo_score: 0,
            seo_rating: 'poor',
            quick_seo_url: '',
            edit_url: '',
        },

        init() {
            if (this.selectedKey && this.items[this.selectedKey]) {
                this.loadItem(this.selectedKey);
            } else {
                const keys = Object.keys(this.items);
                if (keys.length > 0) {
                    this.loadItem(keys[0]);
                }
            }
            this.$nextTick(() => {
                if (window.lucide && typeof window.lucide.createIcons === 'function') {
                    window.lucide.createIcons();
                }
            });
        },

        loadItem(key) {
            if (!this.items[key]) return;
            const item = this.items[key];
            this.selectedKey = key;
            this.currentSeoTab = item.type === 'service' ? 'services' : (item.type === 'post' ? 'posts' : 'pages');
            
            this.studio = {
                key: key,
                type: item.type,
                id: item.id,
                title: item.title || '',
                slug: item.slug || '',
                url: item.url || '',
                focus_keyword: item.focus_keyword || '',
                meta_title: item.meta_title || '',
                meta_description: item.meta_description || '',
                schema_markup: item.schema_markup || '',
                seo_score: item.seo_score || 0,
                seo_rating: item.seo_rating || 'poor',
                quick_seo_url: item.quick_seo_url || '',
                edit_url: item.edit_url || '',
            };

            this.saveSuccessMsg = '';
            this.saveErrorMsg = '';

            this.$nextTick(() => {
                if (window.lucide && typeof window.lucide.createIcons === 'function') {
                    window.lucide.createIcons();
                }
            });
        },

        onSelectKeyChange(newKey) {
            this.loadItem(newKey);
        },

        loadFirstOfType(type) {
            for (const k in this.items) {
                if (this.items[k].type === type) {
                    this.loadItem(k);
                    break;
                }
            }
        },

        selectFromRow(type, id) {
            const key = type + '-' + id;
            if (this.items[key]) {
                this.loadItem(key);
            }
            const container = document.getElementById('seoStudioContainer');
            if (container) {
                container.scrollIntoView({ behavior: 'smooth', block: 'start' });
            }
            setTimeout(() => {
                const input = document.getElementById('studioFocusKeywordInput');
                if (input) {
                    input.focus();
                }
            }, 350);
        },

        get titleLength() {
            return (this.studio.meta_title || '').length;
        },

        get descLength() {
            return (this.studio.meta_description || '').length;
        },

        get hasKwInTitle() {
            const kw = (this.studio.focus_keyword || '').toLowerCase().trim();
            if (!kw) return false;
            const title = (this.studio.meta_title || this.studio.title || '').toLowerCase();
            return title.includes(kw);
        },

        get hasKwInDesc() {
            const kw = (this.studio.focus_keyword || '').toLowerCase().trim();
            if (!kw) return false;
            const desc = (this.studio.meta_description || '').toLowerCase();
            return desc.includes(kw);
        },

        get hasKwInSlug() {
            const kw = (this.studio.focus_keyword || '').toLowerCase().trim();
            if (!kw) return false;
            const slug = (this.studio.slug || '').toLowerCase().replace(/[^a-z0-9]/g, ' ');
            const words = kw.replace(/[^a-z0-9]/g, ' ').split(/\s+/).filter(w => w.length > 2);
            if (words.length === 0) return true;
            return words.some(w => slug.includes(w));
        },

        get isSchemaValid() {
            const code = (this.studio.schema_markup || '').trim();
            if (!code) return true;
            try {
                JSON.parse(code);
                return true;
            } catch (e) {
                return false;
            }
        },

        get computedSeoScore() {
            let score = 0;
            const kw = (this.studio.focus_keyword || '').trim();
            if (kw.length > 0) score += 25;
            
            const tl = this.titleLength;
            if (tl >= 40 && tl <= 65) {
                score += 20;
            } else if (tl > 0) {
                score += 10;
            }

            if (this.hasKwInTitle) score += 15;

            const dl = this.descLength;
            if (dl >= 120 && dl <= 165) {
                score += 20;
            } else if (dl > 0) {
                score += 10;
            }

            if (this.hasKwInDesc) score += 10;
            if (this.hasKwInSlug) score += 5;
            if (this.isSchemaValid && (this.studio.schema_markup || '').trim().length > 0) score += 5;

            return Math.min(100, score);
        },

        get seoScoreRating() {
            const s = this.computedSeoScore;
            if (s >= 75) return 'good';
            if (s >= 45) return 'ok';
            return 'poor';
        },

        insertTemplate(templateName) {
            const title = this.studio.title || 'Cleanroom Hard Drive & SSD Data Recovery';
            const url = this.studio.url || 'https://lifeguarddatarecovery.com';
            const desc = this.studio.meta_description || this.studio.title || 'Professional data recovery services in Dubai & across UAE.';
            let obj = null;

            if (templateName === 'service') {
                obj = {};
                obj['@' + 'context'] = 'https://schema.org';
                obj['@' + 'type'] = 'Service';
                obj['name'] = title;
                obj['serviceType'] = 'Cleanroom Hard Drive, SSD, RAID & Server Data Recovery';
                obj['provider'] = {
                    '@type': 'LocalBusiness',
                    'name': 'LifeGuard Data Recovery',
                    'telephone': '+97143933082',
                    'url': 'https://lifeguarddatarecovery.com',
                    'address': {
                        '@type': 'PostalAddress',
                        'streetAddress': 'Bayat Center S25, Computer Street, Bur Dubai',
                        'addressLocality': 'Dubai',
                        'addressRegion': 'Dubai',
                        'addressCountry': 'AE'
                    }
                };
                obj['areaServed'] = [
                    { '@type': 'City', 'name': 'Dubai' },
                    { '@type': 'City', 'name': 'Abu Dhabi' },
                    { '@type': 'City', 'name': 'Sharjah' },
                    { '@type': 'Country', 'name': 'United Arab Emirates' }
                ];
                obj['description'] = desc;
                obj['url'] = url;
            } else if (templateName === 'article') {
                obj = {};
                obj['@' + 'context'] = 'https://schema.org';
                obj['@' + 'type'] = 'Article';
                obj['headline'] = title;
                obj['description'] = desc;
                obj['author'] = {
                    '@type': 'Organization',
                    'name': 'LifeGuard Data Recovery Lab Engineering Team'
                };
                obj['publisher'] = {
                    '@type': 'Organization',
                    'name': 'LifeGuard Data Recovery',
                    'logo': {
                        '@type': 'ImageObject',
                        'url': 'https://lifeguarddatarecovery.com/assets/images/logo.png'
                    }
                };
                obj['mainEntityOfPage'] = {
                    '@type': 'WebPage',
                    '@id': url
                };
            } else if (templateName === 'faq') {
                obj = {};
                obj['@' + 'context'] = 'https://schema.org';
                obj['@' + 'type'] = 'FAQPage';
                obj['mainEntity'] = [
                    {
                        '@type': 'Question',
                        'name': 'What is the No Data, No Charge guarantee?',
                        'acceptedAnswer': {
                            '@type': 'Answer',
                            'text': 'If our cleanroom engineers cannot recover your critical files, you pay nothing. Diagnostic evaluation is completely free.'
                        }
                    },
                    {
                        '@type': 'Question',
                        'name': 'How fast can you evaluate my failed hard drive or SSD in Dubai?',
                        'acceptedAnswer': {
                            '@type': 'Answer',
                            'text': 'Emergency diagnostic triage is performed within 2 to 4 hours in our ISO-5 Class 100 cleanroom lab in Bur Dubai or Al Barsha.'
                        }
                    }
                ];
            } else if (templateName === 'localbusiness') {
                obj = {};
                obj['@' + 'context'] = 'https://schema.org';
                obj['@' + 'type'] = 'LocalBusiness';
                obj['name'] = 'LifeGuard Data Recovery';
                obj['description'] = desc;
                obj['url'] = 'https://lifeguarddatarecovery.com';
                obj['telephone'] = '+97143933082';
                obj['priceRange'] = '$$';
                obj['address'] = {
                    '@type': 'PostalAddress',
                    'streetAddress': 'Bayat Center S25, Computer Street, Bur Dubai',
                    'addressLocality': 'Dubai',
                    'addressRegion': 'Dubai',
                    'postalCode': '00000',
                    'addressCountry': 'AE'
                };
                obj['geo'] = {
                    '@type': 'GeoCoordinates',
                    'latitude': 25.2532,
                    'longitude': 55.2974
                };
                obj['openingHoursSpecification'] = {
                    '@type': 'OpeningHoursSpecification',
                    'dayOfWeek': ['Monday','Tuesday','Wednesday','Thursday','Friday','Saturday'],
                    'opens': '09:00',
                    'closes': '21:00'
                };
            }

            if (obj) {
                this.studio.schema_markup = JSON.stringify(obj, null, 2);
            }
        },

        formatJson() {
            const raw = (this.studio.schema_markup || '').trim();
            if (!raw) return;
            try {
                const parsed = JSON.parse(raw);
                this.studio.schema_markup = JSON.stringify(parsed, null, 2);
                this.saveSuccessMsg = 'JSON formatting verified successfully.';
                setTimeout(() => { if (this.saveSuccessMsg.includes('JSON')) this.saveSuccessMsg = ''; }, 3000);
            } catch (err) {
                this.saveErrorMsg = 'Invalid JSON: ' + err.message;
            }
        },

        async saveStudioSeo() {
            this.isSaving = true;
            this.saveSuccessMsg = '';
            this.saveErrorMsg = '';

            const endpoint = this.studio.quick_seo_url;
            if (!endpoint) {
                this.saveErrorMsg = 'Quick SEO endpoint is missing for this item.';
                this.isSaving = false;
                return;
            }

            try {
                const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '';
                const response = await fetch(endpoint, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': csrfToken
                    },
                    body: JSON.stringify({
                        focus_keyword: this.studio.focus_keyword,
                        meta_title: this.studio.meta_title,
                        meta_description: this.studio.meta_description,
                        schema_markup: this.studio.schema_markup
                    })
                });

                const data = await response.json();

                if (response.ok && data.success) {
                    this.saveSuccessMsg = '✓ Focus Keyword, Meta Description & Schema Code saved successfully!';
                    
                    if (this.items[this.selectedKey]) {
                        this.items[this.selectedKey].focus_keyword = this.studio.focus_keyword;
                        this.items[this.selectedKey].meta_title = this.studio.meta_title;
                        this.items[this.selectedKey].meta_description = this.studio.meta_description;
                        this.items[this.selectedKey].schema_markup = this.studio.schema_markup;
                        this.items[this.selectedKey].seo_score = data.seo_score ?? this.computedSeoScore;
                        this.items[this.selectedKey].seo_rating = data.seo_rating ?? this.seoScoreRating;
                    }

                    const row = document.getElementById('seo-row-' + this.selectedKey);
                    if (row) {
                        const kwCell = row.querySelector('.seo-kw-cell');
                        if (kwCell) {
                            if (this.studio.focus_keyword && this.studio.focus_keyword.trim()) {
                                kwCell.innerHTML = '<span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-[11px] font-bold bg-purple-50 text-purple-700 border border-purple-200"><i data-lucide="tag" class="w-3 h-3 text-purple-500"></i><span>' + this.escapeHtml(this.studio.focus_keyword) + '</span></span>';
                            } else {
                                kwCell.innerHTML = '<button type="button" class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-[10px] font-bold bg-rose-50 text-rose-700 border border-dashed border-rose-300">+ Write Keyword</button>';
                            }
                        }
                    }

                    if (window.lucide && typeof window.lucide.createIcons === 'function') {
                        window.lucide.createIcons();
                    }

                    setTimeout(() => {
                        this.saveSuccessMsg = '';
                    }, 4500);
                } else {
                    this.saveErrorMsg = data.message || 'Failed to save changes. Please try again.';
                }
            } catch (err) {
                this.saveErrorMsg = 'Network error saving SEO data: ' + err.message;
            } finally {
                this.isSaving = false;
            }
        },

        escapeHtml(str) {
            return (str || '').replace(/[&<>"']/g, function(m) {
                return { '&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;', "'": '&#39;' }[m];
            });
        }
    };
}
window.dashboardSeoStudio = dashboardSeoStudio;
</script>
@endpush
