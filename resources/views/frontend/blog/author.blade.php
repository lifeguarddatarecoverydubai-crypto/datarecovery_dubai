@extends('layouts.app')

@section('seo_meta')
    <title>{{ $author->name }}'s Articles - {{ config('app.name', 'LifeGuard Data Recovery') }}</title>
    <meta name="description" content="Read articles written by {{ $author->name }} on {{ config('app.name') }}.">
    <link rel="canonical" href="{{ request()->query('page') && request()->query('page') > 1 ? route('author.show', ['slug' => $author->slug, 'page' => request()->query('page')]) : route('author.show', $author->slug) }}">
    @if($posts->previousPageUrl())
        <link rel="prev" href="{{ $posts->previousPageUrl() }}">
    @endif
    @if($posts->nextPageUrl())
        <link rel="next" href="{{ $posts->nextPageUrl() }}">
    @endif
@endsection

@section('content')
<div class="py-12 max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-12">
    <!-- Author Profile Card Header -->
    <div class="bg-white rounded-3xl p-8 border border-slate-200/80 shadow-xs flex flex-col sm:flex-row items-center gap-6 text-center sm:text-left">
        <img src="{{ $author->avatar_url }}" alt="{{ $author->name }}" width="96" height="96" loading="eager" class="w-24 h-24 rounded-full object-cover border-4 border-brand-50 shadow-md">
        <div class="space-y-2">
            <span class="text-xs font-bold uppercase tracking-widest text-brand-600 block">Author Profile</span>
            <h1 class="text-3xl font-black text-slate-900 tracking-tight">{{ $author->name }}</h1>
            <p class="text-sm text-slate-600 max-w-2xl leading-relaxed">
                {{ $author->bio ?: 'Writer and publisher on ' . config('app.name') . '.' }}
            </p>
        </div>
    </div>

    <!-- Posts Grid -->
    <div class="space-y-6">
        <h2 class="text-xl font-bold text-slate-900 border-b border-slate-200 pb-3">Articles by {{ $author->name }}</h2>

        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-8">
            @forelse($posts as $post)
                <article class="bg-white rounded-2xl border border-slate-200/80 overflow-hidden shadow-xs hover:shadow-md transition-all flex flex-col justify-between group">
                    <a href="{{ route('post.show', $post->slug) }}" class="aspect-[16/9] block relative overflow-hidden bg-slate-100">
                        @if($post->featured_image_url)
                            <img src="{{ $post->featured_image_url }}" alt="{{ $post->title }}" width="600" height="338" loading="lazy" class="w-full h-full object-cover group-hover:scale-105 transition duration-300">
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
                            <h3 class="text-base font-bold text-slate-900 group-hover:text-brand-600 transition line-clamp-2">
                                <a href="{{ route('post.show', $post->slug) }}">{{ $post->title }}</a>
                            </h3>
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
                    No articles published by this author yet.
                </div>
            @endforelse
        </div>

        @if($posts->hasPages())
            <div class="pt-6">
                {{ $posts->links() }}
            </div>
        @endif
    </div>
</div>
@endsection
