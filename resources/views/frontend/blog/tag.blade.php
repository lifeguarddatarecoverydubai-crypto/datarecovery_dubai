@extends('layouts.app')

@section('seo_meta')
    <title>#{{ $tag->name }} - {{ config('app.name', 'LifeGuard Data Recovery') }}</title>
    <meta name="description" content="Browse articles tagged with #{{ $tag->name }}">
    <link rel="canonical" href="{{ $posts->currentPage() > 1 ? $posts->url($posts->currentPage()) : route('tag.show', $tag->slug) }}">
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
    <div class="border-b border-slate-200 pb-8 space-y-2">
        <span class="text-xs font-bold uppercase tracking-widest text-brand-600">Tag Archive</span>
        <h1 class="text-3xl sm:text-4xl font-extrabold text-slate-900 tracking-tight">#{{ $tag->name }}</h1>
        @if($tag->description)
            <p class="text-xs sm:text-sm text-slate-500 max-w-2xl">{{ $tag->description }}</p>
        @endif
    </div>

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
                No articles tagged with #{{ $tag->name }} yet.
            </div>
        @endforelse
    </div>

    @if($posts->hasPages())
        <div class="pt-6">
            {{ $posts->links() }}
        </div>
    @endif
</div>
@endsection
