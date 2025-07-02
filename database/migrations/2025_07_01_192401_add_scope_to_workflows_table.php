<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('workflows', function (Blueprint $table) {
            $table->foreignId('plant_id')->nullable()->after('description')->constrained()->onDelete('cascade');
            $table->foreignId('project_id')->nullable()->after('plant_id')->constrained()->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::table('workflows', function (Blueprint $table) {
            $table->dropForeign(['plant_id']);
            $table->dropForeign(['project_id']);
            $table->dropColumn(['plant_id', 'project_id']);
        });
    }
};
