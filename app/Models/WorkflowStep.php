<?php

// app/Models/WorkflowStep.php
// --- WorkflowStep Model ---
// This model represents the pivot table and is useful for managing steps directly.

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class WorkflowStep extends Model
{
    use HasFactory;

    // Since this is a pivot model, timestamps are handled differently.
    // The migration sets `created_at` but not `updated_at`.
    public const UPDATED_AT = null;

    protected $fillable = [
        'workflow_id',
        'document_type_id',
        'step_order',
        'is_mandatory',
        'created_by',
    ];

    protected $casts = [
        'is_mandatory' => 'boolean',
    ];

    public function workflow(): BelongsTo
    {
        return $this->belongsTo(Workflow::class);
    }

    public function documentType(): BelongsTo
    {
        return $this->belongsTo(DocumentType::class);
    }
}
