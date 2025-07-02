<?php

// app/Models/DocumentType.php
// --- DocumentType Model ---

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DocumentType extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'name', 'category', 'template_id', 'validity_rule', 'warning_period_days',
        'created_by', 'updated_by', 'deleted_by',
    ];

    protected $casts = ['validity_rule' => 'array'];

    public function template(): BelongsTo
    {
        return $this->belongsTo(DocumentTemplate::class);
    }

    public function createdBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}
