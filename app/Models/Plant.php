<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Plant extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'name',
        'location',
        'logo_path',
        'phone',
        'email',
        'address',
        'manager_name',
        'manager_email',
        'manager_phone',
        'description',
        'status',
        'established_date',
        'capacity',
        'certification',
        'operating_hours',
        'latitude',
        'longitude',
        'created_by',
        'updated_by',
        'deleted_by',
    ];

    protected $casts = [
        'operating_hours' => 'array',
        'established_date' => 'date',
        'latitude' => 'decimal:8',
        'longitude' => 'decimal:8',
    ];

    /**
     * Get the employees associated with the plant.
     */
    public function employees(): HasMany
    {
        return $this->hasMany(Employee::class);
    }

    /**
     * Get the projects associated with the plant.
     */
    public function projects(): HasMany
    {
        return $this->hasMany(Project::class);
    }

    /**
     * Get the workflows associated with the plant.
     */
    public function workflows(): HasMany
    {
        return $this->hasMany(Workflow::class);
    }

    /**
     * Get the user who created the record.
     */
    public function createdBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * Get the user who updated the record.
     */
    public function updatedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    /**
     * Get the user who deleted the record.
     */
    public function deletedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'deleted_by');
    }

    /**
     * Get the plant's logo URL
     */
    public function getLogoUrlAttribute(): ?string
    {
        return $this->logo_path ? asset('storage/' . $this->logo_path) : null;
    }

    /**
     * Get formatted operating hours
     */
    public function getFormattedOperatingHoursAttribute(): string
    {
        if (!$this->operating_hours) {
            return 'Not specified';
        }

        $hours = $this->operating_hours;
        if (isset($hours['start']) && isset($hours['end'])) {
            return $hours['start'] . ' - ' . $hours['end'];
        }

        return 'Not specified';
    }

    /**
     * Get status badge color
     */
    public function getStatusColorAttribute(): string
    {
        return match($this->status) {
            'active' => 'green',
            'inactive' => 'red',
            'maintenance' => 'yellow',
            default => 'gray'
        };
    }

    /**
     * Scope for active plants
     */
    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    /**
     * Get total employees count
     */
    public function getTotalEmployeesAttribute(): int
    {
        return $this->employees()->count();
    }

    /**
     * Get total projects count
     */
    public function getTotalProjectsAttribute(): int
    {
        return $this->projects()->count();
    }

    /**
     * Get active workflows count
     */
    public function getActiveWorkflowsAttribute(): int
    {
        return $this->workflows()->count();
    }
}