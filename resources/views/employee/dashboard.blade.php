@extends('layouts.app')

@section('title', 'Dasbor Pegawai')

@section('content')
<div class="space-y-6">
    <!-- Header / Stats Card -->
    <div class="relative overflow-hidden bg-gradient-to-tr from-slate-900 via-slate-800 to-amber-950 text-white rounded-3xl p-6 md:p-8 shadow-xl">
        <div class="absolute right-0 bottom-0 w-64 h-64 bg-amber-500/10 rounded-full blur-3xl -mr-16 -mb-16"></div>

        <div class="relative z-10 flex flex-col md:flex-row md:items-center justify-between gap-6">
            <div>
                <p class="text-amber-400 font-semibold text-xs tracking-wider uppercase">Operasional Hari Ini</p>
                <h1 class="text-3xl font-extrabold tracking-tight mt-1">Dasbor Pegawai</h1>
                <div class="flex flex-wrap gap-6 mt-4">
                    <div>
                        <p class="text-[10px] text-slate-400 font-semibold tracking-wider uppercase">Transaksi Diproses</p>
                        <p class="text-xl font-bold text-slate-200 mt-0.5">{{ $todayCount }} Kali</p>
                    </div>
                    <div class="border-l border-slate-700/60 pl-6">
                        <p class="text-[10px] text-slate-400 font-semibold tracking-wider uppercase">Total Poin Disalurkan</p>
                        <p class="text-xl font-bold text-amber-400 mt-0.5">{{ number_format($todayPoints, 0, ',', '.') }} Pts</p>
                    </div>
                </div>
            </div>
            
            <div>
                <a href="{{ route('employee.scan') }}" class="w-full md:w-auto flex items-center justify-center gap-2 px-6 py-4 bg-gradient-to-tr from-amber-500 to-amber-600 hover:from-amber-400 hover:to-amber-500 text-slate-950 font-bold text-sm rounded-2xl transition-all duration-200 shadow-lg shadow-amber-500/20 ring-4 ring-amber-500/10">
                    <svg class="w-5 h-5 text-slate-950" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v1m6 11h2m-6 0h-2v4m0-11v3m0 0h.01M12 12h.01M16 12h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                    <span>Mulai Scan Botol</span>
                </a>
            </div>
        </div>
    </div>

    <!-- Today's Transactions Log -->
    <div class="bg-white border border-slate-200/60 rounded-3xl p-6 shadow-sm">
        <div class="flex items-center justify-between mb-5">
            <div>
                <h2 class="text-lg font-bold text-slate-800">Transaksi Hari Ini</h2>
                <p class="text-[11px] text-slate-400 font-medium">Daftar penukaran botol yang Anda proses hari ini</p>
            </div>
            <a href="{{ route('employee.transactions') }}" class="text-xs font-bold text-amber-600 hover:text-amber-500 transition-colors">Lihat Semua Riwayat</a>
        </div>

        @if($todayTransactions->isEmpty())
            <div class="py-12 text-center">
                <div class="w-12 h-12 rounded-full bg-slate-50 flex items-center justify-center text-slate-400 mx-auto mb-2">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01"></path>
                    </svg>
                </div>
                <p class="text-xs text-slate-400 font-medium">Anda belum memproses transaksi hari ini.</p>
            </div>
        @else
            <!-- Table Container -->
            <div class="overflow-x-auto rounded-2xl border border-slate-100">
                <table class="min-w-full divide-y divide-slate-100 text-left text-sm">
                    <thead class="bg-slate-50 text-slate-500 font-bold text-xs uppercase tracking-wider">
                        <tr>
                            <th scope="col" class="px-6 py-4">Waktu</th>
                            <th scope="col" class="px-6 py-4">Nasabah</th>
                            <th scope="col" class="px-6 py-4">Jumlah Poin</th>
                            <th scope="col" class="px-6 py-4">Detail</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 font-medium text-slate-700">
                        @foreach($todayTransactions as $tx)
                            <tr class="hover:bg-slate-50/50 transition-colors">
                                <td class="px-6 py-4 whitespace-nowrap">
                                    {{ $tx->transacted_at->format('H:i') }} WIB
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span class="text-slate-950 font-semibold block">{{ $tx->user->name }}</span>
                                    <span class="text-[10px] text-slate-400 font-medium">{{ $tx->user->profile->phone }}</span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-amber-600 font-bold">
                                    +{{ $tx->total_points }} Pts
                                </td>
                                <td class="px-6 py-4">
                                    <div class="flex flex-wrap gap-1">
                                        @foreach($tx->details as $d)
                                            <span class="inline-block bg-slate-100 px-2 py-0.5 rounded text-[10px] text-slate-600">
                                                {{ $d->quantity }}x {{ $d->bottleType->name }}
                                            </span>
                                        @endforeach
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif
    </div>
</div>
@endsection
