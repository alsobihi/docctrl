<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class WorkflowHistory extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'workflow_id',
        'employee_id',
        'employee_workflow_id',
        'action',
        'details',
        'document_type_id',
        'document_id',
        'created_by',
    ];

    /**
     * Get the workflow that owns the history record.
     */
    public function workflow(): BelongsTo
    {
        return $this->belongsTo(Workflow::class);
    }

    /**
     * Get the employee that owns the history record.
     */
    public function employee(): BelongsTo
    {
        return $this->belongsTo(Employee::class);
    }

    /**
     * Get the employee workflow that owns the history record.
     */
    public function employeeWorkflow(): BelongsTo
    {
        return $this->belongsTo(EmployeeWorkflow::class);
    }

    /**
     * Get the document type related to this history record.
     */
    public function documentType(): BelongsTo
    {
        return $this->belongsTo(DocumentType::class);
    }

    /**
     * Get the document related to this history record.
     */
    public function document(): BelongsTo
    {
        return $this->belongsTo(EmployeeDocument::class, 'document_id');
    }

    /**
     * Get the user who created the record.
     */
    public function createdBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * Get the action color
     */
    public function getActionColorAttribute(): string
    {
        return match($this->action) {
            'started' => 'blue',
            'completed' => 'green',
            'reopened' => 'yellow',
            'document_added' => 'indigo',
            'document_expired' => 'red',
            'document_deleted' => 'red',
            default => 'gray'
        };
    }
}