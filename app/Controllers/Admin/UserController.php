<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\UserModel;
use CodeIgniter\Exceptions\PageNotFoundException;

/**
 * Manajemen pengguna & role (hanya admin). Akun admin/operator diberikan lewat
 * halaman ini, tidak pernah lewat formulir registrasi publik (modul Authentication, langkah 13).
 */
class UserController extends BaseController
{
    public function index()
    {
        $role = (string) $this->request->getGet('role');
        $q = trim((string) $this->request->getGet('q'));
        $model = (new UserModel())
            ->select('users.*')
            ->select('(SELECT COUNT(*) FROM orders o WHERE o.user_id = users.id_user) AS order_count', false);
        if (in_array($role, ['customer', 'admin', 'operator'], true)) {
            $model->where('role', $role);
        }
        if ($q !== '') {
            $model->groupStart()->like('name', $q)->orLike('email', $q)->groupEnd();
        }

        return view('admin/users/index', [
            'title' => 'Pengguna',
            'users' => $model->orderBy('role')->orderBy('name')->paginate(15),
            'pager' => $model->pager,
            'role'  => $role,
            'q'     => $q,
        ]);
    }

    public function updateRole(int $id)
    {
        $users = new UserModel();
        $user = $users->find($id) ?? throw new PageNotFoundException();
        $role = (string) $this->request->getPost('role');
        if (! in_array($role, ['customer', 'admin', 'operator'], true)) {
            return redirect()->back()->with('error', 'Role tidak valid.');
        }
        if ($id === $this->userId()) {
            return redirect()->back()->with('error', 'Anda tidak dapat mengubah role akun sendiri.');
        }
        $users->update($id, ['role' => $role]);

        return redirect()->back()->with('success', "Role {$user['name']} diubah menjadi " . role_label($role) . '.');
    }
}
