@extends('layouts.app')

@section('seo_meta')
    <title>{{ $search ? "Search results for '{$search}'" : "Data Recovery Knowledge Base & Technical Forensic Guides" }} - {{ config('app.name', 'LifeGuard Data Recovery') }}</title>
    <meta name="description" content="Explore expert hard drive recovery guides, SSD NAND forensics, RAID array reconstruction, and cleanroom lab methodologies by LifeGuard Data Recovery Dubai.">
    <link rel="canonical" href="{{ $posts->currentPage() > 1 ? $posts->url($posts->currentPage()) : route('blog.index') }}">
    @if($posts->previousPageUrl())
        <link rel="prev" href="{{ $posts->previousPageUrl() }}">
    @endif
    @if($posts->nextPageUrl())
        <link rel="next" href="{{ $posts->nextPageUrl() }}">
    @endif
@endsection

@section('content')
<div class="py-12 max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-12">
    <!-- Header -->
    <div class="border-b border-slate-200 pb-8 flex flex-col md:flex-row md:items-end md:justify-between gap-4">
        <div>
            <span class="text-xs font-bold uppercase tracking-widest text-sky-600 block mb-1">Technical Research & Case Studies</span>
            <h1 class="text-3xl sm:text-4xl font-extrabold text-slate-900 tracking-tight font-display">
                @if($search)
                    Search results for "{{ $search }}"
                @else
                    Data Recovery Guides & Knowledge Base
                @endif
            </h1>
            <p class="text-xs sm:text-sm text-slate-500 mt-2">Expert guides on clicking hard drives, SSD firmware failures, RAID array crashes, and forensic data recovery.</p>
        </div>

        <!-- Search Bar -->
        <form action="{{ route('blog.index') }}" method="GET" class="relative w-full md:w-80">
            <input type="text" name="q" value="{{ $search }}" placeholder="Search articles..."
                   class="w-full pl-9 pr-4 py-2 text-xs rounded-xl border border-slate-200 focus:outline-none focus:ring-2 focus:ring-brand-500/20 focus:border-brand-500">
            <i data-lucide="search" class="w-4 h-4 text-slate-400 absolute left-3 top-2.5"></i>
        </form>
    </div>

    <!-- Category & Tag Filter Navigation (Internal Linking Hub) -->
    @if(isset($categories) && $categories->isNotEmpty())
        <div class="space-y-4">
            <div class="flex items-center justify-between">
                <span class="text-xs font-bold uppercase tracking-wider text-slate-500">Filter by Topic:</span>
                <span class="text-xs text-slate-400 font-medium">{{ $categories->count() }} Categories</span>
            </div>
            <div class="flex flex-wrap items-center gap-2">
                <a href="{{ route('blog.index') }}" 
                   class="px-3.5 py-1.5 rounded-full text-xs font-bold transition shadow-xs {{ empty($search) ? 'bg-sky-600 text-white shadow-sky-600/20' : 'bg-white text-slate-700 border border-slate-200 hover:border-sky-500 hover:text-sky-600' }}">
                    All Articles ({{ \App\Models\Post::where('status', 'published')->count() }})
                </a>
                @foreach($categories as $cat)
                    <a href="{{ route('category.show', $cat->slug) }}" 
                       class="px-3.5 py-1.5 rounded-full text-xs font-semibold bg-white text-slate-700 border border-slate-200/90 hover:border-sky-500 hover:text-sky-600 hover:bg-sky-50/50 transition shadow-xs flex items-center gap-1.5">
                        <span>{{ $cat->name }}</span>
                        @if($cat->posts_count > 0)
                            <span class="text-[10px] px-1.5 py-0.2 rounded-full bg-slate-100 text-slate-600 font-bold">{{ $cat->posts_count }}</span>
                        @endif
                    </a>
                @endforeach
            </div>

            @if(isset($tags) && $tags->where('posts_count', '>', 1)->isNotEmpty())
                <div class="pt-2 flex flex-wrap items-center gap-1.5 text-xs text-slate-500">
                    <span class="font-bold text-slate-400 text-[11px] uppercase mr-1">Popular Topics:</span>
                    @foreach($tags->where('posts_count', '>', 1)->take(12) as $popularTag)
                        <a href="{{ route('tag.show', $popularTag->slug) }}" class="text-[11px] text-slate-600 hover:text-sky-600 hover:underline">
                            #{{ $popularTag->name }}
                        </a>
                        @if(! $loop->last) <span class="text-slate-300">&bull;</span> @endif
                    @endforeach
                </div>
            @endif
        </div>
    @endif

    <!-- Posts Grid -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-8">
        @forelse($posts as $post)
            <article class="bg-white rounded-2xl border border-slate-200/80 overflow-hidden shadow-xs hover:shadow-md transition-all flex flex-col justify-between group">
                <a href="{{ route('post.show', $post->slug) }}" class="aspect-[16/9] block relative overflow-hidden bg-slate-100">
                    @if($post->featured_image_url)
                        <img src="{{ $post->featured_image_url }}" 
                             alt="{{ $post->title }}" 
                             width="600" 
                             height="338" 
                             loading="lazy" 
                             class="w-full h-full object-cover group-hover:scale-105 transition duration-300">
                    @else

                        <div class="w-full h-full flex items-center justify-center text-slate-300">
                            <i data-lucide="image" class="w-10 h-10"></i>
                        </div>
                    @endif
                    @if($post->categories->first())
                        <span class="absolute top-3 left-3 px-2.5 py-1 rounded-md text-[10px] font-bold bg-white/95 text-slate-800 shadow-sm">
                            {{ $post->categories->first()->name }}
                        </span>
                    @endif
                </a>

                <div class="p-6 flex-1 flex flex-col justify-between space-y-4">
                    <div>
                        <div class="flex items-center gap-2 text-[11px] text-slate-400 mb-1.5">
                            <span>{{ $post->published_at ? $post->published_at->format('M d, Y') : $post->created_at->format('M d, Y') }}</span>
                            <span>•</span>
                            <span>{{ $post->reading_time }} min read</span>
                        </div>
                        <h2 class="text-base font-bold text-slate-900 group-hover:text-brand-600 transition line-clamp-2">
                            <a href="{{ route('post.show', $post->slug) }}">{{ $post->title }}</a>
                        </h2>
                        <p class="text-xs text-slate-500 line-clamp-3 mt-2 leading-relaxed">
                            {{ $post->seo_description }}
                        </p>
                    </div>

                    <div class="pt-4 border-t border-slate-100 flex items-center justify-end">
                        <a href="{{ route('post.show', $post->slug) }}" class="text-xs font-semibold text-brand-600 hover:text-brand-700">
                            Read →
                        </a>
                    </div>
                </div>
            </article>
        @empty
            <div class="col-span-full py-16 text-center text-slate-400 bg-white rounded-3xl border border-slate-200">
                <i data-lucide="inbox" class="w-10 h-10 mx-auto mb-3 opacity-40"></i>
                <p class="text-sm font-semibold text-slate-700">No articles found.</p>
                @if($search)
                    <p class="text-xs text-slate-400 mt-1">Try another search keyword.</p>
                    <a href="{{ route('blog.index') }}" class="mt-4 inline-block px-4 py-2 bg-slate-100 rounded-xl text-xs font-bold text-slate-700 hover:bg-slate-200">Clear Search</a>
                @endif
            </div>
        @endforelse
    </div>

    <!-- Pagination -->
    @if($posts->hasPages())
        <div class="pt-6">
            {{ $posts->links() }}
        </div>
    @endif
</div>
@endsection
