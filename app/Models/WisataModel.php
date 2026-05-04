<?php

namespace App\Models;

use App\Libraries\SupabaseClient;

class WisataModel
{
    protected SupabaseClient $supabase;
    protected string $table = 'wisata';

    public function __construct()
    {
        $this->supabase = new SupabaseClient();
    }

    public function getAll(): array
    {
        return $this->supabase->from($this->table)
            ->select('*,kategori(id,nama,slug)')
            ->order('created_at', false)
            ->get();
    }

    public function getById(int $id): ?array
    {
        $results = $this->supabase->from($this->table)
            ->select('*,kategori(id,nama,slug)')
            ->eq('id', $id)
            ->limit(1)
            ->get();

        return !empty($results) ? $results[0] : null;
    }

    public function getByKategori(string $slug): array
    {
        $kategori = (new KategoriModel())->getBySlug($slug);

        if (!$kategori) {
            return [];
        }

        return $this->supabase->from($this->table)
            ->select('*,kategori(id,nama,slug)')
            ->eq('kategori_id', $kategori['id'])
            ->order('nama')
            ->get();
    }

    public function getNearest(float $lat, float $lon, int $limit = 5): array
    {
        $allWisata = $this->getAll();

        foreach ($allWisata as &$wisata) {
            $wisata['distance_km'] = \App\Libraries\GeoHelper::haversine(
                $lat, $lon,
                (float) $wisata['latitude'],
                (float) $wisata['longitude']
            );
        }

        usort($allWisata, function ($a, $b) {
            return $a['distance_km'] <=> $b['distance_km'];
        });

        return array_slice($allWisata, 0, $limit);
    }

    public function filterByDistance(float $lat, float $lon, float $maxDistance): array
    {
        $allWisata = $this->getAll();
        $filtered = [];

        foreach ($allWisata as $wisata) {
            $distance = \App\Libraries\GeoHelper::haversine(
                $lat, $lon,
                (float) $wisata['latitude'],
                (float) $wisata['longitude']
            );

            if ($distance <= $maxDistance) {
                $wisata['distance_km'] = $distance;
                $filtered[] = $wisata;
            }
        }

        usort($filtered, function ($a, $b) {
            return $a['distance_km'] <=> $b['distance_km'];
        });

        return $filtered;
    }

    public function search(string $keyword): array
    {
        return $this->supabase->from($this->table)
            ->select('*,kategori(id,nama,slug)')
            ->ilike('nama', '%' . $keyword . '%')
            ->get();
    }
}
