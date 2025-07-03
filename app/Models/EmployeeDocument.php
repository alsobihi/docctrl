<?php

// app/Models/EmployeeDocument.php
// --- EmployeeDocument Model ---

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

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
}
