<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('projects', function (Blueprint $table) {
            $table->text('description')->nullable()->after('project_code');
            $table->string('status')->default('planning')->after('description'); // planning, active, on_hold, completed, cancelled
            $table->string('priority')->default('medium')->after('status'); // low, medium, high, critical
            $table->string('client_name')->nullable()->after('priority');
            $table->string('client_contact')->nullable()->after('client_name');
            $table->string('client_email')->nullable()->after('client_contact');
            $table->decimal('budget', 15, 2)->nullable()->after('client_email');
            $table->decimal('actual_cost', 15, 2)->nullable()->after('budget');
            $table->date('start_date')->nullable()->after('actual_cost');
            $table->date('end_date')->nullable()->after('start_date');
            $table->date('actual_start_date')->nullable()->after('end_date');
            $table->date('actual_end_date')->nullable()->after('actual_start_date');
            $table->integer('progress_percentage')->default(0)->after('actual_end_date');
            $table->string('project_manager')->nullable()->after('progress_percentage');
            $table->string('project_manager_email')->nullable()->after('project_manager');
            $table->string('project_manager_phone')->nullable()->after('project_manager_email');
            $table->json('milestones')->nullable()->after('project_manager_phone');
            $table->json('risks')->nullable()->after('milestones');
            $table->text('notes')->nullable()->after('risks');
            $table->string('location')->nullable()->after('notes');
            $table->json('tags')->nullable()->after('location');
            $table->string('contract_number')->nullable()->after('tags');
            $table->date('contract_date')->nullable()->after('contract_number');
        });
    }

    public function down(): void
    {
        Schema::table('projects', function (Blueprint $table) {
            $table->dropColumn([
                'description', 'status', 'priority', 'client_name', 'client_contact', 'client_email',
                'budget', 'actual_cost', 'start_date', 'end_date', 'actual_start_date', 'actual_end_date',
                'progress_percentage', 'project_manager', 'project_manager_email', 'project_manager_phone',
                'milestones', 'risks', 'notes', 'location', 'tags', 'contract_number', 'contract_date'
            ]);
        });
    }
};