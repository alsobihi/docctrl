<?php

// app/Models/Workflow.php
// --- Workflow Model ---

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Workflow extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'name',
        'description',
        'is_reopenable', // <-- Add this
        'plant_id',
        'project_id',
        'created_by',
        'updated_by',
        'deleted_by',
    ];

    protected $casts = [
        'is_reopenable' => 'boolean', // <-- Add this
    ];

    /**
     * The document types that belong to the workflow.
     */
   public function documentTypes(): BelongsToMany
    {
        return $this->belongsToMany(DocumentType::class, 'workflow_steps', 'workflow_id', 'document_type_id')
                    ->withPivot('id', 'step_order', 'is_mandatory') // include pivot `id` for deletion
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



public function assignments()
{
    return $this->hasMany(EmployeeWorkflow::class);
}






}
