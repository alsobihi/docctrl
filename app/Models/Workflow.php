<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Workflow extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'name',
        'description',
        'is_reopenable',
        'plant_id',
        'project_id',
        'auto_reopen_on_expiry',
        'auto_reopen_on_deletion',
        'notification_days_before',
        'created_by',
        'updated_by',
        'deleted_by',
    ];

    protected $casts = [
        'is_reopenable' => 'boolean',
        'auto_reopen_on_expiry' => 'boolean',
        'auto_reopen_on_deletion' => 'boolean',
        'notification_days_before' => 'integer',
    ];

    /**
     * The document types that belong to the workflow.
     */
    public function documentTypes(): BelongsToMany
    {
        return $this->belongsToMany(DocumentType::class, 'workflow_steps', 'workflow_id', 'document_type_id')
                    ->withPivot('id', 'step_order', 'is_mandatory')
                    ->orderBy('step_order');
    }

    public function plant(): BelongsTo
    {
        return $this->belongsTo(Plant::class);
    }

    public function project(): BelongsTo
    {
        return $this->belongsTo(Project::class);
    }

    /**
     * Get the user who created the record.
     */
    public function createdBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * Get the workflow assignments for employees
     */
    public function assignments(): HasMany
    {
        return $this->hasMany(EmployeeWorkflow::class);
    }

    /**
     * Get the workflow history
     */
    public function history(): HasMany
    {
        return $this->hasMany(WorkflowHistory::class);
    }

    /**
     * Get the scope of the workflow as a string
     */
    public function getScopeAttribute(): string
    {
        if ($this->plant_id) {
            return 'plant';
        } elseif ($this->project_id) {
            return 'project';
        } else {
            return 'global';
        }
    }

    /**
     * Get the scope name of the workflow
     */
    public function getScopeNameAttribute(): string
    {
        if ($this->plant_id && $this->plant) {
            return 'Plant: ' . $this->plant->name;
        } elseif ($this->project_id && $this->project) {
            return 'Project: ' . $this->project->name;
        } else {
            return 'Global';
        }
    }

    /**
     * Get active assignments count
     */
    public function getActiveAssignmentsCountAttribute(): int
    {
        return $this->assignments()->where('status', 'in_progress')->count();
    }

    /**
     * Get completed assignments count
     */
    public function getCompletedAssignmentsCountAttribute(): int
    {
        return $this->assignments()->where('status', 'completed')->count();
    }

    /**
     * Get total assignments count
     */
    public function getTotalAssignmentsCountAttribute(): int
    {
        return $this->assignments()->count();
    }

    /**
     * Get completion rate as percentage
     */
    public function getCompletionRateAttribute(): float
    {
        if ($this->total_assignments_count === 0) {
            return 0;
        }
        
        return round(($this->completed_assignments_count / $this->total_assignments_count) * 100, 1);
    }

    /**
     * Reopen all completed workflows for employees with expired or deleted documents
     * 
     * @param Employee $employee The employee whose document was expired or deleted
     * @param DocumentType $documentType The document type that was expired or deleted
     * @param string $reason The reason for reopening (expired or deleted)
     * @return int Number of workflows reopened
     */
    public static function reopenWorkflowsForDocument(Employee $employee, DocumentType $documentType, string $reason): int
    {
        $reopenedCount = 0;
        
        // Find all completed workflows that require this document type
        $completedWorkflows = EmployeeWorkflow::where('employee_id', $employee->id)
            ->where('status', 'completed')
            ->whereHas('workflow', function($query) use ($reason) {
                if ($reason === 'expired') {
                    $query->where('auto_reopen_on_expiry', true);
                } elseif ($reason === 'deleted') {
                    $query->where('auto_reopen_on_deletion', true);
                }
                $query->where('is_reopenable', true);
            })
            ->with(['workflow.documentTypes'])
            ->get();
        
        foreach ($completedWorkflows as $employeeWorkflow) {
            // Check if this workflow requires the document type
            $requiredDocTypes = $employeeWorkflow->workflow->documentTypes->pluck('id')->toArray();
            
            if (in_array($documentType->id, $requiredDocTypes)) {
                // Reopen the workflow
                $employeeWorkflow->update([
                    'status' => 'in_progress',
                    'completed_at' => null,
                    'reopened_at' => now(),
                    'reopened_reason' => "Document {$reason}: {$documentType->name}",
                ]);
                
                // Create a history record
                WorkflowHistory::create([
                    'workflow_id' => $employeeWorkflow->workflow_id,
                    'employee_id' => $employee->id,
                    'action' => 'reopened',
                    'details' => "Workflow reopened automatically because {$documentType->name} was {$reason}",
                    'created_by' => $employeeWorkflow->created_by,
                ]);
                
                $reopenedCount++;
            }
        }
        
        return $reopenedCount;
    }
}