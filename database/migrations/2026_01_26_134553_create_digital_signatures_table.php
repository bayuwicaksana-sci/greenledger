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
        Schema::create('digital_signatures', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->index()->constrained()->restrictOnDelete();
            $table->string('entity_type', 50)->index();
            $table->unsignedBigInteger('entity_id')->index();
            $table->string('signature_hash', 255);
            $table->text('signature_data');
            $table->timestamp('signed_at')->useCurrent()->index();
            $table->string('ip_address', 45);

            // Composite indexes for common query patterns
            $table->index(['entity_type', 'entity_id', 'signed_at']);

            // Unique constraint to prevent duplicate signatures
            $table->unique(['entity_type', 'entity_id', 'user_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('digital_signatures');
    }
};
