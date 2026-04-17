<?php

namespace App\Services;

use App\Models\Teaching;
use Illuminate\Support\Collection;

class TeachingService
{
    public function getAll($masterId = null): Collection
    {
        $query = Teaching::with(['master', 'dharmaNames', 'user']);
        
        if ($masterId === 'daily' || $masterId === 0 || $masterId === '0') {
            // Show all records for the daily/master dashboard
        } elseif ($masterId) {
            $query->where('master_id', $masterId);
        }
        
        return $query->latest()->get();
    }

    public function getPaginatedDates($masterId = null, $perPage = 15)
    {
        $query = Teaching::query();
        
        if ($masterId === 'daily' || $masterId === 0 || $masterId === '0') {
            // Show all dates for the daily/master dashboard
        } elseif ($masterId) {
            $query->where('master_id', $masterId);
        }
        
        return $query->selectRaw('date, count(*) as count')
            ->groupBy('date')
            ->orderBy('date', 'desc')
            ->paginate($perPage);
    }

    public function getByDate($date, $masterId = null)
    {
        $query = Teaching::with(['master', 'dharmaNames', 'user'])
            ->where('date', $date);
            
        if ($masterId === 'daily' || $masterId === 0 || $masterId === '0') {
            // No extra master filter for daily
        } elseif ($masterId) {
            $query->where('master_id', $masterId);
        }
        
        return $query->latest()->get();
    }

    public function create(array $data): Teaching
    {
        $dnIds = $data['dharma_name_ids'] ?? [];
        $data = $this->resolveRelations($data);
        $teaching = Teaching::create($data);
        
        if (!empty($dnIds)) {
            $teaching->dharmaNames()->sync($dnIds);
        }
        
        return $teaching->load('dharmaNames');
    }

    public function findById(int $id): ?Teaching
    {
        return Teaching::with(['master', 'dharmaNames', 'user'])->find($id);
    }

    public function update(int $id, array $data): bool
    {
        $teaching = Teaching::find($id);
        if (!$teaching) return false;
        
        $dnIds = $data['dharma_name_ids'] ?? [];
        $data = $this->resolveRelations($data);
        $success = $teaching->update($data);
        
        if ($success) {
            $teaching->dharmaNames()->sync($dnIds);
        }
        
        return $success;
    }

    public function delete(int $id): bool
    {
        $teaching = Teaching::find($id);
        if (!$teaching) return false;
        return $teaching->delete();
    }

    protected function resolveRelations(array $data): array
    {
        // Resolve Master ID if it's a string name
        if (isset($data['master_name']) && !empty($data['master_name'])) {
            $master = \App\Models\Master::where('name', $data['master_name'])->first();
            if ($master) {
                $data['master_id'] = $master->id;
            }
        }

        // Ensure user_id is set
        if (!isset($data['user_id'])) {
            $data['user_id'] = auth()->id() ?? 1;
        }

        return $data;
    }
}
