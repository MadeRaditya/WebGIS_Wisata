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
}
