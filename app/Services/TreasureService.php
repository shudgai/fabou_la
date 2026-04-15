<?php

namespace App\Services;

use App\Models\Treasure;
use Illuminate\Support\Collection;

class TreasureService
{
    public function getAllTreasures(): Collection
    {
        return Treasure::with(['master', 'dharmaNameTreasures'])->latest()->get();
    }

    public function createTreasure(array $data): Treasure
    {
        $treasure = Treasure::create($data);
        if (isset($data['dharma_name_treasures'])) {
            $treasure->dharmaNameTreasures()->createMany($data['dharma_name_treasures']);
        }
        return $treasure;
    }

    public function updateTreasure(int $id, array $data): ?Treasure
    {
        $treasure = Treasure::find($id);
        if (!$treasure) return null;
        $treasure->update($data);
        
        if (isset($data['dharma_name_treasures'])) {
            $treasure->dharmaNameTreasures()->delete();
            $treasure->dharmaNameTreasures()->createMany($data['dharma_name_treasures']);
        }
        return $treasure;
    }

    public function deleteTreasure(int $id): bool
    {
        $treasure = Treasure::find($id);
        if (!$treasure) return false;
        return $treasure->delete();
    }

    public function createBatchTreasures(array $items): void
    {
        foreach ($items as $item) {
            Treasure::create($item);
        }
    }
}
