@extends('layouts.admin')

@section('title', 'AI WhatsApp Chatbot')
@section('page_title', 'AI WhatsApp Chatbot & Monitoring')

@section('content')
<div class="space-y-6">

    <!-- WhatsApp Web Interactive Chat Simulator & AI Info -->
    <div class="grid grid-cols-1 lg:grid-cols-12 gap-6">
        <!-- Simulator WhatsApp Web UI (7 cols) -->
        <div class="lg:col-span-7 bg-white rounded-3xl border border-slate-200/80 shadow-sm overflow-hidden flex flex-col h-[560px]">
            <!-- WhatsApp Chat Header -->
            <div class="bg-[#005c4b] text-white px-5 py-3.5 flex items-center justify-between shrink-0">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-full bg-emerald-700 border border-white/20 flex items-center justify-center text-white text-lg">
                        <i class="fa-solid fa-mug-hot"></i>
                    </div>
                    <div>
                        <h4 class="font-bold text-sm leading-tight">Asisten Kopi Kutho</h4>
                        <p class="text-[11px] text-emerald-200 flex items-center gap-1.5">
                            <span class="w-2 h-2 rounded-full bg-emerald-400"></span> Online &bull; Terhubung ke Database
                        </p>
                    </div>
                </div>

                <div class="flex items-center gap-3 text-emerald-100 text-xs">
                    <span class="bg-emerald-800/60 px-2.5 py-1 rounded-full text-[10px] font-mono">
                        Webhook Active
                    </span>
                </div>
            </div>

            <!-- WhatsApp Chat Messages Area -->
            <div id="chat-messages" class="flex-1 p-4 overflow-y-auto space-y-3 bg-[#efeae2] custom-scroll" style="background-image: radial-gradient(#d1d7db 1px, transparent 1px); background-size: 16px 16px;">
                <!-- Initial Bot Message -->
                <div class="flex items-start gap-2 max-w-[85%]">
                    <div class="bg-white p-3 rounded-2xl rounded-tl-none shadow-sm text-xs text-slate-800 space-y-1">
                        <p class="font-bold text-emerald-800 text-[11px]">Asisten Virtual Kopi Kutho</p>
                        <p class="leading-relaxed">Halo kak! Selamat datang di layanan WhatsApp Cafe Resto Kopi Kutho. Ada yang bisa saya bantu terkait daftar menu, harga, ketersediaan, atau jam operasional kami?</p>
                        <p class="text-[10px] text-slate-400 text-right">{{ now()->format('H:i') }}</p>
                    </div>
                </div>
            </div>

            <!-- Quick Suggestion Chips -->
            <div class="px-3 py-2 bg-slate-100 border-t border-slate-200 overflow-x-auto flex items-center gap-1.5 shrink-0 whitespace-nowrap custom-scroll">
                <span class="text-[10px] font-bold text-slate-400 uppercase tracking-wider shrink-0 mr-1">Pertanyaan Cepat:</span>
                <button type="button" onclick="sendQuickMessage('Menu makanan yang ada apa saja?')" class="px-2.5 py-1 rounded-full bg-white border border-slate-300 text-[11px] text-slate-700 hover:bg-emerald-50 hover:border-emerald-300 transition-all">
                    Menu Makanan?
                </button>
                <button type="button" onclick="sendQuickMessage('Ada makanan di bawah 20 ribu?')" class="px-2.5 py-1 rounded-full bg-white border border-slate-300 text-[11px] text-slate-700 hover:bg-emerald-50 hover:border-emerald-300 transition-all">
                    Makanan &lt; 20 Ribu?
                </button>
                <button type="button" onclick="sendQuickMessage('Rekomendasi menu kopi spesial')" class="px-2.5 py-1 rounded-full bg-white border border-slate-300 text-[11px] text-slate-700 hover:bg-emerald-50 hover:border-emerald-300 transition-all">
                    Menu Kopi?
                </button>
                <button type="button" onclick="sendQuickMessage('Apakah Kopi Kutho hari ini buka jam berapa?')" class="px-2.5 py-1 rounded-full bg-white border border-slate-300 text-[11px] text-slate-700 hover:bg-emerald-50 hover:border-emerald-300 transition-all">
                    Jam Operasional?
                </button>
                <button type="button" onclick="sendQuickMessage('Lemon Mojito ready gak kak?')" class="px-2.5 py-1 rounded-full bg-white border border-slate-300 text-[11px] text-slate-700 hover:bg-emerald-50 hover:border-emerald-300 transition-all">
                    Cek Menu Kosong
                </button>
            </div>

            <!-- Chat Input Footer -->
            <form id="chat-form" onsubmit="submitChatMessage(event)" class="p-3 bg-[#f0f2f5] border-t border-slate-200 flex items-center gap-2 shrink-0">
                <input type="text" id="chat-input" placeholder="Ketik pesan pelanggan (contoh: harga americano, ada wifi?)..." required
                    class="flex-1 px-4 py-2.5 bg-white border border-slate-300 rounded-full text-xs text-slate-800 placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-emerald-500">
                <button type="submit" id="btn-chat-send" class="w-10 h-10 rounded-full bg-[#00a884] hover:bg-[#008f6f] text-white flex items-center justify-center transition-all shadow-sm shrink-0">
                    <i class="fa-solid fa-paper-plane text-xs"></i>
                </button>
            </form>
        </div>

        <!-- Chatbot Details & Engine Architecture (5 cols) -->
        <div class="lg:col-span-5 space-y-5">
            <!-- Info Card -->
            <div class="bg-white rounded-3xl border border-slate-200/80 shadow-sm p-6 space-y-4">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-2xl bg-emerald-50 text-emerald-600 flex items-center justify-center text-lg">
                        <i class="fa-brands fa-whatsapp"></i>
                    </div>
                    <div>
                        <h4 class="font-bold text-slate-900 text-sm">Integrasi AI WhatsApp Chatbot</h4>
                        <p class="text-xs text-slate-500">Arsitektur NLP + Real-time Database Query</p>
                    </div>
                </div>

                <p class="text-xs text-slate-600 leading-relaxed">
                    Sistem chatbot menggunakan integrasi backend cerdas yang membaca data langsung dari tabel <code class="bg-slate-100 px-1.5 py-0.5 rounded text-amber-800 font-mono text-[11px]">menu</code> dan <code class="bg-slate-100 px-1.5 py-0.5 rounded text-amber-800 font-mono text-[11px]">kategori</code>. Perubahan harga atau ketersediaan stok di admin otomatis tercermin pada respon chatbot.
                </p>

                <div class="space-y-2.5 pt-2 border-t border-slate-100 text-xs">
                    <div class="flex items-center justify-between text-slate-600">
                        <span>Status AI Engine:</span>
                        <span class="font-bold text-emerald-600 flex items-center gap-1.5">
                            <span class="w-2 h-2 rounded-full bg-emerald-500"></span> Aktif & Terhubung
                        </span>
                    </div>
                    <div class="flex items-center justify-between text-slate-600">
                        <span>Privasi Nomor HP (NF-03):</span>
                        <span class="font-medium text-slate-700 bg-slate-100 px-2 py-0.5 rounded">Tersamarkan (0812****890)</span>
                    </div>
                    <div class="flex items-center justify-between text-slate-600">
                        <span>Anti-Halusinasi (NF-06):</span>
                        <span class="font-medium text-slate-700 bg-slate-100 px-2 py-0.5 rounded">Hanya Data Terverifikasi</span>
                    </div>
                    <div class="flex items-center justify-between text-slate-600">
                        <span>Total Riwayat Chat:</span>
                        <span class="font-bold text-amber-600">{{ $totalLogs }} Percakapan</span>
                    </div>
                </div>

                <!-- API Endpoint Spec -->
                <div class="bg-slate-50 p-3.5 rounded-2xl border border-slate-200/80 text-[11px] font-mono text-slate-700 space-y-1">
                    <p class="font-bold text-slate-900 text-[11px]">Webhook Endpoint:</p>
                    <p class="text-emerald-700 font-bold">POST /api/chatbot/webhook</p>
                    <p class="text-slate-500 text-[10px]">Body: { "nomor_whatsapp": "...", "pesan": "..." }</p>
                </div>
            </div>

            <!-- Quick Action Card -->
            <div class="bg-amber-50/70 border border-amber-200/60 rounded-3xl p-5 flex items-center justify-between">
                <div>
                    <h5 class="font-bold text-amber-950 text-xs">Bersihkan Log Percakapan</h5>
                    <p class="text-[11px] text-amber-800/80">Hapus seluruh riwayat chat untuk pengujian ulang</p>
                </div>
                <form action="{{ route('admin.chatbot.clear') }}" method="POST" onsubmit="return confirmClearLogs(event)">
                    @csrf
                    <button type="submit" class="px-3 py-1.5 bg-amber-600 hover:bg-amber-700 text-white rounded-xl text-xs font-semibold shadow-sm transition-all">
                        Reset Log
                    </button>
                </form>
            </div>
        </div>
    </div>

    <!-- Chatbot Logs Table Section -->
    <div class="bg-white rounded-2xl border border-slate-200/80 shadow-sm p-6 space-y-4">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 pb-4 border-b border-slate-100">
            <div>
                <h3 class="font-bold text-slate-900 text-base">Riwayat Log Percakapan Chatbot</h3>
                <p class="text-xs text-slate-500">Daftar pertanyaan pelanggan dan jawaban otomatis yang dihasilkan AI</p>
            </div>

            <!-- Search Form -->
            <form method="GET" action="{{ route('admin.chatbot.index') }}" class="flex items-center gap-2">
                <div class="relative">
                    <input type="text" name="search" value="{{ $search }}" placeholder="Cari pesan atau nomor..."
                        class="pl-8 pr-3 py-1.5 bg-slate-50 border border-slate-200 rounded-xl text-xs text-slate-800 placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-amber-500">
                    <i class="fa-solid fa-magnifying-glass absolute left-2.5 top-2.5 text-xs text-slate-400"></i>
                </div>
                <button type="submit" class="px-3 py-1.5 bg-slate-100 hover:bg-slate-200 text-slate-700 rounded-xl text-xs font-semibold">
                    Cari
                </button>
                @if($search)
                    <a href="{{ route('admin.chatbot.index') }}" class="text-xs text-rose-500 hover:text-rose-700 underline font-medium">Reset</a>
                @endif
            </form>
        </div>

        <!-- Logs Table -->
        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs">
                <thead>
                    <tr class="bg-slate-50/80 border-b border-slate-200 text-slate-500 font-semibold uppercase tracking-wider">
                        <th class="py-3 px-4 w-32">Waktu</th>
                        <th class="py-3 px-4 w-36">Nomor WhatsApp</th>
                        <th class="py-3 px-4">Pesan Pelanggan</th>
                        <th class="py-3 px-4">Respons AI Chatbot</th>
                        <th class="py-3 px-4 text-right w-16">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($logs as $log)
                        <tr class="hover:bg-slate-50/70 transition-colors">
                            <td class="py-3.5 px-4 text-slate-400 font-mono text-[11px] whitespace-nowrap">
                                {{ $log->waktu ? $log->waktu->format('d/m/Y H:i') : '-' }}
                            </td>
                            <td class="py-3.5 px-4 font-mono font-medium text-slate-700 whitespace-nowrap">
                                <span class="bg-slate-100 px-2 py-1 rounded text-[11px]">
                                    <i class="fa-brands fa-whatsapp text-emerald-600 mr-1"></i>
                                    {{ $log->masked_nomor }}
                                </span>
                            </td>
                            <td class="py-3.5 px-4 font-medium text-slate-900 max-w-xs">
                                {{ $log->pesan_pengguna }}
                            </td>
                            <td class="py-3.5 px-4 text-slate-600 max-w-md">
                                <div class="bg-slate-50 p-2 rounded-xl border border-slate-100 whitespace-pre-line text-[11px]">
                                    {{ $log->respons_chatbot }}
                                </div>
                            </td>
                            <td class="py-3.5 px-4 text-right">
                                <form action="{{ route('admin.chatbot.destroy', $log->id) }}" method="POST" onsubmit="return confirmDeleteLog(event)">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="p-1.5 text-slate-400 hover:text-rose-600 hover:bg-rose-50 rounded-lg transition-colors" title="Hapus Log">
                                        <i class="fa-solid fa-trash-can text-xs"></i>
                                    </button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="py-10 text-center text-slate-400 text-xs">
                                <i class="fa-brands fa-whatsapp text-3xl text-slate-300 mb-2 block"></i>
                                Belum ada riwayat percakapan chatbot. Coba uji coba pesan pada simulator di atas!
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($logs->hasPages())
            <div class="pt-4 border-t border-slate-100">
                {{ $logs->links() }}
            </div>
        @endif
    </div>
</div>

@push('scripts')
<script>
    function sendQuickMessage(text) {
        document.getElementById('chat-input').value = text;
        submitChatMessage(new Event('submit'));
    }

    async function submitChatMessage(e) {
        e.preventDefault();
        const input = document.getElementById('chat-input');
        const text = input.value.trim();
        if (!text) return;

        const chatContainer = document.getElementById('chat-messages');
        const btnSend = document.getElementById('btn-chat-send');
        const currentTime = new Date().toTimeString().split(' ')[0].substring(0, 5);

        // Append User Message to UI
        const userMsgHtml = `
            <div class="flex items-start justify-end gap-2">
                <div class="bg-[#d9fdd3] p-3 rounded-2xl rounded-tr-none shadow-sm text-xs text-slate-800 space-y-1 max-w-[85%]">
                    <p class="leading-relaxed">${escapeHtml(text)}</p>
                    <p class="text-[10px] text-slate-400 text-right">${currentTime} &bull; <i class="fa-solid fa-check-double text-blue-500"></i></p>
                </div>
            </div>
        `;
        chatContainer.insertAdjacentHTML('beforeend', userMsgHtml);
        input.value = '';
        chatContainer.scrollTop = chatContainer.scrollHeight;
        btnSend.disabled = true;

        // Temporary Loading bubble
        const typingId = 'typing-' + Date.now();
        const typingHtml = `
            <div id="${typingId}" class="flex items-start gap-2 max-w-[85%]">
                <div class="bg-white p-3 rounded-2xl rounded-tl-none shadow-sm text-xs text-slate-500 flex items-center gap-2">
                    <i class="fa-solid fa-circle-notch fa-spin text-emerald-600"></i>
                    <span>AI Kopi Kutho sedang menganalisis pesan...</span>
                </div>
            </div>
        `;
        chatContainer.insertAdjacentHTML('beforeend', typingHtml);
        chatContainer.scrollTop = chatContainer.scrollHeight;

        try {
            const response = await fetch('/api/chatbot/webhook', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json'
                },
                body: JSON.stringify({
                    nomor_whatsapp: '081234567890',
                    pesan: text
                })
            });

            const data = await response.json();
            document.getElementById(typingId)?.remove();

            if (data.status === 'success') {
                const botMsgHtml = `
                    <div class="flex items-start gap-2 max-w-[85%]">
                        <div class="bg-white p-3 rounded-2xl rounded-tl-none shadow-sm text-xs text-slate-800 space-y-1">
                            <p class="font-bold text-emerald-800 text-[11px]">Asisten Virtual Kopi Kutho</p>
                            <p class="leading-relaxed whitespace-pre-line">${escapeHtml(data.respons_chatbot)}</p>
                            <p class="text-[10px] text-slate-400 text-right">${currentTime}</p>
                        </div>
                    </div>
                `;
                chatContainer.insertAdjacentHTML('beforeend', botMsgHtml);
            } else {
                const errorHtml = `
                    <div class="flex items-start gap-2 max-w-[85%]">
                        <div class="bg-rose-50 text-rose-700 p-3 rounded-2xl rounded-tl-none shadow-sm text-xs">
                            Gagal memproses pesan: ${data.message || 'Error'}
                        </div>
                    </div>
                `;
                chatContainer.insertAdjacentHTML('beforeend', errorHtml);
            }
        } catch (err) {
            document.getElementById(typingId)?.remove();
            const errorHtml = `
                <div class="flex items-start gap-2 max-w-[85%]">
                    <div class="bg-rose-50 text-rose-700 p-3 rounded-2xl rounded-tl-none shadow-sm text-xs">
                        Error koneksi ke server webhook.
                    </div>
                </div>
            `;
            chatContainer.insertAdjacentHTML('beforeend', errorHtml);
        } finally {
            btnSend.disabled = false;
            chatContainer.scrollTop = chatContainer.scrollHeight;
        }
    }

    function escapeHtml(text) {
        const div = document.createElement('div');
        div.innerText = text;
        return div.innerHTML;
    }

    function confirmDeleteLog(e) {
        e.preventDefault();
        Swal.fire({
            title: 'Hapus Log Chat?',
            text: 'Apakah Anda yakin ingin menghapus data percakapan ini?',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d97706',
            cancelButtonColor: '#64748b',
            confirmButtonText: 'Ya, Hapus!',
            cancelButtonText: 'Batal'
        }).then((result) => {
            if (result.isConfirmed) {
                e.target.submit();
            }
        });
        return false;
    }

    function confirmClearLogs(e) {
        e.preventDefault();
        Swal.fire({
            title: 'Bersihkan Semua Log?',
            text: 'Tindakan ini akan menghapus SELURUH riwayat chat chatbot dari sistem.',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#e11d48',
            cancelButtonColor: '#64748b',
            confirmButtonText: 'Ya, Bersihkan!',
            cancelButtonText: 'Batal'
        }).then((result) => {
            if (result.isConfirmed) {
                e.target.submit();
            }
        });
        return false;
    }
</script>
@endpush
@endsection
