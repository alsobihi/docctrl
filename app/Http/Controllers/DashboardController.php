<?php

namespace App\Http\Controllers;

use App\Models\Employee;
use App\Models\EmployeeDocument;
use App\Models\EmployeeWorkflow;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function index(): View
    {
        $user = Auth::user();
        $isManager = $user->role === 'manager';
        $plantId = $user->plant_id;

        // Scope Employee-related queries
        $employeeQuery = Employee::query();
        if ($isManager) {
            $employeeQuery->where('plant_id', $plantId);
        }
        $totalEmployees = $employeeQuery->count();

        // Scope Document-related queries
        $documentQuery = EmployeeDocument::query();
        if ($isManager) {
            $documentQuery->whereHas('employee', function ($q) use ($plantId) {
                $q->where('plant_id', $plantId);
            });
        }

        $expiringSoonCount = (clone $documentQuery)->whereBetween('expiry_date', [Carbon::now(), Carbon::now()->addDays(30)])->count();
        $expiredCount = (clone $documentQuery)->where('expiry_date', '<', Carbon::now())->count();

        $urgentDocuments = (clone $documentQuery)->with(['employee', 'documentType'])
            ->where('expiry_date', '<', Carbon::now()->addDays(30))
            ->orderBy('expiry_date', 'asc')
            ->limit(10)
            ->get();

        // Get real count for Workflows in Progress
        $workflowQuery = EmployeeWorkflow::where('status', 'in_progress');
        if ($isManager) {
            $workflowQuery->whereHas('employee', function ($q) use ($plantId) {
                $q->where('plant_id', $plantId);
            });
        }
        $workflowsInProgress = $workflowQuery->count();

        return view('dashboard', compact(
            'totalEmployees',
            'expiringSoonCount',
            'expiredCount',
            'urgentDocuments',
            'workflowsInProgress'
        ));
    }
}
