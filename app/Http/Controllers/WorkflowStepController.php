<?php

namespace App\Http\Controllers;

use App\Models\Workflow;
use App\Models\WorkflowStep;
use App\Models\DocumentType;
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

        try {
            // Check if this document type is already in the workflow
            $exists = $workflow->documentTypes()->where('document_type_id', $request->document_type_id)->exists();
            
            if ($exists) {
                return redirect()->route('workflows.edit', $workflow)
                                ->withErrors(['error' => 'This document type is already part of the workflow.']);
            }

            // Get the next step order
            $maxOrder = $workflow->documentTypes()->max('step_order') ?? 0;
            
            // Use attach to add the record to the pivot table
            $workflow->documentTypes()->attach($request->document_type_id, [
                'created_by' => Auth::id(),
                'step_order' => $maxOrder + 1,
                'is_mandatory' => true,
            ]);

            return redirect()->route('workflows.edit', $workflow)
                            ->with('success', 'Step added to workflow.');
        } catch (\Exception $e) {
            return redirect()->route('workflows.edit', $workflow)
                            ->withErrors(['error' => 'Failed to add step: ' . $e->getMessage()]);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(WorkflowStep $step): RedirectResponse
    {
        try {
            $workflow = $step->workflow;
            
            if (!$workflow) {
                return redirect()->route('workflows.index')
                                ->withErrors(['error' => 'Workflow not found.']);
            }
            
            $step->delete(); // This deletes the pivot record

            return redirect()->route('workflows.edit', $workflow)
                            ->with('success', 'Step removed from workflow.');
        } catch (\Exception $e) {
            return redirect()->back()
                            ->withErrors(['error' => 'Failed to remove step: ' . $e->getMessage()]);
        }
    }
}