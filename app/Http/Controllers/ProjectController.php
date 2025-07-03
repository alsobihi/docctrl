<?php

namespace App\Http\Controllers;

use App\Models\Project;
use App\Models\Plant;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;

class ProjectController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request): View
    {
        $user = Auth::user();
        $query = Project::with(['plant', 'employees']);

        if ($user->role === 'manager') {
            $query->where('plant_id', $user->plant_id);
        }

        // Apply filters
        if ($request->filled('search')) {
            $searchTerm = $request->search;
            $query->where(function($q) use ($searchTerm) {
                $q->where('name', 'like', "%{$searchTerm}%")
                  ->orWhere('project_code', 'like', "%{$searchTerm}%")
                  ->orWhere('client_name', 'like', "%{$searchTerm}%")
                  ->orWhere('project_manager', 'like', "%{$searchTerm}%");
            });
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('priority')) {
            $query->where('priority', $request->priority);
        }

        if ($request->filled('plant_id')) {
            $query->where('plant_id', $request->plant_id);
        }

        $perPage = min($request->get('per_page', 12), 50);
        $projects = $query->latest()->paginate($perPage)->withQueryString();

        // Get filter options
        $plants = Plant::orderBy('name')->get();
        $statuses = ['planning', 'active', 'on_hold', 'completed', 'cancelled'];
        $priorities = ['low', 'medium', 'high', 'critical'];

        return view('projects.index', compact('projects', 'plants', 'statuses', 'priorities'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(): View
    {
        $plants = Plant::orderBy('name')->get();
        return view('projects.create', compact('plants'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'project_code' => 'required|string|max:255|unique:projects',
            'plant_id' => 'nullable|exists:plants,id',
            'description' => 'nullable|string',
            'status' => 'required|in:planning,active,on_hold,completed,cancelled',
            'priority' => 'required|in:low,medium,high,critical',
            'client_name' => 'nullable|string|max:255',
            'client_contact' => 'nullable|string|max:255',
            'client_email' => 'nullable|email|max:255',
            'budget' => 'nullable|numeric|min:0',
            'start_date' => 'nullable|date',
            'end_date' => 'nullable|date|after_or_equal:start_date',
            'project_manager' => 'nullable|string|max:255',
            'project_manager_email' => 'nullable|email|max:255',
            'project_manager_phone' => 'nullable|string|max:20',
            'location' => 'nullable|string|max:255',
            'contract_number' => 'nullable|string|max:255',
            'contract_date' => 'nullable|date',
            'notes' => 'nullable|string',
            'tags' => 'nullable|array',
        ]);

        $data = $request->all();
        $data['created_by'] = Auth::id();

        // Handle tags array
        if ($request->filled('tags')) {
            $data['tags'] = array_filter($request->tags);
        }

        Project::create($data);

        return redirect()->route('projects.index')
                         ->with('success', 'Project created successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Project $project): View
    {
        $project->load([
            'plant',
            'employees.documents.documentType',
            'requiredDocuments',
            'workflows.documentTypes'
        ]);

        return view('projects.show', compact('project'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Project $project): View
    {
        $plants = Plant::orderBy('name')->get();
        return view('projects.edit', compact('project', 'plants'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Project $project): RedirectResponse
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'project_code' => 'required|string|max:255|unique:projects,project_code,' . $project->id,
            'plant_id' => 'nullable|exists:plants,id',
            'description' => 'nullable|string',
            'status' => 'required|in:planning,active,on_hold,completed,cancelled',
            'priority' => 'required|in:low,medium,high,critical',
            'client_name' => 'nullable|string|max:255',
            'client_contact' => 'nullable|string|max:255',
            'client_email' => 'nullable|email|max:255',
            'budget' => 'nullable|numeric|min:0',
            'actual_cost' => 'nullable|numeric|min:0',
            'start_date' => 'nullable|date',
            'end_date' => 'nullable|date|after_or_equal:start_date',
            'actual_start_date' => 'nullable|date',
            'actual_end_date' => 'nullable|date|after_or_equal:actual_start_date',
            'progress_percentage' => 'nullable|integer|min:0|max:100',
            'project_manager' => 'nullable|string|max:255',
            'project_manager_email' => 'nullable|email|max:255',
            'project_manager_phone' => 'nullable|string|max:20',
            'location' => 'nullable|string|max:255',
            'contract_number' => 'nullable|string|max:255',
            'contract_date' => 'nullable|date',
            'notes' => 'nullable|string',
            'tags' => 'nullable|array',
            'milestones' => 'nullable|array',
            'risks' => 'nullable|array',
        ]);

        $data = $request->all();
        $data['updated_by'] = Auth::id();

        // Handle arrays
        if ($request->filled('tags')) {
            $data['tags'] = array_filter($request->tags);
        }

        if ($request->filled('milestones')) {
            $data['milestones'] = $request->milestones;
        }

        if ($request->filled('risks')) {
            $data['risks'] = $request->risks;
        }

        $project->update($data);

        return redirect()->route('projects.index')
                         ->with('success', 'Project updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Project $project): RedirectResponse
    {
        $project->update(['deleted_by' => Auth::id()]);
        $project->delete();

        return redirect()->route('projects.index')
                         ->with('success', 'Project deleted successfully.');
    }
}