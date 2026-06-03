<?php

namespace LaravelAiMemory\Enums;

enum MemoryType: string
{
    case Fact = 'fact';
    case Decision = 'decision';
    case Todo = 'todo';
    case TechnicalContext = 'technical_context';
    case Summary = 'summary';
}
