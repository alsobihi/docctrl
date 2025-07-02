<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('project_assignments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('employee_id')->constrained('employees')->onDelete('cascade');
            $table->foreignId('project_id')->constrained('projects')->onDelete('cascade');
            $table->timestamp('created_at')->useCurrent();
            $table->foreignId('created_by')->constrained('users');

            $table->unique(['employee_id', 'project_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('project_assignments');
    }
};
