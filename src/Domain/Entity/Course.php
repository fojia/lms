<?php

declare(strict_types=1);

namespace Domain\Entity;

use Domain\Exception\ContentNotFoundException;
use Domain\ValueObject\ContentId;
use Domain\ValueObject\CourseId;
use Domain\ValueObject\DateTimeRange;

final class Course
{
    /** @var array<string, CourseContent> */
    private array $contents = [];

    public function __construct(
        private readonly CourseId $id,
        private readonly string $name,
        private readonly DateTimeRange $period
    ) {
    }

    public function getId(): CourseId
    {
        return $this->id;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getPeriod(): DateTimeRange
    {
        return $this->period;
    }

    public function addLesson(Lesson $lesson): void
    {
        $this->contents[$lesson->getId()->getValue()] = $lesson;
    }

    public function addHomework(Homework $homework): void
    {
        $this->contents[$homework->getId()->getValue()] = $homework;
    }

    public function addPrepMaterial(PrepMaterial $prepMaterial): void
    {
        $this->contents[$prepMaterial->getId()->getValue()] = $prepMaterial;
    }

    public function getContent(ContentId $contentId): CourseContent
    {
        $content = $this->contents[$contentId->getValue()] ?? null;

        if ($content === null) {
            throw new ContentNotFoundException(
                sprintf('Content with ID "%s" not found in course "%s"', $contentId->getValue(), $this->name)
            );
        }

        return $content;
    }

    public function hasStartedAt(\DateTimeImmutable $dateTime): bool
    {
        return $this->period->hasStarted($dateTime);
    }

    /**
     * @return array<CourseContent>
     */
    public function getAllContents(): array
    {
        return array_values($this->contents);
    }
}
