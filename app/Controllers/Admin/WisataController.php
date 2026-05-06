<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\WisataModel;
use App\Models\KategoriModel;

class WisataController extends BaseController
{
    protected $wisataModel;
    protected $kategoriModel;

    public function __construct()
    {
        $this->wisataModel = new WisataModel();
        $this->kategoriModel = new KategoriModel();
    }

    public function index()
    {
        $data = [
            'title' => 'Data Wisata',
            'wisata' => $this->wisataModel->getAll()
        ];
        return view('admin/wisata/index', $data);
    }

    public function create()
    {
        $data = [
            'title' => 'Tambah Wisata',
            'kategori' => $this->kategoriModel->getAll()
        ];
        return view('admin/wisata/form', $data);
    }

    public function store()
    {
        $data = [
            'kategori_id' => $this->request->getPost('kategori_id'),
            'nama' => $this->request->getPost('nama'),
            'deskripsi' => $this->request->getPost('deskripsi'),
            'alamat' => $this->request->getPost('alamat'),
            'latitude' => $this->request->getPost('latitude'),
            'longitude' => $this->request->getPost('longitude'),
        ];

        $gambar = $this->request->getFile('gambar_utama');
        if ($gambar && $gambar->isValid() && !$gambar->hasMoved()) {
            $newName = $gambar->getRandomName();
            
            $supabase = new \App\Libraries\SupabaseClient();
            $storage = $supabase->storage('wisata');
            
            // Upload to Supabase
            $uploadPath = 'utama/' . $newName;
            $res = $storage->upload($uploadPath, $gambar->getTempName(), $gambar->getMimeType());
            
            if (!empty($res) && !isset($res['error']) && !isset($res['statusCode'])) {
                $data['gambar_utama'] = $storage->getPublicUrl($uploadPath);
            } else {
                $errMsg = $res['message'] ?? json_encode($res);
                return redirect()->back()->withInput()->with('error', 'Gagal upload ke Supabase: ' . $errMsg);
            }
        }

        $this->wisataModel->insert($data);

        return redirect()->to('admin/wisata')->with('success', 'Wisata berhasil ditambahkan.');
    }

    public function edit($id)
    {
        $wisata = $this->wisataModel->getById($id);
        if (!$wisata) return redirect()->to('admin/wisata')->with('error', 'Data tidak ditemukan.');

        $data = [
            'title' => 'Edit Wisata',
            'wisata' => $wisata,
            'kategori' => $this->kategoriModel->getAll()
        ];
        return view('admin/wisata/form', $data);
    }

    public function update($id)
    {
        error_log("Update Wisata ID $id POST Data: " . json_encode($_POST));
        $data = [
            'kategori_id' => $this->request->getPost('kategori_id'),
            'nama' => $this->request->getPost('nama'),
            'deskripsi' => $this->request->getPost('deskripsi'),
            'alamat' => $this->request->getPost('alamat'),
            'latitude' => $this->request->getPost('latitude'),
            'longitude' => $this->request->getPost('longitude'),
            'updated_at' => date('Y-m-d H:i:s')
        ];

        $gambar = $this->request->getFile('gambar_utama');
        if ($gambar && $gambar->isValid() && !$gambar->hasMoved()) {
            $newName = $gambar->getRandomName();
            
            $supabase = new \App\Libraries\SupabaseClient();
            $storage = $supabase->storage('wisata');
            
            // Upload to Supabase
            $uploadPath = 'utama/' . $newName;
            $res = $storage->upload($uploadPath, $gambar->getTempName(), $gambar->getMimeType());
            
            if (!empty($res) && !isset($res['error']) && !isset($res['statusCode'])) {
                $data['gambar_utama'] = $storage->getPublicUrl($uploadPath);
            } else {
                $errMsg = $res['message'] ?? json_encode($res);
                return redirect()->back()->withInput()->with('error', 'Gagal upload ke Supabase: ' . $errMsg);
            }
        }

        $this->wisataModel->update($id, $data);

        return redirect()->to('admin/wisata')->with('success', 'Wisata berhasil diupdate.');
    }

    public function delete($id)
    {
        $this->wisataModel->delete($id);
        return redirect()->to('admin/wisata')->with('success', 'Wisata berhasil dihapus.');
    }
}
