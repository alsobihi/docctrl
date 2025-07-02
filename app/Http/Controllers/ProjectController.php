<?php


namespace App\Http\Controllers;

use App\Models\Project;
use Illuminate\Http\Request;

use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;



class ProjectController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(): View
    {
        $user = Auth::user();
        $query = Project::query();

        if ($user->role === 'manager') {
            $query->where('plant_id', $user->plant_id);
        }

        $projects = $query->latest()->paginate(10);
        return view('projects.index', compact('projects'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(): View
    {
        return view('projects.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'project_code' => 'required|string|max:255|unique:projects',
        ]);

        Project::create([
            'name' => $request->name,
            'project_code' => $request->project_code,
            'created_by' => Auth::id(),
        ]);

        return redirect()->route('projects.index')
                         ->with('success', 'Project created successfully.');
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Project $project): View
    {
        return view('projects.edit', compact('project'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Project $project): RedirectResponse
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'project_code' => 'required|string|max:255|unique:projects,project_code,' . $project->id,
        ]);

        $project->update([
            'name' => $request->name,
            'project_code' => $request->project_code,
            'updated_by' => Auth::id(),
        ]);

        return redirect()->route('projects.index')
                         ->with('success', 'Project updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Project $project): RedirectResponse
    {
        $project->update(['deleted_by' => Auth::id()]);
        $project->delete(); // This performs a soft delete

        return redirect()->route('projects.index')
                         ->with('success', 'Project deleted successfully.');
    }
}
