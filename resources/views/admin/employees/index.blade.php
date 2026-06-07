@extends('layouts.app')

@section('title', 'Daftar Pegawai')

@section('content')
<div class="bg-white border border-slate-200/60 rounded-3xl p-6 shadow-sm">
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 mb-6">
        <div>
            <h1 class="text-2xl font-extrabold text-slate-800 tracking-tight">Kelola Akun Pegawai</h1>
            <p class="text-xs text-slate-500 font-medium mt-1">Daftar operator lapangan yang memiliki hak akses pencatatan drop-off botol</p>
        </div>
        <div class="flex gap-2">
            <a href="{{ route('admin.employees.create') }}" class="inline-flex items-center gap-1.5 py-2.5 px-4 bg-emerald-600 hover:bg-emerald-500 text-white font-bold text-xs rounded-xl transition-all duration-200 shadow-sm shadow-emerald-500/10">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                </svg>
                <span>Tambah Pegawai</span>
            </a>
            <a href="{{ route('admin.dashboard') }}" class="inline-block py-2.5 px-4 bg-slate-100 hover:bg-slate-200 text-slate-700 font-bold text-xs rounded-xl transition-colors">
                Dasbor
            </a>
        </div>
    </div>

    <!-- Responsive Table Container -->
    <div class="overflow-x-auto rounded-2xl border border-slate-100">
        <table class="min-w-full divide-y divide-slate-100 text-left text-sm">
            <thead class="bg-slate-50 text-slate-500 font-bold text-xs uppercase tracking-wider">
                <tr>
                    <th scope="col" class="px-6 py-4">Nama Pegawai</th>
                    <th scope="col" class="px-6 py-4">Email</th>
                    <th scope="col" class="px-6 py-4">Tanggal Bergabung</th>
                    <th scope="col" class="px-6 py-4 text-center">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100 font-medium text-slate-700">
                @forelse($employees as $emp)
                    <tr class="hover:bg-slate-50/50 transition-colors">
                        <td class="px-6 py-4 whitespace-nowrap text-slate-800 font-semibold">
                            {{ $emp->name }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            {{ $emp->email }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-slate-500 text-xs">
                            {{ $emp->created_at->format('d M Y - H:i') }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-center">
                            <div class="flex items-center justify-center gap-2">
                                <a href="{{ route('admin.employees.edit', $emp->id) }}" class="py-1.5 px-3 bg-amber-50 hover:bg-amber-100 text-amber-600 border border-amber-100 rounded-lg text-xs font-bold transition-all duration-200">
                                    Edit
                                </a>
                                
                                <form action="{{ route('admin.employees.destroy', $emp->id) }}" method="POST" onsubmit="return confirm('Apakah Anda yakin ingin menghapus akun pegawai ini?')" class="inline-block">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="py-1.5 px-3 bg-rose-50 hover:bg-rose-100 text-rose-600 border border-rose-100 rounded-lg text-xs font-bold transition-all duration-200">
                                        Hapus
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="4" class="px-6 py-10 text-center">
                            <div class="w-12 h-12 rounded-full bg-slate-50 flex items-center justify-center text-slate-400 mx-auto mb-2">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"></path>
                                </svg>
                            </div>
                            <p class="text-xs text-slate-400 font-medium">Belum ada akun pegawai yang terdaftar.</p>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <!-- Pagination -->
    <div class="mt-5">
        {{ $employees->links() }}
    </div>
</div>
@endsection
