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
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('document_expiry_notifications');
    }
};