<!DOCTYPE html>
<html lang="id" class="h-full bg-slate-50 text-slate-800">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Tuker.in') - Daur Ulang Jadi Poin</title>
    
    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=instrument-sans:400,500,600,700&display=swap" rel="stylesheet" />

    <!-- Style and Scripts -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    
    <style>
        body {
            font-family: 'Instrument Sans', sans-serif;
        }
    </style>
    @yield('styles')
</head>
<body class="flex flex-col min-h-full bg-gradient-to-tr from-slate-50 via-slate-100 to-emerald-50/20">

    <!-- Header / Navbar -->
    <nav class="sticky top-0 z-40 bg-white/80 backdrop-blur-md border-b border-slate-200/80">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between h-16">
                <!-- Logo -->
                <div class="flex items-center">
                    <a href="{{ url('/') }}" class="flex items-center gap-2 group">
                        <div class="w-9 h-9 rounded-xl bg-gradient-to-tr from-emerald-500 to-teal-400 flex items-center justify-center shadow-lg shadow-emerald-500/20 group-hover:scale-105 transition-all duration-300">
                            <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M4 4v5h.582m15.356 2A8.001 8.001 0 1121.21 8H17m-.5 8.5V17H12"></path>
                            </svg>
                        </div>
                        <span class="text-xl font-bold bg-gradient-to-r from-emerald-600 to-teal-600 bg-clip-text text-transparent tracking-tight">Tuker.in</span>
                    </a>
                </div>

                <!-- Right Nav -->
                <div class="flex items-center gap-3">
                    @auth
                        <div class="hidden md:flex items-center gap-3 mr-2">
                            <span class="text-xs font-semibold px-2.5 py-1 rounded-full 
                                {{ auth()->user()->isAdmin() ? 'bg-rose-50 text-rose-600 border border-rose-100' : '' }}
                                {{ auth()->user()->isEmployee() ? 'bg-amber-50 text-amber-600 border border-amber-100' : '' }}
                                {{ auth()->user()->isUser() ? 'bg-emerald-50 text-emerald-600 border border-emerald-100' : '' }}">
                                {{ auth()->user()->role->label }}
                            </span>
                            <span class="text-sm font-medium text-slate-600">Hai, <strong class="text-slate-800">{{ auth()->user()->name }}</strong></span>
                        </div>

                        <!-- Logout Button -->
                        <form method="POST" action="{{ route('logout') }}" class="inline">
                            @csrf
                            <button type="submit" class="flex items-center gap-1.5 px-3.5 py-1.5 text-xs font-semibold text-slate-500 hover:text-rose-600 hover:bg-rose-50 rounded-lg transition-all duration-200">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"></path>
                                </svg>
                                <span>Keluar</span>
                            </button>
                        </form>
                    @else
                        <a href="{{ route('login') }}" class="text-sm font-semibold text-slate-600 hover:text-emerald-600 transition-colors">Masuk</a>
                        <a href="{{ route('register') }}" class="px-4 py-2 text-sm font-semibold text-white bg-emerald-600 hover:bg-emerald-500 rounded-xl transition-all duration-200 shadow-md shadow-emerald-500/10">Daftar</a>
                    @endauth
                </div>
            </div>
        </div>
    </nav>

    <!-- Main Content Grid -->
    <main class="flex-1 max-w-7xl w-full mx-auto px-4 sm:px-6 lg:px-8 py-6 pb-24 md:pb-12">
        <!-- Flash Alerts -->
        @if (session('success'))
            <div class="mb-5 flex items-center gap-3 p-4 rounded-2xl bg-emerald-50 border border-emerald-100 text-emerald-800 shadow-sm animate-fade-in">
                <svg class="w-5.5 h-5.5 text-emerald-500 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
                <div class="text-sm font-medium">{{ session('success') }}</div>
            </div>
        @endif

        @if (session('error'))
            <div class="mb-5 flex items-center gap-3 p-4 rounded-2xl bg-rose-50 border border-rose-100 text-rose-800 shadow-sm animate-fade-in">
                <svg class="w-5.5 h-5.5 text-rose-500 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path>
                </svg>
                <div class="text-sm font-medium">{{ session('error') }}</div>
            </div>
        @endif

        @yield('content')
    </main>

    <!-- Bottom Nav for Mobile Screens (Mobile-first Layout shortcuts) -->
    @auth
        <div class="fixed bottom-0 left-0 right-0 z-40 bg-white/90 backdrop-blur-md border-t border-slate-200/80 md:hidden flex justify-around items-center h-16 px-2 shadow-[0_-4px_20px_rgba(0,0,0,0.04)]">
            @if(auth()->user()->isUser())
                <a href="{{ route('user.dashboard') }}" class="flex flex-col items-center justify-center w-16 h-full {{ request()->routeIs('user.dashboard') ? 'text-emerald-600' : 'text-slate-400 hover:text-slate-600' }}">
                    <svg class="w-5.5 h-5.5" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"></path>
                    </svg>
                    <span class="text-[10px] font-medium mt-1">Beranda</span>
                </a>
                <a href="{{ route('user.qr') }}" class="flex flex-col items-center justify-center w-16 h-full {{ request()->routeIs('user.qr') ? 'text-emerald-600' : 'text-slate-400 hover:text-slate-600' }}">
                    <svg class="w-5.5 h-5.5" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v1m6 11h2m-6 0h-2v4m0-11v3m0 0h.01M12 12h.01M16 12h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                    <span class="text-[10px] font-medium mt-1">QR Code</span>
                </a>
                <a href="{{ route('user.transactions') }}" class="flex flex-col items-center justify-center w-16 h-full {{ request()->routeIs('user.transactions') ? 'text-emerald-600' : 'text-slate-400 hover:text-slate-600' }}">
                    <svg class="w-5.5 h-5.5" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01"></path>
                    </svg>
                    <span class="text-[10px] font-medium mt-1">Transaksi</span>
                </a>
                <a href="{{ route('user.rewards') }}" class="flex flex-col items-center justify-center w-16 h-full {{ request()->routeIs('user.rewards*') ? 'text-emerald-600' : 'text-slate-400 hover:text-slate-600' }}">
                    <svg class="w-5.5 h-5.5" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                    <span class="text-[10px] font-medium mt-1">Cairkan</span>
                </a>
            @endif

            @if(auth()->user()->isEmployee())
                <a href="{{ route('employee.dashboard') }}" class="flex flex-col items-center justify-center w-20 h-full {{ request()->routeIs('employee.dashboard') ? 'text-emerald-600' : 'text-slate-400 hover:text-slate-600' }}">
                    <svg class="w-5.5 h-5.5" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"></path>
                    </svg>
                    <span class="text-[10px] font-medium mt-1">Beranda</span>
                </a>
                <a href="{{ route('employee.scan') }}" class="flex flex-col items-center justify-center w-20 h-full {{ request()->routeIs('employee.scan') ? 'text-emerald-600' : 'text-slate-400 hover:text-slate-600' }}">
                    <div class="relative -top-3 w-12 h-12 rounded-full bg-gradient-to-tr from-emerald-600 to-teal-500 flex items-center justify-center text-white shadow-md shadow-emerald-600/30 ring-4 ring-white">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v1m6 11h2m-6 0h-2v4m0-11v3m0 0h.01M12 12h.01M16 12h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                    </div>
                    <span class="text-[10px] font-medium -mt-2">Scan</span>
                </a>
                <a href="{{ route('employee.transactions') }}" class="flex flex-col items-center justify-center w-20 h-full {{ request()->routeIs('employee.transactions') ? 'text-emerald-600' : 'text-slate-400 hover:text-slate-600' }}">
                    <svg class="w-5.5 h-5.5" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01"></path>
                    </svg>
                    <span class="text-[10px] font-medium mt-1">Riwayat</span>
                </a>
            @endif

            @if(auth()->user()->isAdmin())
                <a href="{{ route('admin.dashboard') }}" class="flex flex-col items-center justify-center w-16 h-full {{ request()->routeIs('admin.dashboard') ? 'text-emerald-600' : 'text-slate-400 hover:text-slate-600' }}">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z"></path>
                    </svg>
                    <span class="text-[9px] font-medium mt-1">Dasbor</span>
                </a>
                <a href="{{ route('admin.employees.index') }}" class="flex flex-col items-center justify-center w-16 h-full {{ request()->routeIs('admin.employees*') ? 'text-emerald-600' : 'text-slate-400 hover:text-slate-600' }}">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"></path>
                    </svg>
                    <span class="text-[9px] font-medium mt-1">Pegawai</span>
                </a>
                <a href="{{ route('admin.users') }}" class="flex flex-col items-center justify-center w-16 h-full {{ request()->routeIs('admin.users') ? 'text-emerald-600' : 'text-slate-400 hover:text-slate-600' }}">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
                    </svg>
                    <span class="text-[9px] font-medium mt-1">Nasabah</span>
                </a>
                <a href="{{ route('admin.redemptions') }}" class="flex flex-col items-center justify-center w-16 h-full {{ request()->routeIs('admin.redemptions*') ? 'text-emerald-600' : 'text-slate-400 hover:text-slate-600' }}">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                    <span class="text-[9px] font-medium mt-1">Pencairan</span>
                </a>
            @endif
        </div>
    @endauth

    @yield('scripts')
</body>
</html>
