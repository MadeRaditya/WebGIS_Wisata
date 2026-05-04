<?php

namespace App\Models;

use App\Libraries\SupabaseClient;

class KategoriModel
{
    protected SupabaseClient $supabase;
    protected string $table = 'kategori';

    public function __construct()
    {
        $this->supabase = new SupabaseClient();
    }

    public function getAll(): array
    {
        return $this->supabase->from($this->table)
            ->select('*')
            ->order('nama')
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

    public function getBySlug(string $slug): ?array
    {
        $results = $this->supabase->from($this->table)
            ->select('*')
            ->eq('slug', $slug)
            ->limit(1)
            ->get();

        return !empty($results) ? $results[0] : null;
    }
}
