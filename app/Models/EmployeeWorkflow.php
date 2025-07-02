<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class EmployeeWorkflow extends Model
{
    use HasFactory;
    protected $fillable = ['employee_id', 'workflow_id', 'status', 'created_by', 'completed_at'];

    public function employee(): BelongsTo { return $this->belongsTo(Employee::class); }
    public function workflow(): BelongsTo { return $this->belongsTo(Workflow::class); }
}
