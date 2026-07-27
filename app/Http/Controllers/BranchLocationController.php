<?php

namespace App\Http\Controllers;

use App\Models\BranchLocation;
use App\Traits\LogsActivity;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class BranchLocationController extends Controller
{
    use LogsActivity;

    public function index()
    {
        $branches = BranchLocation::ordered()->get();

        return view('settings.branch-locations.index', compact('branches'));
    }

    public function store(Request $request)
    {
        $validated = $this->validateBranch($request);
        $validated['sort_order'] = $validated['sort_order']
            ?? ((int) BranchLocation::max('sort_order')) + 1;

        $branch = BranchLocation::create($validated);
        $this->logCreate($branch, 'Created showroom: '.$branch->name, 'Showrooms');

        return redirect()
            ->route('settings.branch-locations.index')
            ->with('success', 'Showroom added successfully.')
            ->with('swal_title', 'Saved');
    }

    public function update(Request $request, BranchLocation $branch_location)
    {
        $validated = $this->validateBranch($request, $branch_location);
        $original = $branch_location->getOriginal();
        $branch_location->update($validated);

        $changes = [];
        foreach ($validated as $key => $value) {
            if (array_key_exists($key, $original) && $original[$key] != $value) {
                $changes[$key] = ['old' => $original[$key], 'new' => $value];
            }
        }

        $this->logUpdate(
            $branch_location,
            ! empty($changes) ? $changes : null,
            'Updated showroom: '.$branch_location->name,
            'Showrooms'
        );

        return redirect()
            ->route('settings.branch-locations.index')
            ->with('success', 'Showroom updated successfully.')
            ->with('swal_title', 'Saved');
    }

    public function destroy(BranchLocation $branch_location)
    {
        $name = $branch_location->name;
        $this->logDelete($branch_location, 'Deleted showroom: '.$name, 'Showrooms');
        $branch_location->delete();

        return redirect()
            ->route('settings.branch-locations.index')
            ->with('success', 'Showroom removed.')
            ->with('swal_title', 'Deleted');
    }

    private function validateBranch(Request $request, ?BranchLocation $branch = null): array
    {
        $validated = $request->validate([
            'name' => [
                'required',
                'string',
                'max:150',
                Rule::unique('branch_locations', 'name')->ignore($branch?->id),
            ],
            'description' => 'nullable|string|max:255',
            'is_active' => 'nullable|boolean',
            'sort_order' => 'nullable|integer|min:0',
        ]);

        $validated['is_active'] = $request->boolean('is_active');

        return $validated;
    }
}
