<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::connection(config('ai-memory.database.connection'))->create('ai_memory_threads', function (Blueprint $table) {
            $table->ulid('id')->primary();
            $table->string('external_id')->nullable()->index();
            $table->string('owner_type')->nullable();
            $table->string('owner_id')->nullable();
            $table->string('title')->nullable();
            $table->text('summary')->nullable();
            $table->string('status')->default('active')->index();
            $table->json('metadata')->nullable();
            $table->timestamp('last_message_at')->nullable()->index();
            $table->timestamps();

            $table->index(['owner_type', 'owner_id']);
        });
    }

    public function down(): void
    {
        Schema::connection(config('ai-memory.database.connection'))->dropIfExists('ai_memory_threads');
    }
};
