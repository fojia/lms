<?php

declare(strict_types=1);

namespace Domain\Entity;

use Domain\ValueObject\ContentId;

abstract class CourseContent
{
    public function __construct(
        private readonly ContentId $id,
        private readonly string $title
    ) {
    }

    public function getId(): ContentId
    {
        return $this->id;
    }

    public function getTitle(): string
    {
        return $this->title;
    }

    abstract public function isAvailableAt(\DateTimeImmutable $dateTime): bool;
}
