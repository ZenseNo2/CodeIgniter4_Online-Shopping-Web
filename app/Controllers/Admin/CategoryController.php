<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\CategoryModel;
use CodeIgniter\Exceptions\PageNotFoundException;

/**
 * CRUD kategori produk (Praktikum 4).
 */
class CategoryController extends BaseController
{
    private CategoryModel $model;

    public function __construct()
    {
        $this->model = new CategoryModel();
    }

    public function index()
    {
        return view('admin/categories/index', ['title' => 'Kategori', 'categories' => $this->model->withCounts()]);
    }

    public function create()
    {
        return view('admin/categories/form', ['title' => 'Kategori Baru', 'row' => null]);
    }

    public function store()
    {
        if (! $this->validate($this->rules())) {
            return $this->backWithErrors();
        }
        $name = trim($this->request->getPost('name'));
        $this->model->insert([
            'name'        => $name,
            'slug'        => $this->model->uniqueSlug($name),
            'description' => trim((string) $this->request->getPost('description')) ?: null,
        ]);

        return redirect()->to('/admin/categories')->with('success', "Kategori {$name} ditambahkan.");
    }

    public function edit(int $id)
    {
        $row = $this->model->find($id) ?? throw new PageNotFoundException();

        return view('admin/categories/form', ['title' => 'Ubah Kategori', 'row' => $row]);
    }

    public function update(int $id)
    {
        $this->model->find($id) ?? throw new PageNotFoundException();
        if (! $this->validate($this->rules($id))) {
            return $this->backWithErrors();
        }
        $name = trim($this->request->getPost('name'));
        $this->model->update($id, [
            'name'        => $name,
            'slug'        => $this->model->uniqueSlug($name, $id),
            'description' => trim((string) $this->request->getPost('description')) ?: null,
        ]);

        return redirect()->to('/admin/categories')->with('success', 'Kategori diperbarui.');
    }

    /** Kategori yang masih memiliki produk tidak boleh dihapus (relasi produk). */
    public function delete(int $id)
    {
        $row = $this->model->find($id) ?? throw new PageNotFoundException();
        $count = db_connect()->table('products')->where('category_id', $id)->countAllResults();
        if ($count > 0) {
            return redirect()->back()->with('error', "Kategori {$row['name']} masih memiliki {$count} produk. Pindahkan atau hapus produknya terlebih dahulu.");
        }
        $this->model->delete($id);

        return redirect()->to('/admin/categories')->with('success', "Kategori {$row['name']} dihapus.");
    }

    private function rules(?int $id = null): array
    {
        return [
            'name'        => ['label' => 'Nama kategori', 'rules' => 'required|min_length[3]|max_length[100]|is_unique[categories.name,id,' . ($id ?? 0) . ']'],
            'description' => ['label' => 'Deskripsi', 'rules' => 'permit_empty|max_length[500]'],
        ];
    }
}
