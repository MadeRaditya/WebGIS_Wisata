<?php

namespace App\Controllers;

use App\Models\WisataModel;
use App\Models\KategoriModel;
use App\Models\GaleriModel;
use App\Libraries\GeoHelper;

class ApiController extends BaseController
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

    public function list()
    {
        $kategori = $this->request->getGet('kategori');
        $jarak = $this->request->getGet('jarak');
        $lat = $this->request->getGet('lat');
        $lon = $this->request->getGet('lon');
        $search = $this->request->getGet('search');

        if ($search) {
            $data = $this->wisataModel->search($search);
        } elseif ($kategori) {
            $data = $this->wisataModel->getByKategori($kategori);
        } else {
            $data = $this->wisataModel->getAll();
        }

        $processedData = [];
        foreach ($data as $item) {
            $processed = $item;
            $processed['kategori_nama'] = isset($item['kategori']['nama']) ? $item['kategori']['nama'] : '-';

            if ($lat && $lon && GeoHelper::isValidLatitude($lat) && GeoHelper::isValidLongitude($lon)) {
                $processed['distance_km'] = GeoHelper::haversine(
                    (float) $lat, (float) $lon,
                    (float) $item['latitude'], (float) $item['longitude']
                );
            }

            $processedData[] = $processed;
        }

        if ($jarak && $lat && $lon) {
            $processedData = array_filter($processedData, function ($item) use ($jarak) {
                return isset($item['distance_km']) && $item['distance_km'] <= (float) $jarak;
            });
            $processedData = array_values($processedData);
        }

        if (isset($processedData[0]['distance_km'])) {
            usort($processedData, function ($a, $b) {
                return ($a['distance_km'] ?? PHP_FLOAT_MAX) <=> ($b['distance_km'] ?? PHP_FLOAT_MAX);
            });
        }

        return $this->response->setJSON([
            'status' => true,
            'data' => $processedData,
            'total' => count($processedData),
        ]);
    }

    public function show($id)
    {
        $wisata = $this->wisataModel->getById((int) $id);

        if (!$wisata) {
            return $this->response->setStatusCode(404)->setJSON([
                'status' => false,
                'message' => 'Wisata tidak ditemukan',
            ]);
        }

        $wisata['kategori_nama'] = isset($wisata['kategori']['nama']) ? $wisata['kategori']['nama'] : '-';
        $wisata['galeri'] = $this->galeriModel->getByWisataId((int) $id);

        return $this->response->setJSON([
            'status' => true,
            'data' => $wisata,
        ]);
    }

    public function nearest()
    {
        $json = $this->request->getJSON(true);

        $lat = $json['lat'] ?? null;
        $lon = $json['lon'] ?? null;
        $limit = $json['limit'] ?? 5;

        if (!$lat || !$lon || !GeoHelper::isValidLatitude($lat) || !GeoHelper::isValidLongitude($lon)) {
            return $this->response->setStatusCode(400)->setJSON([
                'status' => false,
                'message' => 'Koordinat latitude dan longitude diperlukan dan harus valid.',
            ]);
        }

        $data = $this->wisataModel->getNearest((float) $lat, (float) $lon, (int) $limit);

        foreach ($data as &$item) {
            $item['kategori_nama'] = isset($item['kategori']['nama']) ? $item['kategori']['nama'] : '-';
        }

        return $this->response->setJSON([
            'status' => true,
            'data' => $data,
            'user_location' => ['lat' => (float) $lat, 'lon' => (float) $lon],
        ]);
    }

    public function galeri($id)
    {
        $galeri = $this->galeriModel->getByWisataId((int) $id);

        return $this->response->setJSON([
            'status' => true,
            'data' => $galeri,
        ]);
    }

    public function kategori()
    {
        $data = $this->kategoriModel->getAll();

        return $this->response->setJSON([
            'status' => true,
            'data' => $data,
        ]);
    }
}
