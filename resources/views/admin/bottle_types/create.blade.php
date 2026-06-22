@extends('layouts.app')

@section('title', 'Tambah Jenis Botol Plastik')

@section('content')
<div class="max-w-lg mx-auto">
    <div class="bg-white border border-slate-200/60 rounded-3xl p-6 md:p-8 shadow-xl">
        <!-- Header -->
        <div class="flex items-center gap-3 mb-6">
            <div class="w-10 h-10 rounded-2xl bg-gradient-to-tr from-emerald-500 to-teal-400 flex items-center justify-center shadow-lg shadow-emerald-500/20 shrink-0">
                <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M19.428 15.428a2 2 0 00-1.022-.547l-2.387-.477a6 6 0 00-3.86.517l-.318.158a6 6 0 01-3.86.517L6.05 15.21a2 2 0 00-1.806.547M8 4h8l-1 1v5.172a2 2 0 00.586 1.414l5 5c1.26 1.26.367 3.414-1.415 3.414H4.828c-1.782 0-2.674-2.154-1.414-3.414l5-5A2 2 0 009 10.172V5L8 4z"></path>
                </svg>
            </div>
            <div>
                <h1 class="text-2xl font-extrabold text-slate-800 tracking-tight">Tambah Jenis Botol</h1>
                <p class="text-xs text-slate-500 font-medium mt-0.5">Daftarkan kategori botol plastik baru beserta nilai poin dan barcode-nya.</p>
            </div>
        </div>

        <form action="{{ route('admin.bottle-types.store') }}" method="POST" class="space-y-5">
            @csrf

            <!-- Nama Jenis Botol -->
            <div>
                <label for="name" class="block text-sm font-semibold text-slate-700">Nama Jenis Botol</label>
                <div class="mt-1.5">
                    <input type="text" name="name" id="name" required value="{{ old('name') }}"
                        placeholder="Contoh: Botol PET 600ml"
                        class="appearance-none block w-full px-4 py-3 border border-slate-200 rounded-2xl placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-500 text-sm transition-all duration-200">
                </div>
                @error('name')
                    <p class="mt-1.5 text-xs text-rose-600 font-medium">{{ $message }}</p>
                @enderror
            </div>

            <!-- Kode Barcode -->
            <div>
                <label for="barcode" class="block text-sm font-semibold text-slate-700">Kode Barcode</label>
                <div class="mt-1.5">
                    <input type="text" name="barcode" id="barcode" required value="{{ old('barcode') }}"
                        placeholder="Contoh: 8990123456789"
                        class="appearance-none block w-full px-4 py-3 border border-slate-200 rounded-2xl placeholder-slate-400 font-mono focus:outline-none focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-500 text-sm transition-all duration-200">
                </div>
                <p class="mt-1 text-xs text-slate-400">Barcode harus unik dan sesuai dengan kode fisik pada botol.</p>
                @error('barcode')
                    <p class="mt-1.5 text-xs text-rose-600 font-medium">{{ $message }}</p>
                @enderror
            </div>

            <!-- Nilai Poin -->
            <div>
                <label for="points_value" class="block text-sm font-semibold text-slate-700">Nilai Poin per Botol</label>
                <div class="mt-1.5 relative">
                    <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                        <svg class="w-4 h-4 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                    </div>
                    <input type="number" name="points_value" id="points_value" required min="1" value="{{ old('points_value') }}"
                        placeholder="Contoh: 10"
                        class="appearance-none block w-full pl-10 pr-4 py-3 border border-slate-200 rounded-2xl placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-500 text-sm transition-all duration-200">
                </div>
                @error('points_value')
                    <p class="mt-1.5 text-xs text-rose-600 font-medium">{{ $message }}</p>
                @enderror
            </div>

            <!-- Deskripsi -->
            <div>
                <label for="description" class="block text-sm font-semibold text-slate-700">
                    Deskripsi
                    <span class="ml-1 text-slate-400 font-normal">(Opsional)</span>
                </label>
                <div class="mt-1.5">
                    <textarea name="description" id="description" rows="3"
                        placeholder="Keterangan tambahan mengenai jenis botol ini..."
                        class="appearance-none block w-full px-4 py-3 border border-slate-200 rounded-2xl placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-500 text-sm transition-all duration-200 resize-none">{{ old('description') }}</textarea>
                </div>
                @error('description')
                    <p class="mt-1.5 text-xs text-rose-600 font-medium">{{ $message }}</p>
                @enderror
            </div>

            <!-- Submit Buttons -->
            <div class="flex gap-3 pt-2">
                <a href="{{ route('admin.bottle-types.index') }}" class="flex-1 py-3 px-4 bg-slate-100 hover:bg-slate-200 text-slate-700 font-bold text-sm rounded-2xl text-center transition-colors">
                    Batal
                </a>
                <button type="submit" class="flex-1 py-3 px-4 bg-gradient-to-r from-emerald-600 to-teal-500 hover:from-emerald-500 hover:to-teal-400 text-white font-bold text-sm rounded-2xl transition-all shadow-md shadow-emerald-500/10">
                    Simpan Data
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
