<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('employees', function (Blueprint $table) {
            $table->string('profile_photo_path')->nullable()->after('employee_code');
            $table->string('phone')->nullable()->after('profile_photo_path');
            $table->string('email')->nullable()->after('phone');
            $table->text('address')->nullable()->after('email');
            $table->date('date_of_birth')->nullable()->after('address');
            $table->string('nationality')->nullable()->after('date_of_birth');
            $table->string('position')->nullable()->after('nationality');
            $table->string('department')->nullable()->after('position');
            $table->date('hire_date')->nullable()->after('department');
            $table->string('employment_status')->default('active')->after('hire_date'); // active, inactive, terminated
            $table->string('emergency_contact_name')->nullable()->after('employment_status');
            $table->string('emergency_contact_phone')->nullable()->after('emergency_contact_name');
            $table->string('emergency_contact_relationship')->nullable()->after('emergency_contact_phone');
            $table->decimal('salary', 10, 2)->nullable()->after('emergency_contact_relationship');
            $table->string('contract_type')->nullable()->after('salary'); // permanent, contract, temporary
            $table->date('contract_end_date')->nullable()->after('contract_type');
            $table->text('notes')->nullable()->after('contract_end_date');
            $table->json('skills')->nullable()->after('notes');
            $table->string('badge_number')->nullable()->unique()->after('skills');
        });
    }

    public function down(): void
    {
        Schema::table('employees', function (Blueprint $table) {
            $table->dropColumn([
                'profile_photo_path', 'phone', 'email', 'address', 'date_of_birth',
                'nationality', 'position', 'department', 'hire_date', 'employment_status',
                'emergency_contact_name', 'emergency_contact_phone', 'emergency_contact_relationship',
                'salary', 'contract_type', 'contract_end_date', 'notes', 'skills', 'badge_number'
            ]);
        });
    }
};