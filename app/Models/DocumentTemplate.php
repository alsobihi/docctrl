<?php

// app/Models/DocumentTemplate.php
// --- DocumentTemplate Model ---

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DocumentTemplate extends Model
{
    use HasFactory, SoftDeletes;

   
    protected $fillable = ['name', 'fields', 'created_by', 'updated_by'];
    protected $casts = ['fields' => 'array'];
    /**
     * Get the user who created the record.
     */
    public function createdBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}
