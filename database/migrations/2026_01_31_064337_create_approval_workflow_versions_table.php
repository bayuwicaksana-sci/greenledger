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
        Schema::create('approval_workflow_versions', function (
            Blueprint $table,
        ) {
            $table->id();
            $table
                ->foreignId('approval_workflow_id')
                ->constrained('approval_workflows')
                ->cascadeOnDelete();
            $table->integer('version_number')->default(1);
            $table->json('configuration')->nullable(); // Stores complete workflow config
            $table->boolean('is_active')->default(false);
            $table->timestamp('activated_at')->nullable();
            $table->timestamp('deactivated_at')->nullable();
            $table->timestamps();

            $table->unique(
                ['approval_workflow_id', 'version_number'],
                'awv_workflow_version_unique',
            );
            $table->index(['approval_workflow_id', 'is_active']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('approval_workflow_versions');
    }
};
