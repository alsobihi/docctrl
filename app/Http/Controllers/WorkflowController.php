<?php

namespace App\Http\Controllers;

use App\Models\Workflow;
use App\Models\DocumentType;
use App\Models\Plant;
use App\Models\Project;
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
        ]);

        $workflow = Workflow::create([
            'name' => $request->name,
            'description' => $request->description,
            'plant_id' => $request->scope === 'plant' ? $request->plant_id : null,
            'project_id' => $request->scope === 'project' ? $request->project_id : null,
            'created_by' => Auth::id(),
        ]);

        return redirect()->route('workflows.edit', $workflow)
                         ->with('success', 'Workflow created successfully. Now add document steps.');
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
        ]);

        $workflow->update([
            'name' => $request->name,
            'description' => $request->description,
            'plant_id' => $request->scope === 'plant' ? $request->plant_id : null,
            'project_id' => $request->scope === 'project' ? $request->project_id : null,
            'updated_by' => Auth::id(),
        ]);

        return redirect()->route('workflows.edit', $workflow)
                         ->with('success', 'Workflow updated successfully.');
    }


    public function destroy(Workflow $workflow): RedirectResponse
    {
        // The database constraints will handle deleting pivot records
        $workflow->update(['deleted_by' => Auth::id()]);
        $workflow->delete();

        return redirect()->route('workflows.index')
                         ->with('success', 'Workflow deleted successfully.');
    }
}
