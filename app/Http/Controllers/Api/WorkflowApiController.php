<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Employee;
use App\Models\Workflow;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Log;

class WorkflowApiController extends Controller
{
    /**
     * Get workflows relevant to a specific employee.
     */
    public function getRelevantWorkflows(Employee $employee): JsonResponse
    {
        try {
            // Eager load the projects the employee is assigned to
            $employee->load('projects');
            $projectIds = $employee->projects->pluck('id');

            $workflows = Workflow::query()
                // The `orWhere` clauses are wrapped in a function to ensure proper SQL grouping
                ->where(function ($query) use ($employee, $projectIds) {
                    // 1. Get Global workflows
                    $query->whereNull('plant_id')->whereNull('project_id');

                    // 2. Get workflows for the employee's plant
                    if ($employee->plant_id) {
                        $query->orWhere('plant_id', $employee->plant_id);
                    }

                    // 3. Get workflows for the employee's assigned projects
                    if ($projectIds->isNotEmpty()) {
                        $query->orWhereIn('project_id', $projectIds);
                    }
                })
                ->orderBy('name')
                ->get(['id', 'name']); // Only select the columns we need

            return response()->json($workflows);
        } catch (\Exception $e) {
            Log::error('Error fetching relevant workflows: ' . $e->getMessage(), [
                'employee_id' => $employee->id,
                'trace' => $e->getTraceAsString()
            ]);
            
            return response()->json(['error' => 'Failed to fetch workflows'], 500);
        }
    }
}