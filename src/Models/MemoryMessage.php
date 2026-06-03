<?php

namespace LaravelAiMemory\Models;

use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphMany;

class MemoryMessage extends Model
{
    use HasUlids;

    protected $table = 'ai_memory_messages';

    protected $guarded = [];

    protected $casts = [
        'metadata' => 'array',
    ];

    public function thread(): BelongsTo
    {
        return $this->belongsTo(MemoryThread::class, 'thread_id');
    }

    public function embeddings(): MorphMany
    {
        return $this->morphMany(MemoryEmbedding::class, 'embeddable');
    }
}
