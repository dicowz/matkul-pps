@extends('layouts.kasir')

@section('title', 'Point of Sales (Kasir)')

@section('content')
<div class="flex-1 flex flex-col lg:flex-row overflow-hidden">

    <!-- LEFT AREA: Category Tabs, Search Bar, & Menu Cards Grid -->
    <div class="flex-1 flex flex-col overflow-hidden bg-slate-100 p-4 lg:p-6 space-y-4">

        <!-- Search Bar & Category Filters -->
        <div class="bg-white p-4 rounded-2xl border border-slate-200/80 shadow-sm space-y-3 shrink-0">
            <div class="flex items-center gap-3">
                <div class="relative flex-1">
                    <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-slate-400">
                        <i class="fa-solid fa-magnifying-glass text-sm"></i>
                    </div>
                    <input type="text" id="menu-search-input" onkeyup="filterMenus()" placeholder="Cari menu minuman atau makanan (tekan Ctrl+F)..."
                        class="w-full pl-10 pr-4 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-xs text-slate-800 placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-amber-500 focus:bg-white transition-all">
                </div>
            </div>

            <!-- Category Filter Tabs -->
            <div class="flex items-center gap-2 overflow-x-auto pb-1 custom-scroll" id="category-pills">
                <button type="button" onclick="selectCategory('all', this)" class="cat-pill active px-4 py-2 rounded-xl text-xs font-semibold whitespace-nowrap transition-all bg-amber-600 text-white shadow-sm">
                    <i class="fa-solid fa-layer-group mr-1.5"></i> Semua Kategori
                </button>
                @foreach($categories as $cat)
                    <button type="button" onclick="selectCategory('{{ $cat->id }}', this)" class="cat-pill px-4 py-2 rounded-xl text-xs font-semibold whitespace-nowrap transition-all bg-slate-100 text-slate-700 hover:bg-slate-200">
                        {{ $cat->nama_kategori }}
                    </button>
                @endforeach
            </div>
        </div>

        <!-- Menu Cards Grid (Scrollable) -->
        <div class="flex-1 overflow-y-auto custom-scroll pr-1">
            <div class="grid grid-cols-2 sm:grid-cols-3 xl:grid-cols-4 2xl:grid-cols-5 gap-3.5" id="menu-grid">
                @foreach($allMenus as $menu)
                    <div class="menu-card bg-white rounded-2xl border border-slate-200/80 p-3 shadow-sm hover:shadow-md transition-all flex flex-col justify-between cursor-pointer group {{ $menu->status === 'tidak' ? 'opacity-50 grayscale cursor-not-allowed' : '' }}"
                        data-category="{{ $menu->kategori_id }}"
                        data-name="{{ strtolower($menu->nama_menu) }}"
                        data-available="{{ $menu->status === 'tersedia' ? '1' : '0' }}"
                        onclick="{{ $menu->status === 'tersedia' ? "addToCart({$menu->id}, '".addslashes($menu->nama_menu)."', {$menu->harga}, '".($menu->foto_url ?? '')."')" : "showUnavailableAlert('".addslashes($menu->nama_menu)."')" }}">
                        
                        <div>
                            <!-- Image / Thumbnail -->
                            <div class="w-full h-28 rounded-xl bg-amber-50/70 overflow-hidden flex items-center justify-center text-amber-700 relative mb-2.5">
                                @if($menu->foto_url)
                                    <img src="{{ $menu->foto_url }}" alt="{{ $menu->nama_menu }}" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-300">
                                @else
                                    <i class="fa-solid fa-mug-hot text-2xl group-hover:scale-110 transition-transform text-amber-600/70"></i>
                                @endif

                                @if($menu->status === 'tidak')
                                    <span class="absolute inset-0 bg-black/50 backdrop-blur-[1px] flex items-center justify-center text-white text-[11px] font-bold uppercase tracking-wider">
                                        Habis
                                    </span>
                                @endif
                            </div>

                            <!-- Menu Details -->
                            <p class="text-[10px] font-semibold uppercase tracking-wider text-amber-700">{{ $menu->kategori->nama_kategori ?? 'Umum' }}</p>
                            <h4 class="font-bold text-slate-900 text-xs mt-0.5 line-clamp-2 leading-tight group-hover:text-amber-600 transition-colors">
                                {{ $menu->nama_menu }}
                            </h4>
                        </div>

                        <div class="mt-3 pt-2 border-t border-slate-100 flex items-center justify-between">
                            <span class="font-bold text-slate-900 text-xs font-mono">
                                {{ $menu->formatted_harga }}
                            </span>
                            @if($menu->status === 'tersedia')
                                <button type="button" class="w-7 h-7 rounded-lg bg-amber-50 text-amber-700 group-hover:bg-amber-600 group-hover:text-white flex items-center justify-center text-xs transition-colors shadow-sm">
                                    <i class="fa-solid fa-plus"></i>
                                </button>
                            @else
                                <span class="text-[10px] text-rose-500 font-semibold">Kosong</span>
                            @endif
                        </div>
                    </div>
                @endforeach
            </div>

            <!-- Empty State for Search Filter -->
            <div id="no-menu-found" class="hidden text-center py-16 text-slate-400">
                <i class="fa-solid fa-mug-hot text-4xl text-slate-300 mb-2 block"></i>
                <p class="text-xs">Tidak ada menu yang sesuai dengan pencarian Anda.</p>
            </div>
        </div>
    </div>

    <!-- RIGHT AREA: Active Cart Sidebar (Keranjang Pesanan) -->
    <div class="w-full lg:w-96 bg-white border-t lg:border-t-0 lg:border-l border-slate-200 flex flex-col h-[400px] lg:h-auto shrink-0 shadow-lg z-20">
        <!-- Cart Header -->
        <div class="px-5 py-4 border-b border-slate-100 flex items-center justify-between bg-slate-50/50">
            <div class="flex items-center gap-2">
                <i class="fa-solid fa-cart-shopping text-amber-600"></i>
                <h3 class="font-bold text-slate-900 text-sm">Keranjang Pesanan</h3>
                <span id="cart-item-count" class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-amber-100 text-amber-800">0</span>
            </div>
            <button type="button" onclick="clearCart()" class="text-xs text-rose-500 hover:text-rose-700 font-medium flex items-center gap-1 transition-colors">
                <i class="fa-solid fa-trash-can text-[11px]"></i>
                <span>Reset</span>
            </button>
        </div>

        <!-- Cart Items List (Scrollable) -->
        <div class="flex-1 overflow-y-auto p-4 space-y-3 custom-scroll" id="cart-items-container">
            <!-- Dynamic Item Rows Inserted by JavaScript -->
            <div id="empty-cart-message" class="text-center py-16 text-slate-400 space-y-2">
                <i class="fa-solid fa-basket-shopping text-4xl text-slate-300"></i>
                <p class="text-xs">Keranjang masih kosong.<br>Klik menu di samping untuk menambahkan.</p>
            </div>
        </div>

        <!-- Cart Calculation & Checkout Area -->
        <div class="p-5 border-t border-slate-200 bg-slate-50 space-y-4 shrink-0">
            <div class="space-y-1.5 text-xs text-slate-600">
                <div class="flex justify-between">
                    <span>Subtotal</span>
                    <span class="font-semibold text-slate-800 font-mono" id="summary-subtotal">Rp 0</span>
                </div>
                <div class="flex justify-between text-slate-400">
                    <span>Pajak Resto (Termasuk)</span>
                    <span class="font-mono">Rp 0</span>
                </div>
                <div class="flex justify-between items-baseline pt-2 border-t border-slate-200 text-slate-900">
                    <span class="font-bold text-sm">TOTAL BAYAR</span>
                    <span class="font-bold text-xl text-amber-700 font-mono" id="summary-total">Rp 0</span>
                </div>
            </div>

            <!-- Button Checkout / Bayar -->
            <button type="button" id="btn-open-payment" onclick="openPaymentModal()" disabled
                class="w-full py-3.5 px-4 bg-gradient-to-r from-amber-600 to-amber-500 hover:from-amber-500 hover:to-amber-400 disabled:from-slate-300 disabled:to-slate-300 disabled:cursor-not-allowed text-white font-bold rounded-2xl text-xs uppercase tracking-wider flex items-center justify-center gap-2 shadow-lg shadow-amber-950/20 transition-all">
                <i class="fa-solid fa-credit-card"></i>
                <span>Proses Pembayaran</span>
            </button>
        </div>
    </div>
</div>

<!-- MODAL PEMBAYARAN (TUNAI / DEBIT / QRIS) -->
<div id="paymentModal" class="fixed inset-0 bg-black/60 z-50 hidden flex items-center justify-center p-4">
    <div class="bg-white rounded-3xl max-w-lg w-full p-6 shadow-2xl animate-fade-in space-y-5">
        <div class="flex items-center justify-between pb-3 border-b border-slate-100">
            <div>
                <h4 class="font-bold text-slate-900 text-base">Penyelesaian Pembayaran</h4>
                <p class="text-xs text-slate-500">Pilih metode pembayaran dan masukkan nominal</p>
            </div>
            <button onclick="closePaymentModal()" class="text-slate-400 hover:text-slate-700"><i class="fa-solid fa-xmark text-lg"></i></button>
        </div>

        <!-- Total yang Harus Dibayar Highlight -->
        <div class="bg-amber-50 border border-amber-200/80 rounded-2xl p-4 text-center">
            <p class="text-xs font-semibold uppercase tracking-wider text-amber-800">Total Tagihan Pesanan</p>
            <h2 class="text-3xl font-bold text-amber-900 font-mono mt-1" id="modal-total-tagihan">Rp 0</h2>
        </div>

        <!-- Payment Method Tabs -->
        <div>
            <label class="block text-xs font-semibold text-slate-700 uppercase tracking-wider mb-2">Metode Pembayaran</label>
            <div class="grid grid-cols-3 gap-2.5">
                <button type="button" onclick="selectPaymentMethod('tunai')" id="tab-pay-tunai" class="pay-method-btn active py-2.5 px-3 rounded-xl border-2 border-amber-600 bg-amber-50 text-amber-900 font-bold text-xs flex flex-col items-center gap-1 transition-all">
                    <i class="fa-solid fa-money-bill-wave text-base text-emerald-600"></i>
                    <span>Tunai (Cash)</span>
                </button>
                <button type="button" onclick="selectPaymentMethod('qris')" id="tab-pay-qris" class="pay-method-btn py-2.5 px-3 rounded-xl border border-slate-200 hover:bg-slate-50 text-slate-700 font-medium text-xs flex flex-col items-center gap-1 transition-all">
                    <i class="fa-solid fa-qrcode text-base text-blue-600"></i>
                    <span>QRIS Kutho</span>
                </button>
                <button type="button" onclick="selectPaymentMethod('debit')" id="tab-pay-debit" class="pay-method-btn py-2.5 px-3 rounded-xl border border-slate-200 hover:bg-slate-50 text-slate-700 font-medium text-xs flex flex-col items-center gap-1 transition-all">
                    <i class="fa-solid fa-credit-card text-base text-purple-600"></i>
                    <span>Debit Card</span>
                </button>
            </div>
        </div>

        <!-- TUNAI SECTION: Nominal Uang Diterima & Kembalian -->
        <div id="section-tunai" class="space-y-4">
            <div>
                <label class="block text-xs font-semibold text-slate-700 uppercase tracking-wider mb-1.5">Uang Diterima (Rp)</label>
                <div class="relative">
                    <span class="absolute inset-y-0 left-0 pl-3.5 flex items-center text-xs font-bold text-slate-400">Rp</span>
                    <input type="number" id="cash-received-input" oninput="calculateChange()" min="0" placeholder="0"
                        class="w-full pl-10 pr-4 py-2.5 bg-slate-50 border border-slate-300 rounded-xl text-slate-900 font-mono font-bold text-base focus:outline-none focus:ring-2 focus:ring-amber-500 focus:bg-white transition-all">
                </div>
            </div>

            <!-- Quick Cash Preset Buttons -->
            <div>
                <p class="text-[11px] text-slate-400 mb-1.5 font-medium">Uang Cepat / Pas:</p>
                <div class="grid grid-cols-4 gap-2 text-xs">
                    <button type="button" onclick="setCashExact()" class="py-1.5 bg-slate-100 hover:bg-slate-200 rounded-lg font-semibold text-slate-700 transition-colors">Uang Pas</button>
                    <button type="button" onclick="setCashAmount(20000)" class="py-1.5 bg-slate-100 hover:bg-slate-200 rounded-lg font-mono text-slate-700 transition-colors">20.000</button>
                    <button type="button" onclick="setCashAmount(50000)" class="py-1.5 bg-slate-100 hover:bg-slate-200 rounded-lg font-mono text-slate-700 transition-colors">50.000</button>
                    <button type="button" onclick="setCashAmount(100000)" class="py-1.5 bg-slate-100 hover:bg-slate-200 rounded-lg font-mono text-slate-700 transition-colors">100.000</button>
                </div>
            </div>

            <!-- Uang Kembalian Box -->
            <div class="bg-slate-100 p-3.5 rounded-2xl flex items-center justify-between border border-slate-200">
                <span class="text-xs font-semibold text-slate-600">Uang Kembalian:</span>
                <span class="text-lg font-bold text-emerald-700 font-mono" id="cash-change-display">Rp 0</span>
            </div>
            <p id="cash-error-msg" class="text-xs text-rose-500 font-medium hidden">Uang yang diterima masih kurang dari total tagihan.</p>
        </div>

        <!-- QRIS SECTION -->
        <div id="section-qris" class="hidden text-center py-3 space-y-3">
            <div class="w-44 h-44 mx-auto bg-white p-3 rounded-2xl border-2 border-slate-300 shadow-sm flex flex-col items-center justify-center">
                <!-- Mock QRIS Barcode -->
                <img src="https://api.qrserver.com/v1/create-qr-code/?size=150x150&data=KOPIKUTHO-QRIS-TEST-2026" alt="QRIS Kopi Kutho" class="w-36 h-36">
            </div>
            <p class="text-xs text-slate-600 font-medium">Scan QRIS menggunakan BCA Mobile, GoPay, OVO, Dana, atau ShopeePay</p>
        </div>

        <!-- DEBIT SECTION -->
        <div id="section-debit" class="hidden text-center py-4 space-y-2">
            <i class="fa-solid fa-credit-card text-4xl text-purple-600"></i>
            <p class="text-xs text-slate-700 font-medium">Silakan gesek / tap kartu debit pelanggan pada mesin EDC.</p>
            <p class="text-[11px] text-slate-400">Pastikan transaksi pada mesin EDC telah berhasil sebelum konfirmasi.</p>
        </div>

        <!-- Modal Action Buttons -->
        <div class="flex items-center justify-end gap-3 pt-3 border-t border-slate-100">
            <button type="button" onclick="closePaymentModal()" class="px-5 py-2.5 rounded-xl border border-slate-200 text-slate-600 text-xs font-medium hover:bg-slate-50 transition-colors">Batal</button>
            <button type="button" id="btn-confirm-transaction" onclick="processTransaction()"
                class="px-6 py-2.5 bg-emerald-600 hover:bg-emerald-700 text-white rounded-xl text-xs font-bold shadow-md shadow-emerald-950/20 transition-all flex items-center gap-2">
                <i class="fa-solid fa-check"></i>
                <span>Selesaikan & Simpan</span>
            </button>
        </div>
    </div>
</div>

<!-- MODAL CETAK STRUK PEMBAYARAN -->
<div id="receiptModal" class="fixed inset-0 bg-black/60 z-50 hidden flex items-center justify-center p-4">
    <div class="bg-white rounded-3xl max-w-sm w-full p-6 shadow-2xl animate-fade-in space-y-5">
        <div class="text-center pb-3 border-b border-slate-100">
            <div class="w-12 h-12 rounded-full bg-emerald-100 text-emerald-600 mx-auto flex items-center justify-center text-xl mb-2">
                <i class="fa-solid fa-circle-check"></i>
            </div>
            <h4 class="font-bold text-slate-900 text-base">Transaksi Berhasil Disimpan!</h4>
            <p class="text-xs text-slate-500" id="receipt-modal-subtitle">No. Transaksi: -</p>
        </div>

        <!-- Thermal Receipt Preview Box -->
        <div id="receipt-preview-content" class="bg-slate-50 p-4 rounded-2xl border border-slate-200 font-mono text-[11px] text-slate-800 space-y-2 max-h-72 overflow-y-auto custom-scroll">
            <!-- Dynamically populated receipt content -->
        </div>

        <!-- Actions -->
        <div class="space-y-2">
            <a id="receipt-print-link" href="#" target="_blank" class="w-full py-2.5 px-4 bg-amber-600 hover:bg-amber-700 text-white rounded-xl text-xs font-bold flex items-center justify-center gap-2 shadow-sm transition-all">
                <i class="fa-solid fa-print"></i>
                <span>Cetak Struk Thermal</span>
            </a>
            <button type="button" onclick="resetForNewTransaction()" class="w-full py-2.5 px-4 bg-slate-100 hover:bg-slate-200 text-slate-800 rounded-xl text-xs font-semibold transition-all">
                Mulai Transaksi Baru (Esc)
            </button>
        </div>
    </div>
</div>

@push('scripts')
<script>
    // Cart State Management
    let cart = {}; // { menu_id: { id, name, price, qty, photo } }
    let selectedMetode = 'tunai';
    let currentTotal = 0;

    function addToCart(id, name, price, photo) {
        if (cart[id]) {
            cart[id].qty += 1;
        } else {
            cart[id] = {
                id: id,
                name: name,
                price: price,
                qty: 1,
                photo: photo
            };
        }
        renderCart();
    }

    function updateQty(id, delta) {
        if (!cart[id]) return;
        cart[id].qty += delta;
        if (cart[id].qty <= 0) {
            delete cart[id];
        }
        renderCart();
    }

    function removeFromCart(id) {
        delete cart[id];
        renderCart();
    }

    function clearCart() {
        if (Object.keys(cart).length === 0) return;
        Swal.fire({
            title: 'Kosongkan Keranjang?',
            text: 'Semua item pesanan yang dipilih akan dihapus.',
            icon: 'question',
            showCancelButton: true,
            confirmButtonColor: '#d97706',
            cancelButtonColor: '#64748b',
            confirmButtonText: 'Ya, Kosongkan',
            cancelButtonText: 'Batal'
        }).then((result) => {
            if (result.isConfirmed) {
                cart = {};
                renderCart();
            }
        });
    }

    function renderCart() {
        const container = document.getElementById('cart-items-container');
        const countBadge = document.getElementById('cart-item-count');
        const emptyMsg = document.getElementById('empty-cart-message');
        const btnPay = document.getElementById('btn-open-payment');

        const itemKeys = Object.keys(cart);
        let totalItems = 0;
        let totalAmount = 0;

        if (itemKeys.length === 0) {
            container.innerHTML = `
                <div id="empty-cart-message" class="text-center py-16 text-slate-400 space-y-2">
                    <i class="fa-solid fa-basket-shopping text-4xl text-slate-300"></i>
                    <p class="text-xs">Keranjang masih kosong.<br>Klik menu di samping untuk menambahkan.</p>
                </div>
            `;
            countBadge.innerText = '0';
            document.getElementById('summary-subtotal').innerText = 'Rp 0';
            document.getElementById('summary-total').innerText = 'Rp 0';
            currentTotal = 0;
            btnPay.disabled = true;
            return;
        }

        let html = '';
        itemKeys.forEach(key => {
            const item = cart[key];
            const subtotal = item.price * item.qty;
            totalItems += item.qty;
            totalAmount += subtotal;

            html += `
                <div class="bg-slate-50 p-3 rounded-2xl border border-slate-200/70 space-y-2 hover:border-amber-300 transition-colors">
                    <div class="flex items-start justify-between gap-2">
                        <div>
                            <h5 class="font-bold text-slate-900 text-xs leading-snug">${escapeHtml(item.name)}</h5>
                            <span class="text-[11px] text-slate-500 font-mono">Rp ${item.price.toLocaleString('id-ID')}</span>
                        </div>
                        <button type="button" onclick="removeFromCart(${item.id})" class="text-slate-400 hover:text-rose-500 p-1 transition-colors" title="Hapus">
                            <i class="fa-solid fa-xmark text-xs"></i>
                        </button>
                    </div>

                    <div class="flex items-center justify-between pt-1 border-t border-slate-100">
                        <div class="flex items-center gap-1.5 bg-white border border-slate-200 rounded-lg p-0.5">
                            <button type="button" onclick="updateQty(${item.id}, -1)" class="w-6 h-6 rounded flex items-center justify-center text-slate-600 hover:bg-slate-100 text-xs font-bold">-</button>
                            <span class="w-6 text-center font-bold text-xs font-mono text-slate-800">${item.qty}</span>
                            <button type="button" onclick="updateQty(${item.id}, 1)" class="w-6 h-6 rounded flex items-center justify-center text-slate-600 hover:bg-slate-100 text-xs font-bold">+</button>
                        </div>
                        <span class="font-bold text-slate-900 text-xs font-mono">Rp ${subtotal.toLocaleString('id-ID')}</span>
                    </div>
                </div>
            `;
        });

        container.innerHTML = html;
        countBadge.innerText = totalItems;
        currentTotal = totalAmount;
        document.getElementById('summary-subtotal').innerText = 'Rp ' + totalAmount.toLocaleString('id-ID');
        document.getElementById('summary-total').innerText = 'Rp ' + totalAmount.toLocaleString('id-ID');
        btnPay.disabled = false;
    }

    // Category and Search Filtering
    function selectCategory(catId, btn) {
        document.querySelectorAll('.cat-pill').forEach(el => {
            el.classList.remove('bg-amber-600', 'text-white', 'shadow-sm');
            el.classList.add('bg-slate-100', 'text-slate-700');
        });
        btn.classList.remove('bg-slate-100', 'text-slate-700');
        btn.classList.add('bg-amber-600', 'text-white', 'shadow-sm');

        const cards = document.querySelectorAll('.menu-card');
        let visibleCount = 0;
        cards.forEach(card => {
            const cardCat = card.getAttribute('data-category');
            if (catId === 'all' || cardCat === catId) {
                card.style.display = '';
                visibleCount++;
            } else {
                card.style.display = 'none';
            }
        });
        document.getElementById('no-menu-found').classList.toggle('hidden', visibleCount > 0);
    }

    function filterMenus() {
        const query = document.getElementById('menu-search-input').value.toLowerCase().trim();
        const cards = document.querySelectorAll('.menu-card');
        let visibleCount = 0;
        cards.forEach(card => {
            const name = card.getAttribute('data-name');
            if (name.includes(query)) {
                card.style.display = '';
                visibleCount++;
            } else {
                card.style.display = 'none';
            }
        });
        document.getElementById('no-menu-found').classList.toggle('hidden', visibleCount > 0);
    }

    function showUnavailableAlert(name) {
        Swal.fire({
            title: 'Menu Tidak Tersedia',
            text: `Maaf, stok menu "${name}" saat ini sedang kosong/habis.`,
            icon: 'info',
            confirmButtonColor: '#d97706'
        });
    }

    // Payment Flow
    function openPaymentModal() {
        if (currentTotal <= 0) return;
        document.getElementById('modal-total-tagihan').innerText = 'Rp ' + currentTotal.toLocaleString('id-ID');
        selectPaymentMethod('tunai');
        setCashExact();
        document.getElementById('paymentModal').classList.remove('hidden');
    }

    function closePaymentModal() {
        document.getElementById('paymentModal').classList.add('hidden');
    }

    function selectPaymentMethod(metode) {
        selectedMetode = metode;
        document.querySelectorAll('.pay-method-btn').forEach(b => {
            b.classList.remove('border-2', 'border-amber-600', 'bg-amber-50', 'text-amber-900', 'font-bold');
            b.classList.add('border-slate-200', 'text-slate-700');
        });

        const activeBtn = document.getElementById(`tab-pay-${metode}`);
        activeBtn.classList.remove('border-slate-200', 'text-slate-700');
        activeBtn.classList.add('border-2', 'border-amber-600', 'bg-amber-50', 'text-amber-900', 'font-bold');

        document.getElementById('section-tunai').classList.toggle('hidden', metode !== 'tunai');
        document.getElementById('section-qris').classList.toggle('hidden', metode !== 'qris');
        document.getElementById('section-debit').classList.toggle('hidden', metode !== 'debit');

        calculateChange();
    }

    function setCashExact() {
        document.getElementById('cash-received-input').value = currentTotal;
        calculateChange();
    }

    function setCashAmount(amt) {
        document.getElementById('cash-received-input').value = amt;
        calculateChange();
    }

    function calculateChange() {
        if (selectedMetode !== 'tunai') {
            document.getElementById('cash-error-msg').classList.add('hidden');
            document.getElementById('btn-confirm-transaction').disabled = false;
            return;
        }

        const input = document.getElementById('cash-received-input');
        const cash = parseInt(input.value) || 0;
        const change = cash - currentTotal;
        const changeDisplay = document.getElementById('cash-change-display');
        const errorMsg = document.getElementById('cash-error-msg');
        const confirmBtn = document.getElementById('btn-confirm-transaction');

        if (change >= 0) {
            changeDisplay.innerText = 'Rp ' + change.toLocaleString('id-ID');
            changeDisplay.classList.remove('text-rose-600');
            changeDisplay.classList.add('text-emerald-700');
            errorMsg.classList.add('hidden');
            confirmBtn.disabled = false;
        } else {
            changeDisplay.innerText = '- Rp ' + Math.abs(change).toLocaleString('id-ID');
            changeDisplay.classList.remove('text-emerald-700');
            changeDisplay.classList.add('text-rose-600');
            errorMsg.classList.remove('hidden');
            confirmBtn.disabled = true;
        }
    }

    // Process Transaction via AJAX to /kasir/transaksi
    async function processTransaction() {
        const btn = document.getElementById('btn-confirm-transaction');
        btn.disabled = true;

        const itemsPayload = Object.keys(cart).map(k => ({
            menu_id: cart[k].id,
            jumlah: cart[k].qty
        }));

        let uangDiterima = null;
        if (selectedMetode === 'tunai') {
            uangDiterima = parseInt(document.getElementById('cash-received-input').value) || currentTotal;
        }

        try {
            const response = await fetch('{{ route('kasir.store') }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                },
                body: JSON.stringify({
                    items: itemsPayload,
                    metode_pembayaran: selectedMetode,
                    uang_diterima: uangDiterima
                })
            });

            const data = await response.json();

            if (response.ok && data.status === 'success') {
                closePaymentModal();
                showReceiptModal(data.transaksi, data.print_url);
            } else {
                Swal.fire({
                    title: 'Gagal Memproses Transaksi!',
                    text: data.message || 'Terjadi kesalahan sistem.',
                    icon: 'error',
                    confirmButtonColor: '#d97706'
                });
            }
        } catch (error) {
            Swal.fire({
                title: 'Error Koneksi!',
                text: 'Tidak dapat menghubungi server kasir.',
                icon: 'error'
            });
        } finally {
            btn.disabled = false;
        }
    }

    function showReceiptModal(trx, printUrl) {
        document.getElementById('receipt-modal-subtitle').innerText = `No: ${trx.nomor_transaksi} &bull; ${new Date().toLocaleTimeString()}`;
        document.getElementById('receipt-print-link').href = printUrl;

        let itemsHtml = '';
        trx.detail_transaksi.forEach(d => {
            itemsHtml += `
                <div class="flex justify-between py-1 border-b border-dashed border-slate-200">
                    <div>
                        <p class="font-bold">${escapeHtml(d.menu ? d.menu.nama_menu : 'Item')}</p>
                        <span class="text-slate-400">${d.jumlah}x @ Rp ${d.harga.toLocaleString('id-ID')}</span>
                    </div>
                    <span class="font-bold">Rp ${d.subtotal.toLocaleString('id-ID')}</span>
                </div>
            `;
        });

        const receiptBox = document.getElementById('receipt-preview-content');
        receiptBox.innerHTML = `
            <div class="text-center pb-2 border-b border-dashed border-slate-300">
                <p class="font-bold text-xs uppercase">CAFE RESTO KOPI KUTHO</p>
                <p class="text-[10px] text-slate-500">Jl. Semolowaru No. 45, Surabaya</p>
                <p class="text-[10px] text-slate-500">No. Trx: ${trx.nomor_transaksi}</p>
            </div>
            <div class="py-1">
                ${itemsHtml}
            </div>
            <div class="pt-2 border-t border-slate-300 space-y-1">
                <div class="flex justify-between font-bold text-xs">
                    <span>TOTAL:</span>
                    <span>Rp ${trx.total.toLocaleString('id-ID')}</span>
                </div>
                <div class="flex justify-between text-[11px] text-slate-500">
                    <span>METODE:</span>
                    <span class="uppercase font-semibold">${trx.metode_pembayaran}</span>
                </div>
                ${trx.metode_pembayaran === 'tunai' ? `
                    <div class="flex justify-between text-[11px] text-slate-500">
                        <span>DITERIMA:</span>
                        <span>Rp ${(trx.uang_diterima || 0).toLocaleString('id-ID')}</span>
                    </div>
                    <div class="flex justify-between text-[11px] font-bold text-emerald-700">
                        <span>KEMBALIAN:</span>
                        <span>Rp ${(trx.kembalian || 0).toLocaleString('id-ID')}</span>
                    </div>
                ` : ''}
            </div>
        `;

        document.getElementById('receiptModal').classList.remove('hidden');
    }

    function resetForNewTransaction() {
        document.getElementById('receiptModal').classList.add('hidden');
        cart = {};
        renderCart();
    }

    function escapeHtml(text) {
        const div = document.createElement('div');
        div.innerText = text;
        return div.innerHTML;
    }
</script>
@endpush
@endsection
