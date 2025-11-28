<?php

declare(strict_types=1);

namespace Tests\Integration;

use Application\Handler\Query\CheckContentAccessQueryHandler;
use Application\Query\CheckContentAccessQuery;
use Domain\Contract\Repository\CourseRepositoryInterface;
use Domain\Contract\Repository\EnrolmentRepositoryInterface;
use Domain\Contract\Repository\StudentRepositoryInterface;
use Domain\Entity\Course;
use Domain\Entity\Enrolment;
use Domain\Entity\Homework;
use Domain\Entity\Lesson;
use Domain\Entity\PrepMaterial;
use Domain\Entity\Student;
use Domain\Service\ContentAccessControl;
use Domain\ValueObject\ContentId;
use Domain\ValueObject\CourseId;
use Domain\ValueObject\DateTimeRange;
use Domain\ValueObject\EnrolmentId;
use Domain\ValueObject\StudentId;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

final class CheckContentAccessTest extends TestCase
{
    private CheckContentAccessQueryHandler $handler;
    private Student $emma;
    private Course $course;
    private Enrolment $enrolment;

    protected function setUp(): void
    {
        // Create test data
        $this->emma = new Student(
            new StudentId('emma-id'),
            'Emma'
        );

        $this->course = new Course(
            new CourseId('biology-course-id'),
            'A-Level Biology',
            new DateTimeRange(
                new \DateTimeImmutable('2025-05-13 00:00:00'),
                new \DateTimeImmutable('2025-06-12 23:59:59')
            )
        );

        $lesson = new Lesson(
            new ContentId('lesson-cell-structure'),
            'Cell Structure',
            new \DateTimeImmutable('2025-05-15 10:00:00')
        );
        $this->course->addLesson($lesson);

        $homework = new Homework(
            new ContentId('homework-plant-cell'),
            'Label a Plant Cell',
            new \DateTimeImmutable('2025-05-13 00:00:00')
        );
        $this->course->addHomework($homework);

        $prepMaterial = new PrepMaterial(
            new ContentId('prep-biology-guide'),
            'Biology Reading Guide',
            new \DateTimeImmutable('2025-05-13 00:00:00')
        );
        $this->course->addPrepMaterial($prepMaterial);

        $this->enrolment = new Enrolment(
            new EnrolmentId('enrolment-emma-biology'),
            $this->emma->getId(),
            $this->course->getId(),
            new DateTimeRange(
                new \DateTimeImmutable('2025-05-01 00:00:00'),
                new \DateTimeImmutable('2025-05-30 23:59:59')
            )
        );

        // Create mocks
        $studentRepository = $this->createMock(StudentRepositoryInterface::class);
        $studentRepository->method('findById')->willReturn($this->emma);

        $courseRepository = $this->createMock(CourseRepositoryInterface::class);
        $courseRepository->method('findById')->willReturn($this->course);

        $enrolmentRepository = $this->createMock(EnrolmentRepositoryInterface::class);
        $enrolmentRepository->method('findByStudentAndCourse')->willReturn($this->enrolment);

        $this->handler = new CheckContentAccessQueryHandler(
            new ContentAccessControl(),
            $studentRepository,
            $courseRepository,
            $enrolmentRepository
        );
    }

    #[Test]
    public function accessDeniedBeforeCourseStarts(): void
    {
        $query = new CheckContentAccessQuery(
            studentId: 'emma-id',
            courseId: 'biology-course-id',
            contentId: 'prep-biology-guide',
            accessTime: new \DateTimeImmutable('2025-05-01 12:00:00')
        );

        $result = $this->handler->handle($query);

        $this->assertFalse($result->allowed);
        $this->assertStringContainsString('has not started yet', $result->reason);
    }

    #[Test]
    public function accessAllowedAfterCourseStarts(): void
    {
        $query = new CheckContentAccessQuery(
            studentId: 'emma-id',
            courseId: 'biology-course-id',
            contentId: 'prep-biology-guide',
            accessTime: new \DateTimeImmutable('2025-05-13 09:00:00')
        );

        $result = $this->handler->handle($query);

        $this->assertTrue($result->allowed);
        $this->assertNull($result->reason);
    }

    #[Test]
    public function lessonAccessAllowedAfterScheduledTime(): void
    {
        $query = new CheckContentAccessQuery(
            studentId: 'emma-id',
            courseId: 'biology-course-id',
            contentId: 'lesson-cell-structure',
            accessTime: new \DateTimeImmutable('2025-05-15 10:01:00')
        );

        $result = $this->handler->handle($query);

        $this->assertTrue($result->allowed);
    }

    #[Test]
    public function lessonAccessDeniedBeforeScheduledTime(): void
    {
        $query = new CheckContentAccessQuery(
            studentId: 'emma-id',
            courseId: 'biology-course-id',
            contentId: 'lesson-cell-structure',
            accessTime: new \DateTimeImmutable('2025-05-15 09:59:00')
        );

        $result = $this->handler->handle($query);

        $this->assertFalse($result->allowed);
        $this->assertStringContainsString('not available yet', $result->reason);
    }

    #[Test]
    public function accessDeniedAfterEnrolmentExpires(): void
    {
        $query = new CheckContentAccessQuery(
            studentId: 'emma-id',
            courseId: 'biology-course-id',
            contentId: 'homework-plant-cell',
            accessTime: new \DateTimeImmutable('2025-06-10 12:00:00')
        );

        $result = $this->handler->handle($query);

        $this->assertFalse($result->allowed);
        $this->assertStringContainsString('not enrolled', $result->reason);
    }

}
