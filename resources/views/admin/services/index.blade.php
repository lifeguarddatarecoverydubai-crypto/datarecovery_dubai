@extends('layouts.admin')

@section('title', 'Services Management')

@section('content')
<div class="space-y-6">

    <!-- Flash Notification -->
    @if(session('success'))
        <div class="p-4 rounded-2xl bg-emerald-600 text-white shadow-lg shadow-emerald-600/20 flex items-center justify-between">
            <div class="flex items-center gap-2 text-xs font-bold">
                <i data-lucide="check-circle" class="w-4 h-4"></i>
                <span>{{ session('success') }}</span>
            </div>
            <button type="button" onclick="this.parentElement.remove()" class="text-white/80 hover:text-white">&times;</button>
        </div>
    @endif

    <!-- Header -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h1 class="text-2xl font-black text-slate-900 tracking-tight flex items-center gap-2">
                <span>Data Recovery Services</span>
                <span class="text-xs font-semibold px-2.5 py-0.5 rounded-full bg-slate-200 text-slate-700">{{ $counts['all'] }}</span>
            </h1>
            <p class="text-xs text-slate-500 mt-1">Manage certified cleanroom data recovery services across Hard Drives, SSD, RAID arrays, NAS, CCTV, and mobile devices.</p>
        </div>
        <a href="{{ route('admin.services.create') }}" class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl bg-brand-600 hover:bg-brand-700 text-white text-xs font-bold shadow-md shadow-brand-500/20 transition self-start sm:self-auto">
            <i data-lucide="plus" class="w-4 h-4"></i> Add New Service
        </a>
    </div>

    <!-- Status Tabs -->
    <div class="flex items-center gap-2 border-b border-slate-200 pb-3 text-xs font-medium">
        <a href="{{ route('admin.services.index') }}" 
           class="px-3 py-1.5 rounded-lg transition {{ empty($status) ? 'bg-slate-900 text-white font-semibold' : 'text-slate-600 hover:bg-slate-200/60' }}">
            All ({{ $counts['all'] }})
        </a>
        <a href="{{ route('admin.services.index', ['status' => 'published']) }}" 
           class="px-3 py-1.5 rounded-lg transition {{ $status === 'published' ? 'bg-emerald-600 text-white font-semibold' : 'text-slate-600 hover:bg-slate-200/60' }}">
            Published ({{ $counts['published'] }})
        </a>
        <a href="{{ route('admin.services.index', ['status' => 'draft']) }}" 
           class="px-3 py-1.5 rounded-lg transition {{ $status === 'draft' ? 'bg-amber-600 text-white font-semibold' : 'text-slate-600 hover:bg-slate-200/60' }}">
            Drafts ({{ $counts['draft'] }})
        </a>
    </div>

    <!-- Search Toolbar -->
    <div class="bg-white p-4 rounded-2xl border border-slate-200/80 shadow-sm flex flex-col md:flex-row items-center justify-between gap-4">
        <form action="{{ route('admin.services.index') }}" method="GET" class="w-full flex flex-col sm:flex-row items-center gap-3">
            @if($status)
                <input type="hidden" name="status" value="{{ $status }}">
            @endif

            <div class="relative w-full sm:w-80">
                <input type="text" name="search" value="{{ $search }}" placeholder="Search services or keywords..." 
                       class="w-full pl-8 pr-3 py-2 text-xs rounded-xl border border-slate-200 focus:outline-none focus:ring-2 focus:ring-purple-500/20">
                <i data-lucide="search" class="w-3.5 h-3.5 text-slate-400 absolute left-2.5 top-2.5"></i>
            </div>

            <div class="flex items-center gap-2 w-full sm:w-auto">
                <button type="submit" class="px-4 py-2 bg-slate-100 hover:bg-slate-200 text-slate-700 text-xs font-semibold rounded-xl transition">
                    Filter
                </button>
                @if($search)
                    <a href="{{ route('admin.services.index') }}" class="text-xs text-rose-600 hover:underline">Reset</a>
                @endif
            </div>
        </form>
    </div>

    <!-- Services Table -->
    <div class="bg-white rounded-2xl border border-slate-200/80 shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse text-xs">
                <thead>
                    <tr class="bg-slate-50 border-b border-slate-200 text-slate-500 font-bold uppercase tracking-wider text-[10px]">
                        <th class="py-3.5 px-4">Service & Slug</th>
                        <th class="py-3.5 px-4">Focus Keyword</th>
                        <th class="py-3.5 px-4">SEO Signal</th>
                        <th class="py-3.5 px-4">Meta Description</th>
                        <th class="py-3.5 px-4">Schema Code</th>
                        <th class="py-3.5 px-4">Status</th>
                        <th class="py-3.5 px-4 text-right">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 text-slate-700">
                    @forelse($services as $service)
                        @php
                            $score = $service->seo_score ?? 0;
                            $rating = $score >= 80 ? 'good' : ($score >= 50 ? 'ok' : 'poor');
                            $hasDesc = !empty($service->meta_description);
                            $hasCustomSchema = !empty($service->schema_markup);
                        @endphp
                        <tr id="seo-row-service-{{ $service->id }}" class="hover:bg-slate-50/80 transition group">
                            <!-- Service Title & Summary -->
                            <td class="py-4 px-4 max-w-xs">
                                <div class="font-bold text-slate-900 group-hover:text-brand-600 transition text-sm flex items-center gap-2">
                                    <a href="{{ route('admin.services.edit', $service) }}">{{ $service->title }}</a>
                                    @if($service->is_featured)
                                        <span class="px-1.5 py-0.5 rounded text-[9px] font-bold bg-amber-100 text-amber-800">Featured</span>
                                    @endif
                                </div>
                                <div class="text-[11px] text-slate-400 flex items-center gap-1 mt-0.5 font-mono">
                                    <span>/service/{{ $service->slug }}</span>
                                </div>
                                @if($service->summary)
                                    <p class="text-slate-500 text-[11px] mt-1 line-clamp-1">{{ $service->summary }}</p>
                                @endif
                            </td>

                            <!-- Focus Keyword -->
                            <td class="py-4 px-4 seo-kw-cell">
                                @if($service->focus_keyword)
                                    <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-[11px] font-bold bg-purple-50 text-purple-700 border border-purple-200">
                                        <i data-lucide="tag" class="w-3 h-3 text-purple-500"></i>
                                        <span>{{ $service->focus_keyword }}</span>
                                    </span>
                                @else
                                    <button type="button" 
                                            @click="$dispatch('open-quick-seo', {
                                                type: 'service',
                                                endpoint: '{{ route('admin.services.quick-seo', $service) }}',
                                                id: {{ $service->id }},
                                                title: @js($service->title),
                                                slug: @js($service->slug),
                                                focus_keyword: @js($service->focus_keyword ?? ''),
                                                meta_title: @js($service->meta_title ?? ''),
                                                meta_description: @js($service->meta_description ?? ''),
                                                schema_markup: @js($service->schema_markup ?? ''),
                                                seo_score: {{ (int) ($service->seo_score ?? 0) }}
                                            })" 
                                            class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-[10px] font-bold bg-rose-50 text-rose-700 border border-dashed border-rose-300 hover:bg-rose-100 transition">
                                        <i data-lucide="plus" class="w-3 h-3"></i>
                                        <span>+ Add Keyword</span>
                                    </button>
                                @endif
                            </td>

                            <!-- Yoast SEO Score & Traffic Light -->
                            <td class="py-4 px-4 seo-sig-cell">
                                <div class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-[11px] font-bold border {{ $rating === 'good' ? 'bg-emerald-50 text-emerald-700 border-emerald-200 shadow-xs' : ($rating === 'ok' ? 'bg-amber-50 text-amber-700 border-amber-200' : 'bg-rose-50 text-rose-700 border-rose-200') }}">
                                    <span class="w-2 h-2 rounded-full {{ $rating === 'good' ? 'bg-emerald-500 animate-pulse' : ($rating === 'ok' ? 'bg-amber-500' : 'bg-rose-500') }}"></span>
                                    <span>{{ $score }}/100</span>
                                    <span class="uppercase text-[9px] opacity-75">({{ $rating }})</span>
                                </div>
                            </td>

                            <!-- Meta Description -->
                            <td class="py-4 px-4">
                                @if($hasDesc)
                                    <div class="flex items-center gap-1.5 text-emerald-700 text-xs font-semibold" title="{{ $service->meta_description }}">
                                        <i data-lucide="check-circle" class="w-3.5 h-3.5 text-emerald-600 shrink-0"></i>
                                        <span class="truncate max-w-[140px] text-slate-600 text-[11px] font-normal">{{ $service->meta_description }}</span>
                                    </div>
                                @else
                                    <span class="inline-flex items-center gap-1 text-[10px] font-bold text-rose-600 bg-rose-50 px-2 py-0.5 rounded-md border border-rose-200">
                                        <i data-lucide="alert-triangle" class="w-3 h-3"></i>
                                        <span>Missing Meta</span>
                                    </span>
                                @endif
                            </td>

                            <!-- Schema Code -->
                            <td class="py-4 px-4">
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
                            <td class="py-4 px-4">
                                @if($service->status === 'published')
                                    <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-[11px] font-bold bg-emerald-100 text-emerald-800">
                                        <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span> Published
                                    </span>
                                @else
                                    <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-[11px] font-bold bg-amber-100 text-amber-800">
                                        <span class="w-1.5 h-1.5 rounded-full bg-amber-500"></span> Draft
                                    </span>
                                @endif
                            </td>

                            <!-- Actions -->
                            <td class="py-4 px-4 text-right">
                                <div class="flex items-center justify-end gap-1.5">
                                    <button type="button" 
                                            @click="$dispatch('open-quick-seo', {
                                                type: 'service',
                                                endpoint: '{{ route('admin.services.quick-seo', $service) }}',
                                                id: {{ $service->id }},
                                                title: @js($service->title),
                                                slug: @js($service->slug),
                                                focus_keyword: @js($service->focus_keyword ?? ''),
                                                meta_title: @js($service->meta_title ?? ''),
                                                meta_description: @js($service->meta_description ?? ''),
                                                schema_markup: @js($service->schema_markup ?? ''),
                                                seo_score: {{ (int) ($service->seo_score ?? 0) }}
                                            })"
                                            class="px-2.5 py-1 rounded-lg text-purple-700 hover:text-purple-900 bg-purple-50 hover:bg-purple-100 text-[11px] font-bold border border-purple-200 transition flex items-center gap-1"
                                            title="Quick Edit Keyword, Description & Schema">
                                        <i data-lucide="sparkles" class="w-3 h-3 text-purple-600"></i>
                                        <span>Quick SEO</span>
                                    </button>
                                    <a href="{{ route('service.show', $service->slug) }}" target="_blank" 
                                       class="p-1.5 rounded-lg text-slate-400 hover:text-brand-600 hover:bg-brand-50 transition" title="Preview Public Page">
                                        <i data-lucide="external-link" class="w-4 h-4"></i>
                                    </a>
                                    <a href="{{ route('admin.services.edit', $service) }}" 
                                       class="p-1.5 rounded-lg text-slate-400 hover:text-slate-900 hover:bg-slate-100 transition" title="Edit Service">
                                        <i data-lucide="edit-3" class="w-4 h-4"></i>
                                    </a>
                                    <form action="{{ route('admin.services.destroy', $service) }}" method="POST" 
                                          onsubmit="return confirm('Are you sure you want to remove this service?');" class="inline">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="p-1.5 rounded-lg text-slate-400 hover:text-rose-600 hover:bg-rose-50 transition" title="Delete">
                                            <i data-lucide="trash-2" class="w-4 h-4"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="py-12 text-center text-slate-400">
                                <i data-lucide="layers" class="w-8 h-8 mx-auto mb-2 opacity-40"></i>
                                <p class="text-sm font-semibold">No services found.</p>
                                <p class="text-xs text-slate-400 mt-1">Get started by creating your first service.</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($services->hasPages())
            <div class="p-4 border-t border-slate-100">
                {{ $services->links() }}
            </div>
        @endif
    </div>
</div>
@endsection
