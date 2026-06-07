@extends('layouts.app')

@section('title', 'Riwayat Pencairan')

@section('content')
<div class="bg-white border border-slate-200/60 rounded-3xl p-6 shadow-sm">
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 mb-6">
        <div>
            <h1 class="text-2xl font-extrabold text-slate-800 tracking-tight">Riwayat Pencairan Poin</h1>
            <p class="text-xs text-slate-500 font-medium mt-1">Daftar pengajuan pencairan poin Anda menjadi uang tunai atau saldo e-wallet</p>
        </div>
        <div class="flex gap-2">
            <a href="{{ route('user.rewards.create') }}" class="inline-flex items-center gap-1.5 py-2.5 px-4 bg-emerald-600 hover:bg-emerald-500 text-white font-bold text-xs rounded-xl transition-all duration-200 shadow-sm shadow-emerald-500/10">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                </svg>
                <span>Ajukan Pencairan</span>
            </a>
            <a href="{{ route('user.dashboard') }}" class="inline-block py-2.5 px-4 bg-slate-100 hover:bg-slate-200 text-slate-700 font-bold text-xs rounded-xl transition-colors">
                Kembali
            </a>
        </div>
    </div>

    <!-- Responsive Table Container -->
    <div class="overflow-x-auto rounded-2xl border border-slate-100">
        <table class="min-w-full divide-y divide-slate-100 text-left text-sm">
            <thead class="bg-slate-50 text-slate-500 font-bold text-xs uppercase tracking-wider">
                <tr>
                    <th scope="col" class="px-6 py-4">Tanggal Pengajuan</th>
                    <th scope="col" class="px-6 py-4">Poin Ditukar</th>
                    <th scope="col" class="px-6 py-4">Jumlah Diterima</th>
                    <th scope="col" class="px-6 py-4">Metode & Akun</th>
                    <th scope="col" class="px-6 py-4 text-center">Status</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100 font-medium text-slate-700">
                @forelse($redemptions as $rr)
                    <tr class="hover:bg-slate-50/50 transition-colors">
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span class="text-slate-800 font-semibold block">{{ $rr->created_at->format('d M Y') }}</span>
                            <span class="text-slate-400 text-[10px]">{{ $rr->created_at->format('H:i') }} WIB</span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-amber-600 font-bold">
                            {{ number_format($rr->points_used, 0, ',', '.') }} Poin
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap font-extrabold text-slate-900">
                            Rp {{ number_format($rr->amount, 0, ',', '.') }}
                        </td>
                        <td class="px-6 py-4">
                            <span class="text-xs font-semibold px-2 py-0.5 rounded-full inline-block mb-1
                                {{ $rr->method === 'cash' ? 'bg-indigo-50 text-indigo-600 border border-indigo-100' : 'bg-purple-50 text-purple-600 border border-purple-100' }}">
                                {{ $rr->method === 'cash' ? 'Cash' : 'E-Wallet' }}
                            </span>
                            <p class="text-xs text-slate-500 font-semibold">{{ $rr->bank_name }} - {{ $rr->recipient_account }}</p>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-center">
                            <span class="inline-block px-3 py-1 text-xs font-bold rounded-lg
                                {{ $rr->status === 'pending' ? 'bg-amber-50 text-amber-600 border border-amber-100' : '' }}
                                {{ $rr->status === 'approved' ? 'bg-emerald-50 text-emerald-600 border border-emerald-100' : '' }}
                                {{ $rr->status === 'rejected' ? 'bg-rose-50 text-rose-600 border border-rose-100' : '' }}">
                                {{ ucfirst($rr->status) }}
                            </span>
                            @if($rr->status === 'rejected' && $rr->rejection_note)
                                <div class="text-[10px] text-rose-500 mt-1 max-w-[200px] mx-auto whitespace-normal leading-tight">
                                    Catatan: <strong class="font-medium">"{{ $rr->rejection_note }}"</strong>
                                </div>
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="px-6 py-10 text-center">
                            <div class="w-12 h-12 rounded-full bg-slate-50 flex items-center justify-center text-slate-400 mx-auto mb-2">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                            </div>
                            <p class="text-xs text-slate-400 font-medium">Anda belum pernah mengajukan pencairan poin.</p>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <!-- Pagination -->
    <div class="mt-5">
        {{ $redemptions->links() }}
    </div>
</div>
@endsection
