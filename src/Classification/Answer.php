<?php

namespace JevPHP\Classification;

abstract class Answer
{
    public function __construct(
        public readonly string $type,
        public readonly int $inputTokens = 0,
        public readonly int $outputTokens = 0,
    ) {
    }
}
