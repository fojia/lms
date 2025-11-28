<?php

declare(strict_types=1);

namespace Domain\Entity;

use Domain\ValueObject\ContentId;

final class PrepMaterial extends CourseContent
{
    public function __construct(
        ContentId $id,
        string $title,
        private readonly \DateTimeImmutable $courseStartDate
    ) {
        parent::__construct($id, $title);
    }

    public function isAvailableAt(\DateTimeImmutable $dateTime): bool
    {
        return $dateTime >= $this->courseStartDate;
    }
}
