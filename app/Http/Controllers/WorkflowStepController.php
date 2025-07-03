<?php

namespace App\Http\Controllers;

use App\Models\Workflow;
use App\Models\WorkflowStep;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\RedirectResponse;

class WorkflowStepController extends Controller
{
    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request, Workflow $workflow): RedirectResponse
    {
        $request->validate([
            'document_type_id' => 'required|exists:document_types,id',
        ]);

        // Use attach to add the record to the pivot table
        $workflow->documentTypes()->attach($request->document_type_id, [
            'created_by' => Auth::id(),
            // You can add logic for step_order here if needed
        ]);

        return redirect()->route('workflows.edit', $workflow)
                         ->with('success', 'Step added to workflow.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(WorkflowStep $step): RedirectResponse
    {
        $workflow = $step->workflow;
        $step->delete(); // This deletes the pivot record

        return redirect()->route('workflows.edit', $workflow)
                         ->with('success', 'Step removed from workflow.');
    }
}
