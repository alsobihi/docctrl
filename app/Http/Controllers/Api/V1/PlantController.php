<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\Plant;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;

class PlantController extends Controller
{
    /**
     * Display a listing of plants
     */
    public function index(Request $request): JsonResponse
    {
        $user = Auth::user();
        $query = Plant::query();

        // Apply role-based filtering
        if ($user->role === 'manager') {
            $query->where('id', $user->plant_id);
        }

        $perPage = min($request->get('per_page', 15), 100);
        $plants = $query->latest()->paginate($perPage);

        return response()->json([
            'success' => true,
            'data' => $plants->items(),
            'meta' => [
                'current_page' => $plants->currentPage(),
                'last_page' => $plants->lastPage(),
                'per_page' => $plants->perPage(),
                'total' => $plants->total(),
            ]
        ]);
    }

    /**
     * Store a newly created plant
     */
    public function store(Request $request): JsonResponse
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'location' => 'nullable|string|max:255',
        ]);

        $plant = Plant::create([
            'name' => $request->name,
            'location' => $request->location,
            'created_by' => Auth::id(),
        ]);

        return response()->json([
            'success' => true,
            'data' => $plant,
            'message' => 'Plant created successfully'
        ], 201);
    }

    /**
     * Display the specified plant
     */
    public function show(Plant $plant): JsonResponse
    {
        return response()->json([
            'success' => true,
            'data' => $plant
        ]);
    }

    /**
     * Update the specified plant
     */
    public function update(Request $request, Plant $plant): JsonResponse
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

        return response()->json([
            'success' => true,
            'data' => $plant,
            'message' => 'Plant updated successfully'
        ]);
    }

    /**
     * Remove the specified plant
     */
    public function destroy(Plant $plant): JsonResponse
    {
        $plant->update(['deleted_by' => Auth::id()]);
        $plant->delete();

        return response()->json([
            'success' => true,
            'message' => 'Plant deleted successfully'
        ]);
    }

    /**
     * Get plant employees
     */
    public function employees(Plant $plant): JsonResponse
    {
        $employees = $plant->employees()
            ->with(['documents.documentType'])
            ->latest()
            ->get();

        return response()->json([
            'success' => true,
            'data' => $employees
        ]);
    }
}