<?php

declare(strict_types=1);

namespace Tests\Unit;

use Domain\Entity\Course;
use Domain\Entity\Homework;
use Domain\Entity\Lesson;
use Domain\Entity\PrepMaterial;
use Domain\Exception\ContentNotFoundException;
use Domain\ValueObject\ContentId;
use Domain\ValueObject\CourseId;
use Domain\ValueObject\DateTimeRange;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\Attributes\TestDox;
use PHPUnit\Framework\TestCase;

final class CourseTest extends TestCase
{
    #[Test]
    #[TestDox('Course can be constructed with basic properties')]
    public function construction(): void
    {
        $courseId = new CourseId();
        $period = new DateTimeRange(
            new \DateTimeImmutable('2025-05-01'),
            new \DateTimeImmutable('2025-05-31')
        );

        $course = new Course($courseId, 'Mathematics', $period);

        $this->assertSame($courseId, $course->getId());
        $this->assertSame('Mathematics', $course->getName());
        $this->assertSame($period, $course->getPeriod());
    }

    #[Test]
    #[TestDox('Course can add and retrieve lessons')]
    public function addLesson(): void
    {
        $course = new Course(
            new CourseId(),
            'Biology',
            new DateTimeRange(
                new \DateTimeImmutable('2025-05-01'),
                new \DateTimeImmutable('2025-05-31')
            )
        );

        $lessonId = new ContentId();
        $lesson = new Lesson(
            $lessonId,
            'Cell Structure',
            new \DateTimeImmutable('2025-05-15 10:00:00')
        );

        $course->addLesson($lesson);

        $retrievedLesson = $course->getContent($lessonId);
        $this->assertSame($lesson, $retrievedLesson);
    }

    #[Test]
    #[TestDox('Course can add and retrieve homework')]
    public function addHomework(): void
    {
        $course = new Course(
            new CourseId(),
            'Biology',
            new DateTimeRange(
                new \DateTimeImmutable('2025-05-01'),
                new \DateTimeImmutable('2025-05-31')
            )
        );

        $homeworkId = new ContentId();
        $homework = new Homework(
            $homeworkId,
            'Plant Cell Diagram',
            new \DateTimeImmutable('2025-05-01')
        );

        $course->addHomework($homework);

        $retrievedHomework = $course->getContent($homeworkId);
        $this->assertSame($homework, $retrievedHomework);
    }

    #[Test]
    #[TestDox('Course can add and retrieve prep material')]
    public function addPrepMaterial(): void
    {
        $course = new Course(
            new CourseId(),
            'Biology',
            new DateTimeRange(
                new \DateTimeImmutable('2025-05-01'),
                new \DateTimeImmutable('2025-05-31')
            )
        );

        $prepId = new ContentId();
        $prep = new PrepMaterial(
            $prepId,
            'Reading Guide',
            new \DateTimeImmutable('2025-05-01')
        );

        $course->addPrepMaterial($prep);

        $retrievedPrep = $course->getContent($prepId);
        $this->assertSame($prep, $retrievedPrep);
    }

    #[Test]
    #[TestDox('Course throws exception when content not found')]
    public function contentNotFound(): void
    {
        $course = new Course(
            new CourseId(),
            'Biology',
            new DateTimeRange(
                new \DateTimeImmutable('2025-05-01'),
                new \DateTimeImmutable('2025-05-31')
            )
        );

        $this->expectException(ContentNotFoundException::class);
        $this->expectExceptionMessage('Content with ID');

        $course->getContent(new ContentId());
    }

    #[Test]
    #[TestDox('Course hasStartedAt returns true when date is after course start')]
    public function hasStarted(): void
    {
        $course = new Course(
            new CourseId(),
            'Biology',
            new DateTimeRange(
                new \DateTimeImmutable('2025-05-01'),
                new \DateTimeImmutable('2025-05-31')
            )
        );

        $this->assertTrue($course->hasStartedAt(new \DateTimeImmutable('2025-05-15')));
    }

    #[Test]
    #[TestDox('Course hasStartedAt returns false when date is before course start')]
    public function hasNotStarted(): void
    {
        $course = new Course(
            new CourseId(),
            'Biology',
            new DateTimeRange(
                new \DateTimeImmutable('2025-05-01'),
                new \DateTimeImmutable('2025-05-31')
            )
        );

        $this->assertFalse($course->hasStartedAt(new \DateTimeImmutable('2025-04-30')));
    }

    #[Test]
    #[TestDox('Course getAllContents returns all added content')]
    public function getAllContents(): void
    {
        $course = new Course(
            new CourseId(),
            'Biology',
            new DateTimeRange(
                new \DateTimeImmutable('2025-05-01'),
                new \DateTimeImmutable('2025-05-31')
            )
        );

        $lesson = new Lesson(
            new ContentId(),
            'Lesson 1',
            new \DateTimeImmutable('2025-05-15 10:00:00')
        );
        $homework = new Homework(
            new ContentId(),
            'Homework 1',
            new \DateTimeImmutable('2025-05-01')
        );
        $prep = new PrepMaterial(
            new ContentId(),
            'Prep 1',
            new \DateTimeImmutable('2025-05-01')
        );

        $course->addLesson($lesson);
        $course->addHomework($homework);
        $course->addPrepMaterial($prep);

        $contents = $course->getAllContents();

        $this->assertCount(3, $contents);
        $this->assertContains($lesson, $contents);
        $this->assertContains($homework, $contents);
        $this->assertContains($prep, $contents);
    }
}
