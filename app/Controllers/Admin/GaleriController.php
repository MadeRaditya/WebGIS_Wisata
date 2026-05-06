<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\GaleriModel;
use App\Models\WisataModel;

class GaleriController extends BaseController
{
    protected $galeriModel;
    protected $wisataModel;

    public function __construct()
    {
        $this->galeriModel = new GaleriModel();
        $this->wisataModel = new WisataModel();
    }

    public function index()
    {
        $data = [
            'title' => 'Galeri Foto',
            'galeri' => $this->galeriModel->getAll()
        ];
        return view('admin/galeri/index', $data);
    }

    public function create()
    {
        $data = [
            'title' => 'Tambah Foto Galeri',
            'wisata' => $this->wisataModel->getAll()
        ];
        return view('admin/galeri/form', $data);
    }

    public function store()
    {
        $data = [
            'wisata_id' => $this->request->getPost('wisata_id'),
            'caption' => $this->request->getPost('caption')
        ];

        $gambar = $this->request->getFile('url_gambar');
        if ($gambar && $gambar->isValid() && !$gambar->hasMoved()) {
            $newName = $gambar->getRandomName();
            
            $supabase = new \App\Libraries\SupabaseClient();
            $storage = $supabase->storage('wisata');
            
            // Upload to Supabase
            $uploadPath = 'galeri/' . $newName;
            $res = $storage->upload($uploadPath, $gambar->getTempName(), $gambar->getMimeType());
            
            if (!empty($res) && !isset($res['error']) && !isset($res['statusCode'])) {
                $data['url_gambar'] = $storage->getPublicUrl($uploadPath);
                $this->galeriModel->insert($data);
                return redirect()->to('admin/galeri')->with('success', 'Foto galeri berhasil ditambahkan.');
            } else {
                $errMsg = $res['message'] ?? json_encode($res);
                return redirect()->back()->withInput()->with('error', 'Gagal upload ke Supabase: ' . $errMsg);
            }
        }

        return redirect()->back()->withInput()->with('error', 'Pilih file gambar terlebih dahulu.');
    }

    public function edit($id)
    {
        $galeri = $this->galeriModel->getById($id);
        if (!$galeri) return redirect()->to('admin/galeri')->with('error', 'Data tidak ditemukan.');

        $data = [
            'title' => 'Edit Foto Galeri',
            'galeri' => $galeri,
            'wisata' => $this->wisataModel->getAll()
        ];
        return view('admin/galeri/form', $data);
    }

    public function update($id)
    {
        $data = [
            'wisata_id' => $this->request->getPost('wisata_id'),
            'caption' => $this->request->getPost('caption')
        ];

        $gambar = $this->request->getFile('url_gambar');
        if ($gambar && $gambar->isValid() && !$gambar->hasMoved()) {
            $newName = $gambar->getRandomName();
            
            $supabase = new \App\Libraries\SupabaseClient();
            $storage = $supabase->storage('wisata');
            
            // Upload to Supabase
            $uploadPath = 'galeri/' . $newName;
            $res = $storage->upload($uploadPath, $gambar->getTempName(), $gambar->getMimeType());
            
            if (!empty($res) && !isset($res['error']) && !isset($res['statusCode'])) {
                $data['url_gambar'] = $storage->getPublicUrl($uploadPath);
            } else {
                $errMsg = $res['message'] ?? json_encode($res);
                return redirect()->back()->withInput()->with('error', 'Gagal upload ke Supabase: ' . $errMsg);
            }
        }

        $this->galeriModel->update($id, $data);

        return redirect()->to('admin/galeri')->with('success', 'Foto galeri berhasil diupdate.');
    }

    public function delete($id)
    {
        $this->galeriModel->delete($id);
        return redirect()->to('admin/galeri')->with('success', 'Foto galeri berhasil dihapus.');
    }
}
