<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::connection(config('ai-memory.database.connection'))->create('ai_memory_messages', function (Blueprint $table) {
            $table->ulid('id')->primary();
            $table->foreignUlid('thread_id')->constrained('ai_memory_threads')->cascadeOnDelete();
            $table->string('external_id')->nullable()->index();
            $table->string('role');
            $table->longText('content');
            $table->json('metadata')->nullable();
            $table->unsignedInteger('token_count')->nullable();
            $table->timestamps();

            $table->index(['thread_id', 'created_at']);
        });
    }

    public function down(): void
    {
        Schema::connection(config('ai-memory.database.connection'))->dropIfExists('ai_memory_messages');
    }
};
