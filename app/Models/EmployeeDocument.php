<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class EmployeeDocument extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'employee_id',
        'document_type_id',
        'project_id',
        'issue_date',
        'expiry_date',
        'data',
        'file_path',
        'created_by',
        'updated_by',
        'deleted_by',
    ];

    protected $casts = [
        'data' => 'array',
        'issue_date' => 'date',
        'expiry_date' => 'date',
    ];

    /**
     * Get the employee that owns the document.
     */
    public function employee(): BelongsTo
    {
        return $this->belongsTo(Employee::class);
    }

    /**
     * Get the type of the document.
     */
    public function documentType(): BelongsTo
    {
        return $this->belongsTo(DocumentType::class);
    }

    /**
     * Get the project associated with the document.
     */
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
     * Get the expiry notifications for this document
     */
    public function expiryNotifications(): HasMany
    {
        return $this->hasMany(DocumentExpiryNotification::class, 'document_id');
    }

    /**
     * Get the workflow history records for this document
     */
    public function workflowHistory(): HasMany
    {
        return $this->hasMany(WorkflowHistory::class, 'document_id');
    }

    /**
     * Check if the document is expired
     */
    public function getIsExpiredAttribute(): bool
    {
        return $this->expiry_date && $this->expiry_date->isPast();
    }

    /**
     * Check if the document is expiring soon (within 30 days)
     */
    public function getIsExpiringSoonAttribute(): bool
    {
        if (!$this->expiry_date) {
            return false;
        }
        
        return !$this->is_expired && $this->expiry_date->isBetween(now(), now()->addDays(30));
    }

    /**
     * Get days until expiry
     */
    public function getDaysUntilExpiryAttribute(): ?int
    {
        if (!$this->expiry_date) {
            return null;
        }
        
        if ($this->is_expired) {
            return 0;
        }
        
        return now()->diffInDays($this->expiry_date);
    }

    /**
     * Get days since expiry
     */
    public function getDaysSinceExpiryAttribute(): ?int
    {
        if (!$this->expiry_date || !$this->is_expired) {
            return null;
        }
        
        return $this->expiry_date->diffInDays(now());
    }

    /**
     * Scope for expired documents
     */
    public function scopeExpired($query)
    {
        return $query->where('expiry_date', '<', now());
    }

    /**
     * Scope for expiring soon documents
     */
    public function scopeExpiringSoon($query, $days = 30)
    {
        return $query->whereBetween('expiry_date', [now(), now()->addDays($days)]);
    }

    /**
     * Scope for valid documents
     */
    public function scopeValid($query)
    {
        return $query->where('expiry_date', '>', now()->addDays(30));
    }
}