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
        Schema::create('subsidi_types', function (Blueprint $table) {
            $table->id();
            $table->string('type_code')->unique();
            $table->string('type_name');
            $table->text('description')->nullable();
            $table->decimal('monthly_pagu', 15, 2)->default(0.0);
            $table->boolean('is_active')->default(true)->index();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('subsidi_types');
    }
};
