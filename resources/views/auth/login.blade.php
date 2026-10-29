<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Cafe Resto Kopi Kutho</title>
    
    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    
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
                    },
                    colors: {
                        coffee: {
                            800: '#54291c',
                            900: '#351710',
                            950: '#1b0b07',
                        }
                    }
                }
            }
        }
    </script>
</head>
<body class="min-h-screen bg-[#140b07] flex items-center justify-center p-4 relative overflow-hidden font-sans">

    <!-- Ambient Glow Background -->
    <div class="absolute -top-40 -right-40 w-96 h-96 bg-amber-600/20 rounded-full blur-3xl pointer-events-none"></div>
    <div class="absolute -bottom-40 -left-40 w-96 h-96 bg-amber-800/20 rounded-full blur-3xl pointer-events-none"></div>

    <div class="w-full max-w-md relative z-10">
        <!-- Logo & Header -->
        <div class="text-center mb-8">
            <div class="inline-flex items-center justify-center w-16 h-16 rounded-2xl bg-gradient-to-tr from-amber-600 to-amber-400 text-white shadow-xl shadow-amber-950/60 mb-4 border border-amber-300/30">
                <i class="fa-solid fa-mug-hot text-2xl"></i>
            </div>
            <h1 class="text-2xl font-bold text-white tracking-tight">Cafe Resto Kopi Kutho</h1>
            <p class="text-sm text-stone-400 mt-1">Sistem Informasi Kasir, Absensi & AI Chatbot</p>
        </div>

        <!-- Login Card -->
        <div class="bg-[#1f140e]/90 backdrop-blur-xl border border-white/10 rounded-3xl p-8 shadow-2xl">
            <!-- Flash Errors -->
            @if(session('error'))
                <div class="bg-rose-500/10 border border-rose-500/30 text-rose-300 px-4 py-3 rounded-xl text-xs flex items-center gap-2 mb-6">
                    <i class="fa-solid fa-circle-exclamation text-rose-400 text-sm"></i>
                    <span>{{ session('error') }}</span>
                </div>
            @endif

            @if(session('success'))
                <div class="bg-emerald-500/10 border border-emerald-500/30 text-emerald-300 px-4 py-3 rounded-xl text-xs flex items-center gap-2 mb-6">
                    <i class="fa-solid fa-circle-check text-emerald-400 text-sm"></i>
                    <span>{{ session('success') }}</span>
                </div>
            @endif

            <form action="{{ route('login.post') }}" method="POST" class="space-y-5">
                @csrf

                <!-- Username Input -->
                <div>
                    <label for="username" class="block text-xs font-semibold text-stone-300 uppercase tracking-wider mb-2">Username</label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-stone-400">
                            <i class="fa-solid fa-user text-sm"></i>
                        </div>
                        <input type="text" id="username" name="username" value="{{ old('username') }}" required autofocus
                            placeholder="Masukkan username Anda"
                            class="w-full pl-10 pr-4 py-3 bg-white/5 border {{ $errors->has('username') ? 'border-rose-500/80' : 'border-white/10' }} rounded-xl text-white placeholder-stone-500 text-sm focus:outline-none focus:ring-2 focus:ring-amber-500 focus:border-transparent transition-all">
                    </div>
                    @error('username')
                        <p class="text-rose-400 text-xs mt-1.5 flex items-center gap-1">
                            <i class="fa-solid fa-circle-xmark text-[11px]"></i> {{ $message }}
                        </p>
                    @enderror
                </div>

                <!-- Password Input -->
                <div>
                    <label for="password" class="block text-xs font-semibold text-stone-300 uppercase tracking-wider mb-2">Password</label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-stone-400">
                            <i class="fa-solid fa-lock text-sm"></i>
                        </div>
                        <input type="password" id="password" name="password" required
                            placeholder="Masukkan password Anda"
                            class="w-full pl-10 pr-10 py-3 bg-white/5 border {{ $errors->has('password') ? 'border-rose-500/80' : 'border-white/10' }} rounded-xl text-white placeholder-stone-500 text-sm focus:outline-none focus:ring-2 focus:ring-amber-500 focus:border-transparent transition-all">
                        <button type="button" onclick="togglePasswordVisibility()" class="absolute inset-y-0 right-0 pr-3.5 flex items-center text-stone-400 hover:text-white">
                            <i class="fa-regular fa-eye" id="toggle-eye"></i>
                        </button>
                    </div>
                    @error('password')
                        <p class="text-rose-400 text-xs mt-1.5 flex items-center gap-1">
                            <i class="fa-solid fa-circle-xmark text-[11px]"></i> {{ $message }}
                        </p>
                    @enderror
                </div>

                <!-- Remember Me -->
                <div class="flex items-center justify-between text-xs text-stone-400">
                    <label class="flex items-center gap-2 cursor-pointer">
                        <input type="checkbox" name="remember" class="w-4 h-4 rounded bg-white/10 border-white/20 text-amber-500 focus:ring-amber-500 focus:ring-offset-0">
                        <span>Ingat saya di perangkat ini</span>
                    </label>
                </div>

                <!-- Submit Button -->
                <button type="submit" class="w-full py-3.5 px-4 bg-gradient-to-r from-amber-600 to-amber-500 hover:from-amber-500 hover:to-amber-400 text-white font-semibold rounded-xl text-sm shadow-lg shadow-amber-950/40 hover:shadow-amber-900/50 transition-all flex items-center justify-center gap-2">
                    <i class="fa-solid fa-right-to-bracket text-sm"></i>
                    <span>Masuk ke Sistem</span>
                </button>
            </form>

            <!-- Demo Quick Fill Buttons -->
            <div class="mt-8 pt-6 border-t border-white/10">
                <p class="text-[11px] font-semibold uppercase tracking-wider text-stone-400 text-center mb-3">Login Cepat Akun Demo</p>
                <div class="grid grid-cols-2 gap-2.5">
                    <button type="button" onclick="fillCredentials('admin', 'password')" class="px-3 py-2 bg-white/5 hover:bg-white/10 border border-white/10 rounded-xl text-xs text-stone-300 hover:text-white flex items-center justify-center gap-2 transition-all">
                        <i class="fa-solid fa-shield-halved text-amber-400"></i>
                        <span>Admin</span>
                    </button>
                    <button type="button" onclick="fillCredentials('kasir', 'password')" class="px-3 py-2 bg-white/5 hover:bg-white/10 border border-white/10 rounded-xl text-xs text-stone-300 hover:text-white flex items-center justify-center gap-2 transition-all">
                        <i class="fa-solid fa-cash-register text-emerald-400"></i>
                        <span>Kasir</span>
                    </button>
                </div>
            </div>
        </div>

        <!-- System Footnote -->
        <p class="text-center text-xs text-stone-500 mt-6">
            Cafe Resto Kopi Kutho &bull; Terhubung IoT ESP32 & AI Engine
        </p>
    </div>

    <script>
        function togglePasswordVisibility() {
            const pwdInput = document.getElementById('password');
            const eyeIcon = document.getElementById('toggle-eye');
            if (pwdInput.type === 'password') {
                pwdInput.type = 'text';
                eyeIcon.classList.remove('fa-eye');
                eyeIcon.classList.add('fa-eye-slash');
            } else {
                pwdInput.type = 'password';
                eyeIcon.classList.remove('fa-eye-slash');
                eyeIcon.classList.add('fa-eye');
            }
        }

        function fillCredentials(username, password) {
            document.getElementById('username').value = username;
            document.getElementById('password').value = password;
        }
    </script>
</body>
</html>
