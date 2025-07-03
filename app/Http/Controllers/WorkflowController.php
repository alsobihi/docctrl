<?php

namespace App\Http\Controllers;

use App\Models\Workflow;
use App\Models\DocumentType;
use App\Models\Plant;
use App\Models\Project;
use App\Models\WorkflowHistory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Validation\Rule;

class WorkflowController extends Controller
{
    public function index(): View
    {
        $workflows = Workflow::with(['plant', 'project'])->withCount('documentTypes')->latest()->paginate(10);
        return view('workflows.index', compact('workflows'));
    }

    public function create(): View
    {
        $plants = Plant::orderBy('name')->get();
        $projects = Project::orderBy('name')->get();
        return view('workflows.create', compact('plants', 'projects'));
    }

    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'scope' => ['required', Rule::in(['global', 'plant', 'project'])],
            'plant_id' => 'required_if:scope,plant|nullable|exists:plants,id',
            'project_id' => 'required_if:scope,project|nullable|exists:projects,id',
            'is_reopenable' => 'boolean',
            'auto_reopen_on_expiry' => 'boolean',
            'auto_reopen_on_deletion' => 'boolean',
            'notification_days_before' => 'nullable|integer|min:1|max:90',
        ]);

        $workflow = Workflow::create([
            'name' => $request->name,
            'description' => $request->description,
            'is_reopenable' => $request->boolean('is_reopenable'),
            'auto_reopen_on_expiry' => $request->boolean('auto_reopen_on_expiry'),
            'auto_reopen_on_deletion' => $request->boolean('auto_reopen_on_deletion'),
            'notification_days_before' => $request->notification_days_before,
            'plant_id' => $request->scope === 'plant' ? $request->plant_id : null,
            'project_id' => $request->scope === 'project' ? $request->project_id : null,
            'created_by' => Auth::id(),
        ]);

        return redirect()->route('workflows.edit', $workflow)
                        ->with('success', 'Workflow created successfully.');
    }

    public function edit(Workflow $workflow): View
    {
        $workflow->load('documentTypes');
        $existingDocTypeIds = $workflow->documentTypes->pluck('id')->toArray();
        $availableDocumentTypes = DocumentType::whereNotIn('id', $existingDocTypeIds)->orderBy('name')->get();
        $plants = Plant::orderBy('name')->get();
        $projects = Project::orderBy('name')->get();

        return view('workflows.edit', compact('workflow', 'availableDocumentTypes', 'plants', 'projects'));
    }

    public function update(Request $request, Workflow $workflow): RedirectResponse
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'scope' => ['required', Rule::in(['global', 'plant', 'project'])],
            'plant_id' => 'required_if:scope,plant|nullable|exists:plants,id',
            'project_id' => 'required_if:scope,project|nullable|exists:projects,id',
            'is_reopenable' => 'boolean',
            'auto_reopen_on_expiry' => 'boolean',
            'auto_reopen_on_deletion' => 'boolean',
            'notification_days_before' => 'nullable|integer|min:1|max:90',
        ]);

        $workflow->update([
            'name' => $request->name,
            'description' => $request->description,
            'is_reopenable' => $request->boolean('is_reopenable'),
            'auto_reopen_on_expiry' => $request->boolean('auto_reopen_on_expiry'),
            'auto_reopen_on_deletion' => $request->boolean('auto_reopen_on_deletion'),
            'notification_days_before' => $request->notification_days_before,
            'plant_id' => $request->scope === 'plant' ? $request->plant_id : null,
            'project_id' => $request->scope === 'project' ? $request->project_id : null,
            'updated_by' => Auth::id(),
        ]);

        return redirect()->route('workflows.edit', $workflow)->with('success', 'Workflow updated successfully.');
    }

    public function destroy(Workflow $workflow): RedirectResponse
    {
        // Create history records for all assignments before deletion
        foreach ($workflow->assignments as $assignment) {
            WorkflowHistory::create([
                'workflow_id' => $workflow->id,
                'employee_id' => $assignment->employee_id,
                'employee_workflow_id' => $assignment->id,
                'action' => 'workflow_deleted',
                'details' => "Workflow '{$workflow->name}' was deleted",
                'created_by' => Auth::id(),
            ]);
        }

        // The database constraints will handle deleting pivot records
        $workflow->update(['deleted_by' => Auth::id()]);
        $workflow->delete();

        return redirect()->route('workflows.index')
                        ->with('success', 'Workflow deleted successfully.');
    }

    /**
     * Show workflow history
     */
    public function history(Workflow $workflow): View
    {
        $workflow->load(['history.employee', 'history.documentType', 'history.createdBy']);
        
        return view('workflows.history', compact('workflow'));
    }

    /**
     * Show workflow statistics
     */
    public function statistics(Workflow $workflow): View
    {
        $workflow->load(['assignments.employee', 'documentTypes']);
        
        // Calculate statistics
        $totalAssignments = $workflow->assignments->count();
        $completedAssignments = $workflow->assignments->where('status', 'completed')->count();
        $inProgressAssignments = $workflow->assignments->where('status', 'in_progress')->count();
        $completionRate = $totalAssignments > 0 ? round(($completedAssignments / $totalAssignments) * 100, 1) : 0;
        
        // Get reopened workflows
        $reopenedWorkflows = $workflow->assignments()
            ->whereNotNull('reopened_at')
            ->with('employee')
            ->get();
        
        // Get document compliance rate
        $documentTypes = $workflow->documentTypes;
        $documentCompliance = [];
        
        foreach ($documentTypes as $docType) {
            $requiredCount = $totalAssignments;
            $presentCount = 0;
            
            foreach ($workflow->assignments as $assignment) {
                $hasDocument = $assignment->employee->documents()
                    ->where('document_type_id', $docType->id)
                    ->exists();
                
                if ($hasDocument) {
                    $presentCount++;
                }
            }
            
            $complianceRate = $requiredCount > 0 ? round(($presentCount / $requiredCount) * 100, 1) : 0;
            
            $documentCompliance[] = [
                'document_type' => $docType,
                'required_count' => $requiredCount,
                'present_count' => $presentCount,
                'compliance_rate' => $complianceRate,
            ];
        }
        
        return view('workflows.statistics', compact(
            'workflow',
            'totalAssignments',
            'completedAssignments',
            'inProgressAssignments',
            'completionRate',
            'reopenedWorkflows',
            'documentCompliance'
        ));
    }
}