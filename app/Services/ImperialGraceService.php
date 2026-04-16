<?php

namespace App\Services;

use App\Models\ImperialGraceRegistry;
use App\Models\UserImperialGrace;
use Illuminate\Support\Collection;

class ImperialGraceService
{
    // Registry methods
    public function getAllRegistries(): Collection
    {
        return ImperialGraceRegistry::with('master')->latest()->get();
    }

    public function createRegistry(array $data): ImperialGraceRegistry
    {
        $name = trim($data['name'] ?? '');
        if (ImperialGraceRegistry::where('name', $name)->exists()) {
            throw new \Exception("法寶名稱「{$name}」在重大皇恩中已存在，請勿重複存檔。");
        }
        return ImperialGraceRegistry::create($data);
    }

    // User Grace methods
    public function getAllUserGraces(): Collection
    {
        return UserImperialGrace::with(['user', 'registry.master'])->latest()->get();
    }

    public function createUserGrace(array $data): UserImperialGrace
    {
        return UserImperialGrace::create($data);
    }

    public function deleteUserGrace(int $id): bool
    {
        $grace = UserImperialGrace::find($id);
        if (!$grace) return false;
        return $grace->delete();
    }

    public function updateRegistry(int $id, array $data): ?ImperialGraceRegistry
    {
        $registry = ImperialGraceRegistry::find($id);
        if (!$registry) return null;

        $newName = trim($data['name'] ?? '');
        if ($newName && $newName !== $registry->name) {
            if (ImperialGraceRegistry::where('name', $newName)->where('id', '!=', $id)->exists()) {
                throw new \Exception("法寶名稱「{$newName}」在重大皇恩中已存在，請勿重複存檔。");
            }
        }

        $registry->update($data);
        return $registry;
    }

    public function deleteRegistry(int $id): bool
    {
        $registry = ImperialGraceRegistry::find($id);
        if (!$registry) return false;
        
        // Delete related user graces first to avoid foreign key constraints
        $registry->userGraces()->delete();
        
        return $registry->delete();
    }
}
