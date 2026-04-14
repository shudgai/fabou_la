<?php

namespace App\Services;

use App\Models\Treasure;
use Illuminate\Support\Collection;

class TreasureService
{
    public function getAllTreasures(): Collection
    {
        return Treasure::with('master')->latest()->get();
    }

    public function createTreasure(array $data): Treasure
    {
        return Treasure::create($data);
    }

    public function updateTreasure(int $id, array $data): ?Treasure
    {
        $treasure = Treasure::find($id);
        if (!$treasure) return null;
        $treasure->update($data);
        return $treasure;
    }

    public function deleteTreasure(int $id): bool
    {
        $treasure = Treasure::find($id);
        if (!$treasure) return false;
        return $treasure->delete();
    }
}
