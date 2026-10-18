@extends('layouts.admin')

@section('title', 'All Posts')

@section('content')
<div class="space-y-6">
    <!-- Header -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h1 class="text-2xl font-black text-slate-900 tracking-tight flex items-center gap-2">
                <span>Posts</span>
                <span class="text-xs font-semibold px-2.5 py-0.5 rounded-full bg-slate-200 text-slate-700">{{ $counts['all'] }}</span>
            </h1>
            <p class="text-xs text-slate-500 mt-1">Manage, write and publish blog articles</p>
        </div>
        <a href="{{ route('admin.posts.create') }}" class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl bg-brand-600 hover:bg-brand-700 text-white text-xs font-bold shadow-md shadow-brand-500/20 transition self-start sm:self-auto">
            <i data-lucide="plus" class="w-4 h-4"></i> Add New Post
        </a>
    </div>

    <!-- Status Filter Tabs -->
    <div class="flex items-center gap-2 border-b border-slate-200 pb-3 text-xs font-medium">
        <a href="{{ route('admin.posts.index') }}" 
           class="px-3 py-1.5 rounded-lg transition {{ empty($status) ? 'bg-slate-900 text-white font-semibold' : 'text-slate-600 hover:bg-slate-200/60' }}">
            All ({{ $counts['all'] }})
        </a>
        <a href="{{ route('admin.posts.index', ['status' => 'published']) }}" 
           class="px-3 py-1.5 rounded-lg transition {{ $status === 'published' ? 'bg-emerald-600 text-white font-semibold' : 'text-slate-600 hover:bg-slate-200/60' }}">
            Published ({{ $counts['published'] }})
        </a>
        <a href="{{ route('admin.posts.index', ['status' => 'draft']) }}" 
           class="px-3 py-1.5 rounded-lg transition {{ $status === 'draft' ? 'bg-amber-600 text-white font-semibold' : 'text-slate-600 hover:bg-slate-200/60' }}">
            Drafts ({{ $counts['draft'] }})
        </a>
    </div>

    <!-- Filter & Search Toolbar -->
    <div class="bg-white p-4 rounded-2xl border border-slate-200/80 shadow-sm flex flex-col md:flex-row items-center justify-between gap-4">
        <form action="{{ route('admin.posts.index') }}" method="GET" class="w-full flex flex-col sm:flex-row items-center gap-3">
            @if($status)
                <input type="hidden" name="status" value="{{ $status }}">
            @endif

            <!-- Category Filter -->
            <select name="category_id" class="w-full sm:w-48 text-xs rounded-xl border border-slate-200 py-2 px-3 focus:outline-none focus:ring-2 focus:ring-brand-500/20">
                <option value="">All Categories</option>
                @foreach($categories as $cat)
                    <option value="{{ $cat->id }}" {{ (string)$categoryId === (string)$cat->id ? 'selected' : '' }}>{{ $cat->name }}</option>
                @endforeach
            </select>

            <!-- Search Input -->
            <div class="relative w-full sm:w-64">
                <input type="text" name="search" value="{{ $search }}" placeholder="Search posts..." 
                       class="w-full pl-8 pr-3 py-2 text-xs rounded-xl border border-slate-200 focus:outline-none focus:ring-2 focus:ring-brand-500/20">
                <i data-lucide="search" class="w-3.5 h-3.5 text-slate-400 absolute left-2.5 top-2.5"></i>
            </div>

            <div class="flex items-center gap-2 w-full sm:w-auto">
                <button type="submit" class="px-4 py-2 bg-slate-100 hover:bg-slate-200 text-slate-700 text-xs font-semibold rounded-xl transition">
                    Filter
                </button>
                @if($search || $categoryId)
                    <a href="{{ route('admin.posts.index') }}" class="text-xs text-rose-600 hover:underline">Reset</a>
                @endif
            </div>
        </form>
    </div>

    <!-- Posts Table -->
    <div class="bg-white rounded-2xl border border-slate-200/80 shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse text-xs">
                <thead>
                    <tr class="bg-slate-50 border-b border-slate-200 text-slate-500 font-bold uppercase tracking-wider text-[10px]">
                        <th class="py-3.5 px-4">Title & Slug</th>
                        <th class="py-3.5 px-4">Focus Keyword</th>
                        <th class="py-3.5 px-4">Yoast Signal</th>
                        <th class="py-3.5 px-4">Author</th>
                        <th class="py-3.5 px-4">Categories</th>
                        <th class="py-3.5 px-4">Date & Status</th>
                        <th class="py-3.5 px-4 text-right">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 text-slate-700">
                    @forelse($posts as $post)
                        @php
                            $score = $post->seo_score ?? 0;
                            $rating = $score >= 80 ? 'good' : ($score >= 50 ? 'ok' : 'poor');
                        @endphp
                        <tr id="seo-row-post-{{ $post->id }}" class="hover:bg-slate-50/80 transition group">
                            <!-- Title & Slug -->
                            <td class="py-4 px-4 max-w-sm">
                                <div class="font-bold text-slate-900 group-hover:text-brand-600 transition text-sm">
                                    <a href="{{ route('admin.posts.edit', $post) }}">{{ $post->title }}</a>
                                </div>
                                <div class="text-[11px] text-slate-400 flex items-center gap-1 mt-0.5 font-mono">
                                    <span>/{{ $post->slug }}</span>
                                </div>
                            </td>

                            <!-- Focus Keyword -->
                            <td class="py-4 px-4 seo-kw-cell">
                                @if($post->focus_keyword)
                                    <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-[11px] font-bold bg-purple-50 text-purple-700 border border-purple-200">
                                        <i data-lucide="tag" class="w-3 h-3 text-purple-500"></i>
                                        <span>{{ $post->focus_keyword }}</span>
                                    </span>
                                @else
                                    <button type="button" 
                                            @click="$dispatch('open-quick-seo', {
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
                                            class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-[10px] font-bold bg-rose-50 text-rose-700 border border-dashed border-rose-300 hover:bg-rose-100 transition">
                                        <i data-lucide="plus" class="w-3 h-3"></i>
                                        <span>+ Add Keyword</span>
                                    </button>
                                @endif
                            </td>

                            <!-- Yoast SEO Signal -->
                            <td class="py-4 px-4 seo-sig-cell">
                                <div class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-[11px] font-bold border {{ $rating === 'good' ? 'bg-emerald-50 text-emerald-700 border-emerald-200 shadow-xs' : ($rating === 'ok' ? 'bg-amber-50 text-amber-700 border-amber-200' : 'bg-rose-50 text-rose-700 border-rose-200') }}">
                                    <span class="w-2 h-2 rounded-full {{ $rating === 'good' ? 'bg-emerald-500 animate-pulse' : ($rating === 'ok' ? 'bg-amber-500' : 'bg-rose-500') }}"></span>
                                    <span>{{ $score }}/100</span>
                                    <span class="uppercase text-[9px] opacity-75">({{ $rating }})</span>
                                </div>
                            </td>

                            <!-- Author -->
                            <td class="py-4 px-4">
                                <div class="flex items-center gap-2">
                                    <img src="{{ $post->user->avatar_url }}" alt="{{ $post->user->name }}" class="w-6 h-6 rounded-full object-cover">
                                    <span class="font-medium text-slate-800">{{ $post->user->name ?? 'Deleted' }}</span>
                                </div>
                            </td>

                            <!-- Categories -->
                            <td class="py-4 px-4">
                                <div class="flex flex-wrap gap-1">
                                    @forelse($post->categories as $c)
                                        <span class="px-2 py-0.5 rounded-md bg-slate-100 text-slate-600 text-[10px] font-medium">{{ $c->name }}</span>
                                    @empty
                                        <span class="text-slate-400 italic text-[11px]">Uncategorized</span>
                                    @endforelse
                                </div>
                            </td>

                            <!-- Status & Date -->
                            <td class="py-4 px-4 whitespace-nowrap">
                                <div>
                                    @if($post->status === 'published')
                                        <span class="inline-flex items-center px-2 py-0.5 rounded text-[10px] font-semibold bg-emerald-50 text-emerald-700 border border-emerald-200">Published</span>
                                    @elseif($post->status === 'scheduled')
                                        <span class="inline-flex items-center px-2 py-0.5 rounded text-[10px] font-semibold bg-sky-50 text-sky-700 border border-sky-200">Scheduled</span>
                                    @else
                                        <span class="inline-flex items-center px-2 py-0.5 rounded text-[10px] font-semibold bg-amber-50 text-amber-700 border border-amber-200">Draft</span>
                                    @endif
                                </div>
                                <div class="text-[11px] text-slate-400 mt-1">
                                    {{ $post->published_at ? $post->published_at->format('M d, Y') : $post->created_at->format('M d, Y') }}
                                </div>
                            </td>

                            <!-- Actions -->
                            <td class="py-4 px-4 text-right whitespace-nowrap">
                                <div class="flex items-center justify-end gap-1.5">
                                    <button type="button" 
                                            @click="$dispatch('open-quick-seo', {
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
                                            class="px-2.5 py-1 rounded-lg text-purple-700 hover:text-purple-900 bg-purple-50 hover:bg-purple-100 text-[11px] font-bold border border-purple-200 transition flex items-center gap-1"
                                            title="Quick Edit Keyword & Signals">
                                        <i data-lucide="sparkles" class="w-3 h-3 text-purple-600"></i>
                                        <span>Quick SEO</span>
                                    </button>
                                    <a href="{{ route('post.show', $post->slug) }}" target="_blank" class="p-1.5 text-slate-400 hover:text-slate-700 hover:bg-slate-100 rounded-lg transition" title="View Public Post">
                                        <i data-lucide="external-link" class="w-4 h-4"></i>
                                    </a>
                                    <a href="{{ route('admin.posts.edit', $post) }}" class="p-1.5 text-slate-400 hover:text-brand-600 hover:bg-slate-100 rounded-lg transition" title="Edit Post">
                                        <i data-lucide="edit-3" class="w-4 h-4"></i>
                                    </a>
                                    <form action="{{ route('admin.posts.destroy', $post) }}" method="POST" onsubmit="return confirm('Are you sure you want to delete this post?');" class="inline">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="p-1.5 text-slate-400 hover:text-rose-600 hover:bg-rose-50 rounded-lg transition" title="Delete Post">
                                            <i data-lucide="trash-2" class="w-4 h-4"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="p-12 text-center text-slate-400">
                                <i data-lucide="file-question" class="w-8 h-8 mx-auto mb-2 opacity-40"></i>
                                No posts match your criteria.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($posts->hasPages())
            <div class="p-4 border-t border-slate-100">
                {{ $posts->links() }}
            </div>
        @endif
    </div>
</div>
@endsection
