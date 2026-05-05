<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\KategoriModel;
use App\Models\WisataModel;
use App\Models\GaleriModel;

class Dashboard extends BaseController
{
    public function index()
    {
        $kategoriModel = new KategoriModel();
        $wisataModel = new WisataModel();
        $galeriModel = new GaleriModel();

        $kategoriList = $kategoriModel->getAll();
        $chartLabels = [];
        $chartData = [];

        foreach ($kategoriList as $k) {
            $wisataInKategori = $wisataModel->getByKategori($k['slug']);
            $chartLabels[] = $k['nama'];
            $chartData[] = count($wisataInKategori);
        }

        $data = [
            'title' => 'Dashboard',
            'total_kategori' => count($kategoriList),
            'total_wisata' => count($wisataModel->getAll()),
            'total_galeri' => count($galeriModel->getAll()),
            'chartLabels' => json_encode($chartLabels),
            'chartData' => json_encode($chartData),
        ];

        return view('admin/dashboard', $data);
    }
}
