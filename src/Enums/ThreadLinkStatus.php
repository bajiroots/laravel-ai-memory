<?php

namespace LaravelAiMemory\Enums;

enum ThreadLinkStatus: string
{
    case Suggested = 'suggested';
    case Accepted = 'accepted';
    case Rejected = 'rejected';
}
