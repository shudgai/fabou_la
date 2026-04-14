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
