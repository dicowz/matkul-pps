<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Struk Transaksi #{{ $transaksi->nomor_transaksi }} - Kopi Kutho</title>
    
    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=JetBrains+Mono:wght@400;500;600;700&family=Plus+Jakarta+Sans:wght@400;600;700&display=swap" rel="stylesheet">
    
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    
    <!-- Tailwind CSS Play CDN -->
    <script src="https://cdn.tailwindcss.com"></script>

    <style>
        body {
            font-family: 'JetBrains Mono', monospace;
            background-color: #f1f5f9;
        }
        @media print {
            body {
                background-color: #ffffff;
                padding: 0;
                margin: 0;
            }
            .no-print {
                display: none !important;
            }
            .thermal-receipt {
                box-shadow: none !important;
                border: none !important;
                width: 100% !important;
                max-width: 80mm !important;
                margin: 0 auto !important;
                padding: 0 !important;
            }
        }
    </style>
</head>
<body class="min-h-screen py-8 px-4 flex flex-col items-center justify-center text-slate-900">

    <!-- Top Action Buttons (No-print) -->
    <div class="no-print max-w-sm w-full mb-4 flex items-center justify-between gap-3">
        <a href="{{ route('kasir.index') }}" class="px-4 py-2 rounded-xl bg-white border border-slate-300 hover:bg-slate-50 text-slate-700 text-xs font-semibold flex items-center gap-2 shadow-sm transition-all font-sans">
            <i class="fa-solid fa-arrow-left"></i>
            <span>Kembali ke Kasir</span>
        </a>
        <button onclick="window.print()" class="px-5 py-2 rounded-xl bg-amber-600 hover:bg-amber-700 text-white text-xs font-bold flex items-center gap-2 shadow-sm transition-all font-sans">
            <i class="fa-solid fa-print"></i>
            <span>Cetak Struk (F12)</span>
        </button>
    </div>

    <!-- Thermal Receipt Card -->
    <div class="thermal-receipt bg-white w-full max-w-sm p-6 rounded-2xl border border-slate-200 shadow-xl space-y-4 text-xs leading-relaxed">
        
        <!-- Header -->
        <div class="text-center space-y-1">
            <h1 class="text-base font-bold uppercase tracking-wider text-slate-900">CAFE RESTO KOPI KUTHO</h1>
            <p class="text-[11px] text-slate-600">Jl. Semolowaru No. 45, Surabaya</p>
            <p class="text-[11px] text-slate-600">WhatsApp: 0812-3456-7890</p>
        </div>

        <!-- Divider -->
        <div class="border-b-2 border-dashed border-slate-300"></div>

        <!-- Transaction Meta -->
        <div class="space-y-1 text-[11px] text-slate-600">
            <div class="flex justify-between">
                <span>No. Transaksi :</span>
                <span class="font-bold text-slate-900 font-mono">{{ $transaksi->nomor_transaksi ?? 'TRK-'.$transaksi->id }}</span>
            </div>
            <div class="flex justify-between">
                <span>Waktu / Tgl   :</span>
                <span class="text-slate-900">{{ $transaksi->tanggal ? $transaksi->tanggal->format('d/m/Y H:i:s') : '-' }}</span>
            </div>
            <div class="flex justify-between">
                <span>Kasir Bertugas:</span>
                <span class="text-slate-900">{{ $transaksi->user->name ?? 'Kasir' }}</span>
            </div>
        </div>

        <!-- Divider -->
        <div class="border-b-2 border-dashed border-slate-300"></div>

        <!-- Order Items -->
        <div class="space-y-2.5">
            @foreach($transaksi->detailTransaksi as $item)
                <div>
                    <div class="flex justify-between items-start">
                        <span class="font-bold text-slate-900">{{ $item->menu->nama_menu ?? 'Item' }}</span>
                        <span class="font-bold font-mono">Rp {{ number_format($item->subtotal, 0, ',', '.') }}</span>
                    </div>
                    <div class="text-[11px] text-slate-500 font-mono pl-2">
                        {{ $item->jumlah }} x Rp {{ number_format($item->harga, 0, ',', '.') }}
                    </div>
                </div>
            @endforeach
        </div>

        <!-- Divider -->
        <div class="border-b-2 border-dashed border-slate-300"></div>

        <!-- Totals & Payment Details -->
        <div class="space-y-1.5 text-xs">
            <div class="flex justify-between text-slate-600">
                <span>Total Item</span>
                <span class="font-bold font-mono">{{ $transaksi->detailTransaksi->sum('jumlah') }} Porsi</span>
            </div>
            <div class="flex justify-between font-bold text-sm text-slate-900 pt-1">
                <span>TOTAL BELANJA</span>
                <span class="font-mono text-base">Rp {{ number_format($transaksi->total, 0, ',', '.') }}</span>
            </div>
            <div class="flex justify-between text-[11px] text-slate-700">
                <span>Metode Pembayaran</span>
                <span class="uppercase font-bold">{{ $transaksi->metode_pembayaran }}</span>
            </div>

            @if($transaksi->metode_pembayaran === 'tunai')
                <div class="flex justify-between text-[11px] text-slate-600">
                    <span>Uang Diterima</span>
                    <span class="font-mono">Rp {{ number_format($transaksi->uang_diterima ?? $transaksi->total, 0, ',', '.') }}</span>
                </div>
                <div class="flex justify-between text-[11px] font-bold text-slate-900">
                    <span>Uang Kembalian</span>
                    <span class="font-mono">Rp {{ number_format($transaksi->kembalian ?? 0, 0, ',', '.') }}</span>
                </div>
            @elseif($transaksi->metode_pembayaran === 'qris')
                <div class="flex justify-between text-[11px] text-slate-600">
                    <span>Status QRIS</span>
                    <span class="font-semibold text-emerald-700">LUNAS / VERIFIED</span>
                </div>
            @else
                <div class="flex justify-between text-[11px] text-slate-600">
                    <span>Status Debit</span>
                    <span class="font-semibold text-emerald-700">TEROTORISASI EDC</span>
                </div>
            @endif
        </div>

        <!-- Divider -->
        <div class="border-b-2 border-dashed border-slate-300"></div>

        <!-- Footer Receipt Notes -->
        <div class="text-center space-y-1 text-[11px] text-slate-500 pt-1">
            <p class="font-bold text-slate-700">TERIMA KASIH ATAS KUNJUNGAN ANDA</p>
            <p>Nikmati Kopi Terbaik Anda di Kopi Kutho</p>
            <p class="text-[10px] text-slate-400">Instagram: @kopikutho &bull; Free Wi-Fi</p>
        </div>
    </div>

    <!-- Print Shortcut Trigger -->
    <script>
        // Automatic print prompt if opened with ?autoprint=1
        if (window.location.search.includes('autoprint=1')) {
            window.addEventListener('load', () => {
                window.print();
            });
        }
    </script>
</body>
</html>
