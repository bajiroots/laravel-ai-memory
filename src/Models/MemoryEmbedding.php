<?php

namespace LaravelAiMemory\Models;

use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class MemoryEmbedding extends Model
{
    use HasUlids;

    protected $table = 'ai_memory_embeddings';

    protected $guarded = [];

    protected $casts = [
        'embedding_json' => 'array',
        'metadata' => 'array',
    ];

    public function embeddable(): MorphTo
    {
        return $this->morphTo();
    }
}
