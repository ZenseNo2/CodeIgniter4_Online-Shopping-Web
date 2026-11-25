<?php

/**
 * Fungsi bantu ShopEase. Dimuat otomatis lewat BaseController::$helpers,
 * sehingga tersedia di semua controller dan view.
 */

// ------------------------------------------------------------------ sesi / user

if (! function_exists('is_logged_in')) {
    function is_logged_in(): bool
    {
        return (bool) session('isLoggedIn');
    }
}

if (! function_exists('current_user')) {
    /** Data user dari session (kunci sesuai modul: user_id, name, email, role). */
    function current_user(?string $key = null): mixed
    {
        if (! is_logged_in()) {
            return null;
        }
        $user = [
            'user_id' => session('user_id'),
            'name'    => session('name'),
            'email'   => session('email'),
            'role'    => session('role'),
        ];

        return $key === null ? $user : ($user[$key] ?? null);
    }
}

if (! function_exists('is_staff')) {
    /** Admin atau operator. */
    function is_staff(): bool
    {
        return in_array(session('role'), ['admin', 'operator'], true);
    }
}

if (! function_exists('role_home')) {
    /** Halaman awal setelah login, sesuai role (modul Authentication). */
    function role_home(?string $role = null): string
    {
        return match ($role ?? session('role')) {
            'admin'    => '/admin',
            'operator' => '/operator',
            default    => '/',
        };
    }
}

if (! function_exists('role_label')) {
    function role_label(?string $role): string
    {
        return ['admin' => 'Administrator', 'operator' => 'Operator', 'customer' => 'Customer'][$role] ?? '';
    }
}

if (! function_exists('cart_count')) {
    /** Jumlah total barang di keranjang customer yang sedang login (untuk badge). */
    function cart_count(): int
    {
        if (! is_logged_in() || session('role') !== 'customer') {
            return 0;
        }
        static $count = null;
        if ($count === null) {
            $row = db_connect()->table('cart_items ci')
                ->selectSum('ci.quantity', 'qty')
                ->join('carts c', 'c.id = ci.cart_id')
                ->where('c.user_id', session('user_id'))
                ->get()->getRowArray();
            $count = (int) ($row['qty'] ?? 0);
        }

        return $count;
    }
}

// ------------------------------------------------------------------- format

if (! function_exists('rupiah')) {
    /** 299000 → "Rp299.000" */
    function rupiah(float|int|string|null $n): string
    {
        return 'Rp' . number_format((float) $n, 0, ',', '.');
    }
}

if (! function_exists('tanggal_id')) {
    /** "2026-11-25 10:15:00" → "25 Nov 2026" (atau dengan jam). */
    function tanggal_id(?string $datetime = null, bool $withTime = false): string
    {
        if ($datetime === null || $datetime === '') {
            return '–';
        }
        $bulan = [1 => 'Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun', 'Jul', 'Agu', 'Sep', 'Okt', 'Nov', 'Des'];
        $t = strtotime($datetime);
        $s = date('j', $t) . ' ' . $bulan[(int) date('n', $t)] . ' ' . date('Y', $t);

        return $withTime ? $s . ', ' . date('H.i', $t) : $s;
    }
}

if (! function_exists('hari_ini_id')) {
    function hari_ini_id(): string
    {
        $hari  = [1 => 'Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu', 'Minggu'];
        $bulan = [1 => 'Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni', 'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'];

        return $hari[(int) date('N')] . ', ' . date('j') . ' ' . $bulan[(int) date('n')] . ' ' . date('Y');
    }
}

if (! function_exists('status_badge')) {
    /**
     * Badge berwarna untuk status. $type: order | payment | shipment.
     */
    function status_badge(string $type, ?string $status): string
    {
        $map = match ($type) {
            'payment'  => config('Shop')->paymentStatusLabels,
            'shipment' => config('Shop')->shipmentStatusLabels,
            default    => config('Shop')->orderStatusLabels,
        };
        [$label, $color] = $map[$status] ?? [ucfirst((string) $status), 'gray'];

        return '<span class="badge ' . $color . '">' . esc($label) . '</span>';
    }
}

if (! function_exists('product_image')) {
    /** URL gambar produk, dengan cadangan bila gambar belum ada. */
    function product_image(?string $file): string
    {
        if ($file && is_file(FCPATH . 'uploads/products/' . $file)) {
            return base_url('uploads/products/' . rawurlencode($file));
        }

        return base_url('assets/img/no-image.svg');
    }
}

if (! function_exists('inisial')) {
    function inisial(string $nama): string
    {
        $kata = preg_split('/\s+/', trim($nama)) ?: [''];
        $hasil = mb_substr($kata[0], 0, 1) . (count($kata) > 1 ? mb_substr(end($kata), 0, 1) : '');

        return mb_strtoupper($hasil);
    }
}

// --------------------------------------------------------------------- UI

if (! function_exists('icon')) {
    /** Ikon dari SVG sprite (partials/sprite.php). */
    function icon(string $name, string $class = ''): string
    {
        return '<svg class="icon ' . esc($class, 'attr') . '" aria-hidden="true"><use href="#i-' . esc($name, 'attr') . '"></use></svg>';
    }
}

if (! function_exists('menu_active')) {
    /** "is-active" bila URL sekarang = $path atau berada di bawahnya. */
    function menu_active(string $path, bool $exact = false): string
    {
        $uri  = trim(uri_string(), '/');
        $path = trim($path, '/');
        if ($exact || $path === '') {
            return $uri === $path ? 'is-active' : '';
        }

        return ($uri === $path || str_starts_with($uri, $path . '/')) ? 'is-active' : '';
    }
}

if (! function_exists('field_value')) {
    function field_value(string $field, ?array $row = null, mixed $default = ''): string
    {
        return (string) old($field, $row[$field] ?? $default);
    }
}

if (! function_exists('validation_errors_array')) {
    /**
     * Error validasi dari dua sumber: $validation yang dikirim ke view (pola modul)
     * atau session setelah redirect()->withInput().
     */
    function validation_errors_array(): array
    {
        $fromView = service('renderer')->getData()['validation'] ?? null;
        if ($fromView instanceof \CodeIgniter\Validation\ValidationInterface) {
            return $fromView->getErrors();
        }
        $errors = session('_ci_validation_errors');

        return is_array($errors) ? $errors : [];
    }
}

if (! function_exists('field_error')) {
    function field_error(string $field): string
    {
        $msg = validation_errors_array()[$field] ?? null;

        return $msg ? '<p class="field-error">' . esc($msg) . '</p>' : '';
    }
}

if (! function_exists('has_error')) {
    function has_error(string $field): string
    {
        return isset(validation_errors_array()[$field]) ? 'has-error' : '';
    }
}

if (! function_exists('field_row')) {
    /** Satu baris form bergaya Settings iOS. */
    function field_row(string $name, string $label, string $control): string
    {
        return '<div class="row stack ' . has_error($name) . '">'
            . '<div class="field"><label for="' . esc($name, 'attr') . '">' . esc($label) . '</label>' . $control . '</div>'
            . field_error($name)
            . '</div>';
    }
}

if (! function_exists('select_options')) {
    function select_options(array $options, mixed $selected, ?string $placeholder = null): string
    {
        $html = $placeholder !== null ? '<option value="">' . esc($placeholder) . '</option>' : '';
        foreach ($options as $value => $label) {
            $sel = (string) $value === (string) $selected ? ' selected' : '';
            $html .= '<option value="' . esc((string) $value, 'attr') . '"' . $sel . '>' . esc($label) . '</option>';
        }

        return $html;
    }
}

if (! function_exists('query_with')) {
    /** URL halaman sekarang dengan parameter query yang diganti (untuk filter & sort). */
    function query_with(array $changes): string
    {
        $q = array_merge(service('request')->getGet() ?? [], $changes);
        $q = array_filter($q, static fn ($v) => $v !== null && $v !== '');
        unset($q['page']);

        return current_url() . ($q ? '?' . http_build_query($q) : '');
    }
}
