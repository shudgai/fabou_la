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
}
