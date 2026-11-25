<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Libraries\ImageUpload;
use App\Models\CategoryModel;
use App\Models\ProductModel;
use CodeIgniter\Exceptions\PageNotFoundException;
use RuntimeException;

/**
 * CRUD produk dan upload gambar (Praktikum 5).
 */
class ProductController extends BaseController
{
    private ProductModel $model;

    public function __construct()
    {
        $this->model = new ProductModel();
    }

    public function index()
    {
        $q        = trim((string) $this->request->getGet('q'));
        $category = (int) $this->request->getGet('category') ?: null;
        $status   = (string) $this->request->getGet('status');

        $this->model->withCategory()->orderBy('products.created_at', 'DESC')->orderBy('products.id', 'DESC');
        if ($q) {
            $this->model->like('products.name', $q);
        }
        if ($category) {
            $this->model->where('products.category_id', $category);
        }
        if ($status === 'low') {
            $this->model->where('products.stock <=', 5);
        } elseif (in_array($status, ['active', 'inactive'], true)) {
            $this->model->where('products.status', $status);
        }

        return view('admin/products/index', [
            'title'      => 'Produk',
            'products'   => $this->model->paginate(10),
            'pager'      => $this->model->pager,
            'categories' => (new CategoryModel())->options(),
            'q'          => $q,
            'category'   => $category,
            'status'     => $status,
        ]);
    }

    public function create()
    {
        return view('admin/products/form', ['title' => 'Produk Baru', 'row' => null, 'categories' => (new CategoryModel())->options()]);
    }

    public function store()
    {
        if (! $this->validate($this->rules() + ImageUpload::rules('image', 'Foto produk', false))) {
            return $this->backWithErrors();
        }
        try {
            $image = ImageUpload::store($this->request->getFile('image'), FCPATH . 'uploads/products');
        } catch (RuntimeException $e) {
            return $this->backWithErrors($e->getMessage());
        }
        $this->model->insert($this->input() + ['image' => $image]);

        return redirect()->to('/admin/products')->with('success', 'Produk ' . $this->request->getPost('name') . ' ditambahkan.');
    }

    public function edit(int $id)
    {
        $row = $this->model->find($id) ?? throw new PageNotFoundException();

        return view('admin/products/form', ['title' => 'Ubah Produk', 'row' => $row, 'categories' => (new CategoryModel())->options()]);
    }

    public function update(int $id)
    {
        $row = $this->model->find($id) ?? throw new PageNotFoundException();
        if (! $this->validate($this->rules() + ImageUpload::rules('image', 'Foto produk', false))) {
            return $this->backWithErrors();
        }
        $data = $this->input();
        try {
            $image = ImageUpload::store($this->request->getFile('image'), FCPATH . 'uploads/products');
        } catch (RuntimeException $e) {
            return $this->backWithErrors($e->getMessage());
        }
        if ($image) {
            $data['image'] = $image;
            ImageUpload::delete($row['image'], FCPATH . 'uploads/products');
        }
        $this->model->update($id, $data);

        return redirect()->to('/admin/products')->with('success', 'Produk diperbarui.');
    }

    /** Aktif ↔ nonaktif. Produk nonaktif tidak tampil di katalog. */
    public function toggle(int $id)
    {
        $row = $this->model->find($id) ?? throw new PageNotFoundException();
        $new = $row['status'] === 'active' ? 'inactive' : 'active';
        $this->model->update($id, ['status' => $new]);

        return redirect()->back()->with('success', $row['name'] . ($new === 'active' ? ' diaktifkan kembali.' : ' dinonaktifkan dan disembunyikan dari katalog.'));
    }

    /**
     * Produk yang sudah pernah dipesan tidak dihapus fisik (riwayat transaksi harus utuh);
     * produk tersebut dinonaktifkan saja.
     */
    public function delete(int $id)
    {
        $row = $this->model->find($id) ?? throw new PageNotFoundException();
        $used = db_connect()->table('order_items')->where('product_id', $id)->countAllResults();
        if ($used > 0) {
            $this->model->update($id, ['status' => 'inactive']);

            return redirect()->back()->with('success', "{$row['name']} sudah ada di {$used} transaksi, sehingga hanya dinonaktifkan (tidak dihapus).");
        }
        $this->model->delete($id);
        ImageUpload::delete($row['image'], FCPATH . 'uploads/products');

        return redirect()->to('/admin/products')->with('success', "{$row['name']} dihapus.");
    }

    private function rules(): array
    {
        return [
            'name'        => ['label' => 'Nama produk', 'rules' => 'required|min_length[3]|max_length[150]'],
            'category_id' => ['label' => 'Kategori', 'rules' => 'required|is_not_unique[categories.id]'],
            'price'       => ['label' => 'Harga', 'rules' => 'required|decimal|greater_than_equal_to[0]|less_than[10000000000]'],
            'stock'       => ['label' => 'Stok', 'rules' => 'required|is_natural|less_than[1000000]'],
            'status'      => ['label' => 'Status', 'rules' => 'required|in_list[active,inactive]'],
            'description' => ['label' => 'Deskripsi', 'rules' => 'permit_empty|max_length[2000]'],
        ];
    }

    private function input(): array
    {
        $p = $this->request->getPost();

        return [
            'name'        => trim($p['name']),
            'category_id' => (int) $p['category_id'],
            'price'       => (float) $p['price'],
            'stock'       => (int) $p['stock'],
            'status'      => $p['status'],
            'description' => trim((string) ($p['description'] ?? '')) ?: null,
        ];
    }
}
