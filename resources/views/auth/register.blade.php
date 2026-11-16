@extends('layouts.app')

@section('title', 'Daftar Nasabah')

@section('content')
<div class="auth-layout">
    <aside class="auth-aside">
        <span class="auth-chip"><span class="h-2 w-2 rounded-full bg-lime-300"></span> Mulai dari satu botol</span>
        <div><h1>Kebaikan kecil punya nilai besar.</h1><p class="mt-4">Buat akun Tuker.in, setorkan botol plastik di titik penukaran, lalu nikmati poin reward dari aksi baikmu.</p></div>
        <div class="relative z-10 flex items-center gap-3 text-xs font-semibold text-white/70"><span class="flex h-9 w-9 items-center justify-center rounded-full bg-white/10 text-lg text-lime-200">✳</span> Akun gratis · Poin transparan · Dampak nyata</div>
    </aside>

    <section class="auth-card">
        <span class="mb-5 inline-flex h-12 w-12 items-center justify-center rounded-2xl bg-[#edf5e2] text-xl text-[#285e43]" aria-hidden="true">✳</span>
        <h2>Gabung gerakan hijau</h2>
        <p class="auth-lead">Daftar sebagai nasabah dan mulai kumpulkan poin.</p>

        <form class="mt-6 space-y-4" action="{{ route('register') }}" method="POST">
            @csrf
            <div>
                <label for="name" class="mb-2 block text-xs font-bold text-slate-700">Nama lengkap</label>
                <input id="name" name="name" type="text" autocomplete="name" required value="{{ old('name') }}" placeholder="Nama sesuai identitas">
                @error('name')<p class="mt-2 text-xs font-semibold text-rose-600">{{ $message }}</p>@enderror
            </div>
            <div>
                <label for="email" class="mb-2 block text-xs font-bold text-slate-700">Alamat email</label>
                <input id="email" name="email" type="email" autocomplete="email" required value="{{ old('email') }}" placeholder="nama@email.com">
                @error('email')<p class="mt-2 text-xs font-semibold text-rose-600">{{ $message }}</p>@enderror
            </div>
            <div>
                <label for="phone" class="mb-2 block text-xs font-bold text-slate-700">Nomor telepon / WhatsApp</label>
                <input id="phone" name="phone" type="tel" autocomplete="tel" required value="{{ old('phone') }}" placeholder="08xxxxxxxxxx">
                @error('phone')<p class="mt-2 text-xs font-semibold text-rose-600">{{ $message }}</p>@enderror
            </div>
            <div class="grid gap-4 sm:grid-cols-2">
                <div><label for="password" class="mb-2 block text-xs font-bold text-slate-700">Password</label><input id="password" name="password" type="password" autocomplete="new-password" required placeholder="Buat password">@error('password')<p class="mt-2 text-xs font-semibold text-rose-600">{{ $message }}</p>@enderror</div>
                <div><label for="password_confirmation" class="mb-2 block text-xs font-bold text-slate-700">Ulangi password</label><input id="password_confirmation" name="password_confirmation" type="password" autocomplete="new-password" required placeholder="Konfirmasi"></div>
            </div>
            <button type="submit" class="w-full px-4 py-3 text-sm">Buat akun nasabah <span class="ml-1" aria-hidden="true">→</span></button>
        </form>
        <p class="mt-6 border-t border-slate-100 pt-5 text-center text-xs font-medium text-slate-500">Sudah punya akun? <a href="{{ route('login') }}" class="font-extrabold text-emerald-700 hover:text-emerald-900">Masuk di sini</a></p>
    </section>
</div>
@endsection
