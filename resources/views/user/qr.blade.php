@extends('layouts.app')

@section('title', 'QR Code Nasabah')

@section('content')
<div class="max-w-md mx-auto py-8">
    <div class="bg-white border border-slate-200/60 rounded-3xl p-6 md:p-8 shadow-xl text-center">
        <h1 class="text-2xl font-extrabold text-slate-800 tracking-tight">QR Code Nasabah</h1>
        <p class="text-xs text-slate-500 font-medium mt-1">
            Tunjukkan QR Code ini kepada Pegawai saat melakukan penukaran botol plastik
        </p>

        <!-- QR Canvas Container -->
        <div class="my-8 flex justify-center">
            <div class="relative p-4 rounded-3xl bg-slate-50 border border-slate-100 shadow-inner flex items-center justify-center">
                <canvas id="qrcode-canvas" class="w-56 h-56"></canvas>
            </div>
        </div>

        <!-- Code value text representation -->
        <div class="bg-slate-50 border border-slate-100 rounded-2xl py-3 px-4 mb-6">
            <p class="text-[10px] text-slate-400 font-semibold tracking-wide uppercase">ID QR Anda</p>
            <p class="text-lg font-mono font-bold text-slate-800 tracking-wider mt-0.5">{{ $profile->qr_code }}</p>
        </div>

        <div class="flex gap-3">
            <a href="{{ route('user.dashboard') }}" class="flex-1 py-3 px-4 bg-slate-100 hover:bg-slate-200 text-slate-700 font-bold text-sm rounded-2xl transition-colors duration-200">
                Kembali ke Beranda
            </a>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script src="https://cdnjs.cloudflare.com/ajax/libs/qrious/4.0.2/qrious.min.js"></script>
<script>
    document.addEventListener("DOMContentLoaded", function() {
        const qr = new QRious({
            element: document.getElementById('qrcode-canvas'),
            value: "{{ $profile->qr_code }}",
            size: 300,
            background: '#f8fafc', // match tailwind slate-50
            foreground: '#0f172a', // slate-900
            level: 'H'
        });
    });
</script>
@endsection
