<?php

namespace App\Http\Controllers;

use App\Models\Project;
use App\Models\Employee;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;

class ProjectTeamController extends Controller
{
    public function index(Project $project): View
    {
        $this->authorize('update', $project);
        $project->load('employees');

        // Get employees not yet on this project to populate the dropdown
        $assignedEmployeeIds = $project->employees->pluck('id')->toArray();
        $availableEmployees = Employee::whereNotIn('id', $assignedEmployeeIds)
            ->where('employment_status', 'active')
            ->orderBy('first_name')
            ->get();

        return view('projects.team.index', compact('project', 'availableEmployees'));
    }

    public function store(Request $request, Project $project): RedirectResponse
    {
        $this->authorize('update', $project);
        $request->validate([
            'employee_id' => 'required|exists:employees,id',
            'role' => 'required|string|in:member,manager,lead,engineer,designer,developer,analyst,consultant,specialist,coordinator,supervisor',
        ]);

        // Use syncWithoutDetaching to avoid adding duplicates
        // Add created_by to the pivot data
        $project->employees()->syncWithoutDetaching([
            $request->employee_id => [
                'role' => $request->role,
                'created_by' => Auth::id()
            ]
        ]);

        // If the role is manager, update the project manager field
        if ($request->role === 'manager') {
            $employee = Employee::find($request->employee_id);
            $project->update([
                'project_manager' => $employee->full_name,
                'project_manager_email' => $employee->email,
                'project_manager_phone' => $employee->phone,
            ]);
        }

        return redirect()->route('projects.team.index', $project)
                         ->with('success', 'Employee added to project team.');
    }

    public function destroy(Project $project, Employee $employee): RedirectResponse
    {
        $this->authorize('update', $project);
        
        // Check if the employee was a project manager
        $wasManager = $project->employees()->where('employee_id', $employee->id)
                             ->where('role', 'manager')
                             ->exists();
                             
        $project->employees()->detach($employee->id);
        
        // If the employee was a manager, clear the project manager fields
        if ($wasManager) {
            $project->update([
                'project_manager' => null,
                'project_manager_email' => null,
                'project_manager_phone' => null,
            ]);
        }

        return redirect()->route('projects.team.index', $project)
                         ->with('success', 'Employee removed from project team.');
    }
}