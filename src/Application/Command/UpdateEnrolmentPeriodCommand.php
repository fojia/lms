<?php

declare(strict_types=1);

namespace Application\Command;

final readonly class UpdateEnrolmentPeriodCommand
{
    public function __construct(
        public string $enrolmentId,
        public \DateTimeImmutable $newStartDate,
        public \DateTimeImmutable $newEndDate
    ) {
    }
}
