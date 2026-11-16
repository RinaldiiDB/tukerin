@extends('layouts.app')

@section('title', 'Dasbor Nasabah')

@section('content')
<div class="space-y-7">
    <div class="dashboard-heading">
        <div>
            <p class="eyebrow">Ruang hijau Anda</p>
            <h1>Halo, {{ auth()->user()->name }} <span aria-hidden="true">✦</span></h1>
            <p class="mt-1">Setiap botol yang kamu tukarkan membawa dampak baik.</p>
        </div>
        <span class="hidden rounded-full border border-emerald-900/10 bg-white/70 px-4 py-2 text-xs font-bold text-emerald-900 sm:inline-flex">Perjalanan berkelanjutan dimulai dari sini</span>
    </div>

    <section class="hero-card p-6 md:p-9" aria-label="Saldo poin">
        <div class="relative z-10 flex flex-col justify-between gap-7 md:flex-row md:items-end">
            <div>
                <p class="hero-kicker text-xs font-extrabold uppercase tracking-[.18em]">Saldo poin tersedia</p>
                <p class="hero-value mt-2 text-5xl font-extrabold md:text-6xl">{{ number_format($profile->points_balance, 0, ',', '.') }} <span class="text-xl font-semibold tracking-normal text-white/60">poin</span></p>
                <p class="hero-meta mt-3 text-sm">Setara dengan <strong class="font-bold text-lime-200">Rp {{ number_format($profile->points_balance * 200, 0, ',', '.') }}</strong> untuk dicairkan.</p>
            </div>
            <div class="flex flex-col gap-2 sm:flex-row">
                <a href="{{ route('user.qr') }}" class="hero-action">
                    <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4h5v5H4zM15 4h5v5h-5zM4 15h5v5H4zM15 15h2v2h-2zm3 0h2v5h-2zm-3 3h2v2h-2z" /></svg>
                    Tampilkan QR
                </a>
                <a href="{{ route('user.rewards.create') }}" class="hero-action secondary">
                    <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                    Cairkan poin
                </a>
            </div>
        </div>
        <div class="relative z-10 mt-7 flex items-center gap-2 border-t border-white/15 pt-4 text-xs font-medium text-white/65">
            <span class="flex h-6 w-6 items-center justify-center rounded-full bg-lime-300/15 text-lime-200">↗</span>
            <span>Saldo bertambah setiap kali kamu menyetorkan botol di titik Tuker.in.</span>
        </div>
    </section>

    <div class="grid gap-5 lg:grid-cols-2">
        <section class="bg-white border border-slate-200/60 rounded-3xl p-6 shadow-sm">
            <div class="mb-5 flex items-center justify-between gap-3">
                <div>
                    <p class="text-[10px] font-extrabold uppercase tracking-[.16em] text-slate-400">Aktivitas terbaru</p>
                    <h2 class="mt-1 text-lg font-extrabold text-slate-800">Penukaran botol</h2>
                </div>
                <a href="{{ route('user.transactions') }}" class="rounded-full bg-emerald-50 px-3 py-2 text-xs font-bold text-emerald-700 hover:bg-emerald-100">Semua transaksi <span aria-hidden="true">↗</span></a>
            </div>
            @if($recentTransactions->isEmpty())
                <div class="rounded-2xl border border-dashed border-slate-200 bg-slate-50/70 px-5 py-9 text-center">
                    <span class="mx-auto mb-3 flex h-12 w-12 items-center justify-center rounded-2xl bg-white text-2xl shadow-sm" aria-hidden="true">♻</span>
                    <p class="text-sm font-bold text-slate-700">Belum ada penukaran</p>
                    <p class="mt-1 text-xs text-slate-500">Riwayat setoran botolmu akan muncul di sini.</p>
                </div>
            @else
                <div class="divide-y divide-slate-100">
                    @foreach($recentTransactions as $tx)
                        <div class="flex items-center justify-between gap-4 py-4 first:pt-0 last:pb-0">
                            <div class="flex min-w-0 items-center gap-3">
                                <span class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl bg-emerald-50 text-emerald-700" aria-hidden="true">♻</span>
                                <div class="min-w-0"><p class="truncate text-sm font-bold text-slate-800">Penukaran botol plastik</p><p class="mt-1 text-xs text-slate-400">{{ $tx->transacted_at->format('d M Y · H:i') }}</p></div>
                            </div>
                            <span class="shrink-0 rounded-full bg-emerald-50 px-3 py-1.5 text-xs font-extrabold text-emerald-700">+{{ number_format($tx->total_points, 0, ',', '.') }}</span>
                        </div>
                    @endforeach
                </div>
            @endif
        </section>

        <section class="bg-white border border-slate-200/60 rounded-3xl p-6 shadow-sm">
            <div class="mb-5 flex items-center justify-between gap-3">
                <div>
                    <p class="text-[10px] font-extrabold uppercase tracking-[.16em] text-slate-400">Status permintaan</p>
                    <h2 class="mt-1 text-lg font-extrabold text-slate-800">Pencairan poin</h2>
                </div>
                <a href="{{ route('user.rewards') }}" class="rounded-full bg-emerald-50 px-3 py-2 text-xs font-bold text-emerald-700 hover:bg-emerald-100">Riwayat <span aria-hidden="true">↗</span></a>
            </div>
            @if($recentRedemptions->isEmpty())
                <div class="rounded-2xl border border-dashed border-slate-200 bg-slate-50/70 px-5 py-9 text-center">
                    <span class="mx-auto mb-3 flex h-12 w-12 items-center justify-center rounded-2xl bg-white text-2xl shadow-sm" aria-hidden="true">✳</span>
                    <p class="text-sm font-bold text-slate-700">Belum ada pencairan</p>
                    <p class="mt-1 text-xs text-slate-500">Ajukan pencairan poin kapan saja dari saldo Anda.</p>
                </div>
            @else
                <div class="divide-y divide-slate-100">
                    @foreach($recentRedemptions as $rr)
                        <div class="flex items-center justify-between gap-4 py-4 first:pt-0 last:pb-0">
                            <div class="flex min-w-0 items-center gap-3">
                                <span class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl bg-amber-50 text-amber-700" aria-hidden="true">↗</span>
                                <div class="min-w-0"><p class="truncate text-sm font-bold text-slate-800">{{ $rr->bank_name }}</p><p class="mt-1 text-xs text-slate-400">{{ $rr->created_at->format('d M Y · H:i') }}</p></div>
                            </div>
                            <div class="shrink-0 text-right"><p class="text-sm font-extrabold text-slate-800">Rp {{ number_format($rr->amount, 0, ',', '.') }}</p><span class="mt-1 inline-flex rounded-full px-2.5 py-1 text-[10px] font-bold {{ $rr->status === 'pending' ? 'bg-amber-50 text-amber-700' : '' }} {{ $rr->status === 'approved' ? 'bg-emerald-50 text-emerald-700' : '' }} {{ $rr->status === 'rejected' ? 'bg-rose-50 text-rose-700' : '' }}">{{ ucfirst($rr->status) }}</span></div>
                        </div>
                    @endforeach
                </div>
            @endif
        </section>
    </div>
</div>
@endsection
