<?php

namespace App\Controllers;

use App\Libraries\CartService;
use App\Libraries\OrderService;
use App\Models\AddressModel;
use App\Models\UserModel;
use RuntimeException;

/**
 * Checkout: keranjang → pesanan (Praktikum 9).
 */
class CheckoutController extends BaseController
{
    public function index()
    {
        $summary = (new CartService())->summary($this->userId());
        if (! $summary['items']) {
            return redirect()->to('/cart')->with('error', 'Keranjang masih kosong.');
        }
        if ($summary['problems']) {
            return redirect()->to('/cart')->with('error', 'Perbaiki dulu item yang bermasalah sebelum checkout.');
        }

        return view('frontend/checkout', [
            'title'     => 'Checkout',
            'addresses' => (new AddressModel())->forUser($this->userId()),
            'user'      => (new UserModel())->find($this->userId()),
            'couriers'  => config('Shop')->couriers,
            'methods'   => config('Shop')->paymentMethods,
        ] + $summary);
    }

    public function process()
    {
        $shop = config('Shop');
        $addresses = new AddressModel();
        $addressId = $this->request->getPost('address_id');

        $rules = [
            'courier'        => ['label' => 'Kurir', 'rules' => 'required|in_list[' . implode(',', array_keys($shop->couriers)) . ']'],
            'payment_method' => ['label' => 'Metode pembayaran', 'rules' => 'required|in_list[' . implode(',', array_keys($shop->paymentMethods)) . ']'],
            'notes'          => ['label' => 'Catatan', 'rules' => 'permit_empty|max_length[255]'],
        ];
        if ($addressId === 'new' || $addressId === null || $addressId === '') {
            $rules += [
                'recipient_name' => ['label' => 'Nama penerima', 'rules' => 'required|min_length[3]|max_length[100]'],
                'phone'          => ['label' => 'Nomor telepon', 'rules' => 'required|regex_match[/^[0-9+\-\s]{8,20}$/]'],
                'address'        => ['label' => 'Alamat', 'rules' => 'required|min_length[10]'],
                'city'           => ['label' => 'Kota', 'rules' => 'required|max_length[100]'],
                'postal_code'    => ['label' => 'Kode pos', 'rules' => 'permit_empty|numeric|max_length[10]'],
            ];
        }
        if (! $this->validate($rules)) {
            return $this->backWithErrors();
        }

        // Alamat: pilih yang tersimpan (wajib milik user ini) atau alamat baru.
        if ($addressId !== 'new' && $addressId !== null && $addressId !== '') {
            $addr = $addresses->where('user_id', $this->userId())->find((int) $addressId);
            if (! $addr) {
                return $this->backWithErrors('Alamat tidak ditemukan.');
            }
        } else {
            $addr = [
                'recipient_name' => trim($this->request->getPost('recipient_name')),
                'phone'          => trim($this->request->getPost('phone')),
                'address'        => trim($this->request->getPost('address')),
                'city'           => trim($this->request->getPost('city')),
                'postal_code'    => trim((string) $this->request->getPost('postal_code')),
            ];
            if ($this->request->getPost('save_address')) {
                $isFirst = $addresses->where('user_id', $this->userId())->countAllResults() === 0;
                $addresses->insert($addr + ['user_id' => $this->userId(), 'label' => 'Alamat ' . date('d/m'), 'is_default' => $isFirst ? 1 : 0]);
            }
        }

        try {
            $order = (new OrderService())->checkout(
                $this->userId(),
                ['recipient_name' => $addr['recipient_name'], 'phone' => $addr['phone'], 'address' => AddressModel::format($addr)],
                $this->request->getPost('courier'),
                $this->request->getPost('payment_method'),
                trim((string) $this->request->getPost('notes'))
            );
        } catch (RuntimeException $e) {
            return redirect()->to('/cart')->with('error', $e->getMessage());
        }

        return redirect()->to('/orders/' . $order['order_number'])
            ->with('success', 'Pesanan ' . $order['order_number'] . ' berhasil dibuat. Silakan lakukan pembayaran.');
    }
}
