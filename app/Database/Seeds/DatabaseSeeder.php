<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

/**
 * Data contoh ShopEase (semua data fiktif, bukan data pribadi nyata).
 * Jalankan: php spark db:seed DatabaseSeeder
 *
 * Akun demo (password semuanya: shopease123)
 *   admin@shopease.com      -> admin
 *   operator@shopease.com   -> operator
 *   budi@example.com        -> customer
 *   sari@example.com        -> customer
 *   rina@example.com        -> customer
 */
class DatabaseSeeder extends Seeder
{
    private string $now;

    public function run()
    {
        mt_srand(20261125); // data acak tetapi selalu sama setiap seeding
        $this->now = date('Y-m-d H:i:s');
        $db = $this->db;

        // ---------------------------------------------------------------- users
        $hash  = password_hash('shopease123', PASSWORD_DEFAULT);
        $users = [
            ['Administrator ShopEase', 'admin@shopease.com', 'admin', '081200000001', 'Kantor ShopEase, Surabaya'],
            ['Operator Gudang', 'operator@shopease.com', 'operator', '081200000002', 'Gudang ShopEase, Surabaya'],
            ['Budi Pratama', 'budi@example.com', 'customer', '081234567801', 'Jl. Contoh Raya No. 10, Surabaya'],
            ['Sari Wulandari', 'sari@example.com', 'customer', '081234567802', 'Jl. Contoh Indah No. 22, Sidoarjo'],
            ['Rina Kartika', 'rina@example.com', 'customer', '081234567803', 'Jl. Contoh Baru No. 5, Malang'],
        ];
        $uid = [];
        foreach ($users as [$name, $email, $role, $phone, $address]) {
            $db->table('users')->insert([
                'name' => $name, 'email' => $email, 'password' => $hash, 'role' => $role,
                'phone' => $phone, 'address' => $address,
                'created_at' => date('Y-m-d H:i:s', strtotime('-40 days')), 'updated_at' => $this->now,
            ]);
            $uid[$email] = (int) $db->insertID();
        }

        // ------------------------------------------------------------ addresses
        $addr = [
            'budi@example.com' => ['Rumah', 'Budi Pratama', '081234567801', 'Jl. Contoh Raya No. 10, Kel. Keputih', 'Surabaya', '60111'],
            'sari@example.com' => ['Kos', 'Sari Wulandari', '081234567802', 'Jl. Contoh Indah No. 22', 'Sidoarjo', '61219'],
            'rina@example.com' => ['Rumah', 'Rina Kartika', '081234567803', 'Jl. Contoh Baru No. 5', 'Malang', '65145'],
        ];
        foreach ($addr as $email => [$label, $rec, $phone, $street, $city, $zip]) {
            $db->table('addresses')->insert([
                'user_id' => $uid[$email], 'label' => $label, 'recipient_name' => $rec, 'phone' => $phone,
                'address' => $street, 'city' => $city, 'postal_code' => $zip, 'is_default' => 1,
                'created_at' => $this->now, 'updated_at' => $this->now,
            ]);
        }

        // ----------------------------------------------------------- categories
        $cats = [
            ['Elektronik', 'elektronik', 'Gadget dan perangkat elektronik sehari-hari.'],
            ['Fashion', 'fashion', 'Pakaian dan alas kaki untuk berbagai gaya.'],
            ['Aksesoris', 'aksesoris', 'Pelengkap penampilan dan kebutuhan harian.'],
            ['Rumah Tangga', 'rumah-tangga', 'Perlengkapan rumah dan dapur.'],
            ['Olahraga', 'olahraga', 'Peralatan untuk hidup aktif dan sehat.'],
        ];
        $cid = [];
        foreach ($cats as [$name, $slug, $desc]) {
            $db->table('categories')->insert(['name' => $name, 'slug' => $slug, 'description' => $desc, 'created_at' => $this->now, 'updated_at' => $this->now]);
            $cid[$slug] = (int) $db->insertID();
        }

        // -------------------------------------------------------------- products
        // [kategori, nama, deskripsi, harga, stok, gambar, status]
        $products = [
            ['elektronik', 'Wireless Headphone', 'Headphone Bluetooth dengan bantalan empuk dan baterai hingga 30 jam.', 299000, 20, 'wireless-headphone.jpg'],
            ['fashion', 'Sneakers Casual', 'Sepatu casual pria berbahan kanvas, nyaman dipakai seharian.', 350000, 15, 'sneakers-casual.jpg'],
            ['aksesoris', 'Smart Watch', 'Jam tangan digital dengan pemantau detak jantung dan notifikasi.', 450000, 10, 'smart-watch.jpg'],
            ['elektronik', 'Bluetooth Speaker Mini', 'Speaker portabel tahan cipratan air dengan suara jernih.', 189000, 25, 'bluetooth-speaker.jpg'],
            ['elektronik', 'Mechanical Keyboard', 'Keyboard mekanik 87 tombol dengan switch taktil.', 549000, 12, 'mechanical-keyboard.jpg'],
            ['elektronik', 'Wireless Mouse', 'Mouse nirkabel senyap, ergonomis, dan hemat baterai.', 129000, 30, 'wireless-mouse.jpg'],
            ['elektronik', 'Power Bank 10.000 mAh', 'Pengisian cepat 18W dengan dua port USB.', 219000, 18, 'power-bank.jpg'],
            ['elektronik', 'Earbuds TWS', 'Earbuds true wireless dengan case pengisi daya.', 259000, 22, 'earbuds-tws.jpg'],
            ['fashion', 'Kaos Polos Premium', 'Kaos katun combed 30s, adem dan tidak mudah melar.', 89000, 50, 'kaos-polos.jpg'],
            ['fashion', 'Jaket Denim', 'Jaket denim klasik dengan potongan regular fit.', 399000, 8, 'jaket-denim.jpg'],
            ['fashion', 'Kemeja Flanel', 'Kemeja flanel motif kotak, cocok untuk santai maupun kuliah.', 179000, 20, 'kemeja-flanel.jpg'],
            ['fashion', 'Celana Chino', 'Celana chino slim fit berbahan stretch.', 229000, 14, 'celana-chino.jpg'],
            ['aksesoris', 'Kacamata Hitam', 'Kacamata hitam dengan lensa UV400.', 149000, 16, 'kacamata-hitam.jpg'],
            ['aksesoris', 'Dompet Kulit', 'Dompet lipat dari kulit sintetis dengan banyak slot kartu.', 199000, 12, 'dompet-kulit.jpg'],
            ['aksesoris', 'Topi Baseball', 'Topi baseball katun dengan tali pengatur.', 79000, 30, 'topi-baseball.jpg'],
            ['aksesoris', 'Tas Ransel Laptop', 'Ransel laptop 15 inci dengan kompartemen empuk.', 329000, 9, 'tas-ransel.jpg'],
            ['rumah-tangga', 'Tumbler Stainless 750 ml', 'Menjaga minuman tetap dingin hingga 24 jam.', 119000, 40, 'tumbler-stainless.jpg'],
            ['rumah-tangga', 'Lampu Meja LED', 'Lampu belajar LED dengan tiga tingkat kecerahan.', 159000, 15, 'lampu-meja.jpg'],
            ['rumah-tangga', 'Set Pisau Dapur', 'Set tiga pisau dapur stainless anti karat.', 249000, 7, 'set-pisau.jpg'],
            ['olahraga', 'Matras Yoga 6 mm', 'Matras yoga anti selip, ringan, dan mudah digulung.', 189000, 20, 'matras-yoga.jpg'],
            ['olahraga', 'Dumbbell 2 x 3 kg', 'Sepasang dumbbell berlapis vinyl.', 210000, 10, 'dumbbell.jpg'],
            ['olahraga', 'Botol Minum Sport', 'Botol 1 liter bebas BPA dengan penanda waktu minum.', 69000, 35, 'botol-sport.jpg'],
            ['olahraga', 'Tali Skipping', 'Tali skipping dengan bearing, cocok untuk kardio.', 49000, 0, 'tali-skipping.jpg'],
            ['elektronik', 'Kamera Aksi 4K', 'Kamera aksi tahan air. Sementara tidak dijual.', 899000, 5, 'kamera-aksi.jpg', 'inactive'],
        ];
        $prod = [];
        foreach ($products as $i => $p) {
            $created = date('Y-m-d H:i:s', strtotime('-' . (45 - $i) . ' days'));
            $db->table('products')->insert([
                'category_id' => $cid[$p[0]], 'name' => $p[1], 'description' => $p[2], 'price' => $p[3],
                'stock' => $p[4], 'image' => $p[5], 'status' => $p[6] ?? 'active',
                'created_at' => $created, 'updated_at' => $created,
            ]);
            $prod[] = ['id' => (int) $db->insertID(), 'name' => $p[1], 'price' => $p[3], 'active' => ($p[6] ?? 'active') === 'active' && $p[4] > 0];
        }
        $jual = array_values(array_filter($prod, static fn ($p) => $p['active']));

        // ---------------------------------------------------------------- orders
        // 26 pesanan selama 20 hari terakhir dengan status yang beragam.
        $customers = [$uid['budi@example.com'], $uid['sari@example.com'], $uid['rina@example.com']];
        $addrText  = [
            $uid['budi@example.com'] => ['Budi Pratama', '081234567801', "Jl. Contoh Raya No. 10, Kel. Keputih\nSurabaya 60111"],
            $uid['sari@example.com'] => ['Sari Wulandari', '081234567802', "Jl. Contoh Indah No. 22\nSidoarjo 61219"],
            $uid['rina@example.com'] => ['Rina Kartika', '081234567803', "Jl. Contoh Baru No. 5\nMalang 65145"],
        ];
        $couriers = config('Shop')->couriers;
        $courierKeys = array_keys($couriers);
        $methods = array_keys(config('Shop')->paymentMethods);

        for ($n = 1; $n <= 26; $n++) {
            $daysAgo = 20 - (int) floor(($n - 1) * 20 / 26);
            $created = strtotime("-{$daysAgo} days") + mt_rand(8 * 3600, 20 * 3600) - 12 * 3600;
            $created = min($created, time() - 3600);

            // Status ditentukan dari umur pesanan: yang lama sudah selesai, yang baru masih berjalan.
            $status = match (true) {
                $n === 9                  => 'cancelled',
                $n <= 15                  => 'completed',
                $n <= 19                  => 'shipped',
                $n <= 22                  => 'processing',
                $n <= 24                  => 'waiting_verification',
                default                   => 'pending_payment',
            };

            $user = $customers[$n % 3];
            $items = [];
            $count = mt_rand(1, 3);
            $picked = (array) array_rand($jual, $count);
            foreach ($picked as $k) {
                $p = $jual[$k];
                $qty = mt_rand(1, 2);
                $items[] = ['product_id' => $p['id'], 'product_name' => $p['name'], 'price' => $p['price'], 'quantity' => $qty, 'subtotal' => $p['price'] * $qty];
            }
            $subtotal = array_sum(array_column($items, 'subtotal'));
            $ck = $courierKeys[$n % count($courierKeys)];
            $ship = $couriers[$ck]['cost'];
            $orderNo = 'SE-' . date('Ymd', $created) . '-' . str_pad((string) $n, 4, '0', STR_PAD_LEFT);
            [$rec, $phone, $address] = $addrText[$user];

            $db->table('orders')->insert([
                'user_id' => $user, 'order_number' => $orderNo, 'recipient_name' => $rec, 'phone' => $phone,
                'shipping_address' => $address, 'courier' => $couriers[$ck]['name'], 'shipping_cost' => $ship,
                'subtotal' => $subtotal, 'total' => $subtotal + $ship, 'order_status' => $status,
                'notes' => $status === 'cancelled' ? 'Dibatalkan oleh customer.' : null,
                'created_at' => date('Y-m-d H:i:s', $created), 'updated_at' => date('Y-m-d H:i:s', $created + 86400),
            ]);
            $orderId = (int) $db->insertID();
            foreach ($items as $it) {
                $db->table('order_items')->insert($it + ['order_id' => $orderId]);
            }

            // Pembayaran
            $payStatus = match ($status) {
                'pending_payment'      => 'unpaid',
                'waiting_verification' => 'submitted',
                'cancelled'            => 'unpaid',
                default                => 'verified',
            };
            $db->table('payments')->insert([
                'order_id' => $orderId, 'method' => $methods[$n % 2], 'amount' => $subtotal + $ship,
                'proof_file' => $payStatus === 'unpaid' ? null : 'contoh-bukti-' . (($n % 3) + 1) . '.jpg',
                'payment_status' => $payStatus,
                'submitted_at' => $payStatus === 'unpaid' ? null : date('Y-m-d H:i:s', $created + 3 * 3600),
                'verified_by' => $payStatus === 'verified' ? $uid[$n % 2 ? 'admin@shopease.com' : 'operator@shopease.com'] : null,
                'verified_at' => $payStatus === 'verified' ? date('Y-m-d H:i:s', $created + 6 * 3600) : null,
            ]);

            // Pengiriman
            $shipStatus = match ($status) {
                'shipped'   => 'shipped',
                'completed' => 'delivered',
                default     => 'pending',
            };
            $db->table('shipments')->insert([
                'order_id' => $orderId, 'courier' => $couriers[$ck]['name'],
                'tracking_number' => in_array($shipStatus, ['shipped', 'delivered'], true) ? strtoupper($ck) . 'DEMO' . str_pad((string) (1000 + $n * 37), 6, '0', STR_PAD_LEFT) : null,
                'shipment_status' => $shipStatus,
                'shipped_at' => $shipStatus !== 'pending' ? date('Y-m-d H:i:s', $created + 30 * 3600) : null,
                'delivered_at' => $shipStatus === 'delivered' ? date('Y-m-d H:i:s', $created + 3 * 86400) : null,
            ]);
        }

        // -------------------------------------------------------------- keranjang
        // Budi sudah memasukkan dua produk ke keranjang.
        $db->table('carts')->insert(['user_id' => $uid['budi@example.com'], 'created_at' => $this->now, 'updated_at' => $this->now]);
        $cart = (int) $db->insertID();
        foreach ([[$prod[0]['id'], 1], [$prod[8]['id'], 2]] as [$pid, $q]) {
            $db->table('cart_items')->insert(['cart_id' => $cart, 'product_id' => $pid, 'quantity' => $q, 'created_at' => $this->now, 'updated_at' => $this->now]);
        }
    }
}
