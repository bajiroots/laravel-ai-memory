<?php

namespace LaravelAiMemory\Enums;

enum ThreadRelationship: string
{
    case Related = 'related';
    case Duplicate = 'duplicate';
    case Continuation = 'continuation';
    case Merged = 'merged';
}
