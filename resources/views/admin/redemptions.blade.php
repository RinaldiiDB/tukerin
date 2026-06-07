@extends('layouts.app')

@section('title', 'Persetujuan Pencairan')

@section('content')
<div class="bg-white border border-slate-200/60 rounded-3xl p-6 shadow-sm">
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 mb-6">
        <div>
            <h1 class="text-2xl font-extrabold text-slate-800 tracking-tight">Persetujuan Pencairan Poin</h1>
            <p class="text-xs text-slate-500 font-medium mt-1">Daftar pengajuan penarikan dana rupiah dari poin nasabah</p>
        </div>
        <div>
            <a href="{{ route('admin.dashboard') }}" class="inline-block py-2.5 px-4 bg-slate-100 hover:bg-slate-200 text-slate-700 font-bold text-xs rounded-xl transition-colors">
                Kembali ke Dasbor
            </a>
        </div>
    </div>

    <!-- Responsive Table Container -->
    <div class="overflow-x-auto rounded-2xl border border-slate-100">
        <table class="min-w-full divide-y divide-slate-100 text-left text-sm">
            <thead class="bg-slate-50 text-slate-500 font-bold text-xs uppercase tracking-wider">
                <tr>
                    <th scope="col" class="px-6 py-4">Nasabah</th>
                    <th scope="col" class="px-6 py-4">Poin Ditukar</th>
                    <th scope="col" class="px-6 py-4">Nominal</th>
                    <th scope="col" class="px-6 py-4">Tujuan Transfer</th>
                    <th scope="col" class="px-6 py-4 text-center">Status / Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100 font-medium text-slate-700">
                @forelse($redemptions as $rr)
                    <tr class="hover:bg-slate-50/50 transition-colors">
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span class="text-slate-800 font-semibold block">{{ $rr->user->name }}</span>
                            <span class="text-[10px] text-slate-400 block mt-0.5">Diajukan: {{ $rr->created_at->format('d M Y - H:i') }}</span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-amber-600 font-bold">
                            {{ number_format($rr->points_used, 0, ',', '.') }} Pts
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap font-extrabold text-slate-900">
                            Rp {{ number_format($rr->amount, 0, ',', '.') }}
                        </td>
                        <td class="px-6 py-4">
                            <span class="text-[10px] font-bold px-2 py-0.5 rounded-full inline-block mb-1
                                {{ $rr->method === 'cash' ? 'bg-indigo-50 text-indigo-600 border border-indigo-100' : 'bg-purple-50 text-purple-600 border border-purple-100' }}">
                                {{ $rr->method === 'cash' ? 'Cash Transfer' : 'E-Wallet' }}
                            </span>
                            <p class="text-xs text-slate-600 font-semibold">{{ $rr->bank_name }}</p>
                            <p class="text-xs text-slate-400 font-bold font-mono">{{ $rr->recipient_account }}</p>
                        </td>
                        <td class="px-6 py-4 text-center">
                            @if($rr->status === 'pending')
                                <div class="flex flex-col items-center justify-center gap-1.5">
                                    <div class="flex gap-2">
                                        <!-- Approve Form -->
                                        <form action="{{ route('admin.redemptions.approve', $rr->id) }}" method="POST" onsubmit="return confirm('Apakah Anda yakin ingin menyetujui pencairan ini? Saldo poin nasabah akan langsung dipotong.')">
                                            @csrf
                                            <button type="submit" class="py-1 px-3 bg-emerald-600 hover:bg-emerald-500 text-white text-xs font-bold rounded-lg transition-colors shadow-sm">
                                                Setujui
                                            </button>
                                        </form>

                                        <!-- Reject Toggle -->
                                        <button type="button" onclick="toggleRejectForm('{{ $rr->id }}')" class="py-1 px-3 bg-rose-50 hover:bg-rose-100 text-rose-600 border border-rose-100 text-xs font-bold rounded-lg transition-colors">
                                            Tolak
                                        </button>
                                    </div>

                                    <!-- Hidden Reject Input Panel -->
                                    <div id="reject-panel-{{ $rr->id }}" class="hidden w-64 p-3 bg-rose-50 border border-rose-100 rounded-2xl mt-2 text-left animate-fade-in">
                                        <form action="{{ route('admin.redemptions.reject', $rr->id) }}" method="POST" class="space-y-2">
                                            @csrf
                                            <label class="block text-[10px] text-rose-700 font-bold uppercase tracking-wider">Alasan Penolakan</label>
                                            <textarea name="rejection_note" rows="2" placeholder="Masukkan alasan penolakan..." required
                                                class="w-full block p-2 border border-rose-200 rounded-xl text-xs bg-white focus:outline-none focus:ring-1 focus:ring-rose-500 focus:border-rose-500 placeholder-slate-400 transition-colors"></textarea>
                                            <div class="flex justify-end gap-1.5">
                                                <button type="button" onclick="toggleRejectForm('{{ $rr->id }}')" class="px-2.5 py-1 text-[10px] font-bold text-slate-500 hover:text-slate-700 transition-colors">Batal</button>
                                                <button type="submit" class="px-2.5 py-1 bg-rose-600 hover:bg-rose-500 text-white text-[10px] font-bold rounded-lg transition-all duration-200 shadow-sm shadow-rose-600/10">Kirim</button>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            @else
                                <span class="inline-block px-3 py-1 text-xs font-bold rounded-lg
                                    {{ $rr->status === 'approved' ? 'bg-emerald-50 text-emerald-600 border border-emerald-100' : '' }}
                                    {{ $rr->status === 'rejected' ? 'bg-rose-50 text-rose-600 border border-rose-100' : '' }}">
                                    {{ ucfirst($rr->status) }}
                                </span>
                                @if($rr->status === 'rejected' && $rr->rejection_note)
                                    <div class="text-[10px] text-rose-500 mt-1 max-w-[200px] mx-auto whitespace-normal leading-tight">
                                        Alasan: <strong class="font-medium">"{{ $rr->rejection_note }}"</strong>
                                    </div>
                                @endif
                                @if($rr->processed_at)
                                    <span class="block text-[9px] text-slate-400 mt-1 font-semibold">Diproses: {{ $rr->processed_at->format('d M Y') }}</span>
                                @endif
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
                            <p class="text-xs text-slate-400 font-medium">Belum ada pengajuan pencairan poin.</p>
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

@section('scripts')
<script>
    function toggleRejectForm(id) {
        const panel = document.getElementById(`reject-panel-${id}`);
        if (panel.classList.contains('hidden')) {
            panel.classList.remove('hidden');
        } else {
            panel.classList.add('hidden');
        }
    }
</script>
@endsection
