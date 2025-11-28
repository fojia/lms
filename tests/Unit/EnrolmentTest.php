<?php

declare(strict_types=1);

namespace Tests\Unit;

use Domain\Entity\Enrolment;
use Domain\ValueObject\CourseId;
use Domain\ValueObject\DateTimeRange;
use Domain\ValueObject\EnrolmentId;
use Domain\ValueObject\StudentId;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\Attributes\TestDox;
use PHPUnit\Framework\TestCase;

final class EnrolmentTest extends TestCase
{
    #[Test]
    #[TestDox('Enrolment can be constructed with required properties')]
    public function construction(): void
    {
        $enrolmentId = new EnrolmentId();
        $studentId = new StudentId();
        $courseId = new CourseId();
        $period = new DateTimeRange(
            new \DateTimeImmutable('2025-05-01'),
            new \DateTimeImmutable('2025-05-31')
        );

        $enrolment = new Enrolment($enrolmentId, $studentId, $courseId, $period);

        $this->assertSame($enrolmentId, $enrolment->getId());
        $this->assertSame($studentId, $enrolment->getStudentId());
        $this->assertSame($courseId, $enrolment->getCourseId());
        $this->assertSame($period, $enrolment->getPeriod());
    }

    #[Test]
    #[TestDox('Enrolment isActiveAt returns true when date is within period')]
    public function isActiveWithinPeriod(): void
    {
        $enrolment = new Enrolment(
            new EnrolmentId(),
            new StudentId(),
            new CourseId(),
            new DateTimeRange(
                new \DateTimeImmutable('2025-05-01'),
                new \DateTimeImmutable('2025-05-31')
            )
        );

        $this->assertTrue($enrolment->isActiveAt(new \DateTimeImmutable('2025-05-15')));
    }

    #[Test]
    #[TestDox('Enrolment isActiveAt returns false when date is before period')]
    public function isNotActiveBeforePeriod(): void
    {
        $enrolment = new Enrolment(
            new EnrolmentId(),
            new StudentId(),
            new CourseId(),
            new DateTimeRange(
                new \DateTimeImmutable('2025-05-01'),
                new \DateTimeImmutable('2025-05-31')
            )
        );

        $this->assertFalse($enrolment->isActiveAt(new \DateTimeImmutable('2025-04-30')));
    }

    #[Test]
    #[TestDox('Enrolment isActiveAt returns false when date is after period')]
    public function isNotActiveAfterPeriod(): void
    {
        $enrolment = new Enrolment(
            new EnrolmentId(),
            new StudentId(),
            new CourseId(),
            new DateTimeRange(
                new \DateTimeImmutable('2025-05-01'),
                new \DateTimeImmutable('2025-05-31')
            )
        );

        $this->assertFalse($enrolment->isActiveAt(new \DateTimeImmutable('2025-06-01')));
    }

    #[Test]
    #[TestDox('Enrolment period can be updated')]
    public function updatePeriod(): void
    {
        $enrolment = new Enrolment(
            new EnrolmentId(),
            new StudentId(),
            new CourseId(),
            new DateTimeRange(
                new \DateTimeImmutable('2025-05-01'),
                new \DateTimeImmutable('2025-05-31')
            )
        );

        $newPeriod = new DateTimeRange(
            new \DateTimeImmutable('2025-05-01'),
            new \DateTimeImmutable('2025-05-20')
        );

        $enrolment->updatePeriod($newPeriod);

        $this->assertSame($newPeriod, $enrolment->getPeriod());
        $this->assertFalse($enrolment->isActiveAt(new \DateTimeImmutable('2025-05-25')));
    }
}
