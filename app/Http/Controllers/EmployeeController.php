<?php

namespace App\Http\Controllers;

use App\Models\Employee;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use App\Traits\LogsActivity;

class EmployeeController extends Controller
{
    use LogsActivity;
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $status = $request->get('status', 'all');
        $search = $request->get('search');

        $query = Employee::query();

        if ($status !== 'all') {
            $query->where('status', $status);
        }

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('first_name', 'LIKE', "%{$search}%")
                    ->orWhere('middle_name', 'LIKE', "%{$search}%")
                    ->orWhere('last_name', 'LIKE', "%{$search}%")
                    ->orWhere('role', 'LIKE', "%{$search}%")
                    ->orWhere('location', 'LIKE', "%{$search}%")
                    ->orWhere('sss', 'LIKE', "%{$search}%")
                    ->orWhere('philhealth', 'LIKE', "%{$search}%")
                    ->orWhere('pagibig', 'LIKE', "%{$search}%");
            });
        }

        $employees = $query->orderBy('last_name')->orderBy('first_name')->paginate(15);

        $activeCount = Employee::where('status', 'active')->count();
        $inactiveCount = Employee::where('status', 'inactive')->count();

        return view('employees.index', compact('employees', 'status', 'search', 'activeCount', 'inactiveCount'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('employees.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'first_name' => 'required|string|max:255',
            'middle_name' => 'nullable|string|max:255',
            'last_name' => 'required|string|max:255',
            'contract_start' => 'nullable|date',
            'contract_type' => 'nullable|in:PROBATIONARY,REGULAR',
            'role' => 'nullable|string|max:255',
            'location' => 'nullable|string|max:255',
            'sss' => 'nullable|string|max:255',
            'philhealth' => 'nullable|string|max:255',
            'pagibig' => 'nullable|string|max:255',
            'birthdate' => 'nullable|date',
            'status' => 'required|in:active,inactive',
            'notes' => 'nullable|string'
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        $data = $request->all();
        
        // Handle photo upload
        if ($request->hasFile('primary_photo')) {
            $photo = $request->file('primary_photo');
            $extension = $photo->getClientOriginalExtension();
            $fileName = Str::uuid() . '.' . $extension;
            $path = 'employees/photos/' . $fileName;
            
            // Store the image
            $photo->storeAs('public/employees/photos', $fileName);
            $data['primary_photo'] = $path;
        }
        
        $employee = Employee::create($data);

        // Log activity
        $this->logCreate($employee, null, "Employee Management");

        return redirect()->route('employees.index')
            ->with('success', 'Employee created successfully!');
    }

    /**
     * Display the specified resource.
     */
    public function show(Employee $employee)
    {
        return view('employees.show', compact('employee'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Employee $employee)
    {
        return view('employees.edit', compact('employee'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Employee $employee)
    {
        // If only photo is being uploaded, skip validation for other fields
        $isPhotoOnly = $request->hasFile('primary_photo') && count($request->except(['_token', '_method', 'primary_photo'])) === 0;
        
        if (!$isPhotoOnly) {
            $validator = Validator::make($request->all(), [
                'first_name' => 'required|string|max:255',
                'middle_name' => 'nullable|string|max:255',
                'last_name' => 'required|string|max:255',
                'contract_start' => 'nullable|date',
                'contract_type' => 'nullable|in:PROBATIONARY,REGULAR',
                'role' => 'nullable|string|max:255',
                'location' => 'nullable|string|max:255',
                'sss' => 'nullable|string|max:255',
                'philhealth' => 'nullable|string|max:255',
                'pagibig' => 'nullable|string|max:255',
                'birthdate' => 'nullable|date',
                'status' => 'required|in:active,inactive',
                'notes' => 'nullable|string',
                'primary_photo' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:5120'
            ]);

            if ($validator->fails()) {
                return redirect()->back()
                    ->withErrors($validator)
                    ->withInput();
            }
        } else {
            // Validate only photo for photo-only uploads
            $validator = Validator::make($request->all(), [
                'primary_photo' => 'required|image|mimes:jpeg,png,jpg,gif,webp|max:5120'
            ]);

            if ($validator->fails()) {
                return redirect()->back()
                    ->withErrors($validator)
                    ->withInput();
            }
        }

        // Get original values for logging
        $original = $employee->getOriginal();
        
        // If only photo is being uploaded (from modal), preserve all other fields
        if ($isPhotoOnly) {
            $data = $employee->toArray();
            unset($data['id'], $data['created_at'], $data['updated_at']);
        } else {
            $data = $request->all();
        }
        
        // Handle photo upload
        if ($request->hasFile('primary_photo')) {
            // Delete old photo if exists
            if ($employee->primary_photo && Storage::exists('public/' . $employee->primary_photo)) {
                Storage::delete('public/' . $employee->primary_photo);
            }
            
            $photo = $request->file('primary_photo');
            $extension = $photo->getClientOriginalExtension();
            $fileName = Str::uuid() . '.' . $extension;
            $path = 'employees/photos/' . $fileName;
            
            // Store the image
            $photo->storeAs('public/employees/photos', $fileName);
            $data['primary_photo'] = $path;
        }
        
        $employee->update($data);
        
        // Track changes for logging
        $changes = [];
        foreach ($data as $key => $value) {
            if (isset($original[$key]) && $original[$key] != $value) {
                $changes[$key] = [
                    'old' => $original[$key],
                    'new' => $value
                ];
            }
        }
        
        // Log activity
        $this->logUpdate($employee, !empty($changes) ? $changes : null, null, "Employee Management");

        if ($isPhotoOnly) {
            return redirect()->route('employees.show', $employee)
                ->with('success', 'Photo uploaded successfully!');
        }
        
        return redirect()->route('employees.index')
            ->with('success', 'Employee updated successfully!');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Employee $employee)
    {
        // Log activity before deleting
        $this->logDelete($employee);
        
        $employee->delete();

        return redirect()->route('employees.index')
            ->with('success', 'Employee deleted successfully!');
    }
}