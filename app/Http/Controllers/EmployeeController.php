<?php


namespace App\Http\Controllers;

use App\Models\Employee;
use App\Models\Plant;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;

class EmployeeController extends Controller
{
    public function index(Request $request): View
    {
        $user = Auth::user();
        $query = Employee::with('plant');

        if ($request->filled('search')) {
            $searchTerm = $request->search;
            $query->where(function($q) use ($searchTerm) {
                $q->where('first_name', 'like', "%{$searchTerm}%")
                  ->orWhere('last_name', 'like', "%{$searchTerm}%")
                  ->orWhere('employee_code', 'like', "%{$searchTerm}%");
            });
        }

        if ($request->filled('plant_id')) {
            $query->where('plant_id', $request->plant_id);
        }

        if ($user->role === 'manager') {
            $query->where('plant_id', $user->plant_id);
        }

        $employees = $query->latest()->paginate(10)->withQueryString();
        $plants = Plant::orderBy('name')->get();

        return view('employees.index', compact('employees', 'plants'));
    }

    public function create(): View
    {
        $plants = Plant::orderBy('name')->get();
        return view('employees.create', compact('plants'));
    }

    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'employee_code' => 'required|string|max:255|unique:employees',
            'plant_id' => 'required|exists:plants,id',
        ]);

        Employee::create([
            'first_name' => $request->first_name,
            'last_name' => $request->last_name,
            'employee_code' => $request->employee_code,
            'plant_id' => $request->plant_id,
            'created_by' => Auth::id(),
        ]);

        return redirect()->route('employees.index')
                         ->with('success', 'Employee created successfully.');
    }

    public function show(Employee $employee): View
    {
        $employee->load([
            'plant',
            'documents.documentType',
            'projects',
            'assignedWorkflows.workflow'
        ]);

        return view('employees.show', compact('employee'));
    }

    public function edit(Employee $employee): View
    {
        $plants = Plant::orderBy('name')->get();
        return view('employees.edit', compact('employee', 'plants'));
    }

    public function update(Request $request, Employee $employee): RedirectResponse
    {
        $request->validate([
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'employee_code' => 'required|string|max:255|unique:employees,employee_code,' . $employee->id,
            'plant_id' => 'required|exists:plants,id',
        ]);

        $employee->update([
            'first_name' => $request->first_name,
            'last_name' => $request->last_name,
            'employee_code' => $request->employee_code,
            'plant_id' => $request->plant_id,
            'updated_by' => Auth::id(),
        ]);

        return redirect()->route('employees.index')
                         ->with('success', 'Employee updated successfully.');
    }

    public function destroy(Employee $employee): RedirectResponse
    {
        $employee->update(['deleted_by' => Auth::id()]);
        $employee->delete();

        return redirect()->route('employees.index')
                         ->with('success', 'Employee deleted successfully.');
    }
}
