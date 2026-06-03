<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::connection(config('ai-memory.database.connection'))->create('ai_memory_thread_links', function (Blueprint $table) {
            $table->ulid('id')->primary();
            $table->foreignUlid('source_thread_id')->constrained('ai_memory_threads')->cascadeOnDelete();
            $table->foreignUlid('target_thread_id')->constrained('ai_memory_threads')->cascadeOnDelete();
            $table->string('relationship')->default('related')->index();
            $table->decimal('score', 5, 4)->nullable();
            $table->string('status')->default('suggested')->index();
            $table->json('metadata')->nullable();
            $table->timestamps();

            $table->index(['source_thread_id', 'target_thread_id']);
        });
    }

    public function down(): void
    {
        Schema::connection(config('ai-memory.database.connection'))->dropIfExists('ai_memory_thread_links');
    }
};
