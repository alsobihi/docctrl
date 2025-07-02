<?php

namespace App\Http\Controllers;

use App\Models\Project;
use App\Models\Employee;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth; // <-- Add this line

class ProjectTeamController extends Controller
{
    public function index(Project $project): View
    {
        $this->authorize('update', $project); // <-- Enforce policy
        $project->load('employees'); // Eager load the team members

        // Get employees not yet on this project to populate the dropdown
        $assignedEmployeeIds = $project->employees->pluck('id')->toArray();
        $availableEmployees = Employee::whereNotIn('id', $assignedEmployeeIds)->orderBy('first_name')->get();

        return view('projects.team.index', compact('project', 'availableEmployees'));
    }

    public function store(Request $request, Project $project): RedirectResponse
    {
        $this->authorize('update', $project); // <-- Enforce policy
        $request->validate([
            'employee_id' => 'required|exists:employees,id',
            'role' => 'required|string|in:manager,member',
        ]);

        // Use syncWithoutDetaching to avoid adding duplicates
        // Add created_by to the pivot data
        $project->employees()->syncWithoutDetaching([
            $request->employee_id => [
                'role' => $request->role,
                'created_by' => Auth::id() // <-- This line fixes the error
            ]
        ]);

        return redirect()->route('projects.team.index', $project)
                         ->with('success', 'Employee added to project team.');
    }

    public function destroy(Project $project, Employee $employee): RedirectResponse
    {
        $this->authorize('update', $project); // <-- Enforce policy
        $project->employees()->detach($employee->id);

        return redirect()->route('projects.team.index', $project)
                         ->with('success', 'Employee removed from project team.');
    }
}
