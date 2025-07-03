<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\Employee;
use App\Models\Plant;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;

class EmployeeController extends Controller
{
    /**
     * Display a listing of employees
     */
    public function index(Request $request): JsonResponse
    {
        $user = Auth::user();
        $query = Employee::with(['plant', 'documents.documentType']);

        // Apply role-based filtering
        if ($user->role === 'manager') {
            $query->where('plant_id', $user->plant_id);
        }

        // Apply search filter
        if ($request->filled('search')) {
            $searchTerm = $request->search;
            $query->where(function($q) use ($searchTerm) {
                $q->where('first_name', 'like', "%{$searchTerm}%")
                  ->orWhere('last_name', 'like', "%{$searchTerm}%")
                  ->orWhere('employee_code', 'like', "%{$searchTerm}%");
            });
        }

        // Apply plant filter
        if ($request->filled('plant_id')) {
            $query->where('plant_id', $request->plant_id);
        }

        $perPage = min($request->get('per_page', 15), 100);
        $employees = $query->latest()->paginate($perPage);

        return response()->json([
            'success' => true,
            'data' => $employees->items(),
            'meta' => [
                'current_page' => $employees->currentPage(),
                'last_page' => $employees->lastPage(),
                'per_page' => $employees->perPage(),
                'total' => $employees->total(),
            ]
        ]);
    }

    /**
     * Store a newly created employee
     */
    public function store(Request $request): JsonResponse
    {
        $request->validate([
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'employee_code' => 'required|string|max:255|unique:employees',
            'plant_id' => 'required|exists:plants,id',
        ]);

        $employee = Employee::create([
            'first_name' => $request->first_name,
            'last_name' => $request->last_name,
            'employee_code' => $request->employee_code,
            'plant_id' => $request->plant_id,
            'created_by' => Auth::id(),
        ]);

        $employee->load(['plant', 'documents.documentType']);

        return response()->json([
            'success' => true,
            'data' => $employee,
            'message' => 'Employee created successfully'
        ], 201);
    }

    /**
     * Display the specified employee
     */
    public function show(Employee $employee): JsonResponse
    {
        $employee->load([
            'plant',
            'documents.documentType',
            'projects',
            'assignedWorkflows.workflow'
        ]);

        return response()->json([
            'success' => true,
            'data' => $employee
        ]);
    }

    /**
     * Update the specified employee
     */
    public function update(Request $request, Employee $employee): JsonResponse
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

        $employee->load(['plant', 'documents.documentType']);

        return response()->json([
            'success' => true,
            'data' => $employee,
            'message' => 'Employee updated successfully'
        ]);
    }

    /**
     * Remove the specified employee
     */
    public function destroy(Employee $employee): JsonResponse
    {
        $employee->update(['deleted_by' => Auth::id()]);
        $employee->delete();

        return response()->json([
            'success' => true,
            'message' => 'Employee deleted successfully'
        ]);
    }

    /**
     * Get employee's documents
     */
    public function documents(Employee $employee): JsonResponse
    {
        $documents = $employee->documents()
            ->with(['documentType'])
            ->latest()
            ->get();

        return response()->json([
            'success' => true,
            'data' => $documents
        ]);
    }

    /**
     * Get employee's workflows
     */
    public function workflows(Employee $employee): JsonResponse
    {
        $workflows = $employee->assignedWorkflows()
            ->with(['workflow'])
            ->latest()
            ->get();

        return response()->json([
            'success' => true,
            'data' => $workflows
        ]);
    }
}