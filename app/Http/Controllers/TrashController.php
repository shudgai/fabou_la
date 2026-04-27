<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\ImperialGrace;
use App\Models\Teaching;
use App\Models\Grudge;
use App\Models\MilitaryRecord;
use App\Models\OtherRecord;
use App\Models\Registry;
use Carbon\Carbon;

class TrashController extends Controller
{
    public function index()
    {
        // Auto-cleanup for testing JIT
        $this->cleanup();

        $user = auth()->user();
        $isAdmin = $user->isAdmin();

        $queryFn = function($modelClass) use ($user, $isAdmin) {
            $q = $modelClass::onlyTrashed();
            if (!$isAdmin) {
                $q->where('user_id', $user->id);
            }
            return $q;
        };

        // Fetch all soft deleted items
        $graces = $queryFn(ImperialGrace::class)->get()->map(fn($i) => array_merge($i->toArray(), ['type' => 'grace', 'type_label' => '重大皇恩']));
        $teachings = $queryFn(Teaching::class)->get()->map(fn($i) => array_merge($i->toArray(), ['type' => 'teaching', 'type_label' => '父皇仙師開示']));
        $grudges = $queryFn(Grudge::class)->get()->map(fn($i) => array_merge($i->toArray(), ['type' => 'grudge', 'type_label' => '怨靈']));
        $military = $queryFn(MilitaryRecord::class)->get()->map(fn($i) => array_merge($i->toArray(), ['type' => 'military', 'type_label' => '軍隊']));
        $others = $queryFn(OtherRecord::class)->get()->map(fn($i) => array_merge($i->toArray(), ['type' => 'other', 'type_label' => '其他']));
        $registries = $queryFn(Registry::class)->get()->map(fn($i) => array_merge($i->toArray(), ['type' => 'registry', 'type_label' => '法寶登記']));

        return collect([$graces, $teachings, $grudges, $military, $others, $registries])->flatten(1)->sortByDesc('deleted_at')->values();
    }

    public function restore(Request $request)
    {
        $id = $request->id;
        $type = $request->type;
        $user = auth()->user();

        $model = $this->getModel($type);
        if (!$model) return response()->json(['error' => 'Invalid type'], 400);

        $item = $model::onlyTrashed()->find($id);
        if ($item) {
            if (!$user->isAdmin() && $item->user_id !== $user->id) {
                return response()->json(['error' => 'Unauthorized'], 403);
            }
            $item->restore();
        }

        return response()->json(['success' => true]);
    }

    public function forceDelete(Request $request)
    {
        $id = $request->id;
        $type = $request->type;
        $user = auth()->user();

        $model = $this->getModel($type);
        if (!$model) return response()->json(['error' => 'Invalid type'], 400);

        $item = $model::onlyTrashed()->find($id);
        if ($item) {
            if (!$user->isAdmin() && $item->user_id !== $user->id) {
                return response()->json(['error' => 'Unauthorized'], 403);
            }
            $item->forceDelete();
        }

        return response()->json(['success' => true]);
    }

    private function getModel($type)
    {
        return match($type) {
            'grace' => ImperialGrace::class,
            'teaching' => Teaching::class,
            'grudge' => Grudge::class,
            'military' => MilitaryRecord::class,
            'other' => OtherRecord::class,
            'registry' => Registry::class,
            default => null,
        };
    }

    /**
     * Permanent Delete Cleanup (Automated check)
     * For now, manually triggered or we can add a route for it.
     * The user wants 5-minute deletion for test, 1 day for final.
     */
    public function cleanup()
    {
        $types = ['grace', 'teaching', 'grudge', 'military', 'other', 'registry'];
        // Default 5 mins for testing as requested
        $minutes = 5; 
        // In the future: $minutes = 1440; // 1 day

        foreach ($types as $type) {
            $model = $this->getModel($type);
            $model::onlyTrashed()
                ->where('deleted_at', '<=', Carbon::now()->subMinutes($minutes))
                ->forceDelete();
        }

        return response()->json(['success' => true, 'cleaned' => true]);
    }
}
