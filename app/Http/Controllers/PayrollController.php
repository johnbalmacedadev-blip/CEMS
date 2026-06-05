<?php

namespace App\Http\Controllers;

use App\Models\Employee;
use Illuminate\Http\Request;

class PayrollController extends Controller
{
    /**
     * Display the payroll page (employee list for payroll reference).
     */
    public function index(Request $request)
    {
        $status = $request->get('status', 'active');
        $contractType = $request->get('contract_type', 'all');
        $search = $request->get('search');

        $query = Employee::query();

        if ($status !== 'all') {
            $query->where('status', $status);
        }

        if ($contractType !== 'all') {
            $query->where('contract_type', $contractType);
        }

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('first_name', 'LIKE', "%{$search}%")
                    ->orWhere('middle_name', 'LIKE', "%{$search}%")
                    ->orWhere('last_name', 'LIKE', "%{$search}%")
                    ->orWhere('role', 'LIKE', "%{$search}%")
                    ->orWhere('location', 'LIKE', "%{$search}%");
            });
        }

        $employees = $query->orderBy('last_name')->orderBy('first_name')->paginate(15)->withQueryString();

        $activeCount = Employee::where('status', 'active')->count();
        $inactiveCount = Employee::where('status', 'inactive')->count();
        $probationaryCount = Employee::where('contract_type', 'PROBATIONARY')->where('status', 'active')->count();
        $regularCount = Employee::where('contract_type', 'REGULAR')->where('status', 'active')->count();

        return view('payroll.index', compact(
            'employees',
            'status',
            'contractType',
            'search',
            'activeCount',
            'inactiveCount',
            'probationaryCount',
            'regularCount'
        ));
    }
}
