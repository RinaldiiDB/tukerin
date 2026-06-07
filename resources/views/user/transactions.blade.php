@extends('layouts.app')

@section('title', 'Histori Penukaran')

@section('content')
<div class="bg-white border border-slate-200/60 rounded-3xl p-6 shadow-sm">
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 mb-6">
        <div>
            <h1 class="text-2xl font-extrabold text-slate-800 tracking-tight">Histori Penukaran Botol</h1>
            <p class="text-xs text-slate-500 font-medium mt-1">Daftar transaksi penyerahan botol plastik daur ulang Anda</p>
        </div>
        <div>
            <a href="{{ route('user.dashboard') }}" class="inline-block py-2.5 px-4 bg-slate-100 hover:bg-slate-200 text-slate-700 font-bold text-xs rounded-xl transition-colors">
                Kembali ke Beranda
            </a>
        </div>
    </div>

    <!-- Responsive Table Container -->
    <div class="overflow-x-auto rounded-2xl border border-slate-100">
        <table class="min-w-full divide-y divide-slate-100 text-left text-sm">
            <thead class="bg-slate-50 text-slate-500 font-bold text-xs uppercase tracking-wider">
                <tr>
                    <th scope="col" class="px-6 py-4">Waktu Transaksi</th>
                    <th scope="col" class="px-6 py-4">Diproses Oleh</th>
                    <th scope="col" class="px-6 py-4">Rincian Item</th>
                    <th scope="col" class="px-6 py-4 text-right">Poin Diperoleh</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100 font-medium text-slate-700">
                @forelse($transactions as $tx)
                    <tr class="hover:bg-slate-50/50 transition-colors">
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span class="text-slate-800 font-semibold block">{{ $tx->transacted_at->format('d M Y') }}</span>
                            <span class="text-slate-400 text-[10px]">{{ $tx->transacted_at->format('H:i') }} WIB</span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            {{ $tx->employee->name }}
                        </td>
                        <td class="px-6 py-4">
                            <div class="flex flex-wrap gap-1.5 max-w-lg">
                                @foreach($tx->details as $detail)
                                    <span class="inline-flex items-center px-2 py-1 rounded-lg bg-slate-100 border border-slate-200/40 text-xs text-slate-600">
                                        {{ $detail->quantity }}x {{ $detail->bottleType->name }} ({{ $detail->points_earned }} Pts)
                                    </span>
                                @endforeach
                            </div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-right text-base font-bold text-emerald-600">
                            +{{ $tx->total_points }} Pts
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="4" class="px-6 py-10 text-center">
                            <div class="w-12 h-12 rounded-full bg-slate-50 flex items-center justify-center text-slate-400 mx-auto mb-2">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01"></path>
                                </svg>
                            </div>
                            <p class="text-xs text-slate-400 font-medium">Anda belum melakukan transaksi penukaran botol.</p>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <!-- Pagination -->
    <div class="mt-5">
        {{ $transactions->links() }}
    </div>
</div>
@endsection
