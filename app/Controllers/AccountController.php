<?php

namespace App\Controllers;

use App\Models\AddressModel;
use App\Models\OrderModel;
use App\Models\UserModel;

/**
 * Dashboard customer: profil, ringkasan aktivitas, alamat, dan ganti password
 * (tahap pengembangan 2 "Dashboard Customer").
 */
class AccountController extends BaseController
{
    public function index()
    {
        $uid = $this->userId();
        $db = db_connect();
        $stats = $db->table('orders')->select('COUNT(*) AS total_orders')
            ->select("SUM(CASE WHEN order_status IN ('processing','shipped','completed') THEN total ELSE 0 END) AS total_spent", false)
            ->select("SUM(CASE WHEN order_status IN ('pending_payment','waiting_verification','processing','shipped') THEN 1 ELSE 0 END) AS active_orders", false)
            ->where('user_id', $uid)->get()->getRowArray();

        return view('frontend/account', [
            'title'     => 'Akun Saya',
            'user'      => (new UserModel())->find($uid),
            'stats'     => $stats,
            'recent'    => (new OrderModel())->withStatus()->where('orders.user_id', $uid)->orderBy('orders.created_at', 'DESC')->findAll(3),
            'addresses' => (new AddressModel())->forUser($uid),
        ]);
    }

    public function updateProfile()
    {
        $rules = [
            'name'    => ['label' => 'Nama', 'rules' => 'required|min_length[3]|max_length[100]'],
            'phone'   => ['label' => 'Nomor telepon', 'rules' => 'permit_empty|regex_match[/^[0-9+\-\s]{8,20}$/]'],
            'address' => ['label' => 'Alamat', 'rules' => 'permit_empty|max_length[500]'],
        ];
        if (! $this->validate($rules)) {
            return $this->backWithErrors();
        }
        $name = trim($this->request->getPost('name'));
        (new UserModel())->update($this->userId(), [
            'name'    => $name,
            'phone'   => trim((string) $this->request->getPost('phone')) ?: null,
            'address' => trim((string) $this->request->getPost('address')) ?: null,
        ]);
        session()->set('name', $name);

        return redirect()->to('/account')->with('success', 'Profil diperbarui.');
    }

    public function updatePassword()
    {
        $rules = [
            'current_password' => ['label' => 'Password lama', 'rules' => 'required'],
            'password'         => ['label' => 'Password baru', 'rules' => 'required|min_length[8]|max_length[72]'],
            'password_confirm' => ['label' => 'Konfirmasi password', 'rules' => 'required|matches[password]', 'errors' => ['matches' => 'Konfirmasi password tidak sama.']],
        ];
        if (! $this->validate($rules)) {
            return $this->backWithErrors();
        }
        $users = new UserModel();
        $user = $users->find($this->userId());
        if (! password_verify((string) $this->request->getPost('current_password'), $user['password'])) {
            return $this->backWithErrors('Password lama tidak sesuai.');
        }
        $users->update($user['id_user'], ['password' => password_hash($this->request->getPost('password'), PASSWORD_DEFAULT)]);

        return redirect()->to('/account')->with('success', 'Password berhasil diganti.');
    }

    public function addAddress()
    {
        $rules = [
            'label'          => ['label' => 'Label', 'rules' => 'required|max_length[50]'],
            'recipient_name' => ['label' => 'Nama penerima', 'rules' => 'required|min_length[3]|max_length[100]'],
            'phone'          => ['label' => 'Nomor telepon', 'rules' => 'required|regex_match[/^[0-9+\-\s]{8,20}$/]'],
            'address'        => ['label' => 'Alamat', 'rules' => 'required|min_length[10]'],
            'city'           => ['label' => 'Kota', 'rules' => 'required|max_length[100]'],
            'postal_code'    => ['label' => 'Kode pos', 'rules' => 'permit_empty|numeric|max_length[10]'],
        ];
        if (! $this->validate($rules)) {
            return $this->backWithErrors();
        }
        $model = new AddressModel();
        $first = $model->where('user_id', $this->userId())->countAllResults() === 0;
        $model->insert([
            'user_id'        => $this->userId(),
            'label'          => trim($this->request->getPost('label')),
            'recipient_name' => trim($this->request->getPost('recipient_name')),
            'phone'          => trim($this->request->getPost('phone')),
            'address'        => trim($this->request->getPost('address')),
            'city'           => trim($this->request->getPost('city')),
            'postal_code'    => trim((string) $this->request->getPost('postal_code')) ?: null,
            'is_default'     => $first ? 1 : 0,
        ]);

        return redirect()->to('/account#alamat')->with('success', 'Alamat ditambahkan.');
    }

    public function defaultAddress(int $id)
    {
        $model = new AddressModel();
        if ($model->where('user_id', $this->userId())->find($id)) {
            $model->makeDefault($this->userId(), $id);
        }

        return redirect()->to('/account#alamat')->with('success', 'Alamat utama diperbarui.');
    }

    public function deleteAddress(int $id)
    {
        $model = new AddressModel();
        $addr = $model->where('user_id', $this->userId())->find($id);
        if ($addr) {
            $model->delete($id);
        }

        return redirect()->to('/account#alamat')->with('success', 'Alamat dihapus.');
    }
}
