<?php


namespace App\Http\Controllers;
use App\Models\Employee;
use App\Models\DocumentType;
use App\Models\EmployeeDocument;
use App\Services\ValidityRuleService;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
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
        $employee->documents()->create([
            'document_type_id' => $request->document_type_id,
            'issue_date' => $issueDate,
            'expiry_date' => $expiryDate,
            'file_path' => $filePath,
            'data' => $request->custom_data,
            'created_by' => Auth::id(),
        ]);
        $employee->checkAndUpdateWorkflowStatus();
        if ($request->has('workflow_id')) {
            return redirect()->route('process-workflow.show', ['employee' => $employee->id, 'workflow' => $request->workflow_id])
                             ->with('success', 'Document added successfully.');
        }
        return redirect()->route('employees.documents.index', $employee)
                         ->with('success', 'Document added successfully.');
    }
    public function destroy(EmployeeDocument $document): RedirectResponse {
        $employee = $document->employee;
        $document->delete();
        $employee->checkAndUpdateWorkflowStatus();
        return redirect()->route('employees.documents.index', $employee)
                         ->with('success', 'Document deleted successfully.');
    }
}
