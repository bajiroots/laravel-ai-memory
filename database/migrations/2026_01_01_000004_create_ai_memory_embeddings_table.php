<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::connection(config('ai-memory.database.connection'))->create('ai_memory_embeddings', function (Blueprint $table) {
            $table->ulid('id')->primary();
            $table->string('embeddable_type');
            $table->string('embeddable_id');
            $table->string('provider');
            $table->string('model');
            $table->unsignedInteger('dimensions');
            $table->json('embedding_json')->nullable();
            $table->string('content_hash')->index();
            $table->json('metadata')->nullable();
            $table->timestamps();

            $table->index(['embeddable_type', 'embeddable_id']);
            $table->index(['provider', 'model']);
        });

        if (DB::connection(config('ai-memory.database.connection'))->getDriverName() === 'pgsql') {
            $dimensions = (int) config('ai-memory.embeddings.dimensions', 1536);
            DB::connection(config('ai-memory.database.connection'))
                ->statement("ALTER TABLE ai_memory_embeddings ADD COLUMN embedding vector({$dimensions})");
        }
    }

    public function down(): void
    {
        Schema::connection(config('ai-memory.database.connection'))->dropIfExists('ai_memory_embeddings');
    }
};
