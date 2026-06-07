@extends('layouts.app')

@section('title', 'Pemindaian Botol')

@section('styles')
<style>
    #reader {
        border: none !important;
        border-radius: 1.5rem;
        overflow: hidden;
    }
    #reader__dashboard_section_swaplink {
        display: none !important;
    }
    #reader video {
        border-radius: 1.5rem;
        object-fit: cover;
    }
</style>
@endsection

@section('content')
<div class="max-w-xl mx-auto space-y-6">
    <!-- Main Card -->
    <div class="bg-white border border-slate-200/60 rounded-3xl p-6 shadow-xl">
        <div class="flex items-center justify-between mb-4 border-b border-slate-100 pb-4">
            <div>
                <h1 class="text-xl font-bold text-slate-800">Scanner Transaksi</h1>
                <p class="text-xs text-slate-500 font-medium mt-0.5" id="scan-step-title">Tahap 1: Pindai QR Code Nasabah</p>
            </div>
            <a href="{{ route('employee.dashboard') }}" class="py-1.5 px-3 bg-slate-100 hover:bg-slate-200 text-slate-700 font-bold text-xs rounded-lg transition-colors">
                Kembali
            </a>
        </div>

        <!-- Scanning Window (html5-qrcode) -->
        <div class="relative rounded-3xl overflow-hidden bg-slate-950 aspect-square flex items-center justify-center border border-slate-200">
            <!-- Camera frame overlay -->
            <div class="absolute inset-0 z-10 pointer-events-none flex items-center justify-center p-8">
                <div class="w-full h-full border-2 border-emerald-500/50 rounded-2xl relative">
                    <div class="absolute -top-1 -left-1 w-6 h-6 border-t-4 border-l-4 border-emerald-500"></div>
                    <div class="absolute -top-1 -right-1 w-6 h-6 border-t-4 border-r-4 border-emerald-500"></div>
                    <div class="absolute -bottom-1 -left-1 w-6 h-6 border-b-4 border-l-4 border-emerald-500"></div>
                    <div class="absolute -bottom-1 -right-1 w-6 h-6 border-b-4 border-r-4 border-emerald-500"></div>
                </div>
            </div>
            
            <div id="reader" class="w-full h-full z-0"></div>
        </div>

        <!-- Manual Fallback Form (In case camera doesn't work or user has no camera) -->
        <div class="mt-4 p-4 bg-slate-50 rounded-2xl border border-slate-100 space-y-3">
            <h3 class="text-xs font-bold text-slate-500 uppercase tracking-wider">Input Manual (Alternatif)</h3>
            
            <!-- Stage 1 Manual User QR -->
            <div id="manual-user-section" class="flex gap-2">
                <input type="text" id="manual-qr-input" placeholder="Masukkan ID QR Nasabah (contoh: TK-ABC1234)"
                    class="appearance-none flex-1 block px-3.5 py-2.5 border border-slate-200 rounded-xl placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-500 text-xs transition-colors">
                <button type="button" id="btn-manual-user" class="py-2.5 px-4 bg-emerald-600 hover:bg-emerald-500 text-white font-bold text-xs rounded-xl transition-all duration-200 shadow-sm">
                    Cari
                </button>
            </div>

            <!-- Stage 2 Manual Barcode -->
            <div id="manual-bottle-section" class="hidden flex gap-2">
                <input type="text" id="manual-barcode-input" placeholder="Masukkan Barcode Botol (contoh: 60012345)"
                    class="appearance-none flex-1 block px-3.5 py-2.5 border border-slate-200 rounded-xl placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-amber-500/20 focus:border-amber-500 text-xs transition-colors">
                <button type="button" id="btn-manual-bottle" class="py-2.5 px-4 bg-amber-500 hover:bg-amber-400 text-slate-950 font-bold text-xs rounded-xl transition-all duration-200 shadow-sm">
                    Tambah
                </button>
            </div>
        </div>
    </div>

    <!-- Step 1 Result: Nasabah Details -->
    <div id="user-details-card" class="bg-white border border-slate-200/60 rounded-3xl p-5 shadow-md hidden animate-fade-in">
        <h2 class="text-xs font-bold text-slate-400 uppercase tracking-wider mb-3">Identitas Nasabah</h2>
        <div class="flex items-center justify-between gap-4">
            <div>
                <p class="text-lg font-bold text-slate-800" id="user-name">-</p>
                <p class="text-xs text-slate-400 font-semibold" id="user-phone">-</p>
            </div>
            <div class="text-right">
                <p class="text-[10px] text-slate-400 font-semibold uppercase tracking-wider">Saldo Poin Saat Ini</p>
                <p class="text-lg font-extrabold text-emerald-600" id="user-points">0 Pts</p>
            </div>
        </div>
    </div>

    <!-- Step 2 Accumulator: Scanned Items List & Submit -->
    <div id="scanned-items-card" class="bg-white border border-slate-200/60 rounded-3xl p-5 shadow-md hidden animate-fade-in">
        <div class="flex items-center justify-between mb-4 border-b border-slate-100 pb-3">
            <h2 class="text-xs font-bold text-slate-400 uppercase tracking-wider">Daftar Botol Diterima</h2>
            <button type="button" id="btn-reset-scan" class="text-xs font-bold text-rose-500 hover:text-rose-600 transition-colors">Reset</button>
        </div>

        <form action="{{ route('employee.transactions.store') }}" method="POST" id="transaction-form" class="space-y-4">
            @csrf
            <!-- Hidden inputs -->
            <input type="hidden" name="user_id" id="form-user-id">
            
            <!-- Items Container -->
            <div id="scanned-items-list" class="divide-y divide-slate-100 max-h-60 overflow-y-auto pr-1">
                <!-- Appended dynamically -->
            </div>

            <!-- Totals -->
            <div class="pt-3 border-t border-slate-100 flex items-center justify-between text-slate-800 font-bold text-sm">
                <span>Total Poin yang Akan Ditambahkan:</span>
                <span class="text-lg text-emerald-600" id="form-total-points">0 Pts</span>
            </div>

            <!-- Confirm Button -->
            <button type="submit" class="w-full py-3.5 px-4 bg-gradient-to-r from-emerald-600 to-teal-500 hover:from-emerald-500 hover:to-teal-400 text-white font-extrabold text-sm rounded-2xl transition-all duration-200 shadow-md shadow-emerald-500/10">
                Konfirmasi & Simpan Transaksi
            </button>
        </form>
    </div>
</div>
@endsection

@section('scripts')
<script src="https://unpkg.com/html5-qrcode"></script>
<script>
    document.addEventListener("DOMContentLoaded", function() {
        // State
        let currentUserId = null;
        let scanStage = 1; // 1: user, 2: bottle
        let scannedItems = {}; // bottle_type_id -> { name: string, points_value: int, quantity: int }

        // DOM elements
        const scanStepTitle = document.getElementById('scan-step-title');
        
        // Fallback UI
        const manualUserSection = document.getElementById('manual-user-section');
        const manualBottleSection = document.getElementById('manual-bottle-section');
        const manualQrInput = document.getElementById('manual-qr-input');
        const manualBarcodeInput = document.getElementById('manual-barcode-input');
        const btnManualUser = document.getElementById('btn-manual-user');
        const btnManualBottle = document.getElementById('btn-manual-bottle');

        // Results UI
        const userDetailsCard = document.getElementById('user-details-card');
        const userNameText = document.getElementById('user-name');
        const userPhoneText = document.getElementById('user-phone');
        const userPointsText = document.getElementById('user-points');
        
        const scannedItemsCard = document.getElementById('scanned-items-card');
        const scannedItemsList = document.getElementById('scanned-items-list');
        const formTotalPointsText = document.getElementById('form-total-points');
        const formUserIdInput = document.getElementById('form-user-id');
        const btnResetScan = document.getElementById('btn-reset-scan');

        // Initialize html5-qrcode
        let html5QrcodeScanner = new Html5Qrcode("reader");

        // Start scanning
        function startCamera() {
            html5QrcodeScanner.start(
                { facingMode: "environment" },
                {
                    fps: 10,
                    qrbox: (width, height) => {
                        return { width: width * 0.7, height: height * 0.7 };
                    }
                },
                onScanSuccess,
                onScanFailure
            ).catch(err => {
                console.error("Gagal menjalankan kamera:", err);
            });
        }

        startCamera();

        function onScanSuccess(decodedText, decodedResult) {
            if (scanStage === 1) {
                // Decoded QR text
                processUserQR(decodedText);
            } else if (scanStage === 2) {
                // Decoded Barcode text
                processBottleBarcode(decodedText);
            }
        }

        function onScanFailure(error) {
            // Quietly ignore scan failures
        }

        // Process User lookup
        function processUserQR(qrCode) {
            // Stop scanning temporarily
            html5QrcodeScanner.pause(true);

            fetch(`/employee/scan/user/${qrCode}`)
                .then(res => {
                    if (!res.ok) throw new Error("Nasabah tidak ditemukan.");
                    return res.json();
                })
                .then(data => {
                    const user = data.user;
                    currentUserId = user.id;
                    scanStage = 2;

                    // UI Changes
                    scanStepTitle.textContent = "Tahap 2: Pindai Barcode Botol Fisik";
                    userNameText.textContent = user.name;
                    userPhoneText.textContent = user.phone;
                    userPointsText.textContent = user.points_balance.toLocaleString('id-ID') + " Pts";
                    userDetailsCard.classList.remove('hidden');

                    manualUserSection.classList.add('hidden');
                    manualBottleSection.classList.remove('hidden');
                    scannedItemsCard.classList.remove('hidden');

                    formUserIdInput.value = currentUserId;

                    // Play simple beep or visual flash
                    flashFrame('emerald');
                    
                    // Resume scanning for stage 2 (barcode)
                    html5QrcodeScanner.resume();
                })
                .catch(err => {
                    alert(err.message || "Nasabah tidak ditemukan.");
                    html5QrcodeScanner.resume();
                });
        }

        // Process Bottle lookup
        function processBottleBarcode(barcode) {
            html5QrcodeScanner.pause(true);

            fetch(`/employee/scan/bottle/${barcode}`)
                .then(res => {
                    if (!res.ok) throw new Error("Jenis botol tidak ditemukan.");
                    return res.json();
                })
                .then(data => {
                    const bottle = data.bottle;
                    
                    // Append item
                    if (scannedItems[bottle.id]) {
                        scannedItems[bottle.id].quantity += 1;
                    } else {
                        scannedItems[bottle.id] = {
                            name: bottle.name,
                            points_value: bottle.points_value,
                            quantity: 1
                        };
                    }

                    renderItems();
                    flashFrame('amber');
                    html5QrcodeScanner.resume();
                })
                .catch(err => {
                    alert(err.message || "Barcode tidak terdaftar.");
                    html5QrcodeScanner.resume();
                });
        }

        function renderItems() {
            scannedItemsList.innerHTML = '';
            let totalPoints = 0;
            let index = 0;

            for (const id in scannedItems) {
                const item = scannedItems[id];
                const pointsEarned = item.quantity * item.points_value;
                totalPoints += pointsEarned;

                const itemRow = document.createElement('div');
                itemRow.className = "py-3 flex items-center justify-between gap-4";
                itemRow.innerHTML = `
                    <div class="flex-1">
                        <p class="text-xs font-semibold text-slate-800">${item.name}</p>
                        <p class="text-[10px] text-slate-400 font-semibold">${item.points_value} Poin/botol</p>
                        
                        <!-- Form hidden inputs -->
                        <input type="hidden" name="items[${index}][bottle_type_id]" value="${id}">
                    </div>
                    <div class="flex items-center gap-3">
                        <div class="flex items-center border border-slate-200 rounded-xl overflow-hidden bg-slate-50">
                            <button type="button" class="px-2.5 py-1 text-slate-500 hover:bg-slate-200 transition-colors font-bold text-sm" onclick="adjustQty(${id}, -1)">-</button>
                            <input type="number" name="items[${index}][quantity]" value="${item.quantity}" readonly class="w-8 text-center bg-transparent border-none text-xs font-bold text-slate-800 focus:outline-none">
                            <button type="button" class="px-2.5 py-1 text-slate-500 hover:bg-slate-200 transition-colors font-bold text-sm" onclick="adjustQty(${id}, 1)">+</button>
                        </div>
                        <div class="text-right w-16">
                            <span class="text-xs font-bold text-slate-800">${pointsEarned} Pts</span>
                        </div>
                    </div>
                `;
                scannedItemsList.appendChild(itemRow);
                index++;
            }

            formTotalPointsText.textContent = totalPoints.toLocaleString('id-ID') + " Pts";
        }

        // Expose to global window scope so buttons can trigger them
        window.adjustQty = function(id, change) {
            if (scannedItems[id]) {
                scannedItems[id].quantity += change;
                if (scannedItems[id].quantity <= 0) {
                    delete scannedItems[id];
                }
                renderItems();
            }
        };

        // Manual Inputs triggers
        btnManualUser.addEventListener('click', () => {
            const val = manualQrInput.value.trim();
            if (val) processUserQR(val);
        });

        btnManualBottle.addEventListener('click', () => {
            const val = manualBarcodeInput.value.trim();
            if (val) processBottleBarcode(val);
        });

        // Reset scanning process
        btnResetScan.addEventListener('click', () => {
            if (confirm("Reset seluruh data pemindaian saat ini?")) {
                currentUserId = null;
                scanStage = 1;
                scannedItems = {};
                
                scanStepTitle.textContent = "Tahap 1: Pindai QR Code Nasabah";
                userDetailsCard.classList.add('hidden');
                scannedItemsCard.classList.add('hidden');
                manualUserSection.classList.remove('hidden');
                manualBottleSection.classList.add('hidden');
                
                formUserIdInput.value = '';
                manualQrInput.value = '';
                manualBarcodeInput.value = '';
                
                html5QrcodeScanner.resume();
            }
        });

        // Flash Frame function for feedback
        function flashFrame(color) {
            const overlay = document.querySelector('.pointer-events-none div');
            overlay.classList.remove('border-emerald-500/50', 'border-amber-500/50');
            overlay.classList.add(`border-${color}-500`);
            setTimeout(() => {
                overlay.classList.remove(`border-${color}-500`);
                overlay.classList.add('border-emerald-500/50');
            }, 500);
        }
    });
</script>
@endsection
