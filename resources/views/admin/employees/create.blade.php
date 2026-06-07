@extends('layouts.app')

@section('title', 'Tambah Akun Pegawai')

@section('content')
<div class="max-w-md mx-auto">
    <div class="bg-white border border-slate-200/60 rounded-3xl p-6 md:p-8 shadow-xl">
        <h1 class="text-2xl font-extrabold text-slate-800 tracking-tight">Tambah Pegawai Baru</h1>
        <p class="text-xs text-slate-500 font-medium mt-1">Buat akun untuk pegawai agar dapat menggunakan scanner drop-off botol.</p>

        <form action="{{ route('admin.employees.store') }}" method="POST" class="space-y-4 mt-6">
            @csrf

            <!-- Nama Lengkap -->
            <div>
                <label for="name" class="block text-sm font-semibold text-slate-700 font-medium">Nama Lengkap Pegawai</label>
                <div class="mt-1">
                    <input type="text" name="name" id="name" required value="{{ old('name') }}"
                        class="appearance-none block w-full px-4 py-3 border border-slate-200 rounded-2xl placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-500 text-sm transition-all duration-200">
                </div>
                @error('name')
                    <p class="mt-1.5 text-xs text-rose-600 font-medium">{{ $message }}</p>
                @enderror
            </div>

            <!-- Email -->
            <div>
                <label for="email" class="block text-sm font-semibold text-slate-700 font-medium">Alamat Email</label>
                <div class="mt-1">
                    <input type="email" name="email" id="email" required value="{{ old('email') }}"
                        class="appearance-none block w-full px-4 py-3 border border-slate-200 rounded-2xl placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-500 text-sm transition-all duration-200">
                </div>
                @error('email')
                    <p class="mt-1.5 text-xs text-rose-600 font-medium">{{ $message }}</p>
                @enderror
            </div>

            <!-- Password -->
            <div>
                <label for="password" class="block text-sm font-semibold text-slate-700 font-medium">Password Akun</label>
                <div class="mt-1">
                    <input type="password" name="password" id="password" required
                        class="appearance-none block w-full px-4 py-3 border border-slate-200 rounded-2xl placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-500 text-sm transition-all duration-200">
                </div>
                @error('password')
                    <p class="mt-1.5 text-xs text-rose-600 font-medium">{{ $message }}</p>
                @enderror
            </div>

            <!-- Submit Buttons -->
            <div class="flex gap-3 pt-3">
                <a href="{{ route('admin.employees.index') }}" class="flex-1 py-3 px-4 bg-slate-100 hover:bg-slate-200 text-slate-700 font-bold text-sm rounded-2xl text-center transition-colors">
                    Batal
                </a>
                <button type="submit" class="flex-1 py-3 px-4 bg-gradient-to-r from-emerald-600 to-teal-500 hover:from-emerald-500 hover:to-teal-400 text-white font-bold text-sm rounded-2xl transition-all shadow-md shadow-emerald-500/10">
                    Simpan Akun
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
