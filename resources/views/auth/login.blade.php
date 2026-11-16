@extends('layouts.app')

@section('title', 'Masuk')

@section('content')
<div class="auth-layout">
    <aside class="auth-aside">
        <span class="auth-chip"><span class="h-2 w-2 rounded-full bg-lime-300"></span> Tuker.in · Hidupkan kembali</span>
        <div><h1>Sampah hari ini, nilai untuk esok.</h1><p class="mt-4">Satu langkah sederhana untuk bumi yang lebih bersih. Kumpulkan poin dari botol plastik dan tukarkan dengan reward.</p></div>
        <div class="relative z-10 flex items-center gap-3 text-xs font-semibold text-white/70"><span class="flex h-9 w-9 items-center justify-center rounded-full bg-white/10 text-lg text-lime-200">♻</span> Daur ulang lebih mudah bersama Tuker.in</div>
    </aside>

    <section class="auth-card">
        <span class="mb-5 inline-flex h-12 w-12 items-center justify-center rounded-2xl bg-[#edf5e2] text-xl text-[#285e43]" aria-hidden="true">↗</span>
        <h2>Selamat datang kembali</h2>
        <p class="auth-lead">Masuk untuk melanjutkan perjalanan hijau Anda.</p>

        <form class="mt-7 space-y-5" action="{{ route('login') }}" method="POST">
            @csrf
            <div>
                <label for="email" class="mb-2 block text-xs font-bold text-slate-700">Alamat email</label>
                <input id="email" name="email" type="email" autocomplete="email" required value="{{ old('email') }}" placeholder="nama@email.com">
                @error('email')<p class="mt-2 text-xs font-semibold text-rose-600">{{ $message }}</p>@enderror
            </div>
            <div>
                <div class="mb-2 flex items-center justify-between"><label for="password" class="text-xs font-bold text-slate-700">Password</label></div>
                <input id="password" name="password" type="password" autocomplete="current-password" required placeholder="Masukkan password">
                @error('password')<p class="mt-2 text-xs font-semibold text-rose-600">{{ $message }}</p>@enderror
            </div>
            <label class="flex cursor-pointer items-center gap-2 text-xs font-medium text-slate-500"><input id="remember" name="remember" type="checkbox" class="h-4 w-4 rounded border-slate-300 text-emerald-700 focus:ring-emerald-600/20"> Ingat saya di perangkat ini</label>
            <button type="submit" class="w-full px-4 py-3 text-sm">Masuk ke akun <span class="ml-1" aria-hidden="true">→</span></button>
        </form>
        <p class="mt-6 border-t border-slate-100 pt-5 text-center text-xs font-medium text-slate-500">Belum punya akun nasabah? <a href="{{ route('register') }}" class="font-extrabold text-emerald-700 hover:text-emerald-900">Daftar sekarang</a></p>
    </section>
</div>
@endsection
