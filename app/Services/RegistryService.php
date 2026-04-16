<?php

namespace App\Services;

use App\Models\Registry;
use App\Models\UserRegistry;
use Illuminate\Support\Collection;

class RegistryService
{
    // Registry methods
    public function getAllRegistries(): Collection
    {
        return Registry::with('master')->latest()->get();
    }

    public function createRegistry(array $data): Registry
    {
        $name = trim($data['name'] ?? '');
        if (Registry::where('name', $name)->exists()) {
            throw new \Exception("法寶名稱「{$name}」在歸檔資料中已存在，請勿重複存檔。");
        }
        return Registry::create($data);
    }

    // User Grace methods
    public function getAllUserGraces(): Collection
    {
        return UserRegistry::with(['user', 'registry.master'])->latest()->get();
    }

    public function createUserGrace(array $data): UserRegistry
    {
        return UserRegistry::create($data);
    }

    public function deleteUserGrace(int $id): bool
    {
        $grace = UserRegistry::find($id);
        if (!$grace) return false;
        return $grace->delete();
    }

    public function updateRegistry(int $id, array $data): ?Registry
    {
        $registry = Registry::find($id);
        if (!$registry) return null;

        $newName = trim($data['name'] ?? '');
        if ($newName && $newName !== $registry->name) {
            if (Registry::where('name', $newName)->where('id', '!=', $id)->exists()) {
                throw new \Exception("法寶名稱「{$newName}」在歸檔資料中已存在，請勿重複存檔。");
            }
        }

        $registry->update($data);
        return $registry;
    }

    public function deleteRegistry(int $id): bool
    {
        $registry = Registry::find($id);
        if (!$registry) return false;
        
        // Delete related user graces first to avoid foreign key constraints
        $registry->userGraces()->delete();
        
        return $registry->delete();
    }
}
