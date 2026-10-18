@extends('layouts.app')

@section('seo_meta')
    <!-- Primary Meta Tags -->
    @php
        $fullTitle = \App\Support\SeoHelper::formatTitle($post->seo_title, true);
        $canonicalUrl = \App\Support\SeoHelper::canonicalFor($post);
    @endphp
    <title>{{ $fullTitle }}</title>
    <meta name="title" content="{{ $fullTitle }}">
    <meta name="description" content="{{ $post->seo_description }}">
    @if($post->meta_keywords)
        <meta name="keywords" content="{{ $post->meta_keywords }}">
    @endif
    <link rel="canonical" href="{{ $canonicalUrl }}">
    <meta name="robots" content="{{ $post->is_indexable ? 'index, follow' : 'noindex, nofollow' }}">

    <!-- Open Graph / Facebook -->
    <meta property="og:type" content="article">
    <meta property="og:url" content="{{ url()->current() }}">
    <meta property="og:title" content="{{ $post->og_title ?: $post->seo_title }}">
    <meta property="og:description" content="{{ $post->og_description ?: $post->seo_description }}">
    @if($post->seo_image)
        <meta property="og:image" content="{{ $post->seo_image }}">
    @endif
    <meta property="og:site_name" content="{{ config('app.name', 'LifeGuard Data Recovery') }}">
    @if($post->published_at)
        <meta property="article:published_time" content="{{ $post->published_at->toIso8601String() }}">
    @endif

    <!-- Twitter -->
    <meta name="twitter:card" content="summary_large_image">
    <meta name="twitter:url" content="{{ url()->current() }}">
    <meta name="twitter:title" content="{{ $post->og_title ?: $post->seo_title }}">
    <meta name="twitter:description" content="{{ $post->og_description ?: $post->seo_description }}">
    @if($post->seo_image)
        <meta name="twitter:image" content="{{ $post->seo_image }}">
    @endif

    <!-- Schema.org BlogPosting / Article JSON-LD -->
    <script type="application/ld+json">
    {!! json_encode(\App\Services\SchemaService::getArticleSchema($post), JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT) !!}
    </script>

    <!-- Schema.org BreadcrumbList JSON-LD -->
    <script type="application/ld+json">
    {!! json_encode(\App\Services\SchemaService::getBreadcrumbSchema([
        'Home' => route('home'),
        'Articles' => route('blog.index'),
        $post->title => route('post.show', $post->slug),
    ]), JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT) !!}
    </script>

    <!-- Custom Schema.org JSON-LD (from Dashboard) -->
    <x-schema-json-ld :schema="$post->schema_markup" />
@endsection

@section('content')
<div class="py-12 bg-slate-50 min-h-screen">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <!-- Breadcrumbs -->
        <nav class="flex items-center gap-2 text-xs font-semibold text-slate-500 mb-8 flex-wrap" aria-label="Breadcrumb">
            <a href="{{ route('home') }}" class="hover:text-brand-600 transition">Home</a>
            <i data-lucide="chevron-right" class="w-3.5 h-3.5 text-slate-400"></i>
            <a href="{{ route('blog.index') }}" class="hover:text-brand-600 transition">Articles</a>
            @if($post->categories->first())
                <i data-lucide="chevron-right" class="w-3.5 h-3.5 text-slate-400"></i>
                <a href="{{ route('category.show', $post->categories->first()->slug) }}" class="text-brand-600 hover:underline">
                    {{ $post->categories->first()->name }}
                </a>
            @endif
        </nav>

        <div class="grid grid-cols-1 lg:grid-cols-12 gap-8 lg:gap-12 items-start">
            
            <!-- Left Main Column: Article Content & Metadata (8 cols) -->
            <div class="lg:col-span-8 space-y-10">
                <article class="bg-white rounded-3xl p-6 sm:p-10 border border-slate-200/90 shadow-sm space-y-8">
                    <!-- Article Categories & Badges -->
                    <header class="space-y-6">
                        <div class="flex items-center gap-2 flex-wrap">
                            @foreach($post->categories as $c)
                                <a href="{{ route('category.show', $c->slug) }}" class="px-3 py-1 rounded-full text-xs font-bold uppercase tracking-wider bg-brand-50 text-brand-700 hover:bg-brand-100 transition">
                                    {{ $c->name }}
                                </a>
                            @endforeach
                        </div>

                        <h1 class="text-2xl sm:text-3xl lg:text-4xl xl:text-5xl font-black text-slate-900 tracking-tight leading-tight">
                            {{ $post->title }}
                        </h1>

                        @if($post->excerpt)
                            <p class="text-base sm:text-lg text-slate-600 leading-relaxed font-normal">
                                {{ $post->excerpt }}
                            </p>
                        @endif

                        <!-- Publication Metadata Row -->
                        <div class="pt-6 border-t border-slate-100 flex items-center justify-between flex-wrap gap-4">
                            <div class="flex items-center gap-2.5 text-xs text-slate-500 font-medium">
                                <span class="inline-flex items-center gap-1.5">
                                    <i data-lucide="calendar" class="w-4 h-4 text-slate-400"></i>
                                    {{ $post->published_at ? $post->published_at->format('F d, Y') : $post->created_at->format('F d, Y') }}
                                </span>
                                <span>•</span>
                                <span class="inline-flex items-center gap-1.5">
                                    <i data-lucide="clock" class="w-4 h-4 text-slate-400"></i>
                                    {{ $post->reading_time }} min read
                                </span>
                            </div>

                            @auth
                                @if(auth()->user()->canManageContent())
                                    <a href="{{ route('admin.posts.edit', $post) }}" class="inline-flex items-center gap-1.5 px-3.5 py-1.5 rounded-lg bg-slate-100 hover:bg-slate-200 text-xs font-bold text-slate-800 transition">
                                        <i data-lucide="edit-3" class="w-3.5 h-3.5"></i> Edit Post
                                    </a>
                                @endif
                            @endauth
                        </div>
                    </header>

                    <!-- Featured Image -->
                    @if($post->featured_image_url)
                        <div class="rounded-2xl overflow-hidden aspect-[21/9] sm:aspect-[16/8] shadow-md bg-slate-100">
                            <img src="{{ $post->featured_image_url }}" 
                                 alt="{{ $post->title }}" 
                                 width="1200" 
                                 height="600" 
                                 loading="eager" 
                                 fetchpriority="high" 
                                 class="w-full h-full object-cover">
                        </div>

                    @endif

                    <!-- Article WYSIWYG Content Body -->
                    <div class="prose prose-slate prose-lg max-w-none py-4 leading-relaxed">
                        {!! $post->content !!}
                    </div>

                    <!-- Tags List -->
                    @if($post->tags->count())
                        <div class="pt-8 border-t border-slate-200 space-y-3">
                            <span class="text-xs font-bold uppercase tracking-wider text-slate-400">Tagged with</span>
                            <div class="flex flex-wrap gap-2">
                                @foreach($post->tags as $t)
                                    <a href="{{ route('tag.show', $t->slug) }}" class="px-3 py-1 rounded-xl bg-slate-100 text-xs font-medium text-slate-700 hover:bg-brand-50 hover:text-brand-600 transition">
                                        #{{ $t->name }}
                                    </a>
                                @endforeach
                            </div>
                        </div>
                    @endif
                </article>

                <!-- High-Impact Cleanroom Data Recovery Callout (Contextual Internal Link) -->
                @if(isset($targetedService) && $targetedService)
                    <div class="p-6 sm:p-8 rounded-3xl bg-gradient-to-br from-slate-950 via-slate-900 to-sky-950 text-white shadow-xl border border-sky-500/30 space-y-5">
                        <div class="flex flex-wrap items-center justify-between gap-3 border-b border-white/10 pb-4">
                            <div class="flex items-center gap-2">
                                <span class="inline-flex items-center px-2.5 py-1 rounded-full text-[10px] font-bold bg-sky-500/20 text-sky-300 border border-sky-400/30">
                                    <span class="w-1.5 h-1.5 rounded-full bg-sky-400 animate-pulse mr-1.5"></span> EMERGENCY LAB TRIAGE
                                </span>
                                <span class="text-xs text-slate-300 font-medium">ISO-5 Class 100 Cleanroom &bull; PC-3000 Hardware Diagnostics</span>
                            </div>
                            <span class="text-xs font-bold text-sky-400">24/7 Critical Recovery Hotline</span>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-12 gap-6 items-center">
                            <div class="md:col-span-8 space-y-2">
                                <h3 class="text-lg sm:text-xl font-bold font-display text-white">
                                    Facing Storage Media Failure? Lab Diagnostics for <span class="text-sky-400">{{ $targetedService->title }}</span>
                                </h3>
                                <p class="text-xs sm:text-sm text-slate-300 leading-relaxed">
                                    Don't risk permanent data loss with unverified DIY utilities. Our certified engineers safely recover data from clicking mechanical drives, damaged flash NAND, dropped disks, formatted volumes, and crashed RAID arrays.
                                </p>
                            </div>

                            <div class="md:col-span-4 flex flex-col sm:flex-row md:flex-col gap-2.5">
                                <a href="{{ route('service.show', $targetedService->slug) }}" class="w-full py-3 px-4 rounded-xl font-bold text-xs bg-sky-500 hover:bg-sky-400 text-slate-950 text-center shadow-lg shadow-sky-500/20 transition flex items-center justify-center gap-2">
                                    <span>Explore {{ $targetedService->title }}</span>
                                    <i data-lucide="arrow-right" class="w-3.5 h-3.5"></i>
                                </a>
                                <a href="tel:+97143933082" class="w-full py-2.5 px-4 rounded-xl font-bold text-xs bg-emerald-600 hover:bg-emerald-500 text-white text-center transition flex items-center justify-center gap-2">
                                    <i data-lucide="phone-call" class="w-3.5 h-3.5"></i>
                                    <span>Call Lab: +971 43 933 082</span>
                                </a>
                            </div>
                        </div>
                    </div>
                @endif

                <!-- Prev / Next Navigation -->
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 text-xs">
                    @if($previousPost)
                        <a href="{{ route('post.show', $previousPost->slug) }}" class="p-4 rounded-2xl bg-white border border-slate-200 hover:border-brand-500 transition group space-y-1 block shadow-xs">
                            <span class="text-slate-400 flex items-center gap-1"><i data-lucide="arrow-left" class="w-3.5 h-3.5"></i> Previous Article</span>
                            <span class="font-bold text-slate-900 group-hover:text-brand-600 transition line-clamp-1 block">{{ $previousPost->title }}</span>
                        </a>
                    @else
                        <div></div>
                    @endif

                    @if($nextPost)
                        <a href="{{ route('post.show', $nextPost->slug) }}" class="p-4 rounded-2xl bg-white border border-slate-200 hover:border-brand-500 transition group space-y-1 block sm:text-right shadow-xs">
                            <span class="text-slate-400 flex items-center justify-end gap-1">Next Article <i data-lucide="arrow-right" class="w-3.5 h-3.5"></i></span>
                            <span class="font-bold text-slate-900 group-hover:text-brand-600 transition line-clamp-1 block">{{ $nextPost->title }}</span>
                        </a>
                    @endif
                </div>

                <!-- Related Posts Section -->
                @if($relatedPosts->count())
                    <div class="bg-white rounded-3xl p-6 sm:p-8 border border-slate-200 shadow-sm space-y-6">
                        <div class="flex items-center justify-between border-b border-slate-100 pb-4">
                            <div>
                                <h3 class="text-lg sm:text-xl font-bold text-slate-900">Related Articles & Guides</h3>
                                <p class="text-xs text-slate-500 mt-0.5">Explore more technical data recovery case studies, SSD firmware repair manuals, and cleanroom lab procedures</p>
                            </div>
                            <a href="{{ route('blog.index') }}" class="text-xs font-bold text-brand-600 hover:text-brand-700 hidden sm:inline-flex items-center gap-1">
                                <span>All Articles</span>
                                <i data-lucide="arrow-right" class="w-3.5 h-3.5"></i>
                            </a>
                        </div>
                        <div class="grid grid-cols-1 sm:grid-cols-3 gap-5">
                            @foreach($relatedPosts as $rPost)
                                <article class="bg-slate-50 rounded-2xl border border-slate-100 overflow-hidden shadow-xs hover:shadow-md transition group flex flex-col justify-between">
                                    <a href="{{ route('post.show', $rPost->slug) }}" class="aspect-video block overflow-hidden bg-slate-200">
                                        @if($rPost->featured_image_url)
                                             <img src="{{ $rPost->featured_image_url }}" 
                                                  alt="{{ $rPost->title }}" 
                                                  width="400" 
                                                  height="225" 
                                                  loading="lazy" 
                                                  class="w-full h-full object-cover group-hover:scale-105 transition">
                                        @else

                                            <div class="w-full h-full flex items-center justify-center text-slate-300">
                                                <i data-lucide="image" class="w-8 h-8"></i>
                                            </div>
                                        @endif
                                    </a>
                                    <div class="p-4 space-y-2 flex-1 flex flex-col justify-between">
                                        <h4 class="text-xs font-bold text-slate-900 group-hover:text-brand-600 transition line-clamp-2">
                                            <a href="{{ route('post.show', $rPost->slug) }}">{{ $rPost->title }}</a>
                                        </h4>
                                        <span class="text-[10px] text-slate-400">{{ $rPost->published_at ? $rPost->published_at->format('M d, Y') : '' }}</span>
                                    </div>
                                </article>
                            @endforeach
                        </div>
                    </div>
                @endif
            </div>

            <!-- Right Sticky Column: Form & Other Services (4 cols) -->
            <aside class="lg:col-span-4 lg:sticky lg:top-24">
                @include('frontend.partials.service-sidebar', [
                    'post' => $post,
                    'services' => $services ?? null,
                    'subject' => 'Data Recovery Inquiry from Article: ' . $post->title,
                    'whatsappMessage' => 'Hello LifeGuard Data Recovery, I am reading "' . $post->title . '" and would like to request emergency data recovery assistance.'
                ])
            </aside>

        </div>
    </div>
</div>
@endsection
