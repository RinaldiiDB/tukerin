@extends('layouts.app')

@section('title', 'Daftar Nasabah')

@section('content')
<div class="bg-white border border-slate-200/60 rounded-3xl p-6 shadow-sm">
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 mb-6">
        <div>
            <h1 class="text-2xl font-extrabold text-slate-800 tracking-tight">Daftar Nasabah Terdaftar</h1>
            <p class="text-xs text-slate-500 font-medium mt-1">Seluruh nasabah penukar botol daur ulang aktif beserta rincian saldonya</p>
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
                    <th scope="col" class="px-6 py-4">Nama Lengkap</th>
                    <th scope="col" class="px-6 py-4">Kontak Email / Telp</th>
                    <th scope="col" class="px-6 py-4 font-mono">Kode QR</th>
                    <th scope="col" class="px-6 py-4">Terdaftar Sejak</th>
                    <th scope="col" class="px-6 py-4 text-right">Saldo Aktif</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100 font-medium text-slate-700">
                @forelse($users as $user)
                    <tr class="hover:bg-slate-50/50 transition-colors">
                        <td class="px-6 py-4 whitespace-nowrap text-slate-800 font-semibold">
                            {{ $user->name }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span class="block">{{ $user->email }}</span>
                            <span class="text-[10px] text-slate-400 block mt-0.5">{{ $user->profile ? $user->profile->phone : '-' }}</span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap font-mono text-slate-900 font-bold text-xs">
                            {{ $user->profile ? $user->profile->qr_code : '-' }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-slate-500 text-xs">
                            {{ $user->created_at->format('d M Y - H:i') }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-right text-base font-extrabold text-emerald-600">
                            {{ number_format($user->profile ? $user->profile->points_balance : 0, 0, ',', '.') }} Pts
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="px-6 py-10 text-center">
                            <div class="w-12 h-12 rounded-full bg-slate-50 flex items-center justify-center text-slate-400 mx-auto mb-2">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0"></path>
                                </svg>
                            </div>
                            <p class="text-xs text-slate-400 font-medium">Belum ada nasabah terdaftar.</p>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <!-- Pagination -->
    <div class="mt-5">
        {{ $users->links() }}
    </div>
</div>
@endsection
