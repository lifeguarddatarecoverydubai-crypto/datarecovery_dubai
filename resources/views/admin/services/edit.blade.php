@extends('layouts.admin')

@section('title', 'Edit Service: ' . $service->title)

@section('content')
<div class="space-y-6">
    <div class="flex items-center justify-between">
        <div>
            <div class="flex items-center gap-2 text-xs text-slate-500 mb-1">
                <a href="{{ route('admin.services.index') }}" class="hover:text-brand-600 transition">Recovery Services</a>
                <span>&rsaquo;</span>
                <span class="text-slate-800 font-semibold truncate max-w-xs">{{ $service->title }}</span>
            </div>
            <h1 class="text-2xl font-black text-slate-900 tracking-tight">Edit Service: {{ $service->title }}</h1>
        </div>
        <div class="flex items-center gap-2">
            <a href="{{ url('/'.$service->slug) }}" target="_blank" class="px-3.5 py-2 text-xs font-semibold text-brand-600 bg-brand-50 hover:bg-brand-100 rounded-xl transition flex items-center gap-1.5">
                <i data-lucide="external-link" class="w-3.5 h-3.5"></i>
                <span>View on Site</span>
            </a>
            <a href="{{ route('admin.services.index') }}" class="px-3.5 py-2 text-xs font-semibold text-slate-600 hover:text-slate-900 bg-white border border-slate-200 rounded-xl transition">
                &larr; Back to Services
            </a>
        </div>
    </div>

    @include('admin.services.form')
</div>
@endsection
