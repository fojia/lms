<?php

declare(strict_types=1);

namespace Application\DTO;

final readonly class CheckContentAccessResult
{
    public function __construct(
        public bool $allowed,
        public ?string $reason = null
    ) {
    }
}
