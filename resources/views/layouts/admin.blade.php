<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Admin Dashboard') - Cafe Resto Kopi Kutho</title>
    
    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    
    <!-- Tailwind CSS Play CDN with custom theme config -->
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    fontFamily: {
                        sans: ['"Plus Jakarta Sans"', 'sans-serif'],
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
                        },
                        amberbrand: {
                            50: '#fffbeb',
                            100: '#fef3c7',
                            200: '#fde68a',
                            300: '#fcd34d',
                            400: '#fbbf24',
                            500: '#f59e0b',
                            600: '#d97706',
                            700: '#b45309',
                            800: '#92400e',
                            900: '#78350f',
                        }
                    }
                }
            }
        }
    </script>

    <!-- Chart.js & SweetAlert2 -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <style>
        body {
            font-family: 'Plus Jakarta Sans', sans-serif;
            background-color: #f8fafc;
        }
        .sidebar-scroll::-webkit-scrollbar {
            width: 5px;
        }
        .sidebar-scroll::-webkit-scrollbar-thumb {
            background-color: rgba(255, 255, 255, 0.15);
            border-radius: 9999px;
        }
        @media print {
            .no-print {
                display: none !important;
            }
        }
    </style>
    @stack('styles')
</head>
<body class="text-slate-800 antialiased min-h-screen flex flex-col md:flex-row bg-[#f9fafb]">

    <!-- Sidebar Mobile Overlay -->
    <div id="mobile-sidebar-backdrop" onclick="toggleSidebar()" class="fixed inset-0 bg-black/50 z-40 hidden md:hidden"></div>

    <!-- Sidebar Navigation -->
    <aside id="main-sidebar" class="fixed inset-y-0 left-0 z-50 w-72 bg-[#1c120c] text-white flex flex-col transition-transform duration-300 transform -translate-x-full md:translate-x-0 shadow-2xl">
        <!-- Logo & Brand Header -->
        <div class="px-6 py-5 border-b border-white/10 flex items-center justify-between">
            <div class="flex items-center gap-3">
                <div class="w-11 h-11 rounded-xl bg-gradient-to-br from-amber-500 to-amber-700 flex items-center justify-center text-white shadow-lg shadow-amber-900/30">
                    <i class="fa-solid fa-mug-hot text-xl"></i>
                </div>
                <div>
                    <h1 class="text-lg font-bold tracking-tight text-white leading-tight">Kopi Kutho</h1>
                    <p class="text-xs text-amber-400 font-medium">Sistem Kasir & IoT Absensi</p>
                </div>
            </div>
            <button onclick="toggleSidebar()" class="md:hidden text-stone-400 hover:text-white">
                <i class="fa-solid fa-xmark text-lg"></i>
            </button>
        </div>

        <!-- Sidebar Navigation Menu -->
        <div class="flex-1 overflow-y-auto px-4 py-4 sidebar-scroll space-y-6">
            <!-- Nav Group: Utama -->
            <div>
                <p class="px-3 text-[11px] font-bold uppercase tracking-wider text-stone-400 mb-2">Menu Utama</p>
                <div class="space-y-1">
                    <a href="{{ route('admin.dashboard') }}" class="flex items-center gap-3 px-3.5 py-2.5 rounded-xl text-sm font-medium transition-all {{ request()->routeIs('admin.dashboard') ? 'bg-amber-600 text-white shadow-md shadow-amber-950/40' : 'text-stone-300 hover:bg-white/5 hover:text-white' }}">
                        <i class="fa-solid fa-chart-pie w-5 text-center text-amber-400"></i>
                        <span>Dashboard</span>
                    </a>
                    <a href="{{ route('kasir.index') }}" target="_blank" class="flex items-center justify-between px-3.5 py-2.5 rounded-xl text-sm font-medium text-amber-300 bg-amber-500/15 hover:bg-amber-500/25 border border-amber-500/30 transition-all">
                        <div class="flex items-center gap-3">
                            <i class="fa-solid fa-cash-register w-5 text-center text-amber-400"></i>
                            <span>Buka Layar Kasir</span>
                        </div>
                        <i class="fa-solid fa-arrow-up-right-from-square text-xs"></i>
                    </a>
                </div>
            </div>

            <!-- Nav Group: Master Data -->
            <div>
                <p class="px-3 text-[11px] font-bold uppercase tracking-wider text-stone-400 mb-2">Master Data</p>
                <div class="space-y-1">
                    <a href="{{ route('admin.menu.index') }}" class="flex items-center gap-3 px-3.5 py-2.5 rounded-xl text-sm font-medium transition-all {{ request()->routeIs('admin.menu.*') ? 'bg-amber-600 text-white shadow-md' : 'text-stone-300 hover:bg-white/5 hover:text-white' }}">
                        <i class="fa-solid fa-utensils w-5 text-center text-amber-400"></i>
                        <span>Manajemen Menu</span>
                    </a>
                    <a href="{{ route('admin.kategori.index') }}" class="flex items-center gap-3 px-3.5 py-2.5 rounded-xl text-sm font-medium transition-all {{ request()->routeIs('admin.kategori.*') ? 'bg-amber-600 text-white shadow-md' : 'text-stone-300 hover:bg-white/5 hover:text-white' }}">
                        <i class="fa-solid fa-tags w-5 text-center text-amber-400"></i>
                        <span>Kategori Menu</span>
                    </a>
                    <a href="{{ route('admin.karyawan.index') }}" class="flex items-center gap-3 px-3.5 py-2.5 rounded-xl text-sm font-medium transition-all {{ request()->routeIs('admin.karyawan.*') ? 'bg-amber-600 text-white shadow-md' : 'text-stone-300 hover:bg-white/5 hover:text-white' }}">
                        <i class="fa-solid fa-users-gear w-5 text-center text-amber-400"></i>
                        <span>Data Karyawan</span>
                    </a>
                </div>
            </div>

            <!-- Nav Group: Integrasi IoT & AI -->
            <div>
                <p class="px-3 text-[11px] font-bold uppercase tracking-wider text-stone-400 mb-2">Integrasi IoT & AI</p>
                <div class="space-y-1">
                    <a href="{{ route('admin.absensi.index') }}" class="flex items-center gap-3 px-3.5 py-2.5 rounded-xl text-sm font-medium transition-all {{ request()->routeIs('admin.absensi.*') ? 'bg-amber-600 text-white shadow-md' : 'text-stone-300 hover:bg-white/5 hover:text-white' }}">
                        <i class="fa-solid fa-fingerprint w-5 text-center text-amber-400"></i>
                        <span>Absensi Fingerprint</span>
                    </a>
                    <a href="{{ route('admin.chatbot.index') }}" class="flex items-center gap-3 px-3.5 py-2.5 rounded-xl text-sm font-medium transition-all {{ request()->routeIs('admin.chatbot.*') ? 'bg-amber-600 text-white shadow-md' : 'text-stone-300 hover:bg-white/5 hover:text-white' }}">
                        <i class="fa-brands fa-whatsapp w-5 text-center text-emerald-400"></i>
                        <span>AI WhatsApp Chatbot</span>
                    </a>
                </div>
            </div>

            <!-- Nav Group: Laporan -->
            <div>
                <p class="px-3 text-[11px] font-bold uppercase tracking-wider text-stone-400 mb-2">Laporan Terintegrasi</p>
                <div class="space-y-1">
                    <a href="{{ route('admin.laporan.penjualan') }}" class="flex items-center gap-3 px-3.5 py-2.5 rounded-xl text-sm font-medium transition-all {{ request()->routeIs('admin.laporan.penjualan') ? 'bg-amber-600 text-white shadow-md' : 'text-stone-300 hover:bg-white/5 hover:text-white' }}">
                        <i class="fa-solid fa-file-invoice-dollar w-5 text-center text-amber-400"></i>
                        <span>Laporan Penjualan</span>
                    </a>
                    <a href="{{ route('admin.laporan.menu_terjual') }}" class="flex items-center gap-3 px-3.5 py-2.5 rounded-xl text-sm font-medium transition-all {{ request()->routeIs('admin.laporan.menu_terjual') ? 'bg-amber-600 text-white shadow-md' : 'text-stone-300 hover:bg-white/5 hover:text-white' }}">
                        <i class="fa-solid fa-fire w-5 text-center text-amber-400"></i>
                        <span>Laporan Menu Terjual</span>
                    </a>
                    <a href="{{ route('admin.laporan.absensi') }}" class="flex items-center gap-3 px-3.5 py-2.5 rounded-xl text-sm font-medium transition-all {{ request()->routeIs('admin.laporan.absensi') ? 'bg-amber-600 text-white shadow-md' : 'text-stone-300 hover:bg-white/5 hover:text-white' }}">
                        <i class="fa-solid fa-clipboard-user w-5 text-center text-amber-400"></i>
                        <span>Rekap Absensi Karyawan</span>
                    </a>
                </div>
            </div>
        </div>

        <!-- User Profile & Logout Bottom Bar -->
        <div class="p-4 border-t border-white/10 bg-[#160e09]">
            <div class="flex items-center justify-between">
                <div class="flex items-center gap-3">
                    <div class="w-9 h-9 rounded-full bg-amber-600/30 text-amber-400 border border-amber-500/30 flex items-center justify-center font-bold text-sm">
                        {{ strtoupper(substr(Auth::user()->name ?? 'A', 0, 1)) }}
                    </div>
                    <div class="overflow-hidden">
                        <p class="text-xs font-semibold text-white truncate max-w-[120px]">{{ Auth::user()->name ?? 'Admin' }}</p>
                        <span class="inline-block px-1.5 py-0.5 text-[10px] bg-amber-500/20 text-amber-300 rounded font-medium">Administrator</span>
                    </div>
                </div>
                <form action="{{ route('logout') }}" method="POST">
                    @csrf
                    <button type="submit" title="Logout" class="w-8 h-8 rounded-lg bg-red-500/10 text-red-400 hover:bg-red-500 hover:text-white transition-all flex items-center justify-center">
                        <i class="fa-solid fa-arrow-right-from-bracket text-xs"></i>
                    </button>
                </form>
            </div>
        </div>
    </aside>

    <!-- Main Content Area -->
    <div class="flex-1 md:ml-72 flex flex-col min-h-screen">
        <!-- Topbar Header -->
        <header class="bg-white/90 backdrop-blur-md sticky top-0 z-30 border-b border-slate-200/80 px-6 py-3.5 flex items-center justify-between">
            <div class="flex items-center gap-4">
                <button onclick="toggleSidebar()" class="md:hidden text-slate-600 hover:text-slate-900 p-1">
                    <i class="fa-solid fa-bars text-lg"></i>
                </button>
                <div>
                    <h2 class="text-lg font-bold text-slate-900">@yield('page_title', 'Dashboard')</h2>
                    <p class="text-xs text-slate-500 hidden sm:block">Cafe Resto Kopi Kutho &bull; Surabaya, Jawa Timur</p>
                </div>
            </div>

            <div class="flex items-center gap-3">
                <div class="hidden sm:flex items-center gap-2 text-xs text-slate-500 bg-slate-100 px-3 py-1.5 rounded-lg border border-slate-200">
                    <i class="fa-solid fa-calendar-day text-amber-600"></i>
                    <span id="current-clock">{{ now()->translatedFormat('l, d F Y - H:i') }} WIB</span>
                </div>
                <a href="{{ route('kasir.index') }}" class="px-3.5 py-1.5 bg-amber-600 hover:bg-amber-700 text-white rounded-lg text-xs font-medium flex items-center gap-2 shadow-sm transition-all">
                    <i class="fa-solid fa-cash-register"></i>
                    <span class="hidden sm:inline">Buka Kasir</span>
                </a>
            </div>
        </header>

        <!-- Flash Notifications -->
        <div class="px-6 pt-4">
            @if(session('success'))
                <div class="bg-emerald-50 border border-emerald-200 text-emerald-800 px-4 py-3 rounded-xl flex items-center justify-between shadow-sm animate-fade-in mb-4">
                    <div class="flex items-center gap-3">
                        <i class="fa-solid fa-circle-check text-emerald-600 text-lg"></i>
                        <span class="text-sm font-medium">{{ session('success') }}</span>
                    </div>
                    <button onclick="this.parentElement.remove()" class="text-emerald-500 hover:text-emerald-800"><i class="fa-solid fa-xmark"></i></button>
                </div>
            @endif

            @if(session('error'))
                <div class="bg-rose-50 border border-rose-200 text-rose-800 px-4 py-3 rounded-xl flex items-center justify-between shadow-sm animate-fade-in mb-4">
                    <div class="flex items-center gap-3">
                        <i class="fa-solid fa-circle-exclamation text-rose-600 text-lg"></i>
                        <span class="text-sm font-medium">{{ session('error') }}</span>
                    </div>
                    <button onclick="this.parentElement.remove()" class="text-rose-500 hover:text-rose-800"><i class="fa-solid fa-xmark"></i></button>
                </div>
            @endif

            @if(session('warning'))
                <div class="bg-amber-50 border border-amber-200 text-amber-800 px-4 py-3 rounded-xl flex items-center justify-between shadow-sm animate-fade-in mb-4">
                    <div class="flex items-center gap-3">
                        <i class="fa-solid fa-triangle-exclamation text-amber-600 text-lg"></i>
                        <span class="text-sm font-medium">{{ session('warning') }}</span>
                    </div>
                    <button onclick="this.parentElement.remove()" class="text-amber-500 hover:text-amber-800"><i class="fa-solid fa-xmark"></i></button>
                </div>
            @endif

            @if ($errors->any())
                <div class="bg-rose-50 border border-rose-200 text-rose-800 px-4 py-3 rounded-xl shadow-sm mb-4">
                    <div class="flex items-center gap-2 font-semibold text-sm mb-1">
                        <i class="fa-solid fa-circle-exclamation text-rose-600"></i>
                        <span>Mohon periksa kesalahan input berikut:</span>
                    </div>
                    <ul class="list-disc list-inside text-xs space-y-0.5 text-rose-700 ml-4">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif
        </div>

        <!-- Page Content -->
        <main class="flex-1 px-6 py-4">
            @yield('content')
        </main>

        <!-- Footer -->
        <footer class="px-6 py-4 border-t border-slate-200 bg-white text-xs text-slate-500 flex flex-col sm:flex-row items-center justify-between gap-2">
            <div>
                &copy; {{ date('Y') }} <strong>Cafe Resto Kopi Kutho</strong> &bull; Sistem Kasir, Absensi ESP32 & AI WhatsApp Chatbot
            </div>
            <div class="text-slate-400">
                Kelompok 4 Teknik Informatika Universitas 17 Agustus 1945 Surabaya
            </div>
        </footer>
    </div>

    <!-- Scripts -->
    <script>
        function toggleSidebar() {
            const sidebar = document.getElementById('main-sidebar');
            const backdrop = document.getElementById('mobile-sidebar-backdrop');
            sidebar.classList.toggle('-translate-x-full');
            backdrop.classList.toggle('hidden');
        }

        // Live Clock
        setInterval(() => {
            const now = new Date();
            const options = { weekday: 'long', year: 'numeric', month: 'long', day: 'numeric', hour: '2-digit', minute: '2-digit', second: '2-digit' };
            const clockEl = document.getElementById('current-clock');
            if (clockEl) {
                clockEl.innerText = now.toLocaleDateString('id-ID', options) + ' WIB';
            }
        }, 1000);
    </script>
    @stack('scripts')
</body>
</html>
