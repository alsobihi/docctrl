<?php

namespace App\Http\Controllers;

use App\Models\Employee;
use App\Models\Workflow;
use App\Models\EmployeeWorkflow;
use App\Models\WorkflowHistory;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

class ProcessWorkflowController extends Controller
{
    public function create(): View
    {
        $employees = Employee::where('employment_status', 'active')
            ->orderBy('first_name')
            ->get();
        // Workflows are now fetched dynamically via API, so we don't pass them here.
        return view('process-workflow.create', compact('employees'));
    }

    /**
     * Redirects from the POST form to the proper GET route.
     */
    public function redirectToShow(Request $request): RedirectResponse
    {
        try {
            $validated = $request->validate([
                'employee_id' => 'required|exists:employees,id',
                'workflow_id' => 'required|exists:workflows,id',
            ]);

            return redirect()->route('process-workflow.show', [
                'employee' => $validated['employee_id'],
                'workflow' => $validated['workflow_id'],
            ]);
        } catch (\Exception $e) {
            Log::error('Error redirecting to workflow: ' . $e->getMessage(), [
                'request' => $request->all(),
                'trace' => $e->getTraceAsString()
            ]);
            
            return redirect()->back()
                ->withInput()
                ->withErrors(['error' => 'Failed to start workflow: ' . $e->getMessage()]);
        }
    }

    public function show(Employee $employee, Workflow $workflow): View
    {
        try {
            // Load only valid documents (not deleted and not expired)
            $employee->load(['documents' => function($query) {
                $query->whereNull('deleted_at')
                      ->where('expiry_date', '>', now());
            }]);
            
            $workflow->load('documentTypes');

            $employeeWorkflow = EmployeeWorkflow::firstOrCreate(
                ['employee_id' => $employee->id, 'workflow_id' => $workflow->id],
                ['status' => 'in_progress', 'created_by' => Auth::id()]
            );

            // Create a history record if this is a new workflow assignment
            if ($employeeWorkflow->wasRecentlyCreated) {
                WorkflowHistory::create([
                    'workflow_id' => $workflow->id,
                    'employee_id' => $employee->id,
                    'employee_workflow_id' => $employeeWorkflow->id,
                    'action' => 'started',
                    'details' => "Workflow started for {$employee->full_name}",
                    'created_by' => Auth::id(),
                ]);
            }

            // Update workflow status to ensure it's accurate
            $employee->checkAndUpdateWorkflowStatus();
            $employeeWorkflow->refresh();
            
            // Load workflow history for display
            $employeeWorkflow->load('history');

            // Get current valid document type IDs
            $employeeDocumentTypeIds = $employee->documents
                ->pluck('document_type_id')
                ->toArray();

            $checklist = $workflow->documentTypes->map(function ($requiredDocType) use ($employeeDocumentTypeIds) {
                return (object) [
                    'name' => $requiredDocType->name,
                    'category' => $requiredDocType->category,
                    'is_complete' => in_array($requiredDocType->id, $employeeDocumentTypeIds),
                    'document_type_id' => $requiredDocType->id,
                ];
            });

            return view('process-workflow.show', compact('employee', 'workflow', 'checklist', 'employeeWorkflow'));
        } catch (\Exception $e) {
            Log::error('Error showing workflow: ' . $e->getMessage(), [
                'employee_id' => $employee->id,
                'workflow_id' => $workflow->id,
                'trace' => $e->getTraceAsString()
            ]);
            
            return view('error', [
                'message' => 'Failed to load workflow: ' . $e->getMessage(),
                'back_url' => route('process-workflow.create')
            ]);
        }
    }
}