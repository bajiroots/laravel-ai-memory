<?php

namespace LaravelAiMemory\Tests\Unit;

use LaravelAiMemory\Support\ContentHasher;
use PHPUnit\Framework\TestCase;

class ContentHasherTest extends TestCase
{
    public function test_it_normalizes_whitespace_before_hashing(): void
    {
        $hasher = new ContentHasher;

        $this->assertSame(
            $hasher->hash('Stripe invoice sync'),
            $hasher->hash("  Stripe\ninvoice   sync  "),
        );
    }
}
