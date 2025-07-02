<?php

namespace App\Http\Controllers;
use App\Models\DocumentType;
use App\Models\DocumentTemplate;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Validation\Rule;
class DocumentTypeController extends Controller {
    public function index(): View {
        $documentTypes = DocumentType::with('template')->latest()->paginate(10);
        return view('document-types.index', compact('documentTypes'));
    }
    public function create(): View {
        $dependencyTypes = DocumentType::orderBy('name')->get();
        $templates = DocumentTemplate::orderBy('name')->get();
        return view('document-types.create', compact('dependencyTypes', 'templates'));
    }
    public function store(Request $request): RedirectResponse {
        $request->validate([
            'name' => 'required|string|max:255',
            'category' => ['required', Rule::in(['Personal', 'Project', 'Plant'])],
            'template_id' => 'nullable|exists:document_templates,id',
            'warning_period_days' => 'nullable|integer|min:1',
            'rule_type' => ['required', Rule::in(['none', 'fixed', 'dependent'])],
            'fixed_days' => 'required_if:rule_type,fixed|nullable|integer|min:1',
            'dependencies' => 'required_if:rule_type,dependent|nullable|array',
            'dependencies.*' => 'exists:document_types,id',
        ]);
        $validityRule = $this->buildValidityRule($request);
        DocumentType::create($request->only(['name', 'category', 'template_id', 'warning_period_days']) + [
            'validity_rule' => $validityRule, 'created_by' => Auth::id(),
        ]);
        return redirect()->route('document-types.index')->with('success', 'Document Type created successfully.');
    }
    public function edit(DocumentType $documentType): View {
        $dependencyTypes = DocumentType::where('id', '!=', $documentType->id)->orderBy('name')->get();
        $templates = DocumentTemplate::orderBy('name')->get();
        return view('document-types.edit', compact('documentType', 'dependencyTypes', 'templates'));
    }
    public function update(Request $request, DocumentType $documentType): RedirectResponse {
        $request->validate([
            'name' => 'required|string|max:255',
            'category' => ['required', Rule::in(['Personal', 'Project', 'Plant'])],
            'template_id' => 'nullable|exists:document_templates,id',
            'warning_period_days' => 'nullable|integer|min:1',
            'rule_type' => ['required', Rule::in(['none', 'fixed', 'dependent'])],
            'fixed_days' => 'required_if:rule_type,fixed|nullable|integer|min:1',
            'dependencies' => 'required_if:rule_type,dependent|nullable|array',
            'dependencies.*' => 'exists:document_types,id',
        ]);
        $validityRule = $this->buildValidityRule($request);
        $documentType->update($request->only(['name', 'category', 'template_id', 'warning_period_days']) + [
            'validity_rule' => $validityRule, 'updated_by' => Auth::id(),
        ]);
        return redirect()->route('document-types.index')->with('success', 'Document Type updated successfully.');
    }
    public function destroy(DocumentType $documentType): RedirectResponse {
        $documentType->delete();
        return redirect()->route('document-types.index')->with('success', 'Document Type deleted successfully.');
    }
    private function buildValidityRule(Request $request): ?array {
        switch ($request->rule_type) {
            case 'fixed': return ['type' => 'fixed', 'days' => (int)$request->fixed_days];
            case 'dependent':
                $dependencies = collect($request->dependencies)->map(fn($id) => ['document_type_id' => (int)$id])->all();
                return ['type' => 'dependent', 'logic' => 'min', 'dependencies' => $dependencies];
            default: return null;
        }
    }
}
