<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('plants', function (Blueprint $table) {
            $table->string('logo_path')->nullable()->after('location');
            $table->string('phone')->nullable()->after('logo_path');
            $table->string('email')->nullable()->after('phone');
            $table->text('address')->nullable()->after('email');
            $table->string('manager_name')->nullable()->after('address');
            $table->string('manager_email')->nullable()->after('manager_name');
            $table->string('manager_phone')->nullable()->after('manager_email');
            $table->text('description')->nullable()->after('manager_phone');
            $table->string('status')->default('active')->after('description'); // active, inactive, maintenance
            $table->date('established_date')->nullable()->after('status');
            $table->integer('capacity')->nullable()->after('established_date');
            $table->string('certification')->nullable()->after('capacity'); // ISO, safety certifications
            $table->json('operating_hours')->nullable()->after('certification');
            $table->decimal('latitude', 10, 8)->nullable()->after('operating_hours');
            $table->decimal('longitude', 11, 8)->nullable()->after('latitude');
        });
    }

    public function down(): void
    {
        Schema::table('plants', function (Blueprint $table) {
            $table->dropColumn([
                'logo_path', 'phone', 'email', 'address', 'manager_name', 
                'manager_email', 'manager_phone', 'description', 'status',
                'established_date', 'capacity', 'certification', 'operating_hours',
                'latitude', 'longitude'
            ]);
        });
    }
};