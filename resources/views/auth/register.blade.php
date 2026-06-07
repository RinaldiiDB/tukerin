@extends('layouts.app')

@section('title', 'Daftar Nasabah')

@section('content')
<div class="flex flex-col justify-center py-8 sm:px-6 lg:px-8 max-w-md mx-auto">
    <div class="sm:mx-auto sm:w-full sm:max-w-md text-center">
        <h2 class="text-3xl font-extrabold text-slate-900 tracking-tight">Mulai Kumpulkan Kebaikan</h2>
        <p class="mt-2 text-sm text-slate-600">
            Daftar sebagai Nasabah untuk menukarkan botol bekas menjadi poin rupiah.
        </p>
    </div>

    <div class="mt-8 sm:mx-auto sm:w-full sm:max-w-md">
        <div class="bg-white py-8 px-4 border border-slate-200/60 rounded-3xl shadow-xl shadow-slate-100 sm:px-10">
            <form class="space-y-5" action="{{ route('register') }}" method="POST">
                @csrf

                <!-- Nama Lengkap -->
                <div>
                    <label for="name" class="block text-sm font-semibold text-slate-700">Nama Lengkap</label>
                    <div class="mt-1">
                        <input id="name" name="name" type="text" required value="{{ old('name') }}"
                            class="appearance-none block w-full px-4 py-3 border border-slate-200 rounded-2xl placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-500 text-sm transition-all duration-200">
                    </div>
                    @error('name')
                        <p class="mt-1.5 text-xs text-rose-600 font-medium">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Email -->
                <div>
                    <label for="email" class="block text-sm font-semibold text-slate-700">Alamat Email</label>
                    <div class="mt-1">
                        <input id="email" name="email" type="email" autocomplete="email" required value="{{ old('email') }}"
                            class="appearance-none block w-full px-4 py-3 border border-slate-200 rounded-2xl placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-500 text-sm transition-all duration-200">
                    </div>
                    @error('email')
                        <p class="mt-1.5 text-xs text-rose-600 font-medium">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Nomor HP / WA -->
                <div>
                    <label for="phone" class="block text-sm font-semibold text-slate-700">Nomor Telepon / WhatsApp</label>
                    <div class="mt-1">
                        <input id="phone" name="phone" type="text" placeholder="Contoh: 08123456789" required value="{{ old('phone') }}"
                            class="appearance-none block w-full px-4 py-3 border border-slate-200 rounded-2xl placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-500 text-sm transition-all duration-200">
                    </div>
                    @error('phone')
                        <p class="mt-1.5 text-xs text-rose-600 font-medium">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Password -->
                <div>
                    <label for="password" class="block text-sm font-semibold text-slate-700">Password</label>
                    <div class="mt-1">
                        <input id="password" name="password" type="password" required
                            class="appearance-none block w-full px-4 py-3 border border-slate-200 rounded-2xl placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-500 text-sm transition-all duration-200">
                    </div>
                    @error('password')
                        <p class="mt-1.5 text-xs text-rose-600 font-medium">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Konfirmasi Password -->
                <div>
                    <label for="password_confirmation" class="block text-sm font-semibold text-slate-700">Konfirmasi Password</label>
                    <div class="mt-1">
                        <input id="password_confirmation" name="password_confirmation" type="password" required
                            class="appearance-none block w-full px-4 py-3 border border-slate-200 rounded-2xl placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-500 text-sm transition-all duration-200">
                    </div>
                </div>

                <!-- Submit Button -->
                <div class="pt-2">
                    <button type="submit"
                        class="w-full flex justify-center py-3 px-4 border border-transparent rounded-2xl shadow-sm text-sm font-bold text-white bg-gradient-to-r from-emerald-600 to-teal-500 hover:from-emerald-500 hover:to-teal-400 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-emerald-500 transition-all duration-250 hover:shadow-md hover:shadow-emerald-500/10">
                        Buat Akun Nasabah
                    </button>
                </div>
            </form>

            <div class="mt-6 text-center">
                <p class="text-xs font-medium text-slate-500">
                    Sudah terdaftar? 
                    <a href="{{ route('login') }}" class="font-bold text-emerald-600 hover:text-emerald-500 transition-colors">Masuk di sini</a>
                </p>
            </div>
        </div>
    </div>
</div>
@endsection
