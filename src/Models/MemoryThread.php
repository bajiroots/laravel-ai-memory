<?php

namespace LaravelAiMemory\Models;

use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class MemoryThread extends Model
{
    use HasUlids;

    protected $table = 'ai_memory_threads';

    protected $guarded = [];

    protected $casts = [
        'metadata' => 'array',
        'last_message_at' => 'datetime',
    ];

    public function messages(): HasMany
    {
        return $this->hasMany(MemoryMessage::class, 'thread_id');
    }

    public function memories(): HasMany
    {
        return $this->hasMany(Memory::class, 'thread_id');
    }
}
