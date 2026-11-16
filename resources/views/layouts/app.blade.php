<!DOCTYPE html>
<html lang="id" class="h-full bg-stone-50 text-slate-800 antialiased">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="theme-color" content="#f8fafc">
    <title>@yield('title', 'Tuker.in') · Daur Ulang Jadi Poin</title>

    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=instrument-sans:400,500,600,700,800&display=swap" rel="stylesheet" />
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>body { font-family: 'Instrument Sans', sans-serif; }</style>
    @yield('styles')
</head>
<body class="app-shell flex min-h-full flex-col">
    <header class="app-header sticky top-0 z-40">
        <div class="mx-auto flex h-[4.5rem] max-w-7xl items-center justify-between gap-4 px-4 sm:px-6 lg:px-8">
            <a href="{{ url('/') }}" class="group flex shrink-0 items-center gap-2.5" aria-label="Tuker.in beranda">
                <span class="brand-mark flex h-10 w-10 items-center justify-center rounded-2xl text-white transition-transform group-hover:-rotate-6">
                    <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 114.582 9M4 9h5m11 11v-5h-.582" />
                    </svg>
                </span>
                <span class="brand-name text-lg font-extrabold tracking-tight">Tuker<span>.in</span></span>
            </a>

            @auth
                <nav class="desktop-navigation hidden items-center gap-1 lg:flex" aria-label="Navigasi utama">
                    @if(auth()->user()->isUser())
                        <a href="{{ route('user.dashboard') }}" class="rounded-xl px-3 py-2 text-sm font-semibold transition {{ request()->routeIs('user.dashboard') ? 'bg-emerald-50 text-emerald-700' : 'text-slate-500 hover:bg-slate-50 hover:text-slate-900' }}">Beranda</a>
                        <a href="{{ route('user.transactions') }}" class="rounded-xl px-3 py-2 text-sm font-semibold transition {{ request()->routeIs('user.transactions') ? 'bg-emerald-50 text-emerald-700' : 'text-slate-500 hover:bg-slate-50 hover:text-slate-900' }}">Transaksi</a>
                        <a href="{{ route('user.rewards') }}" class="rounded-xl px-3 py-2 text-sm font-semibold transition {{ request()->routeIs('user.rewards*') ? 'bg-emerald-50 text-emerald-700' : 'text-slate-500 hover:bg-slate-50 hover:text-slate-900' }}">Pencairan</a>
                        <a href="{{ route('user.qr') }}" class="rounded-xl px-3 py-2 text-sm font-semibold transition {{ request()->routeIs('user.qr') ? 'bg-emerald-50 text-emerald-700' : 'text-slate-500 hover:bg-slate-50 hover:text-slate-900' }}">QR Saya</a>
                    @elseif(auth()->user()->isEmployee())
                        <a href="{{ route('employee.dashboard') }}" class="rounded-xl px-3 py-2 text-sm font-semibold transition {{ request()->routeIs('employee.dashboard') ? 'bg-amber-50 text-amber-700' : 'text-slate-500 hover:bg-slate-50 hover:text-slate-900' }}">Beranda</a>
                        <a href="{{ route('employee.scan') }}" class="rounded-xl px-3 py-2 text-sm font-semibold transition {{ request()->routeIs('employee.scan') ? 'bg-amber-50 text-amber-700' : 'text-slate-500 hover:bg-slate-50 hover:text-slate-900' }}">Scan Botol</a>
                        <a href="{{ route('employee.transactions') }}" class="rounded-xl px-3 py-2 text-sm font-semibold transition {{ request()->routeIs('employee.transactions') ? 'bg-amber-50 text-amber-700' : 'text-slate-500 hover:bg-slate-50 hover:text-slate-900' }}">Riwayat</a>
                    @elseif(auth()->user()->isAdmin())
                        <a href="{{ route('admin.dashboard') }}" class="rounded-xl px-3 py-2 text-sm font-semibold transition {{ request()->routeIs('admin.dashboard') ? 'bg-emerald-50 text-emerald-700' : 'text-slate-500 hover:bg-slate-50 hover:text-slate-900' }}">Ringkasan</a>
                        <a href="{{ route('admin.employees.index') }}" class="rounded-xl px-3 py-2 text-sm font-semibold transition {{ request()->routeIs('admin.employees*') ? 'bg-emerald-50 text-emerald-700' : 'text-slate-500 hover:bg-slate-50 hover:text-slate-900' }}">Pegawai</a>
                        <a href="{{ route('admin.users') }}" class="rounded-xl px-3 py-2 text-sm font-semibold transition {{ request()->routeIs('admin.users') ? 'bg-emerald-50 text-emerald-700' : 'text-slate-500 hover:bg-slate-50 hover:text-slate-900' }}">Nasabah</a>
                        <a href="{{ route('admin.transactions') }}" class="rounded-xl px-3 py-2 text-sm font-semibold transition {{ request()->routeIs('admin.transactions') ? 'bg-emerald-50 text-emerald-700' : 'text-slate-500 hover:bg-slate-50 hover:text-slate-900' }}">Transaksi</a>
                        <a href="{{ route('admin.redemptions') }}" class="rounded-xl px-3 py-2 text-sm font-semibold transition {{ request()->routeIs('admin.redemptions*') ? 'bg-emerald-50 text-emerald-700' : 'text-slate-500 hover:bg-slate-50 hover:text-slate-900' }}">Pencairan</a>
                        <a href="{{ route('admin.bottle-types.index') }}" class="rounded-xl px-3 py-2 text-sm font-semibold transition {{ request()->routeIs('admin.bottle-types*') ? 'bg-emerald-50 text-emerald-700' : 'text-slate-500 hover:bg-slate-50 hover:text-slate-900' }}">Jenis Botol</a>
                    @endif
                </nav>

                <div class="flex shrink-0 items-center gap-3">
                    <div class="hidden items-center gap-2.5 md:flex">
                        <span class="flex h-9 w-9 items-center justify-center rounded-full bg-slate-900 text-xs font-bold text-white ring-4 ring-slate-100">{{ collect(explode(' ', auth()->user()->name))->map(fn ($part) => substr($part, 0, 1))->take(2)->implode('') }}</span>
                        <div class="hidden xl:block">
                            <p class="max-w-36 truncate text-xs font-bold text-slate-800">{{ auth()->user()->name }}</p>
                            <p class="text-[10px] font-semibold uppercase tracking-wider text-slate-400">{{ auth()->user()->role->label }}</p>
                        </div>
                    </div>
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit" class="flex h-10 items-center gap-2 rounded-xl border border-slate-200 px-3 text-xs font-bold text-slate-600 transition hover:border-rose-200 hover:bg-rose-50 hover:text-rose-600" aria-label="Keluar dari akun">
                            <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" /></svg>
                            <span class="hidden sm:inline">Keluar</span>
                        </button>
                    </form>
                </div>
            @else
                <div class="flex items-center gap-2">
                    <a href="{{ route('login') }}" class="rounded-xl px-3.5 py-2 text-sm font-bold text-slate-600 transition hover:bg-slate-100 hover:text-slate-900">Masuk</a>
                    <a href="{{ route('register') }}" class="rounded-xl bg-emerald-600 px-4 py-2.5 text-sm font-bold text-white shadow-md shadow-emerald-900/10 transition hover:bg-emerald-700">Daftar</a>
                </div>
            @endauth
        </div>
    </header>

    <main class="app-main mx-auto w-full max-w-7xl flex-1 px-4 py-6 sm:px-6 sm:py-8 lg:px-8 lg:py-10 {{ auth()->check() ? 'pb-24 lg:pb-10' : '' }}">
        @if (session('success'))
            <div role="status" class="mb-6 flex items-start gap-3 rounded-2xl border border-emerald-200 bg-emerald-50 px-4 py-3.5 text-emerald-900 shadow-sm">
                <svg class="mt-0.5 h-5 w-5 shrink-0 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                <p class="text-sm font-semibold">{{ session('success') }}</p>
            </div>
        @endif
        @if (session('error'))
            <div role="alert" class="mb-6 flex items-start gap-3 rounded-2xl border border-rose-200 bg-rose-50 px-4 py-3.5 text-rose-900 shadow-sm">
                <svg class="mt-0.5 h-5 w-5 shrink-0 text-rose-600" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" /></svg>
                <p class="text-sm font-semibold">{{ session('error') }}</p>
            </div>
        @endif
        @if ($errors->any())
            <div role="alert" class="mb-6 rounded-2xl border border-rose-200 bg-rose-50 px-4 py-3.5 text-rose-900 shadow-sm">
                <p class="text-sm font-bold">Periksa kembali data yang dimasukkan.</p>
                <ul class="mt-1 list-inside list-disc text-xs font-medium">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        @yield('content')
    </main>

    @auth
        <nav class="mobile-navigation fixed inset-x-0 bottom-0 z-40 px-1 pb-[max(env(safe-area-inset-bottom),0.25rem)] lg:hidden" aria-label="Navigasi mobile">
            <div class="mx-auto flex h-[4.25rem] max-w-lg items-center justify-around">
                @if(auth()->user()->isUser())
                    <a href="{{ route('user.dashboard') }}" class="flex h-full min-w-0 flex-1 flex-col items-center justify-center gap-1 {{ request()->routeIs('user.dashboard') ? 'text-emerald-700' : 'text-slate-400' }}">
                        <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10.5L12 3l9 7.5M5 9v11h14V9m-9 11v-6h4v6" /></svg><span class="text-[10px] font-bold">Beranda</span>
                    </a>
                    <a href="{{ route('user.qr') }}" class="flex h-full min-w-0 flex-1 flex-col items-center justify-center gap-1 {{ request()->routeIs('user.qr') ? 'text-emerald-700' : 'text-slate-400' }}">
                        <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4h5v5H4zM15 4h5v5h-5zM4 15h5v5H4zM15 15h2v2h-2zm3 0h2v5h-2zm-3 3h2v2h-2zM11 4v2m0 2v2m-2 1h2m2 0h2m-2 2v2" /></svg><span class="text-[10px] font-bold">QR Saya</span>
                    </a>
                    <a href="{{ route('user.transactions') }}" class="flex h-full min-w-0 flex-1 flex-col items-center justify-center gap-1 {{ request()->routeIs('user.transactions') ? 'text-emerald-700' : 'text-slate-400' }}">
                        <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 6h13M8 12h13M8 18h13M3 6h.01M3 12h.01M3 18h.01" /></svg><span class="text-[10px] font-bold">Transaksi</span>
                    </a>
                    <a href="{{ route('user.rewards') }}" class="flex h-full min-w-0 flex-1 flex-col items-center justify-center gap-1 {{ request()->routeIs('user.rewards*') ? 'text-emerald-700' : 'text-slate-400' }}">
                        <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" /></svg><span class="text-[10px] font-bold">Pencairan</span>
                    </a>
                @elseif(auth()->user()->isEmployee())
                    <a href="{{ route('employee.dashboard') }}" class="flex h-full min-w-0 flex-1 flex-col items-center justify-center gap-1 {{ request()->routeIs('employee.dashboard') ? 'text-amber-700' : 'text-slate-400' }}">
                        <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10.5L12 3l9 7.5M5 9v11h14V9m-9 11v-6h4v6" /></svg><span class="text-[10px] font-bold">Beranda</span>
                    </a>
                    <a href="{{ route('employee.scan') }}" class="flex h-full min-w-0 flex-1 flex-col items-center justify-center gap-1 {{ request()->routeIs('employee.scan') ? 'text-amber-700' : 'text-slate-400' }}">
                        <span class="flex h-9 w-9 items-center justify-center rounded-2xl bg-amber-500 text-white shadow-md shadow-amber-500/25"><svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4h5v5H4zM15 4h5v5h-5zM4 15h5v5H4zM15 15h2v2h-2zm3 0h2v5h-2zm-3 3h2v2h-2z" /></svg></span><span class="text-[10px] font-bold">Scan</span>
                    </a>
                    <a href="{{ route('employee.transactions') }}" class="flex h-full min-w-0 flex-1 flex-col items-center justify-center gap-1 {{ request()->routeIs('employee.transactions') ? 'text-amber-700' : 'text-slate-400' }}">
                        <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 6h13M8 12h13M8 18h13M3 6h.01M3 12h.01M3 18h.01" /></svg><span class="text-[10px] font-bold">Riwayat</span>
                    </a>
                @elseif(auth()->user()->isAdmin())
                    <a href="{{ route('admin.dashboard') }}" class="flex h-full min-w-0 flex-1 flex-col items-center justify-center gap-1 {{ request()->routeIs('admin.dashboard') ? 'text-emerald-700' : 'text-slate-400' }}"><svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4h6v6H4zM14 4h6v6h-6zM4 14h6v6H4zM14 14h6v6h-6z" /></svg><span class="text-[10px] font-bold">Ringkasan</span></a>
                    <a href="{{ route('admin.employees.index') }}" class="flex h-full min-w-0 flex-1 flex-col items-center justify-center gap-1 {{ request()->routeIs('admin.employees*') ? 'text-emerald-700' : 'text-slate-400' }}"><svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 21v-2a4 4 0 00-4-4H8a4 4 0 00-4 4v2m12-13a4 4 0 11-8 0 4 4 0 018 0zm2 2a4 4 0 014 4v2" /></svg><span class="text-[10px] font-bold">Pegawai</span></a>
                    <a href="{{ route('admin.users') }}" class="flex h-full min-w-0 flex-1 flex-col items-center justify-center gap-1 {{ request()->routeIs('admin.users') ? 'text-emerald-700' : 'text-slate-400' }}"><svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a4 4 0 00-6-3.46M9 20H2v-2a4 4 0 017.5-2M15 7a4 4 0 11-8 0 4 4 0 018 0zm6 3a2 2 0 11-4 0 2 2 0 014 0z" /></svg><span class="text-[10px] font-bold">Nasabah</span></a>
                    <a href="{{ route('admin.redemptions') }}" class="flex h-full min-w-0 flex-1 flex-col items-center justify-center gap-1 {{ request()->routeIs('admin.redemptions*') ? 'text-emerald-700' : 'text-slate-400' }}"><svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" /></svg><span class="text-[10px] font-bold">Pencairan</span></a>
                @endif
            </div>
        </nav>
    @endauth

    @yield('scripts')
</body>
</html>
