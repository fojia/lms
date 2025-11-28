<?php

declare(strict_types=1);

namespace Application\Query;

final readonly class CheckContentAccessQuery
{
    public function __construct(
        public string $studentId,
        public string $courseId,
        public string $contentId,
        public \DateTimeImmutable $accessTime
    ) {
    }
}
