<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\KategoriModel;

class KategoriController extends BaseController
{
    protected $kategoriModel;

    public function __construct()
    {
        $this->kategoriModel = new KategoriModel();
    }

    public function index()
    {
        $data = [
            'title' => 'Data Kategori',
            'kategori' => $this->kategoriModel->getAll()
        ];
        return view('admin/kategori/index', $data);
    }

    public function create()
    {
        return view('admin/kategori/form', ['title' => 'Tambah Kategori']);
    }

    public function store()
    {
        $nama = $this->request->getPost('nama');
        $slug = strtolower(trim(preg_replace('/[^A-Za-z0-9-]+/', '-', $nama)));

        $this->kategoriModel->insert([
            'nama' => $nama,
            'slug' => $slug
        ]);

        return redirect()->to('admin/kategori')->with('success', 'Kategori berhasil ditambahkan.');
    }

    public function edit($id)
    {
        $kategori = $this->kategoriModel->getById($id);
        if (!$kategori) return redirect()->to('admin/kategori')->with('error', 'Kategori tidak ditemukan.');

        return view('admin/kategori/form', [
            'title' => 'Edit Kategori',
            'kategori' => $kategori
        ]);
    }

    public function update($id)
    {
        $nama = $this->request->getPost('nama');
        $slug = strtolower(trim(preg_replace('/[^A-Za-z0-9-]+/', '-', $nama)));

        $this->kategoriModel->update($id, [
            'nama' => $nama,
            'slug' => $slug
        ]);

        return redirect()->to('admin/kategori')->with('success', 'Kategori berhasil diupdate.');
    }

    public function delete($id)
    {
        $this->kategoriModel->delete($id);
        return redirect()->to('admin/kategori')->with('success', 'Kategori berhasil dihapus.');
    }
}
