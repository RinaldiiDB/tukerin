@extends('layouts.app')

@section('title', 'Dasbor Nasabah')

@section('content')
<div class="space-y-6">
    <!-- Welcome Header / Balance Card -->
    <div class="relative overflow-hidden bg-gradient-to-tr from-slate-900 via-slate-800 to-emerald-950 text-white rounded-3xl p-6 md:p-8 shadow-xl">
        <!-- Decorative subtle lights -->
        <div class="absolute right-0 bottom-0 w-64 h-64 bg-emerald-500/10 rounded-full blur-3xl -mr-16 -mb-16"></div>
        <div class="absolute left-1/3 top-0 w-32 h-32 bg-teal-500/10 rounded-full blur-2xl"></div>

        <div class="relative z-10 flex flex-col md:flex-row md:items-center justify-between gap-6">
            <div>
                <p class="text-emerald-400 font-semibold text-xs tracking-wider uppercase">Saldo Aktif Anda</p>
                <h1 class="text-4xl md:text-5xl font-extrabold tracking-tight mt-1">
                    {{ number_format($profile->points_balance, 0, ',', '.') }} <span class="text-lg md:text-xl font-medium text-slate-300">Poin</span>
                </h1>
                <p class="text-xs text-slate-400 mt-2 font-medium">
                    Estimasi Nilai Pencairan: <strong class="text-emerald-400">Rp {{ number_format($profile->points_balance * 200, 0, ',', '.') }}</strong>
                </p>
            </div>
            
            <div class="flex gap-3">
                <a href="{{ route('user.qr') }}" class="flex-1 md:flex-initial flex items-center justify-center gap-2 px-5 py-3.5 bg-emerald-600 hover:bg-emerald-500 text-white font-bold text-sm rounded-2xl transition-all duration-200 shadow-lg shadow-emerald-600/20">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v1m6 11h2m-6 0h-2v4m0-11v3m0 0h.01M12 12h.01M16 12h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                    <span>Tampilkan QR</span>
                </a>
                <a href="{{ route('user.rewards.create') }}" class="flex-1 md:flex-initial flex items-center justify-center gap-2 px-5 py-3.5 bg-slate-800 hover:bg-slate-700 text-white font-bold text-sm border border-slate-700 rounded-2xl transition-all duration-200">
                    <svg class="w-5 h-5 text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                    <span>Cairkan Poin</span>
                </a>
            </div>
        </div>
    </div>

    <!-- Quick info cards -->
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        <!-- Recent Exchange Transactions -->
        <div class="bg-white border border-slate-200/60 rounded-3xl p-6 shadow-sm">
            <div class="flex items-center justify-between mb-4">
                <h2 class="text-lg font-bold text-slate-800">Penukaran Terakhir</h2>
                <a href="{{ route('user.transactions') }}" class="text-xs font-bold text-emerald-600 hover:text-emerald-500 transition-colors">Lihat Semua</a>
            </div>

            @if($recentTransactions->isEmpty())
                <div class="py-8 text-center">
                    <div class="w-12 h-12 rounded-full bg-slate-50 flex items-center justify-center text-slate-400 mx-auto mb-2">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01"></path>
                        </svg>
                    </div>
                    <p class="text-xs text-slate-400 font-medium">Belum ada transaksi penukaran.</p>
                </div>
            @else
                <div class="divide-y divide-slate-100">
                    @foreach($recentTransactions as $tx)
                        <div class="py-3 flex items-center justify-between gap-4 first:pt-0 last:pb-0">
                            <div>
                                <p class="text-sm font-semibold text-slate-800">Penukaran Botol Plastik</p>
                                <p class="text-[11px] text-slate-400 font-medium mt-0.5">{{ $tx->transacted_at->format('d M Y - H:i') }}</p>
                            </div>
                            <div class="text-right">
                                <span class="text-sm font-bold text-emerald-600">+{{ $tx->total_points }} Poin</span>
                            </div>
                        </div>
                    @endforeach
                </div>
            @endif
        </div>

        <!-- Recent Redemption Requests -->
        <div class="bg-white border border-slate-200/60 rounded-3xl p-6 shadow-sm">
            <div class="flex items-center justify-between mb-4">
                <h2 class="text-lg font-bold text-slate-800">Pencairan Terakhir</h2>
                <a href="{{ route('user.rewards') }}" class="text-xs font-bold text-emerald-600 hover:text-emerald-500 transition-colors">Lihat Semua</a>
            </div>

            @if($recentRedemptions->isEmpty())
                <div class="py-8 text-center">
                    <div class="w-12 h-12 rounded-full bg-slate-50 flex items-center justify-center text-slate-400 mx-auto mb-2">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                    </div>
                    <p class="text-xs text-slate-400 font-medium">Belum ada pengajuan pencairan.</p>
                </div>
            @else
                <div class="divide-y divide-slate-100">
                    @foreach($recentRedemptions as $rr)
                        <div class="py-3 flex items-center justify-between gap-4 first:pt-0 last:pb-0">
                            <div>
                                <p class="text-sm font-semibold text-slate-800">Cairkan ke {{ $rr->bank_name }}</p>
                                <p class="text-[11px] text-slate-400 font-medium mt-0.5">{{ $rr->created_at->format('d M Y - H:i') }}</p>
                            </div>
                            <div class="text-right">
                                <p class="text-sm font-bold text-slate-800">Rp {{ number_format($rr->amount, 0, ',', '.') }}</p>
                                <span class="inline-block px-2 py-0.5 text-[10px] font-bold rounded-md mt-1
                                    {{ $rr->status === 'pending' ? 'bg-amber-50 text-amber-600 border border-amber-100' : '' }}
                                    {{ $rr->status === 'approved' ? 'bg-emerald-50 text-emerald-600 border border-emerald-100' : '' }}
                                    {{ $rr->status === 'rejected' ? 'bg-rose-50 text-rose-600 border border-rose-100' : '' }}">
                                    {{ ucfirst($rr->status) }}
                                </span>
                            </div>
                        </div>
                    @endforeach
                </div>
            @endif
        </div>
    </div>
</div>
@endsection
