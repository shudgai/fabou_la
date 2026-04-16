<?php

namespace App\Http\Controllers;

use App\Models\MilitaryRecord;
use Illuminate\Http\Request;

class MilitaryRecordController extends Controller
{
    public function index()
    {
        return MilitaryRecord::orderBy('know_date', 'desc')->get();
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'user_name' => 'required|string',
            'army_type' => 'required|string',
            'quantity' => 'required|integer',
            'know_date' => 'required|date',
            'process_date' => 'nullable|date',
            'destination' => 'nullable|string',
            'user_remarks' => 'nullable|string',
            'remarks_text' => 'nullable|string',
            'yan_zun' => 'nullable|integer',
            'yan_an' => 'nullable|integer',
            'long_sheng' => 'nullable|integer',
            'long_zhan' => 'nullable|integer',
        ]);

        return MilitaryRecord::create($validated);
    }

    public function update(Request $request, $id)
    {
        $record = MilitaryRecord::findOrFail($id);
        $record->update($request->all());
        return $record;
    }

    public function destroy($id)
    {
        $record = MilitaryRecord::findOrFail($id);
        $record->delete();
        return response()->json(['message' => 'Deleted']);
    }
}
