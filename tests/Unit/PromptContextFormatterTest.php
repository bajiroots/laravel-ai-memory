<?php

namespace LaravelAiMemory\Tests\Unit;

use Illuminate\Support\Collection;
use LaravelAiMemory\Models\Memory;
use LaravelAiMemory\Support\PromptContextFormatter;
use PHPUnit\Framework\TestCase;

class PromptContextFormatterTest extends TestCase
{
    public function test_it_formats_memory_context(): void
    {
        $memory = new Memory([
            'type' => 'decision',
            'content' => 'Use Stripe webhooks for invoice sync.',
        ]);

        $formatted = (new PromptContextFormatter)->format(new Collection([$memory]));

        $this->assertStringContainsString('[Decision]', $formatted);
        $this->assertStringContainsString('Use Stripe webhooks', $formatted);
    }
}
