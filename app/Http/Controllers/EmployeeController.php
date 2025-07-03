<?php

namespace App\Http\Controllers;

use App\Models\Employee;
use App\Models\Plant;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class EmployeeController extends Controller
{
    public function index(Request $request): View
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
                  ->orWhere('employee_code', 'like', "%{$searchTerm}%")
                  ->orWhere('position', 'like', "%{$searchTerm}%")
                  ->orWhere('department', 'like', "%{$searchTerm}%")
                  ->orWhere('badge_number', 'like', "%{$searchTerm}%");
            });
        }

        // Apply filters
        if ($request->filled('plant_id')) {
            $query->where('plant_id', $request->plant_id);
        }

        if ($request->filled('department')) {
            $query->where('department', $request->department);
        }

        if ($request->filled('position')) {
            $query->where('position', $request->position);
        }

        if ($request->filled('employment_status')) {
            $query->where('employment_status', $request->employment_status);
        }

        if ($request->filled('contract_type')) {
            $query->where('contract_type', $request->contract_type);
        }

        $perPage = min($request->get('per_page', 12), 50);
        $employees = $query->latest()->paginate($perPage)->withQueryString();

        // Get filter options
        $plants = Plant::orderBy('name')->get();
        $departments = Employee::whereNotNull('department')->distinct()->pluck('department')->sort();
        $positions = Employee::whereNotNull('position')->distinct()->pluck('position')->sort();

        return view('employees.index', compact('employees', 'plants', 'departments', 'positions'));
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
            'profile_photo' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'phone' => 'nullable|string|max:20',
            'email' => 'nullable|email|max:255',
            'address' => 'nullable|string|max:500',
            'date_of_birth' => 'nullable|date|before:today',
            'nationality' => 'nullable|string|max:100',
            'position' => 'nullable|string|max:255',
            'department' => 'nullable|string|max:255',
            'hire_date' => 'nullable|date',
            'employment_status' => 'required|in:active,inactive,terminated',
            'emergency_contact_name' => 'nullable|string|max:255',
            'emergency_contact_phone' => 'nullable|string|max:20',
            'emergency_contact_relationship' => 'nullable|string|max:100',
            'salary' => 'nullable|numeric|min:0',
            'contract_type' => 'nullable|in:permanent,contract,temporary',
            'contract_end_date' => 'nullable|date|after:today',
            'notes' => 'nullable|string|max:1000',
            'skills' => 'nullable|array',
            'badge_number' => 'nullable|string|max:50|unique:employees',
        ]);

        $data = $request->except(['profile_photo', 'skills']);
        $data['created_by'] = Auth::id();

        // Handle profile photo upload
        if ($request->hasFile('profile_photo')) {
            $photoPath = $request->file('profile_photo')->store('employee-photos', 'public');
            $data['profile_photo_path'] = $photoPath;
        }

        // Handle skills array
        if ($request->filled('skills')) {
            $data['skills'] = array_filter($request->skills);
        }

        $employee = Employee::create($data);

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
            'profile_photo' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'phone' => 'nullable|string|max:20',
            'email' => 'nullable|email|max:255',
            'address' => 'nullable|string|max:500',
            'date_of_birth' => 'nullable|date|before:today',
            'nationality' => 'nullable|string|max:100',
            'position' => 'nullable|string|max:255',
            'department' => 'nullable|string|max:255',
            'hire_date' => 'nullable|date',
            'employment_status' => 'required|in:active,inactive,terminated',
            'emergency_contact_name' => 'nullable|string|max:255',
            'emergency_contact_phone' => 'nullable|string|max:20',
            'emergency_contact_relationship' => 'nullable|string|max:100',
            'salary' => 'nullable|numeric|min:0',
            'contract_type' => 'nullable|in:permanent,contract,temporary',
            'contract_end_date' => 'nullable|date|after:today',
            'notes' => 'nullable|string|max:1000',
            'skills' => 'nullable|array',
            'badge_number' => 'nullable|string|max:50|unique:employees,badge_number,' . $employee->id,
        ]);

        $data = $request->except(['profile_photo', 'skills']);
        $data['updated_by'] = Auth::id();

        // Handle profile photo upload
        if ($request->hasFile('profile_photo')) {
            // Delete old photo if exists
            if ($employee->profile_photo_path && Storage::disk('public')->exists($employee->profile_photo_path)) {
                Storage::disk('public')->delete($employee->profile_photo_path);
            }
            
            $photoPath = $request->file('profile_photo')->store('employee-photos', 'public');
            $data['profile_photo_path'] = $photoPath;
        }

        // Handle skills array
        if ($request->filled('skills')) {
            $data['skills'] = array_filter($request->skills);
        }

        $employee->update($data);

        return redirect()->route('employees.index')
                         ->with('success', 'Employee updated successfully.');
    }

    public function destroy(Employee $employee): RedirectResponse
    {
        // Delete profile photo if exists
        if ($employee->profile_photo_path && Storage::disk('public')->exists($employee->profile_photo_path)) {
            Storage::disk('public')->delete($employee->profile_photo_path);
        }

        $employee->update(['deleted_by' => Auth::id()]);
        $employee->delete();

        return redirect()->route('employees.index')
                         ->with('success', 'Employee deleted successfully.');
    }
}