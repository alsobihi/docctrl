<?php

namespace App\Http\Controllers;

use App\Models\Employee;
use App\Models\Workflow;
use App\Models\EmployeeWorkflow;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class ProcessWorkflowController extends Controller
{
    public function create(): View
    {
        $employees = Employee::orderBy('first_name')->get();
        // Workflows are now fetched dynamically via API, so we don't pass them here.
        return view('process-workflow.create', compact('employees'));
    }

    /**
     * Redirects from the POST form to the proper GET route.
     */
    public function redirectToShow(Request $request): RedirectResponse
    {
        $request->validate([
            'employee_id' => 'required|exists:employees,id',
            'workflow_id' => 'required|exists:workflows,id',
        ]);

        return redirect()->route('process-workflow.show', [
            'employee' => $request->employee_id,
            'workflow' => $request->workflow_id,
        ]);
    }

    public function show(Employee $employee, Workflow $workflow): View
    {
        $employee->load('documents');
        $workflow->load('documentTypes');

        $employeeWorkflow = EmployeeWorkflow::firstOrCreate(
            ['employee_id' => $employee->id, 'workflow_id' => $workflow->id],
            ['status' => 'in_progress', 'created_by' => Auth::id()]
        );

        $employee->checkAndUpdateWorkflowStatus();
        $employeeWorkflow->refresh();

        $employeeDocumentTypeIds = $employee->documents->pluck('document_type_id')->toArray();

        $checklist = $workflow->documentTypes->map(function ($requiredDocType) use ($employeeDocumentTypeIds) {
            return (object) [
                'name' => $requiredDocType->name,
                'category' => $requiredDocType->category,
                'is_complete' => in_array($requiredDocType->id, $employeeDocumentTypeIds),
                'document_type_id' => $requiredDocType->id,
            ];
        });

        return view('process-workflow.show', compact('employee', 'workflow', 'checklist', 'employeeWorkflow'));
    }
}
