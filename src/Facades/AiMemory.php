<?php

namespace LaravelAiMemory\Facades;

use Illuminate\Support\Facades\Facade;

class AiMemory extends Facade
{
    protected static function getFacadeAccessor(): string
    {
        return 'ai-memory';
    }
}
