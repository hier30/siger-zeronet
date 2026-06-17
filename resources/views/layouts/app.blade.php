<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="Carbon Trade Dashboard Kota Bandar Lampung - Sistem monitoring emisi dan absorpsi karbon">
    <title>@yield('title', 'SigerZeroNet') — Kota Bandar Lampung</title>

    {{-- Google Fonts --}}
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">

    {{-- Leaflet CSS --}}
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />

    {{-- Vite Assets --}}
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    @stack('styles')
</head>
<body class="bg-[--color-surface] text-[--color-dark-text] font-sans min-h-screen">

    {{-- Sidebar --}}
    <aside id="sidebar" class="fixed top-0 left-0 h-full z-50 transition-all duration-300 ease-in-out"
           style="width: 260px; background: linear-gradient(180deg, #004236 0%, #006954 100%);">

        {{-- Logo Area --}}
        <div class="flex items-center gap-3 px-5 py-5 border-b border-white/10">
            <div class="w-10 h-10 rounded-xl bg-white/15 flex items-center justify-center flex-shrink-0">
                <svg class="w-6 h-6 text-emerald-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3.055 11H5a2 2 0 012 2v1a2 2 0 002 2 2 2 0 012 2v2.945M8 3.935V5.5A2.5 2.5 0 0010.5 8h.5a2 2 0 012 2 2 2 0 104 0 2 2 0 012-2h1.064M15 20.488V18a2 2 0 012-2h3.064M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
            </div>
            <div id="sidebar-logo" class="overflow-hidden">
                <h1 class="text-white font-bold text-sm leading-tight">SigerZeroNet</h1>
                <p class="text-emerald-300/70 text-xs">Kota Bandar Lampung</p>
            </div>
        </div>

        {{-- Navigation --}}
        <nav class="mt-4 px-3 space-y-1">
            {{-- Dashboard --}}
            <a href="{{ route('dashboard') }}"
               class="flex items-center gap-3 px-3 py-2.5 rounded-lg transition-all duration-200 group
                      {{ request()->routeIs('dashboard') ? 'bg-white/15 text-white shadow-lg shadow-black/10' : 'text-white/70 hover:bg-white/10 hover:text-white' }}">
                <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 5a1 1 0 011-1h14a1 1 0 011 1v2a1 1 0 01-1 1H5a1 1 0 01-1-1V5zM4 13a1 1 0 011-1h6a1 1 0 011 1v6a1 1 0 01-1 1H5a1 1 0 01-1-1v-6zM16 13a1 1 0 011-1h2a1 1 0 011 1v6a1 1 0 01-1 1h-2a1 1 0 01-1-1v-6z"/>
                </svg>
                <span class="sidebar-text text-sm font-medium">Dashboard</span>
            </a>

            {{-- Peta Emisi --}}
            <a href="{{ route('peta-emisi') }}"
               class="flex items-center gap-3 px-3 py-2.5 rounded-lg transition-all duration-200 group
                      {{ request()->routeIs('peta-emisi') ? 'bg-white/15 text-white shadow-lg shadow-black/10' : 'text-white/70 hover:bg-white/10 hover:text-white' }}">
                <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 20l-5.447-2.724A1 1 0 013 16.382V5.618a1 1 0 011.447-.894L9 7m0 13l6-3m-6 3V7m6 10l4.553 2.276A1 1 0 0021 18.382V7.618a1 1 0 00-.553-.894L15 4m0 13V4m0 0L9 7"/>
                </svg>
                <span class="sidebar-text text-sm font-medium">Peta Emisi</span>
            </a>

            {{-- Peta Absorpsi --}}
            <a href="{{ route('peta-absorpsi') }}"
               class="flex items-center gap-3 px-3 py-2.5 rounded-lg transition-all duration-200 group
                      {{ request()->routeIs('peta-absorpsi') ? 'bg-white/15 text-white shadow-lg shadow-black/10' : 'text-white/70 hover:bg-white/10 hover:text-white' }}">
                <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3.055 11H5a2 2 0 012 2v1a2 2 0 002 2 2 2 0 012 2v2.945M8 3.935V5.5A2.5 2.5 0 0010.5 8h.5a2 2 0 012 2 2 2 0 104 0 2 2 0 012-2h1.064M15 20.488V18a2 2 0 012-2h3.064M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
                <span class="sidebar-text text-sm font-medium">Peta Absorpsi</span>
            </a>

            {{-- Carbon Trade --}}
            <a href="{{ route('carbon-trade') }}"
               class="flex items-center gap-3 px-3 py-2.5 rounded-lg transition-all duration-200 group
                      {{ request()->routeIs('carbon-trade') ? 'bg-white/15 text-white shadow-lg shadow-black/10' : 'text-white/70 hover:bg-white/10 hover:text-white' }}">
                <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 7h6m0 10v-3m-3 3h.01M9 17h.01M9 14h.01M12 14h.01M15 11h.01M12 11h.01M9 11h.01M7 21h10a2 2 0 002-2V5a2 2 0 00-2-2H7a2 2 0 00-2 2v14a2 2 0 002 2z"/>
                </svg>
                <span class="sidebar-text text-sm font-medium">Carbon Trade</span>
            </a>

            @auth
                {{-- Kelola Data Carbon --}}
                <a href="{{ route('data-carbon.index') }}"
                   class="flex items-center gap-3 px-3 py-2.5 rounded-lg transition-all duration-200 group
                          {{ request()->routeIs('data-carbon.*') ? 'bg-white/15 text-white shadow-lg shadow-black/10' : 'text-white/70 hover:bg-white/10 hover:text-white' }}">
                    <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 7h16M4 12h16M4 17h16M8 3v18M16 3v18"/>
                    </svg>
                    <span class="sidebar-text text-sm font-medium">Kelola Data Carbon</span>
                </a>

                {{-- Sertifikat Karbon --}}
                <a href="{{ route('sertifikat-karbon.index') }}"
                   class="flex items-center gap-3 px-3 py-2.5 rounded-lg transition-all duration-200 group
                          {{ request()->routeIs('sertifikat-karbon.*') ? 'bg-white/15 text-white shadow-lg shadow-black/10' : 'text-white/70 hover:bg-white/10 hover:text-white' }}">
                    <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4M7 4h10a2 2 0 012 2v14l-7-3-7 3V6a2 2 0 012-2z"/>
                    </svg>
                    <span class="sidebar-text text-sm font-medium">Sertifikat Karbon</span>
                </a>
            @endauth
        </nav>

        {{-- Sidebar Footer --}}
        <div class="absolute bottom-0 left-0 right-0 px-3 py-4 border-t border-white/10 space-y-1">
            @guest
                <a href="{{ route('login') }}" class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-white/70 hover:text-white hover:bg-white/10 transition-all duration-200 w-full">
                    <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 7a2 2 0 012 2m4 0a6 6 0 01-7.743 5.743L11 17H9v2H7v2H4a1 1 0 01-1-1v-2.586l6.257-6.257A6 6 0 1121 9z"/>
                    </svg>
                    <span class="sidebar-text text-sm font-medium">Login</span>
                </a>
            @endguest
            @auth
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit" class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-white/70 hover:text-white hover:bg-white/10 transition-all duration-200 w-full">
                        <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a2 2 0 01-2 2H5a2 2 0 01-2-2V7a2 2 0 012-2h6a2 2 0 012 2v1"/>
                        </svg>
                        <span class="sidebar-text text-sm font-medium">Logout</span>
                    </button>
                </form>
            @endauth
            <button id="sidebar-toggle" class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-white/50 hover:text-white hover:bg-white/10 transition-all duration-200 w-full">
                <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 19l-7-7 7-7m8 14l-7-7 7-7"/>
                </svg>
                <span class="sidebar-text text-sm font-medium">Collapse</span>
            </button>
        </div>
    </aside>

    {{-- Main Content --}}
    <div id="main-content" class="transition-all duration-300 ease-in-out" style="margin-left: 260px;">
        {{-- Top Navbar --}}
        <header class="sticky top-0 z-40 bg-white/80 backdrop-blur-xl border-b border-gray-200/50">
            <div class="flex items-center justify-between px-6 py-4">
                <div>
                    <h2 class="text-lg font-bold text-[--color-dark-text]">@yield('page-title', 'Dashboard')</h2>
                    <p class="text-xs text-[--color-muted]">@yield('page-subtitle', 'Carbon Trade Monitoring System')</p>
                </div>
                <div class="flex items-center gap-4">
                    @hasSection('year-filter')
                        @yield('year-filter')
                    @endif
                </div>
            </div>
        </header>

        {{-- Page Content --}}
        <main class="p-6">
            @yield('content')
        </main>
    </div>

    {{-- Leaflet JS --}}
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
    {{-- Chart.js --}}
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
    {{-- SheetJS for Excel export --}}
    <script src="https://cdn.jsdelivr.net/npm/xlsx@0.18.5/dist/xlsx.full.min.js"></script>
    {{-- jsPDF for PDF export --}}
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf-autotable/3.8.1/jspdf.plugin.autotable.min.js"></script>

    @stack('scripts')
</body>
</html>
