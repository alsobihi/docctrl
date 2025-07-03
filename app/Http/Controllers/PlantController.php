<?php

namespace App\Http\Controllers;

use App\Models\Plant;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;

class PlantController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request): View
    {
        $user = Auth::user();
        $query = Plant::withCount(['employees', 'projects', 'workflows']);

        if ($user->role === 'manager') {
            $query->where('id', $user->plant_id);
        }

        // Apply filters
        if ($request->filled('search')) {
            $searchTerm = $request->search;
            $query->where(function($q) use ($searchTerm) {
                $q->where('name', 'like', "%{$searchTerm}%")
                  ->orWhere('location', 'like', "%{$searchTerm}%")
                  ->orWhere('manager_name', 'like', "%{$searchTerm}%");
            });
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $plants = $query->latest()->paginate(12)->withQueryString();

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
            'logo' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'phone' => 'nullable|string|max:20',
            'email' => 'nullable|email|max:255',
            'address' => 'nullable|string|max:500',
            'manager_name' => 'nullable|string|max:255',
            'manager_email' => 'nullable|email|max:255',
            'manager_phone' => 'nullable|string|max:20',
            'description' => 'nullable|string|max:1000',
            'status' => 'required|in:active,inactive,maintenance',
            'established_date' => 'nullable|date',
            'capacity' => 'nullable|integer|min:1',
            'certification' => 'nullable|string|max:255',
            'operating_hours_start' => 'nullable|string',
            'operating_hours_end' => 'nullable|string',
            'latitude' => 'nullable|numeric|between:-90,90',
            'longitude' => 'nullable|numeric|between:-180,180',
        ]);

        $data = $request->except(['logo', 'operating_hours_start', 'operating_hours_end']);
        $data['created_by'] = Auth::id();

        // Handle logo upload
        if ($request->hasFile('logo')) {
            $logoPath = $request->file('logo')->store('plant-logos', 'public');
            $data['logo_path'] = $logoPath;
        }

        // Handle operating hours
        if ($request->filled('operating_hours_start') && $request->filled('operating_hours_end')) {
            $data['operating_hours'] = [
                'start' => $request->operating_hours_start,
                'end' => $request->operating_hours_end,
            ];
        }

        Plant::create($data);

        return redirect()->route('plants.index')
                         ->with('success', 'Plant created successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Plant $plant): View
    {
        $plant->load(['employees.documents.documentType', 'projects', 'workflows.documentTypes']);
        
        // Get recent activities
        $recentDocuments = $plant->employees()
            ->with(['documents' => function($query) {
                $query->with('documentType')->latest()->limit(5);
            }])
            ->get()
            ->pluck('documents')
            ->flatten()
            ->sortByDesc('created_at')
            ->take(5);

        // Get expiring documents
        $expiringDocuments = $plant->employees()
            ->with(['documents' => function($query) {
                $query->with(['documentType', 'employee'])
                      ->where('expiry_date', '<=', now()->addDays(30))
                      ->where('expiry_date', '>=', now())
                      ->orderBy('expiry_date');
            }])
            ->get()
            ->pluck('documents')
            ->flatten();

        // Get expired documents
        $expiredDocuments = $plant->employees()
            ->with(['documents' => function($query) {
                $query->with(['documentType', 'employee'])
                      ->where('expiry_date', '<', now())
                      ->orderBy('expiry_date', 'desc');
            }])
            ->get()
            ->pluck('documents')
            ->flatten();

        return view('plants.show', compact('plant', 'recentDocuments', 'expiringDocuments', 'expiredDocuments'));
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
            'logo' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'phone' => 'nullable|string|max:20',
            'email' => 'nullable|email|max:255',
            'address' => 'nullable|string|max:500',
            'manager_name' => 'nullable|string|max:255',
            'manager_email' => 'nullable|email|max:255',
            'manager_phone' => 'nullable|string|max:20',
            'description' => 'nullable|string|max:1000',
            'status' => 'required|in:active,inactive,maintenance',
            'established_date' => 'nullable|date',
            'capacity' => 'nullable|integer|min:1',
            'certification' => 'nullable|string|max:255',
            'operating_hours_start' => 'nullable|string',
            'operating_hours_end' => 'nullable|string',
            'latitude' => 'nullable|numeric|between:-90,90',
            'longitude' => 'nullable|numeric|between:-180,180',
        ]);

        $data = $request->except(['logo', 'operating_hours_start', 'operating_hours_end']);
        $data['updated_by'] = Auth::id();

        // Handle logo upload
        if ($request->hasFile('logo')) {
            // Delete old logo if exists
            if ($plant->logo_path && Storage::disk('public')->exists($plant->logo_path)) {
                Storage::disk('public')->delete($plant->logo_path);
            }
            
            $logoPath = $request->file('logo')->store('plant-logos', 'public');
            $data['logo_path'] = $logoPath;
        }

        // Handle operating hours
        if ($request->filled('operating_hours_start') && $request->filled('operating_hours_end')) {
            $data['operating_hours'] = [
                'start' => $request->operating_hours_start,
                'end' => $request->operating_hours_end,
            ];
        } else {
            $data['operating_hours'] = null;
        }

        $plant->update($data);

        return redirect()->route('plants.index')
                         ->with('success', 'Plant updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Plant $plant): RedirectResponse
    {
        // Delete logo if exists
        if ($plant->logo_path && Storage::disk('public')->exists($plant->logo_path)) {
            Storage::disk('public')->delete($plant->logo_path);
        }

        $plant->update(['deleted_by' => Auth::id()]);
        $plant->delete();

        return redirect()->route('plants.index')
                         ->with('success', 'Plant deleted successfully.');
    }
}