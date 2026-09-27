<?php

declare(strict_types=1);

namespace JevPHP\Classification;

abstract class QuestionType
{
    public function __construct(
        public readonly string $type,
        public readonly string $instructions,
    ) {
    }
}
