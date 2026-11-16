@extends('layouts.app')

@section('title', 'Dasbor Admin')

@section('content')
<div class="space-y-7">
    <div class="dashboard-heading">
        <div>
            <p class="eyebrow">Pusat kendali Tuker.in</p>
            <h1>Ringkasan operasional</h1>
            <p class="mt-1">Satu pandangan untuk aktivitas daur ulang hari ini.</p>
        </div>
        <span class="hidden rounded-full border border-emerald-900/10 bg-white/70 px-4 py-2 text-xs font-bold text-emerald-900 sm:inline-flex">{{ now()->translatedFormat('d F Y') }}</span>
    </div>

    <section class="hero-card p-6 md:p-8">
        <div class="relative z-10 flex flex-col justify-between gap-5 sm:flex-row sm:items-end">
            <div><p class="hero-kicker text-xs font-extrabold uppercase tracking-[.18em]">Dampak yang terus bertumbuh</p><h2 class="mt-2 max-w-2xl text-3xl font-extrabold leading-tight tracking-tight text-white md:text-4xl">Mengubah kebiasaan kecil menjadi perubahan besar.</h2></div>
            <a href="{{ route('admin.redemptions') }}" class="hero-action shrink-0">Tinjau pencairan <span aria-hidden="true">↗</span></a>
        </div>
    </section>

    <section class="grid gap-4 sm:grid-cols-2 xl:grid-cols-4" aria-label="Statistik sistem">
        <article class="bg-white border border-slate-200/60 rounded-3xl p-5 shadow-sm">
            <div class="flex items-start justify-between"><span class="text-[10px] font-extrabold uppercase tracking-[.14em] text-slate-400">Total nasabah</span><span class="flex h-10 w-10 items-center justify-center rounded-xl bg-emerald-50 text-emerald-700" aria-hidden="true">♧</span></div>
            <p class="mt-5 text-3xl font-extrabold tracking-tight text-slate-900">{{ number_format($totalUsers, 0, ',', '.') }}</p><p class="mt-1 text-xs font-medium text-slate-500">anggota terdaftar</p>
        </article>
        <article class="bg-white border border-slate-200/60 rounded-3xl p-5 shadow-sm">
            <div class="flex items-start justify-between"><span class="text-[10px] font-extrabold uppercase tracking-[.14em] text-slate-400">Transaksi botol</span><span class="flex h-10 w-10 items-center justify-center rounded-xl bg-sky-50 text-sky-700" aria-hidden="true">↗</span></div>
            <p class="mt-5 text-3xl font-extrabold tracking-tight text-slate-900">{{ number_format($totalTransactions, 0, ',', '.') }}</p><p class="mt-1 text-xs font-medium text-slate-500">penukaran tercatat</p>
        </article>
        <article class="bg-white border border-slate-200/60 rounded-3xl p-5 shadow-sm">
            <div class="flex items-start justify-between"><span class="text-[10px] font-extrabold uppercase tracking-[.14em] text-slate-400">Poin tersalurkan</span><span class="flex h-10 w-10 items-center justify-center rounded-xl bg-amber-50 text-amber-700" aria-hidden="true">✳</span></div>
            <p class="mt-5 text-3xl font-extrabold tracking-tight text-amber-700">{{ number_format($totalPointsCirculated, 0, ',', '.') }}</p><p class="mt-1 text-xs font-medium text-slate-500">poin beredar</p>
        </article>
        <article class="bg-white border border-slate-200/60 rounded-3xl p-5 shadow-sm">
            <div class="flex items-start justify-between"><span class="text-[10px] font-extrabold uppercase tracking-[.14em] text-slate-400">Menunggu tinjauan</span><span class="flex h-10 w-10 items-center justify-center rounded-xl bg-rose-50 text-rose-700" aria-hidden="true">◷</span></div>
            <p class="mt-5 text-3xl font-extrabold tracking-tight text-rose-700">{{ number_format($pendingRedemptions, 0, ',', '.') }}</p><p class="mt-1 text-xs font-medium text-slate-500">permintaan pencairan</p>
        </article>
    </section>

    <section>
        <div class="mb-4"><p class="text-[10px] font-extrabold uppercase tracking-[.16em] text-slate-400">Akses cepat</p><h2 class="mt-1 text-lg font-extrabold text-slate-800">Kelola sistem</h2></div>
        <div class="grid gap-4 sm:grid-cols-2 xl:grid-cols-3">
            <a href="{{ route('admin.employees.index') }}" class="group flex items-center gap-4 rounded-2xl border border-slate-200 bg-[#fffefa] p-5 shadow-sm transition hover:-translate-y-1 hover:border-emerald-200 hover:shadow-lg"><span class="flex h-12 w-12 shrink-0 items-center justify-center rounded-2xl bg-emerald-50 text-xl text-emerald-700">♙</span><span class="min-w-0 flex-1"><strong class="block text-sm font-extrabold text-slate-800">Kelola pegawai</strong><small class="mt-1 block text-xs text-slate-500">Akun operator lapangan</small></span><span class="text-lg text-slate-300 transition group-hover:translate-x-1 group-hover:text-emerald-700">→</span></a>
            <a href="{{ route('admin.bottle-types.index') }}" class="group flex items-center gap-4 rounded-2xl border border-slate-200 bg-[#fffefa] p-5 shadow-sm transition hover:-translate-y-1 hover:border-emerald-200 hover:shadow-lg"><span class="flex h-12 w-12 shrink-0 items-center justify-center rounded-2xl bg-lime-50 text-xl text-lime-800">♻</span><span class="min-w-0 flex-1"><strong class="block text-sm font-extrabold text-slate-800">Jenis botol</strong><small class="mt-1 block text-xs text-slate-500">Kategori, barcode, dan poin</small></span><span class="text-lg text-slate-300 transition group-hover:translate-x-1 group-hover:text-emerald-700">→</span></a>
            <a href="{{ route('admin.users') }}" class="group flex items-center gap-4 rounded-2xl border border-slate-200 bg-[#fffefa] p-5 shadow-sm transition hover:-translate-y-1 hover:border-emerald-200 hover:shadow-lg"><span class="flex h-12 w-12 shrink-0 items-center justify-center rounded-2xl bg-sky-50 text-xl text-sky-700">♧</span><span class="min-w-0 flex-1"><strong class="block text-sm font-extrabold text-slate-800">Direktori nasabah</strong><small class="mt-1 block text-xs text-slate-500">Data anggota dan saldo poin</small></span><span class="text-lg text-slate-300 transition group-hover:translate-x-1 group-hover:text-emerald-700">→</span></a>
            <a href="{{ route('admin.transactions') }}" class="group flex items-center gap-4 rounded-2xl border border-slate-200 bg-[#fffefa] p-5 shadow-sm transition hover:-translate-y-1 hover:border-emerald-200 hover:shadow-lg"><span class="flex h-12 w-12 shrink-0 items-center justify-center rounded-2xl bg-violet-50 text-xl text-violet-700">▤</span><span class="min-w-0 flex-1"><strong class="block text-sm font-extrabold text-slate-800">Log transaksi</strong><small class="mt-1 block text-xs text-slate-500">Semua penukaran tercatat</small></span><span class="text-lg text-slate-300 transition group-hover:translate-x-1 group-hover:text-emerald-700">→</span></a>
            <a href="{{ route('admin.redemptions') }}" class="group flex items-center gap-4 rounded-2xl border border-slate-200 bg-[#fffefa] p-5 shadow-sm transition hover:-translate-y-1 hover:border-emerald-200 hover:shadow-lg"><span class="flex h-12 w-12 shrink-0 items-center justify-center rounded-2xl bg-amber-50 text-xl text-amber-700">↗</span><span class="min-w-0 flex-1"><strong class="block text-sm font-extrabold text-slate-800">Persetujuan pencairan</strong><small class="mt-1 block text-xs text-slate-500">Tinjau permintaan nasabah</small></span><span class="text-lg text-slate-300 transition group-hover:translate-x-1 group-hover:text-emerald-700">→</span></a>
        </div>
    </section>
</div>
@endsection
