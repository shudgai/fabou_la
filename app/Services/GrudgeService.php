<?php

namespace App\Services;

use App\Models\Grudge;
use Illuminate\Support\Collection;

class GrudgeService
{
    public function getAll(): Collection
    {
        $user = auth()->user();
        $query = Grudge::with(['user'])->latest();

        if (!$user->isAdmin()) {
            $permissions = $user->getPermissions();
            $allowedArmies = $permissions['allowed_armies'] ?? [];
            
            $query->where(function($q) use ($user, $allowedArmies) {
                // Own records
                $q->where('user_id', $user->id);
                
                // Linkage via army destination
                if (!empty($allowedArmies)) {
                    $q->orWhereIn('destination', $allowedArmies);
                    // Also check for partial matches in destination summary
                    foreach($allowedArmies as $army) {
                        $q->orWhere('destination', 'like', '%' . $army . '%');
                    }
                }
            });
        }

        return $query->get();
    }

    public function create(array $data): Grudge
    {
        $data['user_id'] = auth()->id();
        return Grudge::create($data);
    }

    public function batchCreate(array $items): Collection
    {
        $results = collect();
        $userId = auth()->id();
        foreach ($items as $item) {
            $item['user_id'] = $userId;
            // Upsert logic: if destination is NOT '未處理', check if there's a pending record for this name
            if (($item['destination'] ?? '未處理') !== '未處理') {
                $pending = Grudge::where('user_name', $item['user_name'])
                    ->where('status', '待處理')
                    ->latest()
                    ->first();
                
                if ($pending) {
                    $pending->update($item);
                    $results->push($pending);
                    continue;
                }
            }
            
            $results->push(Grudge::create($item));
        }
        return $results;
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
