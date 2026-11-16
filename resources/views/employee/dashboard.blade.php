@extends('layouts.app')

@section('title', 'Dasbor Pegawai')

@section('content')
<div class="space-y-7">
    <div class="dashboard-heading">
        <div>
            <p class="eyebrow">Panel operasional</p>
            <h1>Siap melayani hari ini?</h1>
            <p class="mt-1">Pantau setoran botol dan proses penukaran nasabah.</p>
        </div>
        <span class="hidden rounded-full border border-emerald-900/10 bg-white/70 px-4 py-2 text-xs font-bold text-emerald-900 sm:inline-flex">{{ now()->format('d M Y') }}</span>
    </div>

    <section class="hero-card p-6 md:p-9">
        <div class="relative z-10 flex flex-col justify-between gap-7 md:flex-row md:items-center">
            <div>
                <p class="hero-kicker text-xs font-extrabold uppercase tracking-[.18em]">Ringkasan hari ini</p>
                <h2 class="mt-2 text-3xl font-extrabold tracking-tight text-white">Aktivitas setoran</h2>
                <div class="mt-6 flex flex-wrap gap-7">
                    <div><p class="hero-meta text-xs font-semibold">Transaksi diproses</p><p class="mt-1 text-2xl font-extrabold text-white">{{ number_format($todayCount, 0, ',', '.') }} <span class="text-sm font-semibold text-white/60">kali</span></p></div>
                    <div class="border-l border-white/20 pl-7"><p class="hero-meta text-xs font-semibold">Poin tersalurkan</p><p class="mt-1 text-2xl font-extrabold text-lime-200">{{ number_format($todayPoints, 0, ',', '.') }} <span class="text-sm font-semibold text-white/60">poin</span></p></div>
                </div>
            </div>
            <a href="{{ route('employee.scan') }}" class="hero-action self-start md:self-center">
                <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4h5v5H4zM15 4h5v5h-5zM4 15h5v5H4zM15 15h2v2h-2zm3 0h2v5h-2zm-3 3h2v2h-2z" /></svg>
                Mulai scan setoran
            </a>
        </div>
    </section>

    <section class="bg-white border border-slate-200/60 rounded-3xl p-6 shadow-sm">
        <div class="mb-5 flex flex-col gap-3 sm:flex-row sm:items-end sm:justify-between">
            <div><p class="text-[10px] font-extrabold uppercase tracking-[.16em] text-slate-400">Aktivitas hari ini</p><h2 class="mt-1 text-lg font-extrabold text-slate-800">Setoran terbaru</h2><p class="mt-1 text-xs text-slate-500">Transaksi yang sudah Anda proses hari ini.</p></div>
            <a href="{{ route('employee.transactions') }}" class="w-fit rounded-full bg-emerald-50 px-3 py-2 text-xs font-bold text-emerald-700 hover:bg-emerald-100">Buka seluruh riwayat <span aria-hidden="true">↗</span></a>
        </div>

        @if($todayTransactions->isEmpty())
            <div class="rounded-2xl border border-dashed border-slate-200 bg-slate-50/70 px-5 py-12 text-center">
                <span class="mx-auto mb-3 flex h-12 w-12 items-center justify-center rounded-2xl bg-white text-2xl shadow-sm" aria-hidden="true">♻</span>
                <p class="text-sm font-bold text-slate-700">Belum ada transaksi hari ini</p>
                <p class="mt-1 text-xs text-slate-500">Transaksi baru akan tampil setelah proses scan selesai.</p>
            </div>
        @else
            <div class="overflow-x-auto rounded-2xl border border-slate-100">
                <table class="min-w-full divide-y divide-slate-100 text-left text-sm">
                    <thead class="bg-slate-50 text-slate-500 font-bold text-xs uppercase tracking-wider"><tr><th scope="col" class="px-5 py-4">Waktu</th><th scope="col" class="px-5 py-4">Nasabah</th><th scope="col" class="px-5 py-4">Poin</th><th scope="col" class="px-5 py-4">Rincian botol</th></tr></thead>
                    <tbody class="divide-y divide-slate-100 font-medium text-slate-700">
                        @foreach($todayTransactions as $tx)
                            <tr>
                                <td class="whitespace-nowrap px-5 py-4 text-xs font-semibold text-slate-500">{{ $tx->transacted_at->format('H:i') }} WIB</td>
                                <td class="whitespace-nowrap px-5 py-4"><span class="block font-bold text-slate-800">{{ $tx->user->name }}</span><span class="mt-1 block text-xs text-slate-400">{{ $tx->user->profile->phone }}</span></td>
                                <td class="whitespace-nowrap px-5 py-4"><span class="rounded-full bg-emerald-50 px-3 py-1.5 text-xs font-extrabold text-emerald-700">+{{ number_format($tx->total_points, 0, ',', '.') }}</span></td>
                                <td class="px-5 py-4"><div class="flex flex-wrap gap-1.5">@foreach($tx->details as $d)<span class="rounded-full bg-slate-100 px-2.5 py-1 text-[10px] font-semibold text-slate-600">{{ $d->quantity }}× {{ $d->bottleType->name }}</span>@endforeach</div></td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif
    </section>
</div>
@endsection
