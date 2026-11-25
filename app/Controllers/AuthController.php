<?php

namespace App\Controllers;

use App\Models\UserModel;

/**
 * Registrasi, login, dan logout (Hands-On Lab Authentication System, langkah 4).
 * Struktur method, aturan validasi, dan pesan error mengikuti modul. Tambahan dari
 * "Catatan Keamanan": pembatasan percobaan login dan pengalihan ke halaman tujuan.
 */
class AuthController extends BaseController
{
    protected $userModel;

    public function __construct()
    {
        $this->userModel = new UserModel();
    }

    // Menampilkan halaman registrasi
    public function register()
    {
        return view('auth/register');
    }

    // Menyimpan akun baru
    public function store()
    {
        $rules = [
            'name' => [
                'rules'  => 'required|min_length[3]|max_length[100]',
                'errors' => [
                    'required'   => 'Nama wajib diisi.',
                    'min_length' => 'Nama minimal 3 karakter.',
                    'max_length' => 'Nama maksimal 100 karakter.',
                ],
            ],
            'email' => [
                'rules'  => 'required|valid_email|max_length[100]|is_unique[users.email]',
                'errors' => [
                    'required'    => 'Email wajib diisi.',
                    'valid_email' => 'Format email tidak valid.',
                    'is_unique'   => 'Email sudah terdaftar.',
                ],
            ],
            'phone' => [
                'rules'  => 'permit_empty|regex_match[/^[0-9+\-\s]{8,20}$/]',
                'errors' => ['regex_match' => 'Nomor telepon tidak valid.'],
            ],
            'password' => [
                'rules'  => 'required|min_length[8]|max_length[72]',
                'errors' => [
                    'required'   => 'Password wajib diisi.',
                    'min_length' => 'Password minimal 8 karakter.',
                ],
            ],
            'password_confirm' => [
                'rules'  => 'required|matches[password]',
                'errors' => [
                    'required' => 'Konfirmasi password wajib diisi.',
                    'matches'  => 'Konfirmasi password tidak sama.',
                ],
            ],
        ];

        // Email dinormalisasi lebih dulu agar is_unique tidak tertipu huruf besar/spasi.
        $post = $this->request->getPost();
        $post['email'] = strtolower(trim((string) ($post['email'] ?? '')));

        if (! $this->validateData($post, $rules)) {
            return view('auth/register', [
                'validation' => $this->validator,
            ]);
        }

        $this->userModel->insert([
            'name'     => trim($this->request->getPost('name')),
            'email'    => $post['email'],
            'password' => password_hash(
                $this->request->getPost('password'),
                PASSWORD_DEFAULT
            ),
            'role'    => 'customer', // registrasi publik selalu customer
            'phone'   => trim((string) $this->request->getPost('phone')) ?: null,
            'address' => trim((string) $this->request->getPost('address')) ?: null,
        ]);

        return redirect()->to('/login')
            ->with('success', 'Registrasi berhasil. Silakan login.');
    }

    // Menampilkan halaman login
    public function login()
    {
        return view('auth/login');
    }

    // Memproses login
    public function authenticate()
    {
        $rules = [
            'email'    => ['rules' => 'required|valid_email', 'errors' => ['required' => 'Email wajib diisi.', 'valid_email' => 'Format email tidak valid.']],
            'password' => ['rules' => 'required', 'errors' => ['required' => 'Password wajib diisi.']],
        ];

        if (! $this->validate($rules)) {
            return view('auth/login', [
                'validation' => $this->validator,
            ]);
        }

        $email    = strtolower(trim($this->request->getPost('email')));
        $password = $this->request->getPost('password');

        // Rate limiting: maksimal 5 percobaan per menit untuk kombinasi IP + email.
        $throttler = service('throttler');
        $key = 'login-' . md5($this->request->getIPAddress() . '|' . $email);
        if ($throttler->check($key, 5, MINUTE) === false) {
            return redirect()->back()->withInput()
                ->with('error', 'Terlalu banyak percobaan login. Coba lagi dalam ' . $throttler->getTokenTime() . ' detik.');
        }

        $user = $this->userModel
            ->where('email', $email)
            ->first();

        if (
            ! $user
            || ! password_verify($password, $user['password'])
        ) {
            return redirect()->back()
                ->withInput()
                ->with('error', 'Email atau password salah.');
        }

        // Simpan tujuan awal sebelum session diregenerasi.
        $tujuan = session('redirect_after_login');

        // Regenerasi session ID setelah autentikasi berhasil
        session()->regenerate(true);

        session()->set([
            'isLoggedIn' => true,
            'user_id'    => $user['id_user'],
            'name'       => $user['name'],
            'email'      => $user['email'],
            'role'       => $user['role'],
        ]);
        session()->remove('redirect_after_login');

        // Customer dikembalikan ke halaman yang tadi ingin dibuka (mis. keranjang).
        if ($user['role'] === 'customer' && is_string($tujuan) && str_starts_with($tujuan, base_url()) && ! str_contains($tujuan, '/login')) {
            return redirect()->to($tujuan);
        }

        // Redirect sesuai role
        switch ($user['role']) {
            case 'admin':
                return redirect()->to('/admin');

            case 'operator':
                return redirect()->to('/operator');

            default:
                return redirect()->to('/');
        }
    }

    // Mengakhiri session
    public function logout()
    {
        // Modul memakai session()->destroy(). Di sini data login dihapus lalu ID session
        // diganti (session lama ikut dihapus dari server) — efeknya sama, tetapi pesan
        // flash "berhasil logout" masih bisa dibawa ke halaman login.
        $session = session();
        $session->remove(['isLoggedIn', 'user_id', 'name', 'email', 'role', 'redirect_after_login']);
        $session->regenerate(true);

        return redirect()->to('/login')
            ->with('success', 'Anda berhasil logout.');
    }
}
