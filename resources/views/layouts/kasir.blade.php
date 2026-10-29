<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Point of Sales') - Kasir Kopi Kutho</title>
    
    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&family=JetBrains+Mono:wght@400;600&display=swap" rel="stylesheet">
    
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    
    <!-- Tailwind CSS Play CDN -->
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    fontFamily: {
                        sans: ['"Plus Jakarta Sans"', 'sans-serif'],
                        mono: ['"JetBrains Mono"', 'monospace'],
                    },
                    colors: {
                        coffee: {
                            50: '#fdf8f4',
                            100: '#f8eee4',
                            200: '#edd5c1',
                            300: '#deb696',
                            400: '#cc9269',
                            500: '#b46e42',
                            600: '#985233',
                            700: '#7a3e29',
                            800: '#54291c',
                            900: '#351710',
                            950: '#1b0b07',
                        }
                    }
                }
            }
        }
    </script>

    <!-- SweetAlert2 -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <style>
        body {
            font-family: 'Plus Jakarta Sans', sans-serif;
            background-color: #f1f5f9;
        }
        .custom-scroll::-webkit-scrollbar {
            width: 6px;
            height: 6px;
        }
        .custom-scroll::-webkit-scrollbar-thumb {
            background-color: #cbd5e1;
            border-radius: 9999px;
        }
        .custom-scroll::-webkit-scrollbar-thumb:hover {
            background-color: #94a3b8;
        }
    </style>
    @stack('styles')
</head>
<body class="text-slate-800 antialiased h-screen flex flex-col overflow-hidden bg-slate-100">

    <!-- POS Top Navigation -->
    <header class="bg-[#1c120c] text-white px-5 py-3 flex items-center justify-between shadow-md z-30 shrink-0">
        <div class="flex items-center gap-4">
            <div class="flex items-center gap-2.5">
                <div class="w-9 h-9 rounded-xl bg-gradient-to-br from-amber-500 to-amber-700 flex items-center justify-center text-white shadow-md shadow-amber-950/40">
                    <i class="fa-solid fa-mug-hot text-base"></i>
                </div>
                <div>
                    <h1 class="text-base font-bold leading-none tracking-tight text-white">KOPI KUTHO POS</h1>
                    <p class="text-[11px] text-amber-400 font-medium">Sistem Kasir Digital Terintegrasi</p>
                </div>
            </div>

            <!-- Navigation Links for Cashier -->
            <div class="hidden sm:flex items-center gap-1.5 ml-4 pl-4 border-l border-white/10">
                <a href="{{ route('kasir.index') }}" class="px-3 py-1.5 rounded-lg text-xs font-medium transition-all {{ request()->routeIs('kasir.index') ? 'bg-amber-600 text-white font-semibold shadow-sm' : 'text-stone-300 hover:bg-white/10 hover:text-white' }}">
                    <i class="fa-solid fa-cash-register mr-1.5"></i>Kasir Baru
                </a>
                <a href="{{ route('kasir.riwayat') }}" class="px-3 py-1.5 rounded-lg text-xs font-medium transition-all {{ request()->routeIs('kasir.riwayat') ? 'bg-amber-600 text-white font-semibold shadow-sm' : 'text-stone-300 hover:bg-white/10 hover:text-white' }}">
                    <i class="fa-solid fa-clock-rotate-left mr-1.5"></i>Riwayat Transaksi
                </a>
            </div>
        </div>

        <div class="flex items-center gap-3">
            <!-- Clock -->
            <div class="hidden md:flex items-center gap-2 text-xs font-mono text-stone-300 bg-white/5 px-3 py-1.5 rounded-lg border border-white/10">
                <i class="fa-regular fa-clock text-amber-400"></i>
                <span id="pos-clock">{{ now()->format('H:i:s') }}</span>
            </div>

            <!-- User Badge -->
            <div class="flex items-center gap-2.5 text-xs text-stone-200">
                <div class="w-8 h-8 rounded-full bg-amber-600 flex items-center justify-center font-bold text-white text-xs">
                    {{ strtoupper(substr(Auth::user()->name ?? 'K', 0, 1)) }}
                </div>
                <div class="hidden lg:block text-left leading-tight">
                    <p class="font-semibold text-white">{{ Auth::user()->name ?? 'Kasir' }}</p>
                    <span class="text-[10px] text-stone-400 uppercase tracking-wider">{{ Auth::user()->role }}</span>
                </div>
            </div>

            <!-- Admin Backlink if Admin -->
            @if(Auth::user()->isAdmin())
                <a href="{{ route('admin.dashboard') }}" class="px-3 py-1.5 rounded-lg text-xs font-medium bg-white/10 hover:bg-white/20 text-stone-200 transition-all flex items-center gap-1.5 border border-white/10">
                    <i class="fa-solid fa-arrow-left"></i>
                    <span class="hidden sm:inline">Panel Admin</span>
                </a>
            @endif

            <!-- Logout Button -->
            <form action="{{ route('logout') }}" method="POST" class="inline">
                @csrf
                <button type="submit" title="Logout" class="w-8 h-8 rounded-lg bg-red-500/20 hover:bg-red-600 text-red-300 hover:text-white flex items-center justify-center transition-all">
                    <i class="fa-solid fa-power-off text-xs"></i>
                </button>
            </form>
        </div>
    </header>

    <!-- Main POS Container -->
    <main class="flex-1 overflow-hidden flex flex-col">
        @yield('content')
    </main>

    <!-- Script Clock -->
    <script>
        setInterval(() => {
            const now = new Date();
            const clockEl = document.getElementById('pos-clock');
            if (clockEl) {
                clockEl.innerText = now.toTimeString().split(' ')[0];
            }
        }, 1000);
    </script>
    @stack('scripts')
</body>
</html>
