<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Add new fields to workflows table
        Schema::table('workflows', function (Blueprint $table) {
            $table->boolean('auto_reopen_on_expiry')->default(false)->after('is_reopenable');
            $table->boolean('auto_reopen_on_deletion')->default(false)->after('auto_reopen_on_expiry');
            $table->integer('notification_days_before')->nullable()->after('auto_reopen_on_deletion');
        });

        // Add new fields to employee_workflows table
        Schema::table('employee_workflows', function (Blueprint $table) {
            $table->timestamp('reopened_at')->nullable()->after('completed_at');
            $table->string('reopened_reason')->nullable()->after('reopened_at');
            $table->timestamp('last_notification_at')->nullable()->after('reopened_reason');
        });

        // Create workflow_histories table
        Schema::create('workflow_histories', function (Blueprint $table) {
            $table->id();
            $table->foreignId('workflow_id')->constrained()->onDelete('cascade');
            $table->foreignId('employee_id')->constrained()->onDelete('cascade');
            $table->foreignId('employee_workflow_id')->nullable()->constrained('employee_workflows')->onDelete('cascade');
            $table->string('action'); // started, completed, reopened, document_added, document_expired, document_deleted
            $table->text('details')->nullable();
            $table->foreignId('document_type_id')->nullable()->constrained()->onDelete('set null');
            $table->foreignId('document_id')->nullable()->constrained('employee_documents')->onDelete('set null');
            $table->foreignId('created_by')->nullable()->constrained('users')->onDelete('set null');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('workflow_histories');

        Schema::table('employee_workflows', function (Blueprint $table) {
            $table->dropColumn(['reopened_at', 'reopened_reason', 'last_notification_at']);
        });

        Schema::table('workflows', function (Blueprint $table) {
            $table->dropColumn(['auto_reopen_on_expiry', 'auto_reopen_on_deletion', 'notification_days_before']);
        });
    }
};