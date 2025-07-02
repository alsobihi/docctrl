<?php

namespace App\Http\Controllers;
use App\Models\DocumentTemplate;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;
class DocumentTemplateController extends Controller {
    public function index(): View {
        $templates = DocumentTemplate::latest()->paginate(10);
        return view('document-templates.index', compact('templates'));
    }
    public function create(): View { return view('document-templates.create'); }
    public function store(Request $request): RedirectResponse {
        $request->validate(['name' => 'required|string|max:255', 'fields' => 'nullable|array']);
        DocumentTemplate::create($request->all() + ['created_by' => Auth::id()]);
        return redirect()->route('document-templates.index')->with('success', 'Template created.');
    }
    public function edit(DocumentTemplate $documentTemplate): View {
        return view('document-templates.edit', ['template' => $documentTemplate]);
    }
    public function update(Request $request, DocumentTemplate $documentTemplate): RedirectResponse {
        $request->validate(['name' => 'required|string|max:255', 'fields' => 'nullable|array']);
        $documentTemplate->update($request->all() + ['updated_by' => Auth::id()]);
        return redirect()->route('document-templates.index')->with('success', 'Template updated.');
    }
    public function destroy(DocumentTemplate $documentTemplate): RedirectResponse {
        $documentTemplate->delete();
        return redirect()->route('document-templates.index')->with('success', 'Template deleted.');
    }
}
