<?php

namespace App\Services;

use App\Models\ImperialGrace;
use App\Models\Teaching;
use App\Models\OtherRecord;
use Illuminate\Support\Facades\Auth;

class RecordService
{
    /**
     * Create an Imperial Grace record.
     */
    public function createImperialGrace(array $data)
    {
        $data['user_id'] = Auth::id();
        return ImperialGrace::create($data);
    }

    /**
     * Create a Teaching record.
     */
    public function createTeaching(array $data)
    {
        $data['user_id'] = Auth::id();
        return Teaching::create($data);
    }

    /**
     * Create an Other Record (Military etc).
     */
    public function createOtherRecord(array $data)
    {
        $data['user_id'] = Auth::id();
        return OtherRecord::create($data);
    }

    /**
     * Common update logic for records.
     */
    public function updateRecord($record, array $data)
    {
        return $record->update($data);
    }
}
