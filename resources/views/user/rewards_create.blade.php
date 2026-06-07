@extends('layouts.app')

@section('title', 'Ajukan Pencairan')

@section('content')
<div class="max-w-md mx-auto">
    <div class="bg-white border border-slate-200/60 rounded-3xl p-6 md:p-8 shadow-xl">
        <h1 class="text-2xl font-extrabold text-slate-800 tracking-tight">Ajukan Pencairan Poin</h1>
        <p class="text-xs text-slate-500 font-medium mt-1">Konversikan saldo poin aktif Anda menjadi saldo tunai atau e-wallet.</p>

        <!-- Current Balance Helper Info -->
        <div class="my-5 p-4 rounded-2xl bg-emerald-50 border border-emerald-100 flex justify-between items-center text-slate-800">
            <div>
                <p class="text-[10px] text-slate-400 font-semibold tracking-wider uppercase">Saldo Poin Aktif</p>
                <p class="text-2xl font-extrabold text-emerald-700 tracking-tight mt-0.5" id="user-points-balance">{{ $profile->points_balance }} <span class="text-sm font-medium text-slate-500">Poin</span></p>
            </div>
            <div class="text-right">
                <p class="text-[10px] text-slate-400 font-semibold tracking-wider uppercase">Nilai Konversi</p>
                <p class="text-sm font-bold text-slate-600 mt-1">1 Poin = Rp 200</p>
            </div>
        </div>

        <form action="{{ route('user.rewards.store') }}" method="POST" class="space-y-4">
            @csrf

            <!-- Points to Redeem -->
            <div>
                <label for="points_used" class="block text-sm font-semibold text-slate-700 font-medium">Poin yang Ingin Dicairkan</label>
                <div class="mt-1 relative rounded-2xl">
                    <input type="number" name="points_used" id="points_used" min="1" max="{{ $profile->points_balance }}" required value="{{ old('points_used') }}"
                        class="appearance-none block w-full px-4 py-3 border border-slate-200 rounded-2xl placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-500 text-sm transition-all duration-200">
                </div>
                @error('points_used')
                    <p class="mt-1.5 text-xs text-rose-600 font-medium">{{ $message }}</p>
                @enderror
            </div>

            <!-- Calculated Cash Preview -->
            <div class="p-3 bg-slate-50 rounded-2xl border border-slate-100 flex items-center justify-between text-slate-700 text-xs font-semibold">
                <span>Estimasi Uang Diterima:</span>
                <span class="text-base font-extrabold text-slate-900" id="cash-preview">Rp 0</span>
            </div>

            <!-- Method Selection -->
            <div>
                <label for="method" class="block text-sm font-semibold text-slate-700 font-medium">Metode Transfer</label>
                <div class="mt-1">
                    <select name="method" id="method" required
                        class="block w-full px-4 py-3 border border-slate-200 rounded-2xl focus:outline-none focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-500 text-sm transition-all duration-200">
                        <option value="ewallet" {{ old('method') == 'ewallet' ? 'selected' : '' }}>E-Wallet (GoPay, OVO, Dana, LinkAja)</option>
                        <option value="cash" {{ old('method') == 'cash' ? 'selected' : '' }}>Rekening Bank (Cash Transfer)</option>
                    </select>
                </div>
                @error('method')
                    <p class="mt-1.5 text-xs text-rose-600 font-medium">{{ $message }}</p>
                @enderror
            </div>

            <!-- Bank / E-Wallet Name -->
            <div>
                <label for="bank_name" class="block text-sm font-semibold text-slate-700 font-medium" id="bank-label">Nama E-Wallet / Vendor</label>
                <div class="mt-1">
                    <input type="text" name="bank_name" id="bank_name" placeholder="Contoh: GoPay, OVO, Bank Mandiri" required value="{{ old('bank_name') }}"
                        class="appearance-none block w-full px-4 py-3 border border-slate-200 rounded-2xl placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-500 text-sm transition-all duration-200">
                </div>
                @error('bank_name')
                    <p class="mt-1.5 text-xs text-rose-600 font-medium">{{ $message }}</p>
                @enderror
            </div>

            <!-- Account / Phone Number -->
            <div>
                <label for="recipient_account" class="block text-sm font-semibold text-slate-700 font-medium" id="account-label">Nomor HP / Nomor Rekening</label>
                <div class="mt-1">
                    <input type="text" name="recipient_account" id="recipient_account" placeholder="Contoh: 08123456789 atau 1234567890" required value="{{ old('recipient_account') }}"
                        class="appearance-none block w-full px-4 py-3 border border-slate-200 rounded-2xl placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-500 text-sm transition-all duration-200">
                </div>
                @error('recipient_account')
                    <p class="mt-1.5 text-xs text-rose-600 font-medium">{{ $message }}</p>
                @enderror
            </div>

            <!-- Submit Buttons -->
            <div class="flex gap-3 pt-3">
                <a href="{{ route('user.rewards') }}" class="flex-1 py-3 px-4 bg-slate-100 hover:bg-slate-200 text-slate-700 font-bold text-sm rounded-2xl text-center transition-colors">
                    Batal
                </a>
                <button type="submit" class="flex-1 py-3 px-4 bg-gradient-to-r from-emerald-600 to-teal-500 hover:from-emerald-500 hover:to-teal-400 text-white font-bold text-sm rounded-2xl transition-all shadow-md shadow-emerald-500/10">
                    Kirim Pengajuan
                </button>
            </div>
        </form>
    </div>
</div>
@endsection

@section('scripts')
<script>
    document.addEventListener("DOMContentLoaded", function() {
        const pointsInput = document.getElementById('points_used');
        const cashPreview = document.getElementById('cash-preview');
        const methodSelect = document.getElementById('method');
        const bankLabel = document.getElementById('bank-label');
        const accountLabel = document.getElementById('account-label');
        const pointRate = 200; // 1 point = Rp 200

        // Calculate reward value in cash
        function updatePreview() {
            const points = parseInt(pointsInput.value) || 0;
            const money = points * pointRate;
            cashPreview.textContent = 'Rp ' + money.toLocaleString('id-ID');
        }

        pointsInput.addEventListener('input', updatePreview);
        updatePreview();

        // Adjust form labels based on method selection
        function adjustLabels() {
            if (methodSelect.value === 'cash') {
                bankLabel.textContent = 'Nama Bank';
                accountLabel.textContent = 'Nomor Rekening Bank';
            } else {
                bankLabel.textContent = 'Nama E-Wallet / Vendor';
                accountLabel.textContent = 'Nomor HP E-Wallet';
            }
        }

        methodSelect.addEventListener('change', adjustLabels);
        adjustLabels();
    });
</script>
@endsection
