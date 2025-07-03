<?php

namespace App\Http\Controllers;
use App\Models\Employee;
use App\Models\DocumentType;
use App\Models\EmployeeDocument;
use App\Models\Workflow;
use App\Models\WorkflowHistory;
use App\Services\ValidityRuleService;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;
use Carbon\Carbon;
use Illuminate\Validation\Rule;

class EmployeeDocumentController extends Controller {
    public function index(Employee $employee): View {
        $documents = $employee->documents()->with('documentType')->latest()->get();
        return view('employees.documents.index', compact('employee', 'documents'));
    }
    
    public function create(Request $request, Employee $employee): View {
        $documentTypes = DocumentType::with('template')->orderBy('name')->get();
        $selectedDocumentTypeId = $request->query('document_type_id');
        return view('employees.documents.create', compact('employee', 'documentTypes', 'selectedDocumentTypeId'));
    }
    
    public function store(Request $request, Employee $employee, ValidityRuleService $validityRuleService): RedirectResponse {
        $documentType = DocumentType::findOrFail($request->document_type_id);
        $request->validate([
            'document_type_id' => 'required|exists:document_types,id',
            'issue_date' => 'required|date',
            'expiry_date' => ['nullable', Rule::requiredIf(fn () => !$documentType->validity_rule), 'date', 'after:issue_date'],
            'file' => 'required|file|mimes:pdf,jpg,png|max:2048',
            'custom_data' => 'nullable|array',
        ]);
        
        $issueDate = Carbon::parse($request->issue_date);
        $expiryDate = null;
        
        if ($documentType->validity_rule) {
            $expiryDate = $validityRuleService->calculateExpiryDate($documentType, $employee, $issueDate);
            if (!$expiryDate) {
                return back()->withInput()->withErrors(['expiry_date' => 'Could not calculate expiry date. Check dependencies.']);
            }
        } else {
            $expiryDate = Carbon::parse($request->expiry_date);
        }
        
        $filePath = $request->file('file')->store('employee-documents', 'public');
        
        $document = $employee->documents()->create([
            'document_type_id' => $request->document_type_id,
            'issue_date' => $issueDate,
            'expiry_date' => $expiryDate,
            'file_path' => $filePath,
            'data' => $request->custom_data,
            'created_by' => Auth::id(),
        ]);
        
        // Update workflow status
        $employee->checkAndUpdateWorkflowStatus();
        
        // Create history record for document addition
        $this->createDocumentHistoryRecord($employee, $document, 'document_added');
        
        if ($request->has('workflow_id')) {
            return redirect()->route('process-workflow.show', ['employee' => $employee->id, 'workflow' => $request->workflow_id])
                             ->with('success', 'Document added successfully.');
        }
        
        return redirect()->route('employees.documents.index', $employee)
                         ->with('success', 'Document added successfully.');
    }
    
    public function destroy(EmployeeDocument $document): RedirectResponse {
        $employee = $document->employee;
        $documentType = $document->documentType;
        
        // Create history record for document deletion
        $this->createDocumentHistoryRecord($employee, $document, 'document_deleted');
        
        // Delete file if exists
        if ($document->file_path && Storage::disk('public')->exists($document->file_path)) {
            Storage::disk('public')->delete($document->file_path);
        }
        
        $document->delete();
        
        // Check if any completed workflows need to be reopened
        if ($documentType) {
            Workflow::reopenWorkflowsForDocument($employee, $documentType, 'deleted');
        }
        
        // Update workflow status
        $employee->checkAndUpdateWorkflowStatus();
        
        return redirect()->route('employees.documents.index', $employee)
                         ->with('success', 'Document deleted successfully.');
    }
    
    /**
     * Create a history record for document actions
     */
    private function createDocumentHistoryRecord(Employee $employee, EmployeeDocument $document, string $action): void
    {
        // Find all workflows that require this document type
        $workflows = Workflow::whereHas('documentTypes', function($query) use ($document) {
            $query->where('document_types.id', $document->document_type_id);
        })->get();
        
        foreach ($workflows as $workflow) {
            // Find the employee workflow assignment if it exists
            $employeeWorkflow = $employee->assignedWorkflows()
                ->where('workflow_id', $workflow->id)
                ->first();
            
            WorkflowHistory::create([
                'workflow_id' => $workflow->id,
                'employee_id' => $employee->id,
                'employee_workflow_id' => $employeeWorkflow ? $employeeWorkflow->id : null,
                'action' => $action,
                'details' => "{$document->documentType->name} was " . ($action === 'document_added' ? 'added' : 'deleted'),
                'document_type_id' => $document->document_type_id,
                'document_id' => $document->id,
                'created_by' => Auth::id(),
            ]);
        }
    }
}