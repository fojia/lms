<?php

declare(strict_types=1);

namespace Tests\Unit;

use Domain\Entity\Course;
use Domain\Service\ContentAccessControl;
use Domain\Entity\Enrolment;
use Domain\Entity\Homework;
use Domain\Entity\Lesson;
use Domain\Entity\PrepMaterial;
use Domain\Entity\Student;
use Domain\Exception\AccessDeniedException;
use Domain\ValueObject\ContentId;
use Domain\ValueObject\CourseId;
use Domain\ValueObject\DateTimeRange;
use Domain\ValueObject\EnrolmentId;
use Domain\ValueObject\StudentId;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\Attributes\TestDox;
use PHPUnit\Framework\TestCase;

final class ContentAccessControlTest extends TestCase
{
    private ContentAccessControl $accessControl;
    private Student $emma;
    private Course $course;
    private Lesson $cellStructureLesson;
    private Homework $homework;
    private PrepMaterial $prepMaterial;
    private Enrolment $enrolment;

    protected function setUp(): void
    {
        $this->accessControl = new ContentAccessControl();

        // Create student Emma
        $this->emma = new Student(
            new StudentId('emma-id'),
            'Emma'
        );

        // Create course: A-Level Biology (13/05/2025 - 12/06/2025)
        $this->course = new Course(
            new CourseId('biology-course-id'),
            'A-Level Biology',
            new DateTimeRange(
                new \DateTimeImmutable('2025-05-13 00:00:00'),
                new \DateTimeImmutable('2025-06-12 23:59:59')
            )
        );

        // Create lesson: "Cell Structure" — 15/05/2025 10:00
        $this->cellStructureLesson = new Lesson(
            new ContentId('lesson-cell-structure'),
            'Cell Structure',
            new \DateTimeImmutable('2025-05-15 10:00:00')
        );
        $this->course->addLesson($this->cellStructureLesson);

        // Create homework: "Label a Plant Cell" (available from course start)
        $this->homework = new Homework(
            new ContentId('homework-plant-cell'),
            'Label a Plant Cell',
            new \DateTimeImmutable('2025-05-13 00:00:00')
        );
        $this->course->addHomework($this->homework);

        // Create prep material: "Biology Reading Guide" (available from course start)
        $this->prepMaterial = new PrepMaterial(
            new ContentId('prep-biology-guide'),
            'Biology Reading Guide',
            new \DateTimeImmutable('2025-05-13 00:00:00')
        );
        $this->course->addPrepMaterial($this->prepMaterial);

        // Create enrolment: 01/05/2025 → 30/05/2025
        $this->enrolment = new Enrolment(
            new EnrolmentId('enrolment-emma-biology'),
            $this->emma->getId(),
            $this->course->getId(),
            new DateTimeRange(
                new \DateTimeImmutable('2025-05-01 00:00:00'),
                new \DateTimeImmutable('2025-05-30 23:59:59')
            )
        );
    }

    #[Test]
    #[TestDox('Scenario 1: On 01/05/2025, Emma tries to access Prep Material → ❌ Denied (course not started)')]
    public function scenario1_prepMaterialBeforeCourseStarts(): void
    {
        $accessTime = new \DateTimeImmutable('2025-05-01 12:00:00');

        $this->expectException(AccessDeniedException::class);
        $this->expectExceptionMessage('Course "A-Level Biology" has not started yet');

        $this->accessControl->checkAccess(
            $this->emma,
            $this->course,
            $this->prepMaterial,
            $this->enrolment,
            $accessTime
        );
    }

    #[Test]
    #[TestDox('Scenario 2: On 13/05/2025, she accesses Prep Material → ✅ Allowed')]
    public function scenario2_prepMaterialAfterCourseStarts(): void
    {
        $accessTime = new \DateTimeImmutable('2025-05-13 09:00:00');

        // Should not throw exception
        $this->accessControl->checkAccess(
            $this->emma,
            $this->course,
            $this->prepMaterial,
            $this->enrolment,
            $accessTime
        );

        $this->assertTrue(
            $this->accessControl->canAccess(
                $this->emma,
                $this->course,
                $this->prepMaterial,
                $this->enrolment,
                $accessTime
            )
        );
    }

    #[Test]
    #[TestDox('Scenario 3: On 15/05/2025 at 10:01, she accesses the Lesson → ✅ Allowed')]
    public function scenario3_lessonAfterScheduledTime(): void
    {
        $accessTime = new \DateTimeImmutable('2025-05-15 10:01:00');

        $this->accessControl->checkAccess(
            $this->emma,
            $this->course,
            $this->cellStructureLesson,
            $this->enrolment,
            $accessTime
        );

        $this->assertTrue(
            $this->accessControl->canAccess(
                $this->emma,
                $this->course,
                $this->cellStructureLesson,
                $this->enrolment,
                $accessTime
            )
        );
    }

    #[Test]
    #[TestDox('Lesson before scheduled time is denied')]
    public function lessonBeforeScheduledTime(): void
    {
        $accessTime = new \DateTimeImmutable('2025-05-15 09:59:00');

        $this->expectException(AccessDeniedException::class);
        $this->expectExceptionMessage('Content "Cell Structure" is not available yet');

        $this->accessControl->checkAccess(
            $this->emma,
            $this->course,
            $this->cellStructureLesson,
            $this->enrolment,
            $accessTime
        );
    }

    #[Test]
    #[TestDox('Scenario 4-5: On 20/05/2025, enrolment shortened, then on 21/05/2025 access denied')]
    public function scenario4and5_enrolmentShortenedThenAccessDenied(): void
    {
        // Scenario 4: External system shortens Emma's enrolment to end on 20/05/2025
        $this->enrolment->updatePeriod(
            new DateTimeRange(
                new \DateTimeImmutable('2025-05-01 00:00:00'),
                new \DateTimeImmutable('2025-05-20 23:59:59')
            )
        );

        // Scenario 5: On 21/05/2025, she tries to access Homework → ❌ Denied (enrolment expired early)
        $accessTime = new \DateTimeImmutable('2025-05-21 12:00:00');

        $this->expectException(AccessDeniedException::class);
        $this->expectExceptionMessage('Student "Emma" is not enrolled');

        $this->accessControl->checkAccess(
            $this->emma,
            $this->course,
            $this->homework,
            $this->enrolment,
            $accessTime
        );
    }

    #[Test]
    #[TestDox('Scenario 6: On 30/05/2025, she tries again → ❌ Denied')]
    public function scenario6_accessDeniedOnOriginalEnrolmentEnd(): void
    {
        // With shortened enrolment ending on 20/05/2025
        $this->enrolment->updatePeriod(
            new DateTimeRange(
                new \DateTimeImmutable('2025-05-01 00:00:00'),
                new \DateTimeImmutable('2025-05-20 23:59:59')
            )
        );

        $accessTime = new \DateTimeImmutable('2025-05-30 12:00:00');

        $this->expectException(AccessDeniedException::class);
        $this->expectExceptionMessage('Student "Emma" is not enrolled');

        $this->accessControl->checkAccess(
            $this->emma,
            $this->course,
            $this->homework,
            $this->enrolment,
            $accessTime
        );
    }

    #[Test]
    #[TestDox('Scenario 7: On 10/06/2025, course still running but Emma not enrolled → ❌ Denied')]
    public function scenario7_courseRunningButNotEnrolled(): void
    {
        // Using original enrolment (ends 30/05/2025)
        $accessTime = new \DateTimeImmutable('2025-06-10 12:00:00');

        $this->expectException(AccessDeniedException::class);
        $this->expectExceptionMessage('Student "Emma" is not enrolled');

        $this->accessControl->checkAccess(
            $this->emma,
            $this->course,
            $this->homework,
            $this->enrolment,
            $accessTime
        );
    }

    #[Test]
    #[TestDox('Homework is accessible from course start date')]
    public function homeworkAccessibleFromCourseStart(): void
    {
        $accessTime = new \DateTimeImmutable('2025-05-13 08:00:00');

        $this->accessControl->checkAccess(
            $this->emma,
            $this->course,
            $this->homework,
            $this->enrolment,
            $accessTime
        );

        $this->assertTrue(
            $this->accessControl->canAccess(
                $this->emma,
                $this->course,
                $this->homework,
                $this->enrolment,
                $accessTime
            )
        );
    }

    #[Test]
    #[TestDox('Access denied when student enrolled but course has not started')]
    public function enrolledButCourseNotStarted(): void
    {
        $accessTime = new \DateTimeImmutable('2025-05-10 12:00:00');

        $this->expectException(AccessDeniedException::class);
        $this->expectExceptionMessage('Course "A-Level Biology" has not started yet');

        $this->accessControl->checkAccess(
            $this->emma,
            $this->course,
            $this->homework,
            $this->enrolment,
            $accessTime
        );
    }

    #[Test]
    #[TestDox('canAccess returns false instead of throwing exception')]
    public function canAccessReturnsFalseWhenDenied(): void
    {
        $accessTime = new \DateTimeImmutable('2025-05-01 12:00:00');

        $result = $this->accessControl->canAccess(
            $this->emma,
            $this->course,
            $this->prepMaterial,
            $this->enrolment,
            $accessTime
        );

        $this->assertFalse($result);
    }

    #[Test]
    #[TestDox('canAccess returns true when access is allowed')]
    public function canAccessReturnsTrueWhenAllowed(): void
    {
        $accessTime = new \DateTimeImmutable('2025-05-13 12:00:00');

        $result = $this->accessControl->canAccess(
            $this->emma,
            $this->course,
            $this->prepMaterial,
            $this->enrolment,
            $accessTime
        );

        $this->assertTrue($result);
    }
}
