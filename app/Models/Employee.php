<?php

// app/Models/Employee.php
// --- Employee Model ---

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Carbon\Carbon;

class Employee extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'first_name',
        'last_name',
        'employee_code',
        'plant_id',
        'created_by',
        'updated_by',
        'deleted_by',
    ];

    /**
     * Get the plant that the employee belongs to.
     */
    public function plant(): BelongsTo
    {
        return $this->belongsTo(Plant::class);
    }

    /**
     * Get the documents for the employee.
     */
    public function documents(): HasMany
    {
        return $this->hasMany(EmployeeDocument::class);
    }

    /**
     * The projects that belong to the employee.
     */
    public function projects(): BelongsToMany
    {
        return $this->belongsToMany(Project::class, 'project_assignments')->withPivot('role');
    }

    /**
     * Get the user who created the record.
     */
    public function createdBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function assignedWorkflows()
{
    return $this->hasMany(EmployeeWorkflow::class);
}



    /**
     * Checks all in-progress workflows for this employee and updates their
     * status to 'completed' if all required documents are now present.
     */
      /**
     * Checks all in-progress workflows for this employee and updates their
     * status to 'completed' if all required documents are now present.
     */
    public function checkAndUpdateWorkflowStatus(): void
    {
        $inProgressWorkflows = $this->assignedWorkflows()
            ->where('status', 'in_progress')
            ->with('workflow.documentTypes') // Eager load relations
            ->get();

        if ($inProgressWorkflows->isEmpty()) {
            return;
        }

        $currentDocumentIds = $this->documents()->pluck('document_type_id');

        foreach ($inProgressWorkflows as $employeeWorkflow) {
            $requiredIds = $employeeWorkflow->workflow->documentTypes->pluck('id');

            if ($requiredIds->diff($currentDocumentIds)->isEmpty()) {
                // All required documents are present
                $employeeWorkflow->update([
                    'status' => 'completed',
                    'completed_at' => Carbon::now(),
                ]);
            }
        }
    }



}
