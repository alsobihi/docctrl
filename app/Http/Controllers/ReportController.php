<?php

namespace App\Http\Controllers;

use App\Models\EmployeeDocument;
use App\Models\Plant;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;



class ReportController extends Controller
{
    public function expiringDocumentsForm(Request $request): View
    {
        $user = Auth::user();
        $query = Plant::query();

        if ($user->role === 'manager') {
            $query->where('id', $user->plant_id);
        }

        $plants = $query->orderBy('name')->get();
        $documents = collect();

        if ($request->has('start_date') && $request->has('end_date')) {
            $documents = $this->generateExpiringDocumentsReport($request);
        }

        return view('reports.expiring-documents', compact('plants', 'documents'));
    }

    public function generateExpiringDocumentsReport(Request $request)
    {
        $user = Auth::user();

        $request->validate([
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
            'plant_id' => 'nullable|exists:plants,id',
        ]);

        $query = EmployeeDocument::with(['employee.plant', 'documentType'])
            ->whereBetween('expiry_date', [$request->start_date, $request->end_date]);

        // If user is a manager, force the report to be for their plant
        if ($user->role === 'manager') {
            $query->whereHas('employee', function ($q) use ($user) {
                $q->where('plant_id', $user->plant_id);
            });
        } elseif ($request->filled('plant_id')) {
            // Admins can filter by any plant
            $query->whereHas('employee', function ($q) use ($request) {
                $q->where('plant_id', $request->plant_id);
            });
        }

        if ($request->isMethod('get') && $request->has('start_date')) {
             $plantQuery = ($user->role === 'manager') ? Plant::where('id', $user->plant_id) : Plant::query();
             $plants = $plantQuery->orderBy('name')->get();
             $documents = $query->orderBy('expiry_date')->get();
             return view('reports.expiring-documents', compact('plants', 'documents'));
        }

        return $query->orderBy('expiry_date')->get();
    }
}

