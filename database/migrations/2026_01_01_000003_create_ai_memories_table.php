<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::connection(config('ai-memory.database.connection'))->create('ai_memories', function (Blueprint $table) {
            $table->ulid('id')->primary();
            $table->foreignUlid('thread_id')->nullable()->constrained('ai_memory_threads')->nullOnDelete();
            $table->foreignUlid('message_id')->nullable()->constrained('ai_memory_messages')->nullOnDelete();
            $table->string('owner_type')->nullable();
            $table->string('owner_id')->nullable();
            $table->string('type')->index();
            $table->text('content');
            $table->text('summary')->nullable();
            $table->unsignedTinyInteger('importance')->default(3)->index();
            $table->decimal('confidence', 5, 4)->nullable();
            $table->json('metadata')->nullable();
            $table->string('source')->nullable();
            $table->timestamp('expires_at')->nullable()->index();
            $table->timestamps();

            $table->index(['owner_type', 'owner_id']);
        });
    }

    public function down(): void
    {
        Schema::connection(config('ai-memory.database.connection'))->dropIfExists('ai_memories');
    }
};
