<?php

namespace App\Services;

use App\Models\Teaching;
use Illuminate\Support\Collection;

class TeachingService
{
    public function getAll(): Collection
    {
        return Teaching::with(['master', 'group', 'dharmaName', 'user'])->latest()->get();
    }

    public function create(array $data): Teaching
    {
        return Teaching::create($data);
    }

    public function findById(int $id): ?Teaching
    {
        return Teaching::find($id);
    }

    public function update(int $id, array $data): bool
    {
        $teaching = $this->findById($id);
        if (!$teaching) return false;
        return $teaching->update($data);
    }

    public function delete(int $id): bool
    {
        $teaching = $this->findById($id);
        if (!$teaching) return false;
        return $teaching->delete();
    }
}
