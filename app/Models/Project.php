<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Carbon\Carbon;

class Project extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'name',
        'project_code',
        'plant_id',
        'description',
        'status',
        'priority',
        'client_name',
        'client_contact',
        'client_email',
        'budget',
        'actual_cost',
        'start_date',
        'end_date',
        'actual_start_date',
        'actual_end_date',
        'progress_percentage',
        'project_manager',
        'project_manager_email',
        'project_manager_phone',
        'milestones',
        'risks',
        'notes',
        'location',
        'tags',
        'contract_number',
        'contract_date',
        'created_by',
        'updated_by',
        'deleted_by',
    ];

    protected $casts = [
        'start_date' => 'date',
        'end_date' => 'date',
        'actual_start_date' => 'date',
        'actual_end_date' => 'date',
        'contract_date' => 'date',
        'budget' => 'decimal:2',
        'actual_cost' => 'decimal:2',
        'progress_percentage' => 'integer',
        'milestones' => 'array',
        'risks' => 'array',
        'tags' => 'array',
    ];

    public function plant(): BelongsTo
    {
        return $this->belongsTo(Plant::class);
    }

    /**
     * The employees that are assigned to the project.
     */
    public function employees(): BelongsToMany
    {
        return $this->belongsToMany(Employee::class, 'project_assignments')->withPivot('role', 'created_by');
    }

    /**
     * The documents required for this project.
     */
    public function requiredDocuments(): BelongsToMany
    {
        return $this->belongsToMany(DocumentType::class, 'project_required_documents');
    }

    /**
     * Get the user who created the record.
     */
    public function createdBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * Get the workflows for this project.
     */
    public function workflows(): HasMany
    {
        return $this->hasMany(Workflow::class);
    }

    /**
     * Get project status color
     */
    public function getStatusColorAttribute(): string
    {
        return match($this->status) {
            'planning' => 'blue',
            'active' => 'green',
            'on_hold' => 'yellow',
            'completed' => 'purple',
            'cancelled' => 'red',
            default => 'gray'
        };
    }

    /**
     * Get priority color
     */
    public function getPriorityColorAttribute(): string
    {
        return match($this->priority) {
            'low' => 'green',
            'medium' => 'yellow',
            'high' => 'orange',
            'critical' => 'red',
            default => 'gray'
        };
    }

    /**
     * Get project duration in days
     */
    public function getDurationAttribute(): ?int
    {
        if (!$this->start_date || !$this->end_date) {
            return null;
        }
        return $this->start_date->diffInDays($this->end_date);
    }

    /**
     * Get actual duration in days
     */
    public function getActualDurationAttribute(): ?int
    {
        if (!$this->actual_start_date || !$this->actual_end_date) {
            return null;
        }
        return $this->actual_start_date->diffInDays($this->actual_end_date);
    }

    /**
     * Get days remaining
     */
    public function getDaysRemainingAttribute(): ?int
    {
        if (!$this->end_date || $this->status === 'completed') {
            return null;
        }
        
        $today = Carbon::now();
        if ($this->end_date->isPast()) {
            return 0; // Overdue
        }
        
        return $today->diffInDays($this->end_date);
    }

    /**
     * Check if project is overdue
     */
    public function getIsOverdueAttribute(): bool
    {
        return $this->end_date && $this->end_date->isPast() && $this->status !== 'completed';
    }

    /**
     * Get budget utilization percentage
     */
    public function getBudgetUtilizationAttribute(): ?float
    {
        if (!$this->budget || $this->budget == 0) {
            return null;
        }
        return ($this->actual_cost / $this->budget) * 100;
    }

    /**
     * Get team size
     */
    public function getTeamSizeAttribute(): int
    {
        return $this->employees()->count();
    }

    /**
     * Get completed milestones count
     */
    public function getCompletedMilestonesCountAttribute(): int
    {
        if (!$this->milestones) {
            return 0;
        }
        return collect($this->milestones)->where('completed', true)->count();
    }

    /**
     * Get total milestones count
     */
    public function getTotalMilestonesCountAttribute(): int
    {
        return $this->milestones ? count($this->milestones) : 0;
    }

    /**
     * Scope for active projects
     */
    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    /**
     * Scope for projects by status
     */
    public function scopeByStatus($query, $status)
    {
        return $query->where('status', $status);
    }

    /**
     * Scope for projects by priority
     */
    public function scopeByPriority($query, $priority)
    {
        return $query->where('priority', $priority);
    }

    /**
     * Scope for overdue projects
     */
    public function scopeOverdue($query)
    {
        return $query->where('end_date', '<', now())
                    ->whereNotIn('status', ['completed', 'cancelled']);
    }
}