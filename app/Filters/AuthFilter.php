<?php

namespace App\Filters;

use CodeIgniter\Filters\FilterInterface;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;

/**
 * Membatasi halaman berdasarkan status login dan role (modul Authentication, langkah 9).
 *
 * Contoh di Routes:
 *   ['filter' => 'auth']                 → cukup login
 *   ['filter' => 'auth:admin']           → hanya admin
 *   ['filter' => 'auth:admin,operator']  → admin atau operator
 */
class AuthFilter implements FilterInterface
{
    public function before(RequestInterface $request, $arguments = null)
    {
        $session = session();

        if (! $session->get('isLoggedIn')) {
            // Ingat halaman tujuan agar setelah login pengguna dikembalikan ke sana.
            $query  = $request->getUri()->getQuery();
            $tujuan = strtolower($request->getMethod()) === 'get'
                ? current_url() . ($query !== '' ? '?' . $query : '')
                : (string) previous_url();
            $session->set('redirect_after_login', $tujuan);

            $session->setFlashdata('error', 'Silakan login terlebih dahulu.');

            return redirect()->to('/login');
        }

        // Role selalu dicocokkan ulang dengan database, sehingga perubahan role
        // atau penghapusan akun oleh admin langsung berlaku tanpa menunggu logout.
        $row = db_connect()->table('users')->select('role, name')
            ->where('id_user', $session->get('user_id'))->get()->getRowArray();
        if (! $row) {
            $session->destroy();

            return redirect()->to('/login');
        }
        if ($row['role'] !== $session->get('role')) {
            $session->set('role', $row['role']);
        }

        if (
            ! empty($arguments)
            && ! in_array($session->get('role'), $arguments, true)
        ) {
            $session->setFlashdata('error', 'Anda tidak memiliki akses ke halaman tersebut.');

            return redirect()->to(role_home());
        }
    }

    public function after(RequestInterface $request, ResponseInterface $response, $arguments = null)
    {
        // Halaman yang butuh login tidak disimpan di cache browser,
        // sehingga tombol Back setelah logout tidak menampilkan data lama.
        $response->setHeader('Cache-Control', 'no-store, no-cache, must-revalidate, max-age=0');
    }
}
