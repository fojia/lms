<?php

declare(strict_types=1);

namespace Domain\Entity;

use Domain\ValueObject\ContentId;

final class Lesson extends CourseContent
{
    public function __construct(
        ContentId $id,
        string $title,
        private readonly \DateTimeImmutable $scheduledDateTime
    ) {
        parent::__construct($id, $title);
    }

    public function getScheduledDateTime(): \DateTimeImmutable
    {
        return $this->scheduledDateTime;
    }

    public function isAvailableAt(\DateTimeImmutable $dateTime): bool
    {
        return $dateTime >= $this->scheduledDateTime;
    }
}
