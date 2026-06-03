<?php

namespace LaravelAiMemory\Models;

use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphMany;

class Memory extends Model
{
    use HasUlids;

    protected $table = 'ai_memories';

    protected $guarded = [];

    protected $casts = [
        'metadata' => 'array',
        'confidence' => 'float',
        'expires_at' => 'datetime',
    ];

    public function thread(): BelongsTo
    {
        return $this->belongsTo(MemoryThread::class, 'thread_id');
    }

    public function message(): BelongsTo
    {
        return $this->belongsTo(MemoryMessage::class, 'message_id');
    }

    public function embeddings(): MorphMany
    {
        return $this->morphMany(MemoryEmbedding::class, 'embeddable');
    }
}
