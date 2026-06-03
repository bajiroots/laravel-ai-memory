<?php

namespace LaravelAiMemory\Support;

use Illuminate\Support\Collection;

class PromptContextFormatter
{
    public function format(Collection $memories): string
    {
        if ($memories->isEmpty()) {
            return '';
        }

        $lines = ['Relevant prior context:', ''];

        foreach ($memories as $memory) {
            $type = str($memory->type)->replace('_', ' ')->title();
            $lines[] = "- [{$type}] {$memory->content}";
        }

        return implode(PHP_EOL, $lines);
    }
}
