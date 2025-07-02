<?php

// app/Models/Project.php
// --- Project Model ---

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Project extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'name',
        'project_code',
        'plant_id', // <-- Add this
        'created_by',
        'updated_by',
        'deleted_by',
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
           return $this->belongsToMany(Employee::class, 'project_assignments')->withPivot('role');


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
}
