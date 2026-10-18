<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="h-full bg-slate-900">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>@yield('title', 'Dashboard') - {{ config('app.name', 'LifeGuard Data Recovery') }} Admin Panel</title>

    <!-- Favicon & Icons -->
    <link rel="icon" type="image/x-icon" href="{{ asset('favicon.ico') }}">
    <link rel="icon" type="image/png" sizes="32x32" href="{{ asset('favicon.png') }}">
    <link rel="apple-touch-icon" sizes="180x180" href="{{ asset('apple-touch-icon.png') }}">

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">

    <!-- Tailwind CSS CDN -->
    <script src="https://cdn.tailwindcss.com?plugins=typography,forms,aspect-ratio"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    fontFamily: {
                        sans: ['"Plus Jakarta Sans"', 'system-ui', 'sans-serif'],
                    },
                    colors: {
                        brand: {
                            50: '#f0f9ff',
                            100: '#e0f2fe',
                            200: '#bae6fd',
                            300: '#7dd3fc',
                            400: '#38bdf8',
                            500: '#0ea5e9',
                            600: '#0284c7',
                            700: '#0369a1',
                            800: '#075985',
                            900: '#0c4a6e',
                            950: '#082f49',
                        }
                    }
                }
            }
        }
    </script>

    <!-- Alpine.js -->
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.14.8/dist/cdn.min.js"></script>

    <!-- Lucide Icons -->
    <script src="https://unpkg.com/lucide@latest"></script>

    <!-- Quill WYSIWYG CSS -->
    <link href="https://cdn.jsdelivr.net/npm/quill@2.0.2/dist/quill.snow.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/quill@2.0.2/dist/quill.js"></script>

    <style>
        [x-cloak] { display: none !important; }
        .ql-toolbar.ql-snow { border-top-left-radius: 0.5rem; border-top-right-radius: 0.5rem; border-color: #e2e8f0; background: #f8fafc; }
        .ql-container.ql-snow { border-bottom-left-radius: 0.5rem; border-bottom-right-radius: 0.5rem; border-color: #e2e8f0; min-height: 360px; font-size: 1rem; }
    </style>
    @stack('styles')
</head>
<body class="h-full bg-slate-100 font-sans text-slate-800 antialiased" x-data="{ sidebarOpen: false }">
    <div class="min-h-full flex">
        <!-- Sidebar Backdrop for Mobile -->
        <div x-show="sidebarOpen" x-cloak @click="sidebarOpen = false" 
             class="fixed inset-0 z-40 bg-slate-900/80 backdrop-blur-sm lg:hidden transition-opacity"></div>

        <!-- WordPress-style Sidebar -->
        <aside :class="sidebarOpen ? 'translate-x-0' : '-translate-x-full lg:translate-x-0'"
               class="fixed inset-y-0 left-0 z-50 w-64 bg-slate-900 text-slate-300 transition-transform duration-200 ease-in-out lg:static lg:inset-auto lg:translate-x-0 flex flex-col shadow-xl">
            <!-- Brand -->
            <div class="h-16 flex items-center justify-between px-6 bg-slate-950 border-b border-slate-800/80">
                <a href="{{ route('admin.dashboard') }}" class="flex items-center gap-2.5">
                    <div class="w-7 h-7 rounded-lg bg-sky-500 text-white flex items-center justify-center font-black">
                        <i data-lucide="shield-check" class="w-4 h-4 fill-current"></i>
                    </div>
                    <span class="text-sm font-black tracking-tight text-white">LIFEGUARD <span class="text-sky-400">DUBAI</span></span>
                    <span class="text-[9px] uppercase tracking-widest font-extrabold text-sky-300 bg-sky-950/60 px-1.5 py-0.5 rounded border border-sky-500/30">CMS</span>
                </a>
                <button @click="sidebarOpen = false" class="lg:hidden text-slate-400 hover:text-white">
                    <i data-lucide="x" class="w-5 h-5"></i>
                </button>
            </div>

            <!-- Navigation Links -->
            <div class="flex-1 overflow-y-auto py-5 px-3 space-y-1">
                <!-- Dashboard -->
                <a href="{{ route('admin.dashboard') }}" 
                   class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm font-medium transition-colors {{ request()->routeIs('admin.dashboard') ? 'bg-brand-600 text-white shadow-sm shadow-brand-600/30 font-semibold' : 'hover:bg-slate-800 hover:text-white text-slate-300' }}">
                    <i data-lucide="layout-dashboard" class="w-4 h-4 shrink-0"></i>
                    <span>Dashboard</span>
                </a>

                <div class="pt-4 pb-1 px-3 text-[10px] font-bold uppercase tracking-wider text-slate-400">Services & Lab</div>

                <!-- Services Menu -->
                <div x-data="{ open: {{ request()->routeIs('admin.services.*') ? 'true' : 'false' }} }">
                    <button @click="open = !open" 
                            class="w-full flex items-center justify-between px-3 py-2.5 rounded-lg text-sm font-medium transition-colors {{ request()->routeIs('admin.services.*') ? 'bg-slate-800/80 text-white font-semibold' : 'hover:bg-slate-800 hover:text-white text-slate-300' }}">
                        <div class="flex items-center gap-3">
                            <i data-lucide="layers" class="w-4 h-4 shrink-0 text-brand-400"></i>
                            <span>Recovery Services</span>
                        </div>
                        <i data-lucide="chevron-down" class="w-4 h-4 transition-transform duration-200" :class="open ? 'rotate-180' : ''"></i>
                    </button>
                    <div x-show="open" x-cloak class="pl-9 pr-2 py-1.5 space-y-1 text-xs">
                        <a href="{{ route('admin.services.index') }}" class="block py-1.5 px-2 rounded hover:text-white transition {{ request()->routeIs('admin.services.index') ? 'text-brand-400 font-semibold' : 'text-slate-400' }}">All Services</a>
                        <a href="{{ route('admin.services.create') }}" class="block py-1.5 px-2 rounded hover:text-white transition {{ request()->routeIs('admin.services.create') ? 'text-brand-400 font-semibold' : 'text-slate-400' }}">Add New Service</a>
                    </div>
                </div>

                <!-- Diagnostics & Hardware Menu -->
                <div x-data="{ open: {{ request()->routeIs('admin.products.*') ? 'true' : 'false' }} }">
                    <button @click="open = !open" 
                            class="w-full flex items-center justify-between px-3 py-2.5 rounded-lg text-sm font-medium transition-colors {{ request()->routeIs('admin.products.*') ? 'bg-slate-800/80 text-white font-semibold' : 'hover:bg-slate-800 hover:text-white text-slate-300' }}">
                        <div class="flex items-center gap-3">
                            <i data-lucide="cpu" class="w-4 h-4 shrink-0 text-sky-400"></i>
                            <span>Lab Equipment / Models</span>
                        </div>
                        <i data-lucide="chevron-down" class="w-4 h-4 transition-transform duration-200" :class="open ? 'rotate-180' : ''"></i>
                    </button>
                    <div x-show="open" x-cloak class="pl-9 pr-2 py-1.5 space-y-1 text-xs">
                        <a href="{{ route('admin.products.index') }}" class="block py-1.5 px-2 rounded hover:text-white transition {{ request()->routeIs('admin.products.index') ? 'text-brand-400 font-semibold' : 'text-slate-400' }}">All Equipment / Models</a>
                        <a href="{{ route('admin.products.create') }}" class="block py-1.5 px-2 rounded hover:text-white transition {{ request()->routeIs('admin.products.create') ? 'text-brand-400 font-semibold' : 'text-slate-400' }}">Add New Model</a>
                    </div>
                </div>

                <div class="pt-4 pb-1 px-3 text-[10px] font-bold uppercase tracking-wider text-slate-400">Leads & Customers</div>

                <!-- Client Leads & Inquiries -->
                <a href="{{ route('admin.inquiries.index') }}" 
                   class="flex items-center justify-between px-3 py-2.5 rounded-lg text-sm font-medium transition-colors {{ request()->routeIs('admin.inquiries.*') ? 'bg-brand-600 text-white shadow-sm shadow-brand-600/30 font-semibold' : 'hover:bg-slate-800 hover:text-white text-slate-300' }}">
                    <div class="flex items-center gap-3">
                        <i data-lucide="inbox" class="w-4 h-4 shrink-0 text-brand-400"></i>
                        <span>Leads & Inquiries</span>
                    </div>
                    @php $unreadLeads = \App\Models\Inquiry::unread()->count(); @endphp
                    @if($unreadLeads > 0)
                        <span class="text-[10px] font-extrabold px-1.5 py-0.5 rounded-full bg-rose-500 text-white animate-pulse">
                            {{ $unreadLeads }}
                        </span>
                    @endif
                </a>

                <div class="pt-4 pb-1 px-3 text-[10px] font-bold uppercase tracking-wider text-slate-400">Content Management</div>

                <!-- Posts Menu (Accordion / Group) -->
                <div x-data="{ open: {{ request()->routeIs('admin.posts.*') || request()->routeIs('admin.categories.*') || request()->routeIs('admin.tags.*') ? 'true' : 'false' }} }">
                    <button @click="open = !open" 
                            class="w-full flex items-center justify-between px-3 py-2.5 rounded-lg text-sm font-medium transition-colors {{ request()->routeIs('admin.posts.*') || request()->routeIs('admin.categories.*') || request()->routeIs('admin.tags.*') ? 'bg-slate-800/80 text-white font-semibold' : 'hover:bg-slate-800 hover:text-white text-slate-300' }}">
                        <div class="flex items-center gap-3">
                            <i data-lucide="pin" class="w-4 h-4 shrink-0"></i>
                            <span>Posts</span>
                        </div>
                        <i data-lucide="chevron-down" class="w-4 h-4 transition-transform duration-200" :class="open ? 'rotate-180' : ''"></i>
                    </button>
                    <div x-show="open" x-cloak class="pl-9 pr-2 py-1.5 space-y-1 text-xs">
                        <a href="{{ route('admin.posts.index') }}" class="block py-1.5 px-2 rounded hover:text-white transition {{ request()->routeIs('admin.posts.index') ? 'text-brand-400 font-semibold' : 'text-slate-400' }}">All Posts</a>
                        <a href="{{ route('admin.posts.create') }}" class="block py-1.5 px-2 rounded hover:text-white transition {{ request()->routeIs('admin.posts.create') ? 'text-brand-400 font-semibold' : 'text-slate-400' }}">Add New Post</a>
                        <a href="{{ route('admin.categories.index') }}" class="block py-1.5 px-2 rounded hover:text-white transition {{ request()->routeIs('admin.categories.*') ? 'text-brand-400 font-semibold' : 'text-slate-400' }}">Categories</a>
                        <a href="{{ route('admin.tags.index') }}" class="block py-1.5 px-2 rounded hover:text-white transition {{ request()->routeIs('admin.tags.*') ? 'text-brand-400 font-semibold' : 'text-slate-400' }}">Tags</a>
                    </div>
                </div>

                <!-- Pages Menu -->
                <div x-data="{ open: {{ request()->routeIs('admin.pages.*') ? 'true' : 'false' }} }">
                    <button @click="open = !open" 
                            class="w-full flex items-center justify-between px-3 py-2.5 rounded-lg text-sm font-medium transition-colors {{ request()->routeIs('admin.pages.*') ? 'bg-slate-800/80 text-white font-semibold' : 'hover:bg-slate-800 hover:text-white text-slate-300' }}">
                        <div class="flex items-center gap-3">
                            <i data-lucide="file-text" class="w-4 h-4 shrink-0"></i>
                            <span>Pages</span>
                        </div>
                        <i data-lucide="chevron-down" class="w-4 h-4 transition-transform duration-200" :class="open ? 'rotate-180' : ''"></i>
                    </button>
                    <div x-show="open" x-cloak class="pl-9 pr-2 py-1.5 space-y-1 text-xs">
                        <a href="{{ route('admin.pages.index') }}" class="block py-1.5 px-2 rounded hover:text-white transition {{ request()->routeIs('admin.pages.index') ? 'text-brand-400 font-semibold' : 'text-slate-400' }}">All Pages</a>
                        <a href="{{ route('admin.pages.create') }}" class="block py-1.5 px-2 rounded hover:text-white transition {{ request()->routeIs('admin.pages.create') ? 'text-brand-400 font-semibold' : 'text-slate-400' }}">Add New Page</a>
                    </div>
                </div>

                <!-- Media Library -->
                <a href="{{ route('admin.media.index') }}" 
                   class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm font-medium transition-colors {{ request()->routeIs('admin.media.*') ? 'bg-brand-600 text-white shadow-sm shadow-brand-600/30 font-semibold' : 'hover:bg-slate-800 hover:text-white text-slate-300' }}">
                    <i data-lucide="image" class="w-4 h-4 shrink-0"></i>
                    <span>Media Library</span>
                </a>

                <!-- Navigation Menu Management -->
                <a href="{{ route('admin.menus.index') }}" 
                   class="flex items-center justify-between px-3 py-2.5 rounded-lg text-sm font-medium transition-colors {{ request()->routeIs('admin.menus.*') ? 'bg-brand-600 text-white shadow-sm shadow-brand-600/30 font-semibold' : 'hover:bg-slate-800 hover:text-white text-slate-300' }}">
                    <div class="flex items-center gap-3">
                        <i data-lucide="menu" class="w-4 h-4 shrink-0 text-brand-400"></i>
                        <span>Navigation Menu</span>
                    </div>
                    <span class="text-[9px] uppercase px-1.5 py-0.5 rounded font-bold bg-brand-500/20 text-brand-200 border border-brand-400/20">Header</span>
                </a>

                <!-- 301 Redirection Manager -->
                <a href="{{ route('admin.redirects.index') }}" 
                   class="flex items-center justify-between px-3 py-2.5 rounded-lg text-sm font-medium transition-colors {{ request()->routeIs('admin.redirects.*') ? 'bg-brand-600 text-white shadow-sm shadow-brand-600/30 font-semibold' : 'hover:bg-slate-800 hover:text-white text-slate-300' }}">
                    <div class="flex items-center gap-3">
                        <i data-lucide="arrow-left-right" class="w-4 h-4 shrink-0 text-amber-400"></i>
                        <span>301 Redirection</span>
                    </div>
                    @php $activeRedirectsCount = \App\Models\RedirectRule::where('is_active', true)->count(); @endphp
                    @if($activeRedirectsCount > 0)
                        <span class="text-[10px] font-bold px-1.5 py-0.5 rounded-full bg-slate-800 text-emerald-400 border border-emerald-500/20">
                            {{ $activeRedirectsCount }}
                        </span>
                    @endif
                </a>

                @if(auth()->user()->isAdmin())
                    <div class="pt-4 pb-1 px-3 text-[10px] font-bold uppercase tracking-wider text-slate-400">Administration</div>

                    <!-- Users Menu -->
                    <div x-data="{ open: {{ request()->routeIs('admin.users.*') ? 'true' : 'false' }} }">
                        <button @click="open = !open" 
                                class="w-full flex items-center justify-between px-3 py-2.5 rounded-lg text-sm font-medium transition-colors {{ request()->routeIs('admin.users.*') ? 'bg-slate-800/80 text-white font-semibold' : 'hover:bg-slate-800 hover:text-white text-slate-300' }}">
                            <div class="flex items-center gap-3">
                                <i data-lucide="users" class="w-4 h-4 shrink-0"></i>
                                <span>Users</span>
                            </div>
                            <i data-lucide="chevron-down" class="w-4 h-4 transition-transform duration-200" :class="open ? 'rotate-180' : ''"></i>
                        </button>
                        <div x-show="open" x-cloak class="pl-9 pr-2 py-1.5 space-y-1 text-xs">
                            <a href="{{ route('admin.users.index') }}" class="block py-1.5 px-2 rounded hover:text-white transition {{ request()->routeIs('admin.users.index') ? 'text-brand-400 font-semibold' : 'text-slate-400' }}">All Users</a>
                            <a href="{{ route('admin.users.create') }}" class="block py-1.5 px-2 rounded hover:text-white transition {{ request()->routeIs('admin.users.create') ? 'text-brand-400 font-semibold' : 'text-slate-400' }}">Add New User</a>
                        </div>
                    </div>
                @endif

                <div class="pt-4 pb-1 px-3 text-[10px] font-bold uppercase tracking-wider text-slate-400">Settings</div>

                <!-- Profile -->
                <a href="{{ route('admin.profile.edit') }}" 
                   class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm font-medium transition-colors {{ request()->routeIs('admin.profile.*') ? 'bg-brand-600 text-white shadow-sm shadow-brand-600/30 font-semibold' : 'hover:bg-slate-800 hover:text-white text-slate-300' }}">
                    <i data-lucide="user-check" class="w-4 h-4 shrink-0"></i>
                    <span>My Profile</span>
                </a>

                <!-- Clear Cache -->
                <form action="{{ route('admin.cache.clear') }}" method="POST" class="mt-1">
                    @csrf
                    <input type="hidden" name="type" value="all">
                    <button type="submit" class="w-full flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm font-medium text-amber-300 hover:bg-slate-800 hover:text-amber-200 transition-colors text-left" title="Purge compiled views and system cache">
                        <i data-lucide="refresh-cw" class="w-4 h-4 shrink-0 text-amber-400"></i>
                        <span>Clear Cache</span>
                    </button>
                </form>
            </div>

            <!-- Bottom: View Site Link -->
            <div class="p-3 bg-slate-950 border-t border-slate-800/80">
                <a href="{{ route('home') }}" target="_blank" class="flex items-center justify-between px-3 py-2 text-xs font-semibold rounded-lg bg-slate-900 text-slate-300 hover:text-white hover:bg-slate-800 transition">
                    <span class="flex items-center gap-2">
                        <i data-lucide="external-link" class="w-3.5 h-3.5 text-sky-400"></i>
                        <span>Visit Website</span>
                    </span>
                    <span class="text-[10px] bg-slate-800 px-1.5 py-0.5 rounded text-slate-400">Frontend</span>
                </a>
            </div>
        </aside>

        <!-- Right Side Main Section -->
        <div class="flex-1 flex flex-col min-w-0 overflow-hidden">
            <!-- Top Header Bar -->
            <header class="h-16 bg-white border-b border-slate-200 flex items-center justify-between px-4 sm:px-6 z-20">
                <div class="flex items-center gap-3">
                    <button @click="sidebarOpen = !sidebarOpen" class="lg:hidden p-2 text-slate-500 hover:text-slate-900 rounded-lg hover:bg-slate-100">
                        <i data-lucide="menu" class="w-5 h-5"></i>
                    </button>
                    <!-- Quick "+ New" Dropdown -->
                    <div class="relative" x-data="{ open: false }">
                        <button @click="open = !open" class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg bg-slate-100 hover:bg-slate-200 text-xs font-semibold text-slate-700 transition">
                            <i data-lucide="plus" class="w-3.5 h-3.5 text-brand-600"></i>
                            <span>New</span>
                            <i data-lucide="chevron-down" class="w-3 h-3 text-slate-400"></i>
                        </button>
                        <div x-show="open" x-cloak @click.outside="open = false" 
                             class="absolute left-0 mt-1 w-44 rounded-xl bg-white shadow-lg border border-slate-200 py-1 text-xs z-30">
                            <a href="{{ route('admin.services.create') }}" class="flex items-center gap-2 px-3 py-2 text-slate-700 hover:bg-slate-50"><i data-lucide="layers" class="w-3.5 h-3.5 text-sky-500"></i> Service</a>
                            <a href="{{ route('admin.posts.create') }}" class="flex items-center gap-2 px-3 py-2 text-slate-700 hover:bg-slate-50"><i data-lucide="pin" class="w-3.5 h-3.5 text-brand-600"></i> Post</a>
                            <a href="{{ route('admin.pages.create') }}" class="flex items-center gap-2 px-3 py-2 text-slate-700 hover:bg-slate-50"><i data-lucide="file-text" class="w-3.5 h-3.5 text-emerald-600"></i> Page</a>
                            <a href="{{ route('admin.menus.index') }}" class="flex items-center gap-2 px-3 py-2 text-slate-700 hover:bg-slate-50"><i data-lucide="menu" class="w-3.5 h-3.5 text-amber-500"></i> Menu Item</a>
                            <a href="{{ route('admin.redirects.index', ['open_create' => 'true']) }}" class="flex items-center gap-2 px-3 py-2 text-slate-700 hover:bg-slate-50"><i data-lucide="arrow-left-right" class="w-3.5 h-3.5 text-amber-600"></i> 301 Redirect</a>
                            <a href="{{ route('admin.media.index') }}" class="flex items-center gap-2 px-3 py-2 text-slate-700 hover:bg-slate-50"><i data-lucide="image" class="w-3.5 h-3.5 text-indigo-600"></i> Media</a>
                            @if(auth()->user()->isAdmin())
                                <a href="{{ route('admin.users.create') }}" class="flex items-center gap-2 px-3 py-2 text-slate-700 hover:bg-slate-50"><i data-lucide="user-plus" class="w-3.5 h-3.5 text-purple-600"></i> User</a>
                            @endif
                        </div>
                    </div>

                    <!-- Clear Cache Dropdown -->
                    <div class="relative" x-data="{ cacheOpen: false }">
                        <button @click="cacheOpen = !cacheOpen" 
                                class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg bg-amber-50 hover:bg-amber-100 text-xs font-semibold text-amber-800 border border-amber-200/70 transition" 
                                title="Clear Website Cache">
                            <i data-lucide="refresh-cw" class="w-3.5 h-3.5 text-amber-600"></i>
                            <span class="hidden sm:inline">Clear Cache</span>
                            <i data-lucide="chevron-down" class="w-3 h-3 text-amber-600"></i>
                        </button>
                        <div x-show="cacheOpen" x-cloak @click.outside="cacheOpen = false" 
                             class="absolute left-0 mt-1 w-56 rounded-xl bg-white shadow-xl border border-slate-200 py-1.5 text-xs z-30 divide-y divide-slate-100">
                            <div class="px-3 py-2 text-[11px] text-slate-500 font-medium">
                                Purge compiled views, application data, routes, and config caches.
                            </div>
                            <div class="py-1">
                                <form action="{{ route('admin.cache.clear') }}" method="POST">
                                    @csrf
                                    <input type="hidden" name="type" value="all">
                                    <button type="submit" class="w-full flex items-center justify-between px-3 py-2 text-slate-800 hover:bg-amber-50 hover:text-amber-900 text-left font-bold transition">
                                        <span class="flex items-center gap-2"><i data-lucide="sparkles" class="w-3.5 h-3.5 text-amber-500"></i> Purge All Caches</span>
                                        <span class="text-[10px] text-amber-700 bg-amber-100 px-1.5 py-0.5 rounded font-normal">All</span>
                                    </button>
                                </form>
                            </div>
                            <div class="py-1">
                                <form action="{{ route('admin.cache.clear') }}" method="POST">
                                    @csrf
                                    <input type="hidden" name="type" value="views">
                                    <button type="submit" class="w-full flex items-center gap-2 px-3 py-2 text-slate-700 hover:bg-slate-50 text-left transition">
                                        <i data-lucide="file-code" class="w-3.5 h-3.5 text-sky-500"></i> Clear Compiled Views
                                    </button>
                                </form>
                                <form action="{{ route('admin.cache.clear') }}" method="POST">
                                    @csrf
                                    <input type="hidden" name="type" value="cache">
                                    <button type="submit" class="w-full flex items-center gap-2 px-3 py-2 text-slate-700 hover:bg-slate-50 text-left transition">
                                        <i data-lucide="database" class="w-3.5 h-3.5 text-emerald-500"></i> Clear Data Cache
                                    </button>
                                </form>
                                <form action="{{ route('admin.cache.clear') }}" method="POST">
                                    @csrf
                                    <input type="hidden" name="type" value="routes">
                                    <button type="submit" class="w-full flex items-center gap-2 px-3 py-2 text-slate-700 hover:bg-slate-50 text-left transition">
                                        <i data-lucide="network" class="w-3.5 h-3.5 text-indigo-500"></i> Clear Route Cache
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- User Profile Menu -->
                <div class="flex items-center gap-4">
                    <div class="relative" x-data="{ menuOpen: false }">
                        <button @click="menuOpen = !menuOpen" class="flex items-center gap-2.5 p-1 rounded-full hover:bg-slate-100 transition">
                            <img src="{{ auth()->user()->avatar_url }}" alt="{{ auth()->user()->name }}" class="w-8 h-8 rounded-full object-cover border border-slate-200">
                            <div class="hidden sm:block text-left text-xs">
                                <span class="font-bold text-slate-800 block leading-tight">{{ auth()->user()->name }}</span>
                                <span class="text-[10px] uppercase font-semibold text-brand-600">{{ auth()->user()->role }}</span>
                            </div>
                            <i data-lucide="chevron-down" class="w-3.5 h-3.5 text-slate-400"></i>
                        </button>
                        <div x-show="menuOpen" x-cloak @click.outside="menuOpen = false"
                             class="absolute right-0 mt-2 w-48 rounded-xl bg-white shadow-xl border border-slate-200 py-1.5 text-xs text-slate-700 z-30">
                            <div class="px-3 py-2 border-b border-slate-100">
                                <p class="font-semibold text-slate-900 truncate">{{ auth()->user()->name }}</p>
                                <p class="text-[10px] text-slate-500 truncate">{{ auth()->user()->email }}</p>
                            </div>
                            <a href="{{ route('admin.profile.edit') }}" class="flex items-center gap-2 px-3 py-2 hover:bg-slate-50">
                                <i data-lucide="user" class="w-3.5 h-3.5 text-slate-400"></i> Profile
                            </a>
                            <a href="{{ route('home') }}" target="_blank" class="flex items-center gap-2 px-3 py-2 hover:bg-slate-50">
                                <i data-lucide="external-link" class="w-3.5 h-3.5 text-slate-400"></i> View Site
                            </a>
                            <form action="{{ route('admin.cache.clear') }}" method="POST">
                                @csrf
                                <input type="hidden" name="type" value="all">
                                <button type="submit" class="w-full flex items-center gap-2 px-3 py-2 text-amber-700 hover:bg-amber-50 text-left">
                                    <i data-lucide="refresh-cw" class="w-3.5 h-3.5 text-amber-500"></i> Clear Cache
                                </button>
                            </form>
                            <div class="border-t border-slate-100 my-1"></div>
                            <form action="{{ route('logout') }}" method="POST">
                                @csrf
                                <button type="submit" class="w-full flex items-center gap-2 px-3 py-2 text-rose-600 hover:bg-rose-50 text-left">
                                    <i data-lucide="log-out" class="w-3.5 h-3.5"></i> Log Out
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            </header>

            <!-- Main Scrollable Content Area -->
            <main class="flex-1 overflow-y-auto p-4 sm:p-6 lg:p-8">
                <!-- Session Flash Messages -->
                @if(session('success'))
                    <div class="mb-6 rounded-xl bg-emerald-50 border border-emerald-200 p-4 flex items-center justify-between text-emerald-800 text-sm shadow-sm" x-data="{ show: true }" x-show="show">
                        <div class="flex items-center gap-3">
                            <i data-lucide="check-circle" class="w-5 h-5 text-emerald-600 shrink-0"></i>
                            <span>{{ session('success') }}</span>
                        </div>
                        <button @click="show = false" class="text-emerald-600 hover:text-emerald-800"><i data-lucide="x" class="w-4 h-4"></i></button>
                    </div>
                @endif

                @if(session('error'))
                    <div class="mb-6 rounded-xl bg-rose-50 border border-rose-200 p-4 flex items-center justify-between text-rose-800 text-sm shadow-sm" x-data="{ show: true }" x-show="show">
                        <div class="flex items-center gap-3">
                            <i data-lucide="alert-circle" class="w-5 h-5 text-rose-600 shrink-0"></i>
                            <span>{{ session('error') }}</span>
                        </div>
                        <button @click="show = false" class="text-rose-600 hover:text-rose-800"><i data-lucide="x" class="w-4 h-4"></i></button>
                    </div>
                @endif

                @if(isset($errors) && $errors->any())
                    <div class="mb-6 rounded-xl bg-rose-50 border border-rose-200 p-4 text-rose-800 text-sm shadow-sm">
                        <div class="flex items-center gap-2 font-semibold text-rose-900 mb-1">
                            <i data-lucide="alert-triangle" class="w-4 h-4 text-rose-600"></i>
                            <span>Please fix the following errors:</span>
                        </div>
                        <ul class="list-disc list-inside space-y-1 text-xs text-rose-700">
                            @foreach($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                @yield('content')
            </main>
        </div>
    </div>

    <!-- Include Reusable Media Picker Modal -->
    @include('admin.partials.media-modal')

    <!-- Include Reusable Yoast Quick SEO Modal -->
    @include('admin.partials.quick-seo-modal')

    <script>
        if (typeof lucide !== 'undefined' && typeof lucide.createIcons === 'function') {
            lucide.createIcons();
        } else if (window.lucide && typeof window.lucide.createIcons === 'function') {
            window.lucide.createIcons();
        }
    </script>
    @stack('scripts')
</body>
</html>
