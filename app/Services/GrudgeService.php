<?php

namespace App\Services;

use App\Models\Grudge;
use Illuminate\Support\Collection;

class GrudgeService
{
    public function getAll(): Collection
    {
        return Grudge::with(['user'])->latest()->get();
    }

    public function create(array $data): Grudge
    {
        return Grudge::create($data);
    }

    public function findById(int $id): ?Grudge
    {
        return Grudge::find($id);
    }

    public function update(int $id, array $data): bool
    {
        $grudge = $this->findById($id);
        if (!$grudge) return false;
        return $grudge->update($data);
    }

    public function delete(int $id): bool
    {
        $grudge = $this->findById($id);
        if (!$grudge) return false;
        return $grudge->delete();
    }
}
