<?php

namespace App\Http\Controllers;

use App\Models\MiscellaneousTransaction;
use App\Traits\LogsActivity;
use Illuminate\Http\Request;

class MiscellaneousTransactionController extends Controller
{
    use LogsActivity;

    public function store(Request $request)
    {
        $data = $request->validate([
            'description' => 'required|string|max:500',
            'amount' => 'required|numeric|min:0',
            'transaction_date' => 'required|date',
            'location' => 'required|string|in:'.implode(',', MiscellaneousTransaction::locationOptions()),
        ]);

        $record = MiscellaneousTransaction::create($data);
        $this->logCreate($record);

        return redirect()
            ->route('vehicles.index', ['status' => 'Miscellaneous'])
            ->with('success', 'Miscellaneous entry added successfully.');
    }

    public function destroy(MiscellaneousTransaction $miscellaneousTransaction)
    {
        $this->logDelete($miscellaneousTransaction);
        $miscellaneousTransaction->delete();

        return redirect()
            ->route('vehicles.index', ['status' => 'Miscellaneous'])
            ->with('success', 'Miscellaneous entry deleted successfully.');
    }
}
