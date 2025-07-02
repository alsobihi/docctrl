<?php

namespace App\Http\Controllers;

use App\Models\Plant;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;




class PlantController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(): View
    {
        $user = Auth::user();
        $query = Plant::query();

        if ($user->role === 'manager') {
            $query->where('id', $user->plant_id);
        }

        $plants = $query->latest()->paginate(10);
        return view('plants.index', compact('plants'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(): View
    {
        return view('plants.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'location' => 'nullable|string|max:255',
        ]);

        Plant::create([
            'name' => $request->name,
            'location' => $request->location,
            'created_by' => Auth::id(),
        ]);

        return redirect()->route('plants.index')
                         ->with('success', 'Plant created successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Plant $plant): View
    {
        return view('plants.show', compact('plant'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Plant $plant): View
    {
        return view('plants.edit', compact('plant'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Plant $plant): RedirectResponse
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'location' => 'nullable|string|max:255',
        ]);

        $plant->update([
            'name' => $request->name,
            'location' => $request->location,
            'updated_by' => Auth::id(),
        ]);

        return redirect()->route('plants.index')
                         ->with('success', 'Plant updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Plant $plant): RedirectResponse
    {
        $plant->update(['deleted_by' => Auth::id()]);
        $plant->delete(); // This performs a soft delete

        return redirect()->route('plants.index')
                         ->with('success', 'Plant deleted successfully.');
    }
}
