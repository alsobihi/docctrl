<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\Employee;
use App\Models\EmployeeDocument;
use App\Models\EmployeeWorkflow;
use Illuminate\Http\JsonResponse;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    /**
     * Get dashboard statistics
     */
    public function stats(): JsonResponse
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

        $expiringSoonCount = (clone $documentQuery)
            ->whereBetween('expiry_date', [Carbon::now(), Carbon::now()->addDays(30)])
            ->count();

        $expiredCount = (clone $documentQuery)
            ->where('expiry_date', '<', Carbon::now())
            ->count();

        // Get workflows in progress
        $workflowQuery = EmployeeWorkflow::where('status', 'in_progress');
        if ($isManager) {
            $workflowQuery->whereHas('employee', function ($q) use ($plantId) {
                $q->where('plant_id', $plantId);
            });
        }
        $workflowsInProgress = $workflowQuery->count();

        // Get urgent documents
        $urgentDocuments = (clone $documentQuery)
            ->with(['employee', 'documentType'])
            ->where('expiry_date', '<', Carbon::now()->addDays(30))
            ->orderBy('expiry_date', 'asc')
            ->limit(10)
            ->get();

        return response()->json([
            'success' => true,
            'data' => [
                'stats' => [
                    'total_employees' => $totalEmployees,
                    'expiring_soon_count' => $expiringSoonCount,
                    'expired_count' => $expiredCount,
                    'workflows_in_progress' => $workflowsInProgress,
                ],
                'urgent_documents' => $urgentDocuments,
            ]
        ]);
    }

    /**
     * Get recent activities
     */
    public function activities(): JsonResponse
    {
        $user = Auth::user();
        $isManager = $user->role === 'manager';
        $plantId = $user->plant_id;

        // Recent documents
        $documentsQuery = EmployeeDocument::with(['employee', 'documentType']);
        if ($isManager) {
            $documentsQuery->whereHas('employee', function ($q) use ($plantId) {
                $q->where('plant_id', $plantId);
            });
        }
        $recentDocuments = $documentsQuery->latest()->limit(5)->get();

        // Recent workflow completions
        $workflowsQuery = EmployeeWorkflow::with(['employee', 'workflow'])
            ->where('status', 'completed');
        if ($isManager) {
            $workflowsQuery->whereHas('employee', function ($q) use ($plantId) {
                $q->where('plant_id', $plantId);
            });
        }
        $recentCompletions = $workflowsQuery->latest('completed_at')->limit(5)->get();

        return response()->json([
            'success' => true,
            'data' => [
                'recent_documents' => $recentDocuments,
                'recent_completions' => $recentCompletions,
            ]
        ]);
    }
}