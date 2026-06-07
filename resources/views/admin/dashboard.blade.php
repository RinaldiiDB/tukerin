@extends('layouts.app')

@section('title', 'Dasbor Admin')

@section('content')
<div class="space-y-6">
    <!-- Header -->
    <div>
        <h1 class="text-3xl font-extrabold text-slate-900 tracking-tight">Dasbor Ringkasan Admin</h1>
        <p class="text-xs text-slate-500 font-medium mt-1">Status operasional, perputaran poin, dan antrean persetujuan Tuker.in</p>
    </div>

    <!-- Stats Grid -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-5">
        <!-- Total Nasabah -->
        <div class="bg-white border border-slate-200/60 rounded-3xl p-5 shadow-sm flex items-center gap-4">
            <div class="w-12 h-12 rounded-2xl bg-emerald-50 text-emerald-600 flex items-center justify-center shrink-0">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
                </svg>
            </div>
            <div>
                <p class="text-[10px] text-slate-400 font-bold tracking-wider uppercase">Total Nasabah</p>
                <p class="text-2xl font-extrabold text-slate-900 mt-0.5">{{ number_format($totalUsers, 0, ',', '.') }}</p>
            </div>
        </div>

        <!-- Total Transaksi -->
        <div class="bg-white border border-slate-200/60 rounded-3xl p-5 shadow-sm flex items-center gap-4">
            <div class="w-12 h-12 rounded-2xl bg-sky-50 text-sky-600 flex items-center justify-center shrink-0">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01"></path>
                </svg>
            </div>
            <div>
                <p class="text-[10px] text-slate-400 font-bold tracking-wider uppercase">Transaksi Botol</p>
                <p class="text-2xl font-extrabold text-slate-900 mt-0.5">{{ number_format($totalTransactions, 0, ',', '.') }}</p>
            </div>
        </div>

        <!-- Perputaran Poin -->
        <div class="bg-white border border-slate-200/60 rounded-3xl p-5 shadow-sm flex items-center gap-4">
            <div class="w-12 h-12 rounded-2xl bg-amber-50 text-amber-600 flex items-center justify-center shrink-0">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
            </div>
            <div>
                <p class="text-[10px] text-slate-400 font-bold tracking-wider uppercase">Poin Tersalurkan</p>
                <p class="text-2xl font-extrabold text-amber-600 mt-0.5">{{ number_format($totalPointsCirculated, 0, ',', '.') }}</p>
            </div>
        </div>

        <!-- Pending Redemptions -->
        <div class="bg-white border border-slate-200/60 rounded-3xl p-5 shadow-sm flex items-center gap-4">
            <div class="w-12 h-12 rounded-2xl bg-rose-50 text-rose-600 flex items-center justify-center shrink-0">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
            </div>
            <div>
                <p class="text-[10px] text-slate-400 font-bold tracking-wider uppercase">Pending Pencairan</p>
                <p class="text-2xl font-extrabold text-rose-600 mt-0.5">{{ number_format($pendingRedemptions, 0, ',', '.') }}</p>
            </div>
        </div>
    </div>

    <!-- Administrative Quick Access -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
        <!-- CRUD Pegawai -->
        <a href="{{ route('admin.employees.index') }}" class="group bg-white border border-slate-200/60 hover:border-emerald-200 rounded-3xl p-6 shadow-sm hover:shadow-md transition-all duration-200">
            <div class="w-10 h-10 rounded-xl bg-slate-50 text-slate-500 group-hover:bg-emerald-50 group-hover:text-emerald-600 flex items-center justify-center transition-colors">
                <svg class="w-5.5 h-5.5" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"></path>
                </svg>
            </div>
            <h3 class="text-base font-bold text-slate-800 mt-4">Kelola Pegawai</h3>
            <p class="text-xs text-slate-400 font-medium mt-1">Tambah, edit, dan hapus akun operator lapangan.</p>
        </a>

        <!-- Nasabah List -->
        <a href="{{ route('admin.users') }}" class="group bg-white border border-slate-200/60 hover:border-emerald-200 rounded-3xl p-6 shadow-sm hover:shadow-md transition-all duration-200">
            <div class="w-10 h-10 rounded-xl bg-slate-50 text-slate-500 group-hover:bg-emerald-50 group-hover:text-emerald-600 flex items-center justify-center transition-colors">
                <svg class="w-5.5 h-5.5" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0"></path>
                </svg>
            </div>
            <h3 class="text-base font-bold text-slate-800 mt-4">Direktori Nasabah</h3>
            <p class="text-xs text-slate-400 font-medium mt-1">Lihat data nasabah terdaftar beserta detail poin.</p>
        </a>

        <!-- Log Transaksi -->
        <a href="{{ route('admin.transactions') }}" class="group bg-white border border-slate-200/60 hover:border-emerald-200 rounded-3xl p-6 shadow-sm hover:shadow-md transition-all duration-200">
            <div class="w-10 h-10 rounded-xl bg-slate-50 text-slate-500 group-hover:bg-emerald-50 group-hover:text-emerald-600 flex items-center justify-center transition-colors">
                <svg class="w-5.5 h-5.5" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path>
                </svg>
            </div>
            <h3 class="text-base font-bold text-slate-800 mt-4">Log Transaksi</h3>
            <p class="text-xs text-slate-400 font-medium mt-1">Semua transaksi penukaran botol daur ulang.</p>
        </a>

        <!-- Pending Redemptions Link -->
        <a href="{{ route('admin.redemptions') }}" class="group bg-white border border-slate-200/60 hover:border-emerald-200 rounded-3xl p-6 shadow-sm hover:shadow-md transition-all duration-200">
            <div class="w-10 h-10 rounded-xl bg-slate-50 text-slate-500 group-hover:bg-emerald-50 group-hover:text-emerald-600 flex items-center justify-center transition-colors">
                <svg class="w-5.5 h-5.5" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
            </div>
            <h3 class="text-base font-bold text-slate-800 mt-4">Persetujuan Pencairan</h3>
            <p class="text-xs text-slate-400 font-medium mt-1">Proses penarikan dana rupiah oleh nasabah.</p>
        </a>
    </div>
</div>
@endsection
