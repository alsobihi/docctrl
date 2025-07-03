<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\Employee;
use App\Models\EmployeeDocument;
use App\Models\DocumentType;
use App\Services\ValidityRuleService;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Carbon\Carbon;
use Illuminate\Validation\Rule;

class DocumentController extends Controller
{
    protected ValidityRuleService $validityRuleService;

    public function __construct(ValidityRuleService $validityRuleService)
    {
        $this->validityRuleService = $validityRuleService;
    }

    /**
     * Display a listing of documents
     */
    public function index(Request $request): JsonResponse
    {
        $user = Auth::user();
        $query = EmployeeDocument::with(['employee.plant', 'documentType']);

        // Apply role-based filtering
        if ($user->role === 'manager') {
            $query->whereHas('employee', function ($q) use ($user) {
                $q->where('plant_id', $user->plant_id);
            });
        }

        // Apply filters
        if ($request->filled('employee_id')) {
            $query->where('employee_id', $request->employee_id);
        }

        if ($request->filled('document_type_id')) {
            $query->where('document_type_id', $request->document_type_id);
        }

        if ($request->filled('status')) {
            $now = Carbon::now();
            switch ($request->status) {
                case 'expired':
                    $query->where('expiry_date', '<', $now);
                    break;
                case 'expiring_soon':
                    $query->whereBetween('expiry_date', [$now, $now->copy()->addDays(30)]);
                    break;
                case 'valid':
                    $query->where('expiry_date', '>', $now->copy()->addDays(30));
                    break;
            }
        }

        $perPage = min($request->get('per_page', 15), 100);
        $documents = $query->latest()->paginate($perPage);

        return response()->json([
            'success' => true,
            'data' => $documents->items(),
            'meta' => [
                'current_page' => $documents->currentPage(),
                'last_page' => $documents->lastPage(),
                'per_page' => $documents->perPage(),
                'total' => $documents->total(),
            ]
        ]);
    }

    /**
     * Store a newly created document
     */
    public function store(Request $request): JsonResponse
    {
        $documentType = DocumentType::findOrFail($request->document_type_id);

        $request->validate([
            'employee_id' => 'required|exists:employees,id',
            'document_type_id' => 'required|exists:document_types,id',
            'issue_date' => 'required|date',
            'expiry_date' => ['nullable', Rule::requiredIf(fn () => !$documentType->validity_rule), 'date', 'after:issue_date'],
            'file' => 'required|file|mimes:pdf,jpg,png|max:2048',
            'custom_data' => 'nullable|array',
        ]);

        $employee = Employee::findOrFail($request->employee_id);
        $issueDate = Carbon::parse($request->issue_date);
        $expiryDate = null;

        // Calculate expiry date based on validity rule
        if ($documentType->validity_rule) {
            $expiryDate = $this->validityRuleService->calculateExpiryDate($documentType, $employee, $issueDate);
            if (!$expiryDate) {
                return response()->json([
                    'success' => false,
                    'message' => 'Could not calculate expiry date. Check dependencies.',
                    'errors' => ['expiry_date' => ['Could not calculate expiry date']]
                ], 422);
            }
        } else {
            $expiryDate = Carbon::parse($request->expiry_date);
        }

        // Store file
        $filePath = $request->file('file')->store('employee-documents', 'public');

        $document = EmployeeDocument::create([
            'employee_id' => $request->employee_id,
            'document_type_id' => $request->document_type_id,
            'issue_date' => $issueDate,
            'expiry_date' => $expiryDate,
            'file_path' => $filePath,
            'data' => $request->custom_data,
            'created_by' => Auth::id(),
        ]);

        // Update workflow status
        $employee->checkAndUpdateWorkflowStatus();

        $document->load(['employee', 'documentType']);

        return response()->json([
            'success' => true,
            'data' => $document,
            'message' => 'Document created successfully'
        ], 201);
    }

    /**
     * Display the specified document
     */
    public function show(EmployeeDocument $document): JsonResponse
    {
        $document->load(['employee.plant', 'documentType']);

        return response()->json([
            'success' => true,
            'data' => $document
        ]);
    }

    /**
     * Remove the specified document
     */
    public function destroy(EmployeeDocument $document): JsonResponse
    {
        $employee = $document->employee;
        
        // Delete file if exists
        if ($document->file_path && Storage::disk('public')->exists($document->file_path)) {
            Storage::disk('public')->delete($document->file_path);
        }

        $document->delete();

        // Update workflow status
        $employee->checkAndUpdateWorkflowStatus();

        return response()->json([
            'success' => true,
            'message' => 'Document deleted successfully'
        ]);
    }

    /**
     * Download document file
     */
    public function download(EmployeeDocument $document)
    {
        if (!$document->file_path || !Storage::disk('public')->exists($document->file_path)) {
            return response()->json([
                'success' => false,
                'message' => 'File not found'
            ], 404);
        }

        return Storage::disk('public')->download($document->file_path);
    }

    /**
     * Get expiring documents report
     */
    public function expiringReport(Request $request): JsonResponse
    {
        $request->validate([
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
            'plant_id' => 'nullable|exists:plants,id',
        ]);

        $user = Auth::user();
        $query = EmployeeDocument::with(['employee.plant', 'documentType'])
            ->whereBetween('expiry_date', [$request->start_date, $request->end_date]);

        // Apply role-based filtering
        if ($user->role === 'manager') {
            $query->whereHas('employee', function ($q) use ($user) {
                $q->where('plant_id', $user->plant_id);
            });
        } elseif ($request->filled('plant_id')) {
            $query->whereHas('employee', function ($q) use ($request) {
                $q->where('plant_id', $request->plant_id);
            });
        }

        $documents = $query->orderBy('expiry_date')->get();

        return response()->json([
            'success' => true,
            'data' => $documents,
            'meta' => [
                'total' => $documents->count(),
                'start_date' => $request->start_date,
                'end_date' => $request->end_date,
            ]
        ]);
    }
}