<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::table('document_types', function (Blueprint $table) {
            if (!Schema::hasColumn('document_types', 'template_id')) {
                $table->foreignId('template_id')->nullable()->after('category')->constrained('document_templates')->onDelete('set null');
            }
            if (!Schema::hasColumn('document_types', 'warning_period_days')) {
                $table->integer('warning_period_days')->nullable()->after('validity_rule');
            }
        });
    }
    public function down(): void {
        Schema::table('document_types', function (Blueprint $table) {
            if (Schema::hasColumn('document_types', 'template_id')) {
                $table->dropForeign(['template_id']);
                $table->dropColumn('template_id');
            }
            if (Schema::hasColumn('document_types', 'warning_period_days')) {
                $table->dropColumn('warning_period_days');
            }
        });
    }
};
