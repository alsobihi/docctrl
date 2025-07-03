<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\Workflow;
use App\Models\Employee;
use App\Models\EmployeeWorkflow;
use App\Models\DocumentType;
use App\Models\Plant;
use App\Models\Project;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;

class WorkflowController extends Controller
{
    /**
     * Display a listing of workflows
     */
    public function index(Request $request): JsonResponse
    {
        $query = Workflow::with(['plant', 'project'])->withCount('documentTypes');

        // Apply filters
        if ($request->filled('scope')) {
            switch ($request->scope) {
                case 'global':
                    $query->whereNull('plant_id')->whereNull('project_id');
                    break;
                case 'plant':
                    $query->whereNotNull('plant_id');
                    break;
                case 'project':
                    $query->whereNotNull('project_id');
                    break;
            }
        }

        if ($request->filled('plant_id')) {
            $query->where('plant_id', $request->plant_id);
        }

        if ($request->filled('project_id')) {
            $query->where('project_id', $request->project_id);
        }

        $perPage = min($request->get('per_page', 15), 100);
        $workflows = $query->latest()->paginate($perPage);

        return response()->json([
            'success' => true,
            'data' => $workflows->items(),
            'meta' => [
                'current_page' => $workflows->currentPage(),
                'last_page' => $workflows->lastPage(),
                'per_page' => $workflows->perPage(),
                'total' => $workflows->total(),
            ]
        ]);
    }

    /**
     * Store a newly created workflow
     */
    public function store(Request $request): JsonResponse
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'scope' => ['required', Rule::in(['global', 'plant', 'project'])],
            'plant_id' => 'required_if:scope,plant|nullable|exists:plants,id',
            'project_id' => 'required_if:scope,project|nullable|exists:projects,id',
            'is_reopenable' => 'boolean',
        ]);

        $workflow = Workflow::create([
            'name' => $request->name,
            'description' => $request->description,
            'is_reopenable' => $request->boolean('is_reopenable'),
            'plant_id' => $request->scope === 'plant' ? $request->plant_id : null,
            'project_id' => $request->scope === 'project' ? $request->project_id : null,
            'created_by' => Auth::id(),
        ]);

        $workflow->load(['plant', 'project']);

        return response()->json([
            'success' => true,
            'data' => $workflow,
            'message' => 'Workflow created successfully'
        ], 201);
    }

    /**
     * Display the specified workflow
     */
    public function show(Workflow $workflow): JsonResponse
    {
        $workflow->load(['plant', 'project', 'documentTypes']);

        return response()->json([
            'success' => true,
            'data' => $workflow
        ]);
    }

    /**
     * Update the specified workflow
     */
    public function update(Request $request, Workflow $workflow): JsonResponse
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'scope' => ['required', Rule::in(['global', 'plant', 'project'])],
            'plant_id' => 'required_if:scope,plant|nullable|exists:plants,id',
            'project_id' => 'required_if:scope,project|nullable|exists:projects,id',
            'is_reopenable' => 'boolean',
        ]);

        $workflow->update([
            'name' => $request->name,
            'description' => $request->description,
            'is_reopenable' => $request->boolean('is_reopenable'),
            'plant_id' => $request->scope === 'plant' ? $request->plant_id : null,
            'project_id' => $request->scope === 'project' ? $request->project_id : null,
            'updated_by' => Auth::id(),
        ]);

        $workflow->load(['plant', 'project', 'documentTypes']);

        return response()->json([
            'success' => true,
            'data' => $workflow,
            'message' => 'Workflow updated successfully'
        ]);
    }

    /**
     * Remove the specified workflow
     */
    public function destroy(Workflow $workflow): JsonResponse
    {
        $workflow->update(['deleted_by' => Auth::id()]);
        $workflow->delete();

        return response()->json([
            'success' => true,
            'message' => 'Workflow deleted successfully'
        ]);
    }

    /**
     * Add document type to workflow
     */
    public function addDocumentType(Request $request, Workflow $workflow): JsonResponse
    {
        $request->validate([
            'document_type_id' => 'required|exists:document_types,id',
        ]);

        $workflow->documentTypes()->attach($request->document_type_id, [
            'created_by' => Auth::id(),
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Document type added to workflow'
        ]);
    }

    /**
     * Remove document type from workflow
     */
    public function removeDocumentType(Workflow $workflow, DocumentType $documentType): JsonResponse
    {
        $workflow->documentTypes()->detach($documentType->id);

        return response()->json([
            'success' => true,
            'message' => 'Document type removed from workflow'
        ]);
    }

    /**
     * Start workflow for employee
     */
    public function startForEmployee(Request $request, Workflow $workflow): JsonResponse
    {
        $request->validate([
            'employee_id' => 'required|exists:employees,id',
        ]);

        $employee = Employee::findOrFail($request->employee_id);

        $employeeWorkflow = EmployeeWorkflow::firstOrCreate(
            ['employee_id' => $employee->id, 'workflow_id' => $workflow->id],
            ['status' => 'in_progress', 'created_by' => Auth::id()]
        );

        // Check and update workflow status
        $employee->checkAndUpdateWorkflowStatus();
        $employeeWorkflow->refresh();

        $employeeWorkflow->load(['employee', 'workflow']);

        return response()->json([
            'success' => true,
            'data' => $employeeWorkflow,
            'message' => 'Workflow started for employee'
        ]);
    }

    /**
     * Get workflow checklist for employee
     */
    public function getChecklist(Workflow $workflow, Employee $employee): JsonResponse
    {
        $employee->load('documents');
        $workflow->load('documentTypes');

        $employeeWorkflow = EmployeeWorkflow::where([
            'employee_id' => $employee->id,
            'workflow_id' => $workflow->id
        ])->first();

        $employeeDocumentTypeIds = $employee->documents->pluck('document_type_id')->toArray();

        $checklist = $workflow->documentTypes->map(function ($requiredDocType) use ($employeeDocumentTypeIds) {
            return [
                'document_type_id' => $requiredDocType->id,
                'name' => $requiredDocType->name,
                'category' => $requiredDocType->category,
                'is_complete' => in_array($requiredDocType->id, $employeeDocumentTypeIds),
            ];
        });

        return response()->json([
            'success' => true,
            'data' => [
                'workflow' => $workflow,
                'employee' => $employee,
                'employee_workflow' => $employeeWorkflow,
                'checklist' => $checklist,
            ]
        ]);
    }

    /**
     * Get in-progress workflows
     */
    public function inProgress(Request $request): JsonResponse
    {
        $user = Auth::user();
        $query = EmployeeWorkflow::with(['employee', 'workflow'])
            ->where('status', 'in_progress');

        if ($user->role === 'manager') {
            $query->whereHas('employee', function ($q) use ($user) {
                $q->where('plant_id', $user->plant_id);
            });
        }

        $perPage = min($request->get('per_page', 15), 100);
        $workflows = $query->latest()->paginate($perPage);

        return response()->json([
            'success' => true,
            'data' => $workflows->items(),
            'meta' => [
                'current_page' => $workflows->currentPage(),
                'last_page' => $workflows->lastPage(),
                'per_page' => $workflows->perPage(),
                'total' => $workflows->total(),
            ]
        ]);
    }
}