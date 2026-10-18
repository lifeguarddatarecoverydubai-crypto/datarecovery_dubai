@extends('layouts.admin')

@section('title', '301 Redirection Manager')

@section('content')
<div class="space-y-6" x-data="redirectsManager()">

    <!-- Flash Notifications -->
    @if(session('success'))
        <div class="p-4 rounded-xl bg-emerald-50 border border-emerald-200 text-emerald-800 flex items-center justify-between shadow-sm animate-fade-in">
            <div class="flex items-center gap-3">
                <i data-lucide="check-circle-2" class="w-5 h-5 text-emerald-600 shrink-0"></i>
                <p class="text-xs sm:text-sm font-semibold">{{ session('success') }}</p>
            </div>
            <button type="button" @click="$el.parentElement.remove()" class="text-emerald-500 hover:text-emerald-700">
                <i data-lucide="x" class="w-4 h-4"></i>
            </button>
        </div>
    @endif

    @if(session('error'))
        <div class="p-4 rounded-xl bg-rose-50 border border-rose-200 text-rose-800 flex items-center justify-between shadow-sm">
            <div class="flex items-center gap-3">
                <i data-lucide="alert-triangle" class="w-5 h-5 text-rose-600 shrink-0"></i>
                <p class="text-xs sm:text-sm font-semibold">{{ session('error') }}</p>
            </div>
            <button type="button" @click="$el.parentElement.remove()" class="text-rose-500 hover:text-rose-700">
                <i data-lucide="x" class="w-4 h-4"></i>
            </button>
        </div>
    @endif

    @if($errors->any())
        <div class="p-4 rounded-xl bg-rose-50 border border-rose-200 text-rose-800 shadow-sm space-y-1">
            <div class="flex items-center gap-2 font-bold text-xs uppercase tracking-wider text-rose-700">
                <i data-lucide="alert-circle" class="w-4 h-4"></i> Validation Errors
            </div>
            <ul class="list-disc list-inside text-xs space-y-0.5 text-rose-700 pl-2">
                @foreach($errors->all() as $err)
                    <li>{{ $err }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <!-- Header & Action Toolbar -->
    <div class="bg-white rounded-2xl border border-slate-200/80 p-5 sm:p-6 shadow-sm">
        <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-4">
            <div>
                <div class="flex items-center gap-2.5">
                    <span class="p-2 rounded-xl bg-teal-50 text-teal-600 border border-teal-100">
                        <i data-lucide="arrow-left-right" class="w-5 h-5"></i>
                    </span>
                    <div>
                        <h1 class="text-xl sm:text-2xl font-black text-slate-900 tracking-tight">301 Redirection Manager</h1>
                        <p class="text-xs sm:text-sm text-slate-500 mt-0.5">
                            Manage permanent & temporary redirects, fix 404 errors, consolidate duplicate URLs, and protect SEO search rankings.
                        </p>
                    </div>
                </div>
            </div>

            <!-- Toolbar Buttons -->
            <div class="flex flex-wrap items-center gap-2 sm:gap-3">
                <button type="button" @click="openCreateModal()" 
                        class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl bg-teal-600 hover:bg-teal-500 text-white text-xs font-bold shadow-md shadow-teal-600/20 transition">
                    <i data-lucide="plus-circle" class="w-4 h-4"></i> Add Redirect
                </button>
                <button type="button" @click="testDrawerOpen = !testDrawerOpen"
                        class="inline-flex items-center gap-2 px-3.5 py-2.5 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-700 text-xs font-semibold transition">
                    <i data-lucide="sparkles" class="w-4 h-4 text-amber-500"></i> Test URL Simulator
                </button>
                <button type="button" @click="importModalOpen = true"
                        class="inline-flex items-center gap-2 px-3 py-2.5 rounded-xl bg-white border border-slate-200 hover:bg-slate-50 text-slate-700 text-xs font-semibold shadow-sm transition">
                    <i data-lucide="upload" class="w-3.5 h-3.5 text-slate-500"></i> Import CSV
                </button>
                <a href="{{ route('admin.redirects.export') }}"
                   class="inline-flex items-center gap-2 px-3 py-2.5 rounded-xl bg-white border border-slate-200 hover:bg-slate-50 text-slate-700 text-xs font-semibold shadow-sm transition">
                    <i data-lucide="download" class="w-3.5 h-3.5 text-slate-500"></i> Export CSV
                </a>
            </div>
        </div>
    </div>

    <!-- Stats Metric Cards -->
    <div class="grid grid-cols-2 sm:grid-cols-4 gap-4">
        <div class="bg-white p-5 rounded-2xl border border-slate-200/80 shadow-sm flex items-center justify-between">
            <div>
                <p class="text-[10px] font-bold text-slate-400 uppercase tracking-wider">Total Rules</p>
                <p class="text-2xl font-black text-slate-900 mt-1">{{ number_format($stats['total']) }}</p>
                <p class="text-[10px] text-emerald-600 font-semibold mt-0.5">{{ $stats['active'] }} active &bull; {{ $stats['inactive'] }} paused</p>
            </div>
            <div class="w-10 h-10 rounded-xl bg-sky-50 text-sky-600 flex items-center justify-center">
                <i data-lucide="shuffle" class="w-5 h-5"></i>
            </div>
        </div>

        <div class="bg-white p-5 rounded-2xl border border-slate-200/80 shadow-sm flex items-center justify-between">
            <div>
                <p class="text-[10px] font-bold text-slate-400 uppercase tracking-wider">SEO Hits Rescued</p>
                <p class="text-2xl font-black text-emerald-600 mt-1">{{ number_format($stats['total_hits']) }}</p>
                <p class="text-[10px] text-slate-400 mt-0.5">Visitors saved from 404</p>
            </div>
            <div class="w-10 h-10 rounded-xl bg-emerald-50 text-emerald-600 flex items-center justify-center">
                <i data-lucide="flame" class="w-5 h-5"></i>
            </div>
        </div>

        <div class="bg-white p-5 rounded-2xl border border-slate-200/80 shadow-sm flex items-center justify-between">
            <div>
                <p class="text-[10px] font-bold text-slate-400 uppercase tracking-wider">301 Permanent</p>
                <p class="text-2xl font-black text-slate-900 mt-1">{{ number_format($stats['permanent_301']) }}</p>
                <p class="text-[10px] text-slate-400 mt-0.5">Permanent SEO link equity</p>
            </div>
            <div class="w-10 h-10 rounded-xl bg-teal-50 text-teal-600 flex items-center justify-center">
                <i data-lucide="check" class="w-5 h-5"></i>
            </div>
        </div>

        <div class="bg-white p-5 rounded-2xl border border-slate-200/80 shadow-sm flex items-center justify-between">
            <div>
                <p class="text-[10px] font-bold text-slate-400 uppercase tracking-wider">Temporary & 410</p>
                <p class="text-2xl font-black text-slate-900 mt-1">{{ number_format($stats['temporary_302'] + $stats['gone_410']) }}</p>
                <p class="text-[10px] text-slate-400 mt-0.5">{{ $stats['temporary_302'] }} Temp &bull; {{ $stats['gone_410'] }} Removed</p>
            </div>
            <div class="w-10 h-10 rounded-xl bg-purple-50 text-purple-600 flex items-center justify-center">
                <i data-lucide="clock" class="w-5 h-5"></i>
            </div>
        </div>
    </div>

    <!-- Live URL Simulator Tool Card (Interactive Diagnostic) -->
    <div x-show="testDrawerOpen" x-cloak x-transition
         class="bg-gradient-to-br from-slate-900 via-slate-800 to-teal-950 rounded-2xl p-5 sm:p-6 text-white shadow-lg border border-teal-500/20">
        <div class="flex items-center justify-between mb-3">
            <div class="flex items-center gap-2">
                <span class="w-2.5 h-2.5 rounded-full bg-emerald-400 animate-pulse"></span>
                <h3 class="text-sm font-bold text-white tracking-wide uppercase">Live Redirect Simulator & Diagnostic Tool</h3>
            </div>
            <button @click="testDrawerOpen = false" class="text-slate-400 hover:text-white">
                <i data-lucide="x" class="w-4 h-4"></i>
            </button>
        </div>
        <p class="text-xs text-slate-300 max-w-2xl mb-4">
            Test any source path or query string without needing to clear browser cache. Simulator executes against current active rules in memory.
        </p>
        <div class="flex flex-col sm:flex-row gap-2.5">
            <div class="relative flex-1">
                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-slate-400 text-xs">
                    {{ url('/') }}
                </div>
                <input type="text" x-model="testUrlInput" @keydown.enter.prevent="runTest()"
                       placeholder="/hard-drive-data-recovery-in-dubai?source=google"
                       class="w-full pl-44 pr-4 py-2.5 rounded-xl bg-slate-950/80 border border-slate-700 text-white text-xs placeholder:text-slate-500 focus:outline-none focus:border-teal-500 focus:ring-1 focus:ring-teal-500 font-mono">
            </div>
            <button type="button" @click="runTest()" :disabled="testing"
                    class="px-5 py-2.5 rounded-xl bg-teal-500 hover:bg-teal-400 disabled:opacity-50 text-slate-950 font-bold text-xs flex items-center justify-center gap-2 transition">
                <span x-show="!testing"><i data-lucide="play" class="w-3.5 h-3.5"></i> Test URL</span>
                <span x-show="testing" class="inline-flex items-center gap-1.5"><i data-lucide="loader-2" class="w-3.5 h-3.5 animate-spin"></i> Checking...</span>
            </button>
        </div>

        <!-- Simulation Result Box -->
        <div x-show="testResult" x-cloak class="mt-4 p-4 rounded-xl text-xs transition"
             :class="testResult && testResult.matched ? 'bg-emerald-950/70 border border-emerald-500/40 text-emerald-200' : 'bg-slate-950/80 border border-slate-700 text-slate-300'">
            <template x-if="testResult && testResult.matched">
                <div class="space-y-2">
                    <div class="flex flex-wrap items-center justify-between gap-2 border-b border-emerald-500/20 pb-2">
                        <div class="flex items-center gap-2">
                            <span class="px-2 py-0.5 rounded text-[10px] font-black uppercase bg-emerald-500 text-slate-950" x-text="'HTTP ' + testResult.status_code"></span>
                            <span class="font-bold text-white">Match Found!</span>
                            <span class="text-[10px] px-1.5 py-0.5 rounded bg-emerald-900/60 text-emerald-300 border border-emerald-500/30 font-mono" x-text="testResult.match_type + ' rule'"></span>
                        </div>
                        <span class="text-[10px] text-slate-400 font-mono" x-text="'Evaluated in ' + testResult.latency_ms + 'ms'"></span>
                    </div>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-2 text-[11px] pt-1">
                        <div>
                            <span class="text-slate-400">Tested Path:</span>
                            <span class="font-mono text-white ml-1" x-text="testResult.tested_url"></span>
                        </div>
                        <div>
                            <span class="text-slate-400">Redirect Destination:</span>
                            <span class="font-mono text-emerald-400 font-bold ml-1" x-text="testResult.target_url"></span>
                        </div>
                    </div>
                    <div class="text-[10px] text-slate-400" x-show="testResult.description">
                        Note: <span class="text-slate-300" x-text="testResult.description"></span>
                    </div>
                </div>
            </template>
            <template x-if="testResult && !testResult.matched">
                <div class="flex items-center justify-between">
                    <div class="flex items-center gap-2">
                        <span class="px-2 py-0.5 rounded text-[10px] font-bold uppercase bg-slate-800 text-slate-300 border border-slate-700">No Match</span>
                        <span x-text="testResult.message"></span>
                    </div>
                    <span class="text-[10px] text-slate-400 font-mono" x-text="'Evaluated in ' + testResult.latency_ms + 'ms'"></span>
                </div>
            </template>
        </div>
    </div>

    <!-- Filters, Search & Bulk Actions Form -->
    <div class="bg-white rounded-2xl border border-slate-200/80 p-4 sm:p-5 shadow-sm space-y-4">
        <!-- Search & Filter Controls -->
        <form method="GET" action="{{ route('admin.redirects.index') }}" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-3">
            <!-- Search -->
            <div class="lg:col-span-2 relative">
                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-slate-400">
                    <i data-lucide="search" class="w-4 h-4"></i>
                </div>
                <input type="text" name="search" value="{{ request('search') }}"
                       placeholder="Search source, target, or note..."
                       class="w-full pl-9 pr-3 py-2 rounded-xl border border-slate-200 text-xs focus:ring-2 focus:ring-teal-500 focus:border-teal-500">
            </div>

            <!-- Group Filter -->
            <div>
                <select name="group" class="w-full py-2 px-3 rounded-xl border border-slate-200 text-xs text-slate-700 focus:ring-2 focus:ring-teal-500 focus:border-teal-500">
                    <option value="all">All Groups</option>
                    @foreach($groups as $grp)
                        <option value="{{ $grp }}" {{ request('group') === $grp ? 'selected' : '' }}>{{ $grp }}</option>
                    @endforeach
                </select>
            </div>

            <!-- Status Code Filter -->
            <div>
                <select name="status_code" class="w-full py-2 px-3 rounded-xl border border-slate-200 text-xs text-slate-700 focus:ring-2 focus:ring-teal-500 focus:border-teal-500">
                    <option value="all">All Codes</option>
                    <option value="301" {{ request('status_code') == '301' ? 'selected' : '' }}>301 Permanent</option>
                    <option value="302" {{ request('status_code') == '302' ? 'selected' : '' }}>302 Found / Temp</option>
                    <option value="307" {{ request('status_code') == '307' ? 'selected' : '' }}>307 Temporary</option>
                    <option value="308" {{ request('status_code') == '308' ? 'selected' : '' }}>308 Permanent</option>
                    <option value="410" {{ request('status_code') == '410' ? 'selected' : '' }}>410 Gone / Removed</option>
                </select>
            </div>

            <!-- Action Buttons -->
            <div class="flex items-center gap-2">
                <button type="submit" class="flex-1 px-3 py-2 rounded-xl bg-slate-900 hover:bg-slate-800 text-white text-xs font-semibold shadow-sm transition">
                    Filter
                </button>
                @if(request()->hasAny(['search', 'group', 'status_code', 'match_type', 'status']))
                    <a href="{{ route('admin.redirects.index') }}" class="px-3 py-2 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-600 text-xs font-semibold transition" title="Clear Filters">
                        Reset
                    </a>
                @endif
            </div>
        </form>

        <!-- Bulk Actions Bar -->
        <form id="bulkForm" method="POST" action="{{ route('admin.redirects.bulk') }}" class="flex flex-wrap items-center justify-between gap-3 pt-3 border-t border-slate-100">
            @csrf
            <div class="flex items-center gap-3">
                <label class="inline-flex items-center gap-2 text-xs text-slate-600 font-medium cursor-pointer">
                    <input type="checkbox" @change="toggleSelectAll($event)" class="rounded border-slate-300 text-teal-600 focus:ring-teal-500 w-4 h-4">
                    <span>Select All (<span x-text="selectedIds.length"></span>)</span>
                </label>

                <div class="flex items-center gap-2">
                    <select name="action" x-model="bulkAction" class="py-1.5 px-3 rounded-lg border border-slate-200 text-xs text-slate-700 focus:ring-teal-500">
                        <option value="">Bulk Actions</option>
                        <option value="enable">Enable Selected</option>
                        <option value="disable">Disable Selected</option>
                        <option value="reset_hits">Reset Hits</option>
                        <option value="delete">Delete Selected</option>
                    </select>
                    <button type="button" @click="submitBulk()" :disabled="selectedIds.length === 0 || !bulkAction"
                            class="px-3 py-1.5 rounded-lg bg-slate-200 hover:bg-slate-300 disabled:opacity-40 text-slate-700 text-xs font-semibold transition">
                        Apply
                    </button>
                </div>
            </div>

            <div class="text-xs text-slate-400">
                Showing {{ $redirects->firstItem() ?? 0 }}-{{ $redirects->lastItem() ?? 0 }} of {{ $redirects->total() }} rules
            </div>
        </form>
    </div>

    <!-- Redirection Rules Table -->
    <div class="bg-white rounded-2xl border border-slate-200/80 shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="border-b border-slate-100 bg-slate-50/75 text-[11px] font-bold text-slate-500 uppercase tracking-wider">
                        <th class="py-3.5 px-4 w-10 text-center">#</th>
                        <th class="py-3.5 px-4 min-w-[240px]">Source URL</th>
                        <th class="py-3.5 px-4 min-w-[240px]">Target Destination</th>
                        <th class="py-3.5 px-4 w-28 text-center">Type</th>
                        <th class="py-3.5 px-4 w-28">Group</th>
                        <th class="py-3.5 px-4 w-28 text-center">Traffic</th>
                        <th class="py-3.5 px-4 w-20 text-center">Status</th>
                        <th class="py-3.5 px-4 w-28 text-right">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 text-xs">
                    @forelse($redirects as $rule)
                        <tr class="hover:bg-slate-50/80 transition group {{ !$rule->is_active ? 'opacity-60 bg-slate-50/30' : '' }}">
                            <!-- Checkbox -->
                            <td class="py-3.5 px-4 text-center">
                                <input type="checkbox" name="selected[]" value="{{ $rule->id }}" form="bulkForm"
                                       :checked="selectedIds.includes({{ $rule->id }})"
                                       @change="toggleSelect({{ $rule->id }})"
                                       class="rounded border-slate-300 text-teal-600 focus:ring-teal-500 w-4 h-4">
                            </td>

                            <!-- Source URL -->
                            <td class="py-3.5 px-4">
                                <div class="flex items-start gap-2">
                                    <div class="flex-1 min-w-0">
                                        <div class="flex items-center gap-1.5 flex-wrap">
                                            <span class="font-mono font-bold text-slate-900 break-all select-all">{{ $rule->source_url }}</span>
                                            <button type="button" @click="copyToClipboard('{{ url($rule->source_url) }}')" 
                                                    class="text-slate-400 hover:text-slate-600 transition" title="Copy full source URL">
                                                <i data-lucide="copy" class="w-3 h-3"></i>
                                            </button>
                                        </div>
                                        
                                        <!-- Source Metadata Pills -->
                                        <div class="flex items-center gap-1.5 mt-1">
                                            @if($rule->match_type === 'wildcard')
                                                <span class="px-1.5 py-0.5 rounded text-[9px] font-extrabold bg-amber-50 text-amber-700 border border-amber-200">WILDCARD</span>
                                            @elseif($rule->match_type === 'regex')
                                                <span class="px-1.5 py-0.5 rounded text-[9px] font-extrabold bg-purple-50 text-purple-700 border border-purple-200">REGEX</span>
                                            @else
                                                <span class="px-1.5 py-0.5 rounded text-[9px] font-semibold bg-slate-100 text-slate-600">Exact Match</span>
                                            @endif

                                            @if($rule->query_param_option === 'pass')
                                                <span class="px-1.5 py-0.5 rounded text-[9px] font-medium bg-sky-50 text-sky-600" title="Query parameters are forwarded">Pass Query</span>
                                            @elseif($rule->query_param_option === 'exact')
                                                <span class="px-1.5 py-0.5 rounded text-[9px] font-medium bg-amber-50 text-amber-600" title="Matches exact query string">Exact Query</span>
                                            @endif
                                        </div>

                                        @if($rule->description)
                                            <p class="text-[11px] text-slate-500 mt-1 italic">{{ $rule->description }}</p>
                                        @endif
                                    </div>
                                </div>
                            </td>

                            <!-- Target Destination -->
                            <td class="py-3.5 px-4">
                                @if($rule->status_code === 410)
                                    <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-lg text-[11px] font-bold bg-rose-50 text-rose-700 border border-rose-200">
                                        <i data-lucide="ban" class="w-3.5 h-3.5"></i> HTTP 410 Gone (Removed)
                                    </span>
                                @else
                                    <div class="flex items-center gap-1.5 flex-wrap">
                                        <i data-lucide="corner-down-right" class="w-3.5 h-3.5 text-teal-500 shrink-0"></i>
                                        <span class="font-mono font-medium text-slate-800 break-all select-all">{{ $rule->target_url }}</span>
                                        <button type="button" @click="copyToClipboard('{{ str_starts_with($rule->target_url, 'http') ? $rule->target_url : url($rule->target_url) }}')" 
                                                class="text-slate-400 hover:text-slate-600 transition" title="Copy target URL">
                                            <i data-lucide="copy" class="w-3 h-3"></i>
                                        </button>
                                        <a href="{{ str_starts_with($rule->target_url, 'http') ? $rule->target_url : url($rule->target_url) }}" target="_blank" 
                                           class="text-teal-600 hover:text-teal-800 transition" title="Visit Destination in New Tab">
                                            <i data-lucide="external-link" class="w-3 h-3"></i>
                                        </a>
                                    </div>
                                @endif
                            </td>

                            <!-- HTTP Status Code -->
                            <td class="py-3.5 px-4 text-center">
                                @if($rule->status_code === 301)
                                    <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-black bg-emerald-100 text-emerald-800 border border-emerald-200">
                                        301 Permanent
                                    </span>
                                @elseif($rule->status_code === 302)
                                    <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-black bg-amber-100 text-amber-800 border border-amber-200">
                                        302 Found
                                    </span>
                                @elseif($rule->status_code === 307)
                                    <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-black bg-sky-100 text-sky-800 border border-sky-200">
                                        307 Temporary
                                    </span>
                                @elseif($rule->status_code === 308)
                                    <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-black bg-teal-100 text-teal-800 border border-teal-200">
                                        308 Permanent
                                    </span>
                                @elseif($rule->status_code === 410)
                                    <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-black bg-rose-100 text-rose-800 border border-rose-200">
                                        410 Gone
                                    </span>
                                @endif
                            </td>

                            <!-- Group / Category -->
                            <td class="py-3.5 px-4">
                                <span class="inline-flex items-center px-2 py-0.5 rounded text-[10px] font-semibold bg-slate-100 text-slate-700">
                                    {{ $rule->group ?: 'General' }}
                                </span>
                            </td>

                            <!-- Hits & Traffic Analytics -->
                            <td class="py-3.5 px-4 text-center">
                                <div class="flex flex-col items-center">
                                    <div class="flex items-center gap-1 font-mono font-bold text-slate-900 {{ $rule->hits > 0 ? 'text-emerald-700' : 'text-slate-400' }}">
                                        @if($rule->hits > 0)
                                            <i data-lucide="flame" class="w-3.5 h-3.5 text-amber-500 shrink-0"></i>
                                        @endif
                                        <span>{{ number_format($rule->hits) }}</span>
                                    </div>
                                    <span class="text-[9px] text-slate-400">
                                        {{ $rule->last_accessed_at ? $rule->last_accessed_at->diffForHumans() : 'Never used' }}
                                    </span>
                                </div>
                            </td>

                            <!-- Active Toggle Switch (AJAX) -->
                            <td class="py-3.5 px-4 text-center">
                                <form method="POST" action="{{ route('admin.redirects.toggle', $rule) }}">
                                    @csrf
                                    @method('PATCH')
                                    <button type="submit" 
                                            class="relative inline-flex h-5 w-9 shrink-0 cursor-pointer rounded-full border-2 border-transparent transition-colors duration-200 ease-in-out focus:outline-none {{ $rule->is_active ? 'bg-teal-600' : 'bg-slate-300' }}"
                                            title="{{ $rule->is_active ? 'Click to deactivate' : 'Click to activate' }}">
                                        <span class="pointer-events-none inline-block h-4 w-4 transform rounded-full bg-white shadow ring-0 transition duration-200 ease-in-out {{ $rule->is_active ? 'translate-x-4' : 'translate-x-0' }}"></span>
                                    </button>
                                </form>
                            </td>

                            <!-- Actions -->
                            <td class="py-3.5 px-4 text-right">
                                <div class="flex items-center justify-end gap-1.5">
                                    <!-- Test Button -->
                                    <button type="button" @click="testUrlInput = '{{ $rule->source_url }}'; testDrawerOpen = true; runTest()" 
                                            class="p-1.5 rounded-lg text-slate-400 hover:text-teal-600 hover:bg-slate-100 transition" title="Test this rule">
                                        <i data-lucide="play" class="w-3.5 h-3.5"></i>
                                    </button>

                                    <!-- Edit Button -->
                                    <button type="button" @click="openEditModal(@js($rule))" 
                                            class="p-1.5 rounded-lg text-slate-400 hover:text-sky-600 hover:bg-slate-100 transition" title="Edit Redirect">
                                        <i data-lucide="pencil" class="w-3.5 h-3.5"></i>
                                    </button>

                                    <!-- Delete Button -->
                                    <form method="POST" action="{{ route('admin.redirects.destroy', $rule) }}" onsubmit="return confirm('Permanently remove this redirect rule?');" class="inline">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="p-1.5 rounded-lg text-slate-400 hover:text-rose-600 hover:bg-rose-50 transition" title="Delete Redirect">
                                            <i data-lucide="trash-2" class="w-3.5 h-3.5"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="py-12 text-center">
                                <div class="max-w-sm mx-auto space-y-3">
                                    <div class="w-12 h-12 rounded-2xl bg-teal-50 text-teal-600 flex items-center justify-center mx-auto">
                                        <i data-lucide="shuffle" class="w-6 h-6"></i>
                                    </div>
                                    <h4 class="text-sm font-bold text-slate-800">No Redirection Rules Found</h4>
                                    <p class="text-xs text-slate-400">
                                        Create your first 301 permanent redirect or seed the default SEO canonical redirects for LifeGuard Data Recovery.
                                    </p>
                                    <div class="flex items-center justify-center gap-2 pt-2">
                                        <button type="button" @click="openCreateModal()" 
                                                class="px-4 py-2 rounded-xl bg-teal-600 hover:bg-teal-500 text-white text-xs font-bold shadow-md shadow-teal-600/20 transition">
                                            Create Redirect
                                        </button>
                                        <form method="POST" action="{{ route('admin.redirects.seed-defaults') }}">
                                            @csrf
                                            <button type="submit" class="px-4 py-2 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-700 text-xs font-semibold transition">
                                                Load Legacy Defaults
                                            </button>
                                        </form>
                                    </div>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($redirects->hasPages())
            <div class="p-4 border-t border-slate-100 bg-slate-50/50">
                {{ $redirects->links() }}
            </div>
        @endif
    </div>

    <!-- CREATE REDIRECT MODAL -->
    <div x-show="createModalOpen" x-cloak
         class="fixed inset-0 z-50 overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
        <div class="flex items-center justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:block sm:p-0">
            <!-- Backdrop -->
            <div x-show="createModalOpen" x-transition.opacity @click="createModalOpen = false" 
                 class="fixed inset-0 bg-slate-900/80 backdrop-blur-sm transition-opacity"></div>

            <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>

            <div x-show="createModalOpen" x-transition 
                 class="inline-block align-bottom bg-white rounded-2xl text-left overflow-hidden shadow-2xl transform transition-all sm:my-8 sm:align-middle sm:max-w-xl sm:w-full border border-slate-200">
                <form method="POST" action="{{ route('admin.redirects.store') }}">
                    @csrf
                    <div class="p-6 space-y-4">
                        <div class="flex items-center justify-between pb-3 border-b border-slate-100">
                            <div class="flex items-center gap-2.5">
                                <div class="w-8 h-8 rounded-xl bg-teal-50 text-teal-600 flex items-center justify-center font-bold">
                                    <i data-lucide="plus" class="w-4 h-4"></i>
                                </div>
                                <div>
                                    <h3 class="text-base font-black text-slate-900">Add New Redirect Rule</h3>
                                    <p class="text-xs text-slate-500">Configure URL source, destination, and SEO status code.</p>
                                </div>
                            </div>
                            <button type="button" @click="createModalOpen = false" class="text-slate-400 hover:text-slate-600">
                                <i data-lucide="x" class="w-5 h-5"></i>
                            </button>
                        </div>

                        <!-- Source URL -->
                        <div>
                            <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">Source URL (Old Path) <span class="text-rose-500">*</span></label>
                            <input type="text" name="source_url" required placeholder="/old-service-name or hard-drive-recovery-old"
                                   class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 text-xs font-mono focus:ring-2 focus:ring-teal-500 focus:border-teal-500">
                            <p class="text-[10px] text-slate-400 mt-1">Relative path on this domain. Trailing slashes and domains are auto-normalized.</p>
                        </div>

                        <!-- Target Destination URL -->
                        <div>
                            <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">Target Destination <span class="text-rose-500" x-show="newStatusCode != 410">*</span></label>
                            <input type="text" name="target_url" :required="newStatusCode != 410" placeholder="/service/hard-drive-recovery or https://..."
                                   class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 text-xs font-mono focus:ring-2 focus:ring-teal-500 focus:border-teal-500">
                            <p class="text-[10px] text-slate-400 mt-1">Can be internal relative path (/services/...) or external full URL (https://...). Leave empty for 410 Gone.</p>
                        </div>

                        <!-- Status Code & Match Type -->
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                            <div>
                                <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">Redirect HTTP Code</label>
                                <select name="status_code" x-model="newStatusCode"
                                        class="w-full px-3 py-2.5 rounded-xl border border-slate-200 text-xs text-slate-800 focus:ring-2 focus:ring-teal-500 focus:border-teal-500">
                                    <option value="301">301 - Moved Permanently (SEO Standard)</option>
                                    <option value="302">302 - Found (Temporary Redirect)</option>
                                    <option value="307">307 - Temporary Redirect</option>
                                    <option value="308">308 - Permanent Redirect</option>
                                    <option value="410">410 - Content Deleted / Gone</option>
                                </select>
                            </div>

                            <div>
                                <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">Match Type</label>
                                <select name="match_type"
                                        class="w-full px-3 py-2.5 rounded-xl border border-slate-200 text-xs text-slate-800 focus:ring-2 focus:ring-teal-500 focus:border-teal-500">
                                    <option value="exact">Exact Match (URL only)</option>
                                    <option value="wildcard">Wildcard (e.g. /blog/*)</option>
                                    <option value="regex">Regular Expression (Regex)</option>
                                </select>
                            </div>
                        </div>

                        <!-- Query Params & Group -->
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                            <div>
                                <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">Query Parameters</label>
                                <select name="query_param_option"
                                        class="w-full px-3 py-2.5 rounded-xl border border-slate-200 text-xs text-slate-800 focus:ring-2 focus:ring-teal-500 focus:border-teal-500">
                                    <option value="ignore">Ignore Query Parameters (Recommended)</option>
                                    <option value="pass">Pass Query Parameters to Target</option>
                                    <option value="exact">Exact Query Match Only</option>
                                </select>
                            </div>

                            <div>
                                <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">Group / Category</label>
                                <input type="text" name="group" list="groupList" placeholder="e.g. Services, Old Blog, Migration"
                                       class="w-full px-3 py-2.5 rounded-xl border border-slate-200 text-xs text-slate-800 focus:ring-2 focus:ring-teal-500 focus:border-teal-500">
                                <datalist id="groupList">
                                    @foreach($groups as $grp)
                                        <option value="{{ $grp }}">
                                    @endforeach
                                    <option value="SEO Cannibalization">
                                    <option value="WordPress Legacy">
                                    <option value="Services">
                                    <option value="Blog Migration">
                                </datalist>
                            </div>
                        </div>

                        <!-- Description / Note -->
                        <div>
                            <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">Internal Note / Reason</label>
                            <input type="text" name="description" placeholder="Why is this redirect created? (e.g. Migrated duplicate SEO page)"
                                   class="w-full px-3 py-2 rounded-xl border border-slate-200 text-xs text-slate-800 focus:ring-2 focus:ring-teal-500 focus:border-teal-500">
                        </div>

                        <!-- Status Active Toggle -->
                        <div class="pt-2">
                            <label class="inline-flex items-center gap-2 cursor-pointer">
                                <input type="checkbox" name="is_active" value="1" checked class="rounded border-slate-300 text-teal-600 focus:ring-teal-500 w-4 h-4">
                                <span class="text-xs font-semibold text-slate-700">Enable this redirect immediately</span>
                            </label>
                        </div>
                    </div>

                    <div class="p-4 bg-slate-50 border-t border-slate-100 flex items-center justify-end gap-2">
                        <button type="button" @click="createModalOpen = false" class="px-4 py-2 rounded-xl bg-white border border-slate-200 text-slate-600 text-xs font-semibold hover:bg-slate-50 transition">
                            Cancel
                        </button>
                        <button type="submit" class="px-5 py-2 rounded-xl bg-teal-600 hover:bg-teal-500 text-white text-xs font-bold shadow-md shadow-teal-600/20 transition">
                            Save & Activate Redirect
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- EDIT REDIRECT MODAL -->
    <div x-show="editModalOpen" x-cloak
         class="fixed inset-0 z-50 overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
        <div class="flex items-center justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:block sm:p-0">
            <div x-show="editModalOpen" x-transition.opacity @click="editModalOpen = false" 
                 class="fixed inset-0 bg-slate-900/80 backdrop-blur-sm transition-opacity"></div>

            <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>

            <div x-show="editModalOpen" x-transition 
                 class="inline-block align-bottom bg-white rounded-2xl text-left overflow-hidden shadow-2xl transform transition-all sm:my-8 sm:align-middle sm:max-w-xl sm:w-full border border-slate-200">
                <form method="POST" :action="editFormAction">
                    @csrf
                    @method('PUT')
                    <div class="p-6 space-y-4">
                        <div class="flex items-center justify-between pb-3 border-b border-slate-100">
                            <div class="flex items-center gap-2.5">
                                <div class="w-8 h-8 rounded-xl bg-sky-50 text-sky-600 flex items-center justify-center font-bold">
                                    <i data-lucide="pencil" class="w-4 h-4"></i>
                                </div>
                                <div>
                                    <h3 class="text-base font-black text-slate-900">Edit Redirect Rule #<span x-text="editRule.id"></span></h3>
                                    <p class="text-xs text-slate-500">Update destination URL, match logic, or status.</p>
                                </div>
                            </div>
                            <button type="button" @click="editModalOpen = false" class="text-slate-400 hover:text-slate-600">
                                <i data-lucide="x" class="w-5 h-5"></i>
                            </button>
                        </div>

                        <!-- Source URL -->
                        <div>
                            <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">Source URL (Old Path) <span class="text-rose-500">*</span></label>
                            <input type="text" name="source_url" x-model="editRule.source_url" required
                                   class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 text-xs font-mono focus:ring-2 focus:ring-teal-500 focus:border-teal-500">
                        </div>

                        <!-- Target Destination URL -->
                        <div>
                            <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">Target Destination <span class="text-rose-500" x-show="editRule.status_code != 410">*</span></label>
                            <input type="text" name="target_url" x-model="editRule.target_url" :required="editRule.status_code != 410"
                                   class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 text-xs font-mono focus:ring-2 focus:ring-teal-500 focus:border-teal-500">
                        </div>

                        <!-- Status Code & Match Type -->
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                            <div>
                                <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">Redirect HTTP Code</label>
                                <select name="status_code" x-model="editRule.status_code"
                                        class="w-full px-3 py-2.5 rounded-xl border border-slate-200 text-xs text-slate-800 focus:ring-2 focus:ring-teal-500 focus:border-teal-500">
                                    <option value="301">301 - Moved Permanently (SEO Standard)</option>
                                    <option value="302">302 - Found (Temporary Redirect)</option>
                                    <option value="307">307 - Temporary Redirect</option>
                                    <option value="308">308 - Permanent Redirect</option>
                                    <option value="410">410 - Content Deleted / Gone</option>
                                </select>
                            </div>

                            <div>
                                <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">Match Type</label>
                                <select name="match_type" x-model="editRule.match_type"
                                        class="w-full px-3 py-2.5 rounded-xl border border-slate-200 text-xs text-slate-800 focus:ring-2 focus:ring-teal-500 focus:border-teal-500">
                                    <option value="exact">Exact Match</option>
                                    <option value="wildcard">Wildcard (*)</option>
                                    <option value="regex">Regular Expression (Regex)</option>
                                </select>
                            </div>
                        </div>

                        <!-- Query Params & Group -->
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                            <div>
                                <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">Query Parameters</label>
                                <select name="query_param_option" x-model="editRule.query_param_option"
                                        class="w-full px-3 py-2.5 rounded-xl border border-slate-200 text-xs text-slate-800 focus:ring-2 focus:ring-teal-500 focus:border-teal-500">
                                    <option value="ignore">Ignore Query Parameters</option>
                                    <option value="pass">Pass Query Parameters to Target</option>
                                    <option value="exact">Exact Query Match Only</option>
                                </select>
                            </div>

                            <div>
                                <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">Group / Category</label>
                                <input type="text" name="group" x-model="editRule.group"
                                       class="w-full px-3 py-2.5 rounded-xl border border-slate-200 text-xs text-slate-800 focus:ring-2 focus:ring-teal-500 focus:border-teal-500">
                            </div>
                        </div>

                        <!-- Description -->
                        <div>
                            <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">Internal Note / Reason</label>
                            <input type="text" name="description" x-model="editRule.description"
                                   class="w-full px-3 py-2 rounded-xl border border-slate-200 text-xs text-slate-800 focus:ring-2 focus:ring-teal-500 focus:border-teal-500">
                        </div>

                        <!-- Status Active Toggle -->
                        <div class="pt-2">
                            <label class="inline-flex items-center gap-2 cursor-pointer">
                                <input type="checkbox" name="is_active" value="1" :checked="editRule.is_active" class="rounded border-slate-300 text-teal-600 focus:ring-teal-500 w-4 h-4">
                                <span class="text-xs font-semibold text-slate-700">Active rule</span>
                            </label>
                        </div>
                    </div>

                    <div class="p-4 bg-slate-50 border-t border-slate-100 flex items-center justify-between">
                        <form method="POST" :action="'/admin/redirects/' + editRule.id + '/reset-hits'" class="inline">
                            @csrf
                            <button type="submit" class="text-xs text-slate-500 hover:text-amber-600 font-medium">
                                Reset Hits (<span x-text="editRule.hits || 0"></span>)
                            </button>
                        </form>
                        <div class="flex items-center gap-2">
                            <button type="button" @click="editModalOpen = false" class="px-4 py-2 rounded-xl bg-white border border-slate-200 text-slate-600 text-xs font-semibold hover:bg-slate-50 transition">
                                Cancel
                            </button>
                            <button type="submit" class="px-5 py-2 rounded-xl bg-slate-900 hover:bg-slate-800 text-white text-xs font-bold shadow-md transition">
                                Update Redirect
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- IMPORT CSV MODAL -->
    <div x-show="importModalOpen" x-cloak
         class="fixed inset-0 z-50 overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
        <div class="flex items-center justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:block sm:p-0">
            <div x-show="importModalOpen" x-transition.opacity @click="importModalOpen = false" 
                 class="fixed inset-0 bg-slate-900/80 backdrop-blur-sm transition-opacity"></div>

            <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>

            <div x-show="importModalOpen" x-transition 
                 class="inline-block align-bottom bg-white rounded-2xl text-left overflow-hidden shadow-2xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full border border-slate-200">
                <form method="POST" action="{{ route('admin.redirects.import') }}" enctype="multipart/form-data">
                    @csrf
                    <div class="p-6 space-y-4">
                        <div class="flex items-center justify-between pb-3 border-b border-slate-100">
                            <div class="flex items-center gap-2.5">
                                <div class="w-8 h-8 rounded-xl bg-indigo-50 text-indigo-600 flex items-center justify-center font-bold">
                                    <i data-lucide="upload" class="w-4 h-4"></i>
                                </div>
                                <div>
                                    <h3 class="text-base font-black text-slate-900">Import Redirects from CSV</h3>
                                    <p class="text-xs text-slate-500">Bulk upload hundreds of 301 rules at once.</p>
                                </div>
                            </div>
                            <button type="button" @click="importModalOpen = false" class="text-slate-400 hover:text-slate-600">
                                <i data-lucide="x" class="w-5 h-5"></i>
                            </button>
                        </div>

                        <div>
                            <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-2">Select CSV File</label>
                            <input type="file" name="csv_file" required accept=".csv,text/csv"
                                   class="w-full text-xs text-slate-500 file:mr-4 file:py-2.5 file:px-4 file:rounded-xl file:border-0 file:text-xs file:font-semibold file:bg-teal-50 file:text-teal-700 hover:file:bg-teal-100 border border-slate-200 rounded-xl p-1">
                        </div>

                        <div class="p-3 bg-slate-50 rounded-xl border border-slate-200/80 text-[11px] text-slate-600 space-y-1">
                            <p class="font-bold text-slate-800">CSV Columns Supported:</p>
                            <p class="font-mono text-[10px] text-slate-500">Source URL, Target URL, Status Code, Match Type, Query Param, Group, Description</p>
                            <p class="text-slate-500">Compatible with Screaming Frog, Yoast SEO, and WordPress Redirection plugin exports.</p>
                        </div>
                    </div>

                    <div class="p-4 bg-slate-50 border-t border-slate-100 flex items-center justify-end gap-2">
                        <button type="button" @click="importModalOpen = false" class="px-4 py-2 rounded-xl bg-white border border-slate-200 text-slate-600 text-xs font-semibold hover:bg-slate-50 transition">
                            Cancel
                        </button>
                        <button type="submit" class="px-5 py-2 rounded-xl bg-indigo-600 hover:bg-indigo-500 text-white text-xs font-bold shadow-md transition">
                            Upload & Process CSV
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

</div>

@push('scripts')
<script>
    function redirectsManager() {
        return {
            createModalOpen: {{ request('action') === 'new' || request('open_create') === 'true' ? 'true' : 'false' }},
            editModalOpen: false,
            importModalOpen: false,
            testDrawerOpen: false,
            newStatusCode: 301,
            editRule: {},
            editFormAction: '',
            testUrlInput: '',
            testing: false,
            testResult: null,
            selectedIds: [],
            bulkAction: '',

            init() {
                // Lucide icons re-initialization
                if (window.lucide) {
                    window.lucide.createIcons();
                }
            },

            openCreateModal() {
                this.newStatusCode = 301;
                this.createModalOpen = true;
                this.$nextTick(() => { if (window.lucide) window.lucide.createIcons(); });
            },

            openEditModal(rule) {
                this.editRule = Object.assign({}, rule);
                this.editFormAction = '/admin/redirects/' + rule.id;
                this.editModalOpen = true;
                this.$nextTick(() => { if (window.lucide) window.lucide.createIcons(); });
            },

            toggleSelect(id) {
                const idx = this.selectedIds.indexOf(id);
                if (idx > -1) {
                    this.selectedIds.splice(idx, 1);
                } else {
                    this.selectedIds.push(id);
                }
            },

            toggleSelectAll(e) {
                if (e.target.checked) {
                    const checkboxes = document.querySelectorAll('input[name="selected[]"]');
                    this.selectedIds = Array.from(checkboxes).map(cb => parseInt(cb.value));
                } else {
                    this.selectedIds = [];
                }
            },

            submitBulk() {
                if (this.selectedIds.length === 0 || !this.bulkAction) return;

                if (this.bulkAction === 'delete') {
                    if (!confirm('Are you sure you want to permanently delete ' + this.selectedIds.length + ' redirect rules?')) {
                        return;
                    }
                }

                document.getElementById('bulkForm').submit();
            },

            async runTest() {
                if (!this.testUrlInput.trim()) return;
                this.testing = true;
                this.testResult = null;

                try {
                    const response = await fetch('{{ route('admin.redirects.test') }}', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                            'Accept': 'application/json'
                        },
                        body: JSON.stringify({ url: this.testUrlInput })
                    });
                    this.testResult = await response.json();
                } catch (e) {
                    this.testResult = {
                        matched: false,
                        message: 'Error executing test: ' + e.message,
                        latency_ms: 0
                    };
                } finally {
                    this.testing = false;
                    this.$nextTick(() => { if (window.lucide) window.lucide.createIcons(); });
                }
            },

            copyToClipboard(text) {
                navigator.clipboard.writeText(text).then(() => {
                    alert('Copied to clipboard: ' + text);
                }).catch(() => {
                    prompt('Copy this link:', text);
                });
            }
        };
    }
</script>
@endpush
@endsection
