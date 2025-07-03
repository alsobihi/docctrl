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
        // Check if the table already exists
        if (!Schema::hasTable('document_expiry_notifications')) {
            Schema::create('document_expiry_notifications', function (Blueprint $table) {
                $table->id();
                $table->foreignId('document_id')->constrained('employee_documents')->onDelete('cascade');
                $table->string('notification_type'); // expired, expiring_soon, etc.
                $table->timestamp('notified_at');
                $table->nullableMorphs('recipient'); // For tracking who was notified
                $table->timestamps();
                
                // Prevent duplicate notifications with a shorter index name
                $table->unique(['document_id', 'notification_type'], 'doc_expiry_notif_unique');
            });
        } else {
            // If the table exists but doesn't have the unique constraint, add it
            if (!Schema::hasTable('document_expiry_notifications') || 
                !Schema::getConnection()->getDoctrineSchemaManager()->listTableDetails('document_expiry_notifications')->hasIndex('doc_expiry_notif_unique')) {
                Schema::table('document_expiry_notifications', function (Blueprint $table) {
                    $table->unique(['document_id', 'notification_type'], 'doc_expiry_notif_unique');
                });
            }
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // No need to drop the table here as it's handled in the original migration
    }
};