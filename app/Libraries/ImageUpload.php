<?php

namespace App\Libraries;

use CodeIgniter\HTTP\Files\UploadedFile;
use RuntimeException;

/**
 * Menyimpan file gambar yang sudah lolos validasi CodeIgniter
 * (uploaded, max_size, is_image, mime_in, ext_in) dengan nama acak.
 */
class ImageUpload
{
    public const ALLOWED_MIME = ['image/jpeg', 'image/png', 'image/webp'];

    /** Aturan validasi standar untuk satu field file gambar. */
    public static function rules(string $field, string $label, bool $required): array
    {
        $kb = config('Shop')->maxImageKb;
        $r  = ($required ? "uploaded[{$field}]|" : '')
            . "max_size[{$field},{$kb}]|is_image[{$field}]|mime_in[{$field},image/jpeg,image/png,image/webp]|ext_in[{$field},jpg,jpeg,png,webp]";

        return [$field => ['label' => $label, 'rules' => $r]];
    }

    /**
     * Memindahkan file ke $dir dan mengembalikan nama file acak.
     * Mengembalikan null bila tidak ada file yang dipilih.
     */
    public static function store(?UploadedFile $file, string $dir): ?string
    {
        if ($file === null || $file->getError() === UPLOAD_ERR_NO_FILE) {
            return null;
        }
        if (! $file->isValid() || $file->hasMoved()) {
            throw new RuntimeException('Upload gagal: ' . $file->getErrorString());
        }
        // Pengecekan ulang MIME berdasarkan isi file (bukan dari ekstensi/nama).
        if (! in_array($file->getMimeType(), self::ALLOWED_MIME, true)) {
            throw new RuntimeException('Format gambar tidak valid.');
        }
        if (! is_dir($dir)) {
            mkdir($dir, 0755, true);
        }
        $name = $file->getRandomName();
        $file->move($dir, $name);

        return $name;
    }

    public static function delete(?string $name, string $dir): void
    {
        if ($name && is_file($path = rtrim($dir, '/\\') . DIRECTORY_SEPARATOR . basename($name))) {
            @unlink($path);
        }
    }
}
