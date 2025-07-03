<?php

namespace App\Http\Controllers;

use App\Models\EmployeeWorkflow;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class InProgressWorkflowController extends Controller
{
    public function index(): View
    {
        $query = EmployeeWorkflow::with(['employee', 'workflow'])
            ->where('status', 'in_progress');

        if (Auth::user()->role === 'manager') {
            $query->whereHas('employee', function ($q) {
                $q->where('plant_id', Auth::user()->plant_id);
            });
        }

        $inProgressWorkflows = $query->latest()->paginate(15);

        return view('workflows.in-progress', compact('inProgressWorkflows'));
    }
}
