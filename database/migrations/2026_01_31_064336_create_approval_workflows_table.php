<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('approval_workflows', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->text('description')->nullable();
            $table->string('model_type'); // Polymorphic reference to target model class
            $table->boolean('is_active')->default(false);
            $table->unsignedBigInteger('current_version_id')->nullable(); // FK added later
            $table->timestamps();
            $table->softDeletes();
        });

        // MySQL-only generated column for unique active workflow per model_type
        if (DB::getDriverName() !== 'sqlite') {
            DB::statement(
                'ALTER TABLE approval_workflows ADD COLUMN active_model_type VARCHAR(255) GENERATED ALWAYS AS (IF(is_active = 1, model_type, NULL)) STORED',
            );

            Schema::table('approval_workflows', function (Blueprint $table) {
                $table->unique('active_model_type', 'unique_active_workflow');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('approval_workflows');
    }
};
