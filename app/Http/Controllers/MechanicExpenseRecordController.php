<?php

namespace App\Http\Controllers;

use App\Models\MechanicExpenseRecord;
use Illuminate\Http\Request;
use App\Traits\LogsActivity;

class MechanicExpenseRecordController extends Controller
{
    use LogsActivity;

    public function store(Request $request)
    {
        $data = $this->validated($request);

        $record = MechanicExpenseRecord::create($data);
        $this->logCreate($record);

        return response()->json([
            'success' => true,
            'message' => $data['record_type'] === 'parts'
                ? 'Part added successfully!'
                : 'External expense added successfully!',
            'record' => $this->serialize($record),
        ]);
    }

    public function show(MechanicExpenseRecord $mechanicExpenseRecord)
    {
        return response()->json([
            'success' => true,
            'record' => $this->serialize($mechanicExpenseRecord),
        ]);
    }

    public function update(Request $request, MechanicExpenseRecord $mechanicExpenseRecord)
    {
        $data = $this->validated($request, $mechanicExpenseRecord->record_type);

        $original = $mechanicExpenseRecord->getOriginal();
        $mechanicExpenseRecord->update($data);

        $changes = [];
        foreach ($data as $key => $value) {
            if (array_key_exists($key, $original) && $original[$key] != $value) {
                $changes[$key] = ['old' => $original[$key], 'new' => $value];
            }
        }
        $this->logUpdate($mechanicExpenseRecord, !empty($changes) ? $changes : null);

        return response()->json([
            'success' => true,
            'message' => $mechanicExpenseRecord->record_type === 'parts'
                ? 'Part updated successfully!'
                : 'External expense updated successfully!',
            'record' => $this->serialize($mechanicExpenseRecord),
        ]);
    }

    public function destroy(MechanicExpenseRecord $mechanicExpenseRecord)
    {
        $this->logDelete($mechanicExpenseRecord);
        $mechanicExpenseRecord->delete();

        return response()->json([
            'success' => true,
            'message' => 'Record deleted successfully!',
        ]);
    }

    private function validated(Request $request, ?string $forceType = null): array
    {
        $rules = [
            'record_type' => ($forceType ? 'nullable' : 'required').'|in:parts,external',
            'description' => 'required|string|max:500',
            'amount' => 'required|numeric|min:0',
            'expense_date' => 'required|date',
            'repaired_by' => 'nullable|string|max:255',
            'unit_label' => 'nullable|string|max:255',
        ];

        $data = $request->validate($rules);
        $type = $forceType ?: $data['record_type'];
        $data['record_type'] = $type;

        if ($type === 'parts') {
            $data['repaired_by'] = null;
            $data['unit_label'] = null;
        }

        return $data;
    }

    private function serialize(MechanicExpenseRecord $record): array
    {
        return [
            'id' => $record->id,
            'record_type' => $record->record_type,
            'description' => $record->description,
            'amount' => (float) $record->amount,
            'repaired_by' => $record->repaired_by,
            'unit_label' => $record->unit_label,
            'expense_date' => optional($record->expense_date)->format('Y-m-d'),
        ];
    }
}
