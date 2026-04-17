<?php

namespace App\Services;

use App\Models\Teaching;
use Illuminate\Support\Collection;

class TeachingService
{
    public function getAll($masterId = null): Collection
    {
        $query = Teaching::with(['master', 'group', 'dharmaName', 'user']);
        
        if ($masterId === 'daily' || $masterId === 0 || $masterId === '0') {
            $query->whereNull('master_id');
        } elseif ($masterId) {
            $query->where('master_id', $masterId);
        }
        
        return $query->latest()->get();
    }

    public function getPaginatedDates($masterId = null, $perPage = 15)
    {
        $query = Teaching::query();
        
        if ($masterId === 'daily' || $masterId === 0 || $masterId === '0') {
            $query->whereNull('master_id');
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
        $query = Teaching::with(['master', 'group', 'dharmaName', 'user'])
            ->where('date', $date);
            
        if ($masterId === 'daily' || $masterId === 0 || $masterId === '0') {
            $query->whereNull('master_id');
        } elseif ($masterId) {
            $query->where('master_id', $masterId);
        }
        
        return $query->latest()->get();
    }

    public function create(array $data): Teaching
    {
        $data = $this->resolveRelations($data);
        return Teaching::create($data);
    }

    public function findById(int $id): ?Teaching
    {
        return Teaching::with(['master', 'group', 'dharmaName', 'user'])->find($id);
    }

    public function update(int $id, array $data): bool
    {
        $teaching = Teaching::find($id);
        if (!$teaching) return false;
        
        $data = $this->resolveRelations($data);
        return $teaching->update($data);
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

        // Resolve Group ID from target_remarks (group name)
        if (isset($data['target_remarks']) && !empty($data['target_remarks'])) {
            $group = \App\Models\Group::where('name', $data['target_remarks'])->first();
            if ($group) {
                $data['group_id'] = $group->id;
            }
        }

        // Resolve Dharma Name ID from title (name)
        if (isset($data['title']) && !empty($data['title'])) {
            $dnName = $data['title'];
            $dn = \App\Models\DharmaName::where('name', $dnName)->first();
            if ($dn) {
                $data['dharma_name_id'] = $dn->id;
            }
        }

        // Ensure user_id is set
        if (!isset($data['user_id'])) {
            $data['user_id'] = auth()->id() ?? 1;
        }

        return $data;
    }
}
