<?php

namespace LaravelAiMemory\Models;

use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class MemoryThreadLink extends Model
{
    use HasUlids;

    protected $table = 'ai_memory_thread_links';

    protected $guarded = [];

    protected $casts = [
        'score' => 'float',
        'metadata' => 'array',
    ];

    public function sourceThread(): BelongsTo
    {
        return $this->belongsTo(MemoryThread::class, 'source_thread_id');
    }

    public function targetThread(): BelongsTo
    {
        return $this->belongsTo(MemoryThread::class, 'target_thread_id');
    }
}
