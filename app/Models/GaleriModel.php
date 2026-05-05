<?php

namespace App\Models;

use App\Libraries\SupabaseClient;

class GaleriModel
{
    protected SupabaseClient $supabase;
    protected string $table = 'galeri';

    public function __construct()
    {
        $this->supabase = new SupabaseClient();
    }

    public function getByWisataId(int $wisataId): array
    {
        return $this->supabase->from($this->table)
            ->select('*')
            ->eq('wisata_id', $wisataId)
            ->get();
    }

    public function getAll(): array
    {
        return $this->supabase->from($this->table)
            ->select('*,wisata(id,nama)')
            ->get();
    }

    public function getById(int $id): ?array
    {
        $results = $this->supabase->from($this->table)
            ->select('*')
            ->eq('id', $id)
            ->limit(1)
            ->get();

        return !empty($results) ? $results[0] : null;
    }

    public function insert(array $data): array
    {
        return $this->supabase->from($this->table)->insert($data);
    }

    public function update(int $id, array $data): array
    {
        return $this->supabase->from($this->table)->eq('id', $id)->update($data);
    }

    public function delete(int $id): array
    {
        return $this->supabase->from($this->table)->eq('id', $id)->delete();
    }
}
