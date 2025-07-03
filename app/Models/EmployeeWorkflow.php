<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class EmployeeWorkflow extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'employee_id',
        'workflow_id',
        'status',
        'created_by',
        'completed_at',
        'reopened_at',
        'reopened_reason',
        'last_notification_at'
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'completed_at' => 'datetime',
        'reopened_at' => 'datetime',
        'last_notification_at' => 'datetime',
    ];

    /**
     * Get the employee that owns the workflow assignment.
     */
    public function employee(): BelongsTo
    {
        return $this->belongsTo(Employee::class);
    }

    /**
     * Get the workflow that is assigned.
     */
    public function workflow(): BelongsTo
    {
        return $this->belongsTo(Workflow::class);
    }

    /**
     * Get the history records for this workflow assignment
     */
    public function history(): HasMany
    {
        return $this->hasMany(WorkflowHistory::class, 'employee_workflow_id');
    }

    /**
     * Get the completion percentage of this workflow
     */
    public function getCompletionPercentageAttribute(): int
    {
        if ($this->status === 'completed') {
            return 100;
        }

        $this->load(['workflow.documentTypes', 'employee.documents']);
        
        $requiredDocumentTypeIds = $this->workflow->documentTypes->pluck('id')->toArray();
        $employeeDocumentTypeIds = $this->employee->documents->pluck('document_type_id')->toArray();
        
        if (empty($requiredDocumentTypeIds)) {
            return 0;
        }
        
        $completedCount = 0;
        foreach ($requiredDocumentTypeIds as $docTypeId) {
            if (in_array($docTypeId, $employeeDocumentTypeIds)) {
                $completedCount++;
            }
        }
        
        return round(($completedCount / count($requiredDocumentTypeIds)) * 100);
    }

    /**
     * Get the missing document types for this workflow
     */
    public function getMissingDocumentTypesAttribute(): array
    {
        $this->load(['workflow.documentTypes', 'employee.documents']);
        
        $requiredDocumentTypeIds = $this->workflow->documentTypes->pluck('id')->toArray();
        $employeeDocumentTypeIds = $this->employee->documents->pluck('document_type_id')->toArray();
        
        $missingDocTypes = [];
        foreach ($this->workflow->documentTypes as $docType) {
            if (!in_array($docType->id, $employeeDocumentTypeIds)) {
                $missingDocTypes[] = $docType;
            }
        }
        
        return $missingDocTypes;
    }

    /**
     * Get the days since this workflow was started
     */
    public function getDaysSinceStartAttribute(): int
    {
        return $this->created_at->diffInDays(now());
    }

    /**
     * Get the days since this workflow was completed
     */
    public function getDaysSinceCompletionAttribute(): ?int
    {
        if (!$this->completed_at) {
            return null;
        }
        
        return $this->completed_at->diffInDays(now());
    }

    /**
     * Get the days since this workflow was reopened
     */
    public function getDaysSinceReopenedAttribute(): ?int
    {
        if (!$this->reopened_at) {
            return null;
        }
        
        return $this->reopened_at->diffInDays(now());
    }

    /**
     * Scope for in-progress workflows
     */
    public function scopeInProgress($query)
    {
        return $query->where('status', 'in_progress');
    }

    /**
     * Scope for completed workflows
     */
    public function scopeCompleted($query)
    {
        return $query->where('status', 'completed');
    }

    /**
     * Scope for recently completed workflows
     */
    public function scopeRecentlyCompleted($query, $days = 30)
    {
        return $query->where('status', 'completed')
                    ->whereNotNull('completed_at')
                    ->where('completed_at', '>=', now()->subDays($days));
    }

    /**
     * Scope for recently reopened workflows
     */
    public function scopeRecentlyReopened($query, $days = 30)
    {
        return $query->where('status', 'in_progress')
                    ->whereNotNull('reopened_at')
                    ->where('reopened_at', '>=', now()->subDays($days));
    }
}