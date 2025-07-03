<?php

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
        'profile_photo_path',
        'phone',
        'email',
        'address',
        'date_of_birth',
        'nationality',
        'position',
        'department',
        'hire_date',
        'employment_status',
        'emergency_contact_name',
        'emergency_contact_phone',
        'emergency_contact_relationship',
        'salary',
        'contract_type',
        'contract_end_date',
        'notes',
        'skills',
        'badge_number',
        'created_by',
        'updated_by',
        'deleted_by',
    ];

    protected $casts = [
        'date_of_birth' => 'date',
        'hire_date' => 'date',
        'contract_end_date' => 'date',
        'skills' => 'array',
        'salary' => 'decimal:2',
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
     * Get the employee's full name
     */
    public function getFullNameAttribute(): string
    {
        return $this->first_name . ' ' . $this->last_name;
    }

    /**
     * Get the employee's profile photo URL
     */
    public function getProfilePhotoUrlAttribute(): ?string
    {
        return $this->profile_photo_path ? asset('storage/' . $this->profile_photo_path) : null;
    }

    /**
     * Get the employee's age
     */
    public function getAgeAttribute(): ?int
    {
        return $this->date_of_birth ? $this->date_of_birth->age : null;
    }

    /**
     * Get years of service
     */
    public function getYearsOfServiceAttribute(): ?float
    {
        return $this->hire_date ? $this->hire_date->diffInYears(now()) : null;
    }

    /**
     * Get employment status color
     */
    public function getStatusColorAttribute(): string
    {
        return match($this->employment_status) {
            'active' => 'green',
            'inactive' => 'yellow',
            'terminated' => 'red',
            default => 'gray'
        };
    }

    /**
     * Get contract status
     */
    public function getContractStatusAttribute(): string
    {
        if (!$this->contract_end_date) {
            return 'permanent';
        }

        if ($this->contract_end_date->isPast()) {
            return 'expired';
        }

        if ($this->contract_end_date->isBetween(now(), now()->addDays(30))) {
            return 'expiring_soon';
        }

        return 'active';
    }

    /**
     * Scope for active employees
     */
    public function scopeActive($query)
    {
        return $query->where('employment_status', 'active');
    }

    /**
     * Scope for employees by department
     */
    public function scopeByDepartment($query, $department)
    {
        return $query->where('department', $department);
    }

    /**
     * Scope for employees by position
     */
    public function scopeByPosition($query, $position)
    {
        return $query->where('position', $position);
    }

    /**
     * Get expired documents count
     */
    public function getExpiredDocumentsCountAttribute(): int
    {
        return $this->documents()->where('expiry_date', '<', now())->count();
    }

    /**
     * Get expiring documents count (next 30 days)
     */
    public function getExpiringDocumentsCountAttribute(): int
    {
        return $this->documents()
            ->whereBetween('expiry_date', [now(), now()->addDays(30)])
            ->count();
    }

    /**
     * Checks all in-progress workflows for this employee and updates their
     * status to 'completed' if all required documents are now present.
     */
    public function checkAndUpdateWorkflowStatus(): void
    {
        $inProgressWorkflows = $this->assignedWorkflows()
            ->where('status', 'in_progress')
            ->with('workflow.documentTypes')
            ->get();

        if ($inProgressWorkflows->isEmpty()) {
            return;
        }

        $currentDocumentIds = $this->documents()->pluck('document_type_id');

        foreach ($inProgressWorkflows as $employeeWorkflow) {
            $requiredIds = $employeeWorkflow->workflow->documentTypes->pluck('id');

            if ($requiredIds->diff($currentDocumentIds)->isEmpty()) {
                $employeeWorkflow->update([
                    'status' => 'completed',
                    'completed_at' => Carbon::now(),
                ]);
            }
        }
    }
}