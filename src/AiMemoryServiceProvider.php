<?php

namespace LaravelAiMemory;

use Illuminate\Support\ServiceProvider;
use LaravelAiMemory\Contracts\ConfidenceScorer;
use LaravelAiMemory\Contracts\EmbeddingProvider;
use LaravelAiMemory\Contracts\MemoryRepository;
use LaravelAiMemory\Contracts\SimilaritySearch;
use LaravelAiMemory\Contracts\ThreadDetector;
use LaravelAiMemory\Providers\OpenAiEmbeddingProvider;
use LaravelAiMemory\Repositories\EloquentMemoryRepository;
use LaravelAiMemory\Search\DatabaseFallbackSimilaritySearch;
use LaravelAiMemory\Search\PgvectorSimilaritySearch;
use LaravelAiMemory\Services\AiMemoryManager;
use LaravelAiMemory\Services\DefaultConfidenceScorer;
use LaravelAiMemory\Services\SemanticThreadDetector;

class AiMemoryServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->mergeConfigFrom(__DIR__.'/../config/ai-memory.php', 'ai-memory');

        $this->app->singleton(EmbeddingProvider::class, OpenAiEmbeddingProvider::class);
        $this->app->singleton(MemoryRepository::class, EloquentMemoryRepository::class);
        $this->app->singleton(ConfidenceScorer::class, DefaultConfidenceScorer::class);
        $this->app->singleton(ThreadDetector::class, SemanticThreadDetector::class);

        $this->app->singleton(SimilaritySearch::class, function ($app) {
            return config('ai-memory.database.vector_driver') === 'pgvector'
                ? $app->make(PgvectorSimilaritySearch::class)
                : $app->make(DatabaseFallbackSimilaritySearch::class);
        });

        $this->app->singleton(AiMemoryManager::class);
        $this->app->alias(AiMemoryManager::class, 'ai-memory');
    }

    public function boot(): void
    {
        $this->publishes([
            __DIR__.'/../config/ai-memory.php' => config_path('ai-memory.php'),
        ], 'ai-memory-config');

        $this->publishes([
            __DIR__.'/../database/migrations' => database_path('migrations'),
        ], 'ai-memory-migrations');

        $this->loadMigrationsFrom(__DIR__.'/../database/migrations');
    }
}
