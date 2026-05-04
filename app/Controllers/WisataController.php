<?php

namespace App\Controllers;

use App\Models\WisataModel;
use App\Models\KategoriModel;
use App\Models\GaleriModel;

class WisataController extends BaseController
{
    protected WisataModel $wisataModel;
    protected KategoriModel $kategoriModel;
    protected GaleriModel $galeriModel;

    public function __construct()
    {
        $this->wisataModel = new WisataModel();
        $this->kategoriModel = new KategoriModel();
        $this->galeriModel = new GaleriModel();
    }

    public function index()
    {
        $data = [
            'title'     => 'WebGIS Wisata - Jelajahi Destinasi Wisata Indonesia',
            'kategori'  => $this->kategoriModel->getAll(),
            'wisata'    => $this->wisataModel->getAll(),
        ];

        return view('layouts/main', [
            'title' => $data['title'],
            'content' => view('wisata/index', $data),
        ]);
    }

    public function detail($id)
    {
        $wisata = $this->wisataModel->getById((int) $id);

        if (!$wisata) {
            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound('Wisata tidak ditemukan');
        }

        $galeri = $this->galeriModel->getByWisataId((int) $id);

        $data = [
            'title'  => $wisata['nama'] . ' - WebGIS Wisata',
            'wisata' => $wisata,
            'galeri' => $galeri,
        ];

        return view('layouts/main', [
            'title' => $data['title'],
            'content' => view('wisata/detail', $data),
        ]);
    }

    public function galeri($id)
    {
        $wisata = $this->wisataModel->getById((int) $id);

        if (!$wisata) {
            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound('Wisata tidak ditemukan');
        }

        $galeri = $this->galeriModel->getByWisataId((int) $id);

        $data = [
            'title'  => 'Galeri ' . $wisata['nama'] . ' - WebGIS Wisata',
            'wisata' => $wisata,
            'galeri' => $galeri,
        ];

        return view('layouts/main', [
            'title' => $data['title'],
            'content' => view('wisata/galeri', $data),
        ]);
    }
}
