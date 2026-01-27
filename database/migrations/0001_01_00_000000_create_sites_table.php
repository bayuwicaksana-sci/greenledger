<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('sites', function (Blueprint $table) {
            $table->id();
            $table->string('site_code')->unique()->index();
            $table->string('site_name');
            $table->text('address')->nullable();
            $table->jsonb('contact_info')->nullable();
            $table->boolean('is_active')->default(false)->index();
            $table->timestamps();
            $table->softDeletes();

            // Composite index for common query patterns
            $table->index(['is_active', 'site_code']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sites');
    }
};
