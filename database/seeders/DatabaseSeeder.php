<?php

namespace Database\Seeders;

use App\Models\Absensi;
use App\Models\ChatbotLog;
use App\Models\DetailTransaksi;
use App\Models\Karyawan;
use App\Models\Kategori;
use App\Models\Menu;
use App\Models\Transaksi;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database with Kopi Kutho demo data.
     */
    public function run(): void
    {
        // 1. Users
        $admin = User::create([
            'name' => 'Owner / Admin Kopi Kutho',
            'username' => 'admin',
            'email' => 'admin@kopikutho.id',
            'password' => Hash::make('password'),
            'role' => 'admin',
        ]);

        $kasir = User::create([
            'name' => 'Kasir Utama Kopi Kutho',
            'username' => 'kasir',
            'email' => 'kasir@kopikutho.id',
            'password' => Hash::make('password'),
            'role' => 'kasir',
        ]);

        // 2. Karyawan
        $karyawanList = [
            [
                'nama' => 'Dimas Ardiansyah',
                'jabatan' => 'Head Barista',
                'fingerprint_id' => 101,
                'status' => 'aktif',
            ],
            [
                'nama' => 'Siti Nurhaliza',
                'jabatan' => 'Kasir Senior',
                'fingerprint_id' => 102,
                'status' => 'aktif',
            ],
            [
                'nama' => 'Budi Santoso',
                'jabatan' => 'Kitchen Head Chef',
                'fingerprint_id' => 103,
                'status' => 'aktif',
            ],
            [
                'nama' => 'Rian Hidayat',
                'jabatan' => 'Service / Waiter',
                'fingerprint_id' => 104,
                'status' => 'aktif',
            ],
            [
                'nama' => 'Anita Rahayu',
                'jabatan' => 'Barista Junior',
                'fingerprint_id' => 105,
                'status' => 'aktif',
            ],
            [
                'nama' => 'Agus Pranoto',
                'jabatan' => 'Staff Gudang (Ex)',
                'fingerprint_id' => 106,
                'status' => 'nonaktif',
            ],
        ];

        $karyawanModels = [];
        foreach ($karyawanList as $k) {
            $karyawanModels[] = Karyawan::create($k);
        }

        // 3. Kategori
        $katKopi = Kategori::create(['nama_kategori' => 'Kopi']);
        $katNonKopi = Kategori::create(['nama_kategori' => 'Non-Kopi']);
        $katMakanan = Kategori::create(['nama_kategori' => 'Makanan Utama']);
        $katSnack = Kategori::create(['nama_kategori' => 'Snack & Cemilan']);

        // 4. Menu
        $menus = [
            // Kopi
            [
                'kategori_id' => $katKopi->id,
                'nama_menu' => 'Kopi Susu Kutho Signature',
                'harga' => 18000,
                'foto' => null,
                'deskripsi' => 'Espresso house blend dipadu susu creamy dan gula aren organik khas Kopi Kutho.',
                'status' => 'tersedia',
            ],
            [
                'kategori_id' => $katKopi->id,
                'nama_menu' => 'Americano Kutho',
                'harga' => 15000,
                'foto' => null,
                'deskripsi' => 'Espresso murni ganda dengan air segar dingin atau hangat, aroma pekat menawan.',
                'status' => 'tersedia',
            ],
            [
                'kategori_id' => $katKopi->id,
                'nama_menu' => 'Caramel Macchiato',
                'harga' => 22000,
                'foto' => null,
                'deskripsi' => 'Perpaduan espresso kental, steamed milk, vanilla syrup dengan lelehan saus karamel.',
                'status' => 'tersedia',
            ],
            [
                'kategori_id' => $katKopi->id,
                'nama_menu' => 'Cappuccino Kutho',
                'harga' => 20000,
                'foto' => null,
                'deskripsi' => 'Espresso seimbang dengan susu lembut berbusa tebal dan taburan bubuk coklat.',
                'status' => 'tersedia',
            ],
            [
                'kategori_id' => $katKopi->id,
                'nama_menu' => 'V60 Single Origin Gayo',
                'harga' => 24000,
                'foto' => null,
                'deskripsi' => 'Manual brew filter beans Arabika Aceh Gayo dengan notes floral dan fruity asam segar.',
                'status' => 'tersedia',
            ],
            [
                'kategori_id' => $katKopi->id,
                'nama_menu' => 'Espresso Solo',
                'harga' => 12000,
                'foto' => null,
                'deskripsi' => 'Single shot espresso 30ml ekstraksi murni krema tebal emas kecoklatan.',
                'status' => 'tersedia',
            ],

            // Non-Kopi
            [
                'kategori_id' => $katNonKopi->id,
                'nama_menu' => 'Matcha Latte Uji',
                'harga' => 22000,
                'foto' => null,
                'deskripsi' => 'Bubuk green tea matcha premium Jepang dengan susu segar lembut dan manis pas.',
                'status' => 'tersedia',
            ],
            [
                'kategori_id' => $katNonKopi->id,
                'nama_menu' => 'Classic Chocolate Kutho',
                'harga' => 20000,
                'foto' => null,
                'deskripsi' => 'Dark cocoa rich brew dengan susu kental, cocok dingin maupun hangat saat hujan.',
                'status' => 'tersedia',
            ],
            [
                'kategori_id' => $katNonKopi->id,
                'nama_menu' => 'Red Velvet Cream Latte',
                'harga' => 22000,
                'foto' => null,
                'deskripsi' => 'Rasa kue red velvet lembut dengan sensasi vanilla milk gurih dan creamy.',
                'status' => 'tersedia',
            ],
            [
                'kategori_id' => $katNonKopi->id,
                'nama_menu' => 'Earl Grey Peach Tea',
                'harga' => 16000,
                'foto' => null,
                'deskripsi' => 'Teh hitam aromatik bergamot disajikan dingin bersama sirup persik segar.',
                'status' => 'tersedia',
            ],
            [
                'kategori_id' => $katNonKopi->id,
                'nama_menu' => 'Es Teh Manis Kutho',
                'harga' => 5000,
                'foto' => null,
                'deskripsi' => 'Teh melati seduh tradisional Jawa dengan es batu kristal segar melegakan dahaga.',
                'status' => 'tersedia',
            ],
            [
                'kategori_id' => $katNonKopi->id,
                'nama_menu' => 'Lemon Mojito Sparkle',
                'harga' => 18000,
                'foto' => null,
                'deskripsi' => 'Soda segar dingin dengan perasan lemon asli dan daun mint segar yang menyegarkan.',
                'status' => 'tidak', // Menu habis / tidak tersedia untuk demonstrasi
            ],

            // Makanan Utama
            [
                'kategori_id' => $katMakanan->id,
                'nama_menu' => 'Ayam Geprek Sambal Bawang Kutho',
                'harga' => 15000,
                'foto' => null,
                'deskripsi' => 'Ayam krispi renyah digeprek dengan sambal bawang pedas gurih khas Kutho + timun segar.',
                'status' => 'tersedia',
            ],
            [
                'kategori_id' => $katMakanan->id,
                'nama_menu' => 'Nasi Goreng Spesial Kopi Kutho',
                'harga' => 18000,
                'foto' => null,
                'deskripsi' => 'Nasi goreng harum rempah komplit dengan suwiran ayam, telur mata sapi, dan kerupuk.',
                'status' => 'tersedia',
            ],
            [
                'kategori_id' => $katMakanan->id,
                'nama_menu' => 'Chicken Katsu Sambal Matah Bowl',
                'harga' => 23000,
                'foto' => null,
                'deskripsi' => 'Nasi hangat dengan potongan dada ayam katsu gurih renyah disiram sambal matah Bali segar.',
                'status' => 'tersedia',
            ],
            [
                'kategori_id' => $katMakanan->id,
                'nama_menu' => 'Beef Teriyaki Rice Bowl',
                'harga' => 28000,
                'foto' => null,
                'deskripsi' => 'Irisan daging sapi empuk dimasak saus teriyaki manis gurih dengan taburan wijen sangrai.',
                'status' => 'tersedia',
            ],
            [
                'kategori_id' => $katMakanan->id,
                'nama_menu' => 'Mie Goreng Jawa Kutho',
                'harga' => 17000,
                'foto' => null,
                'deskripsi' => 'Mie kuning kenyal dimasak bumbu kecap manis gurih dengan sayuran segar dan telur orak-arik.',
                'status' => 'tersedia',
            ],

            // Snack
            [
                'kategori_id' => $katSnack->id,
                'nama_menu' => 'French Fries Golden Crispy',
                'harga' => 14000,
                'foto' => null,
                'deskripsi' => 'Kentang goreng renyah bumbu sea salt disajikan dengan saus cocolan tomat & sambal.',
                'status' => 'tersedia',
            ],
            [
                'kategori_id' => $katSnack->id,
                'nama_menu' => 'Cireng Crispy Bumbu Rujak',
                'harga' => 13000,
                'foto' => null,
                'deskripsi' => 'Cireng kenyal di luar renyah di dalam dengan saus gula merah cabai rawit pedas asam manis.',
                'status' => 'tersedia',
            ],
            [
                'kategori_id' => $katSnack->id,
                'nama_menu' => 'Roti Bakar Coklat Keju Susu',
                'harga' => 16000,
                'foto' => null,
                'deskripsi' => 'Roti tebal bakar mentega dengan topping parutan keju cheddar melimpah dan meses coklat.',
                'status' => 'tersedia',
            ],
            [
                'kategori_id' => $katSnack->id,
                'nama_menu' => 'Tahu Walik Krispi Sambal Kecap',
                'harga' => 14000,
                'foto' => null,
                'deskripsi' => 'Tahu pong dibalik dengan isian adonan ayam gurih renyah kriuk bersambal rawit kecap.',
                'status' => 'tersedia',
            ],
        ];

        $createdMenus = [];
        foreach ($menus as $m) {
            $createdMenus[] = Menu::create($m);
        }

        // 5. Absensi Sample Data (Hari ini & Kemarin)
        $today = Carbon::today();
        $yesterday = Carbon::yesterday();

        // Absensi Kemarin (semua masuk & pulang)
        Absensi::create([
            'karyawan_id' => $karyawanModels[0]->id,
            'tanggal' => $yesterday,
            'jam_masuk' => '07:55:00',
            'jam_keluar' => '16:05:00',
            'status' => 'Tepat Waktu',
        ]);
        Absensi::create([
            'karyawan_id' => $karyawanModels[1]->id,
            'tanggal' => $yesterday,
            'jam_masuk' => '08:02:00',
            'jam_keluar' => '16:00:00',
            'status' => 'Tepat Waktu',
        ]);
        Absensi::create([
            'karyawan_id' => $karyawanModels[2]->id,
            'tanggal' => $yesterday,
            'jam_masuk' => '08:15:00',
            'jam_keluar' => '16:10:00',
            'status' => 'Terlambat',
        ]);

        // Absensi Hari Ini
        // Dimas sudah masuk & pulang
        Absensi::create([
            'karyawan_id' => $karyawanModels[0]->id,
            'tanggal' => $today,
            'jam_masuk' => '07:50:00',
            'jam_keluar' => '15:55:00',
            'status' => 'Tepat Waktu',
        ]);
        // Siti sudah masuk, belum keluar (aktif bekerja)
        Absensi::create([
            'karyawan_id' => $karyawanModels[1]->id,
            'tanggal' => $today,
            'jam_masuk' => '08:00:00',
            'jam_keluar' => null,
            'status' => 'Tepat Waktu',
        ]);
        // Budi sudah masuk, belum keluar
        Absensi::create([
            'karyawan_id' => $karyawanModels[2]->id,
            'tanggal' => $today,
            'jam_masuk' => '08:10:00',
            'jam_keluar' => null,
            'status' => 'Terlambat',
        ]);
        // Rian sudah masuk
        Absensi::create([
            'karyawan_id' => $karyawanModels[3]->id,
            'tanggal' => $today,
            'jam_masuk' => '07:45:00',
            'jam_keluar' => null,
            'status' => 'Tepat Waktu',
        ]);

        // 6. Transaksi Sample Data (7 hari terakhir + hari ini)
        $paymentMethods = ['tunai', 'debit', 'qris'];

        for ($day = 6; $day >= 0; $day--) {
            $transDate = Carbon::today()->subDays($day);
            $transCount = ($day === 0) ? 5 : rand(4, 7);

            for ($t = 1; $t <= $transCount; $t++) {
                $hour = rand(9, 21);
                $minute = rand(0, 59);
                $dt = $transDate->copy()->setHour($hour)->setMinute($minute)->setSecond(rand(0, 59));
                $method = $paymentMethods[array_rand($paymentMethods)];

                // Pilih 1 - 3 menu acak
                $selectedMenuKeys = (array) array_rand($createdMenus, rand(1, 3));
                $totalTrans = 0;
                $details = [];

                foreach ($selectedMenuKeys as $mk) {
                    $item = $createdMenus[$mk];
                    $qty = rand(1, 2);
                    $subtotal = $item->harga * $qty;
                    $totalTrans += $subtotal;
                    $details[] = [
                        'menu_id' => $item->id,
                        'jumlah' => $qty,
                        'harga' => $item->harga,
                        'subtotal' => $subtotal,
                    ];
                }

                $uangDiterima = null;
                $kembalian = null;
                if ($method === 'tunai') {
                    // Bulatkan ke kelipatan 10000 atau 50000 di atas total
                    $uangDiterima = (int) (ceil($totalTrans / 10000) * 10000);
                    if ($uangDiterima < $totalTrans) {
                        $uangDiterima = $totalTrans;
                    }
                    $kembalian = $uangDiterima - $totalTrans;
                }

                $trans = Transaksi::create([
                    'user_id' => $kasir->id,
                    'nomor_transaksi' => 'TRK-'.$dt->format('Ymd').'-'.str_pad((string) rand(100, 999), 3, '0', STR_PAD_LEFT).rand(10, 99),
                    'tanggal' => $dt,
                    'total' => $totalTrans,
                    'metode_pembayaran' => $method,
                    'uang_diterima' => $uangDiterima,
                    'kembalian' => $kembalian,
                ]);

                foreach ($details as $d) {
                    DetailTransaksi::create([
                        'transaksi_id' => $trans->id,
                        'menu_id' => $d['menu_id'],
                        'jumlah' => $d['jumlah'],
                        'harga' => $d['harga'],
                        'subtotal' => $d['subtotal'],
                    ]);
                }
            }
        }

        // 7. Sample Chatbot Logs
        ChatbotLog::create([
            'nomor_whatsapp' => '081234567890',
            'pesan_pengguna' => 'Menu makanan yang ada apa saja ya min?',
            'respons_chatbot' => "Halo! Di Cafe Resto Kopi Kutho saat ini tersedia:\n- Ayam Geprek Sambal Bawang Kutho (Rp 15.000)\n- Nasi Goreng Spesial Kopi Kutho (Rp 18.000)\n- Chicken Katsu Sambal Matah Bowl (Rp 23.000)\n- Beef Teriyaki Rice Bowl (Rp 28.000)\n- Mie Goreng Jawa Kutho (Rp 17.000)\nAda yang ingin dipesan kak?",
            'waktu' => Carbon::now()->subHours(3),
        ]);

        ChatbotLog::create([
            'nomor_whatsapp' => '085712345678',
            'pesan_pengguna' => 'Ada makanan di bawah 20 ribu kak?',
            'respons_chatbot' => "Tentu ada kak! Menu makanan di bawah Rp 20.000 di Kopi Kutho:\n- Ayam Geprek Sambal Bawang Kutho (Rp 15.000)\n- Mie Goreng Jawa Kutho (Rp 17.000)\n- Nasi Goreng Spesial Kopi Kutho (Rp 18.000)\nSemuanya porsi mengenyangkan dan lezat!",
            'waktu' => Carbon::now()->subHours(2),
        ]);

        ChatbotLog::create([
            'nomor_whatsapp' => '089876543210',
            'pesan_pengguna' => 'Apakah Kopi Kutho hari ini buka jam berapa?',
            'respons_chatbot' => 'Cafe Resto Kopi Kutho buka setiap hari mulai pukul 09.00 - 23.00 WIB. Silakan mampir kak!',
            'waktu' => Carbon::now()->subHour(),
        ]);
    }
}
