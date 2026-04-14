<?php

namespace App\Services;

use App\Models\Master;
use Illuminate\Support\Collection;

class MasterService
{
    /**
     * Get all masters categorized by type.
     *
     * @return Collection
     */
    public function getAllCategorized(): Collection
    {
        return Master::all()->groupBy('category');
    }

    /**
     * Create a new master.
     *
     * @param array $data
     * @return Master
     */
    public function createMaster(array $data): Master
    {
        return Master::create($data);
    }

    /**
     * Find a master by ID.
     *
     * @param int $id
     * @return Master|null
     */
    public function findById(int $id): ?Master
    {
        return Master::find($id);
    }

    /**
     * Update a master.
     *
     * @param int $id
     * @param array $data
     * @return bool
     */
    public function updateMaster(int $id, array $data): bool
    {
        $master = $this->findById($id);
        if (!$master) return false;
        return $master->update($data);
    }

    /**
     * Delete a master.
     *
     * @param int $id
     * @return bool
     */
    public function deleteMaster(int $id): bool
    {
        $master = $this->findById($id);
        if (!$master) return false;
        return $master->delete();
    }
    /**
     * Resolve a master's name to its main Master ID based on the grouping rules.
     *
     * @param string $name
     * @return int|null
     */
    public function resolveMasterId(string $name): ?int
    {
        $mapping = [
            '龍王仙師' => 7,  // 太子
            '父義' => 8,    // 閻王仙師
            '閻父' => 8,    // 閻王仙師
            '閻王父親' => 8, // 閻王仙師
            '老祖父親' => 1, // 老祖仙師
            '靈寶父親' => 4, // 靈寶仙師
            '元始父親' => 2, // 元始仙師
            '道祖父親' => 3, // 道祖仙師
            '太宰父親' => 6, // 太宰仙師
        ];

        if (isset($mapping[$name])) {
            return $mapping[$name];
        }

        // Default: find by exact name
        $master = Master::where('name', $name)->first();
        return $master ? $master->id : null;
    }
}
