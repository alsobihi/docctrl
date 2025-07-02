<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('project_required_documents', function (Blueprint $table) {
            $table->id();
            $table->foreignId('project_id')->constrained('projects')->onDelete('cascade');
            $table->foreignId('document_type_id')->constrained('document_types')->onDelete('cascade');
            $table->timestamp('created_at')->useCurrent();
            $table->foreignId('created_by')->constrained('users');

            $table->unique(['project_id', 'document_type_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('project_required_documents');
    }
};
