@extends('layouts.app')

@section('title', 'Edit Jenis Botol Plastik')

@section('content')
<div class="max-w-lg mx-auto">
    <div class="bg-white border border-slate-200/60 rounded-3xl p-6 md:p-8 shadow-xl">
        <!-- Header -->
        <div class="flex items-center gap-3 mb-6">
            <div>
                <h1 class="text-2xl font-extrabold text-slate-800 tracking-tight">Edit Jenis Botol</h1>
                <p class="text-xs text-slate-500 font-medium mt-0.5">Perbarui informasi kategori botol plastik yang terdaftar di sistem.</p>
            </div>
        </div>

        <form action="{{ route('admin.bottle-types.update', $bottleType->id) }}" method="POST" class="space-y-5">
            @csrf
            @method('PUT')

            <!-- Nama Jenis Botol -->
            <div>
                <label for="name" class="block text-sm font-semibold text-slate-700">Nama Jenis Botol</label>
                <div class="mt-1.5">
                    <input type="text" name="name" id="name" required value="{{ old('name', $bottleType->name) }}"
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
                    <input type="text" name="barcode" id="barcode" required value="{{ old('barcode', $bottleType->barcode) }}"
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
                    <input type="number" name="points_value" id="points_value" required min="1" value="{{ old('points_value', $bottleType->points_value) }}"
                        placeholder="Contoh: 10"
                        class="appearance-none block w-full px-4 py-3 border border-slate-200 rounded-2xl placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-500 text-sm transition-all duration-200">
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
                        class="appearance-none block w-full px-4 py-3 border border-slate-200 rounded-2xl placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-500 text-sm transition-all duration-200 resize-none">{{ old('description', $bottleType->description) }}</textarea>
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
                    Perbarui Data
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
