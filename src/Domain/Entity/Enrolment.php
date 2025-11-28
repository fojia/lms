<?php

declare(strict_types=1);

namespace Domain\Entity;

use Domain\Exception\InvalidEnrolmentPeriodException;
use Domain\ValueObject\CourseId;
use Domain\ValueObject\DateTimeRange;
use Domain\ValueObject\EnrolmentId;
use Domain\ValueObject\StudentId;

final class Enrolment
{
    public function __construct(
        private readonly EnrolmentId $id,
        private readonly StudentId $studentId,
        private readonly CourseId $courseId,
        private DateTimeRange $period
    ) {
    }

    public function getId(): EnrolmentId
    {
        return $this->id;
    }

    public function getStudentId(): StudentId
    {
        return $this->studentId;
    }

    public function getCourseId(): CourseId
    {
        return $this->courseId;
    }

    public function getPeriod(): DateTimeRange
    {
        return $this->period;
    }

    public function isActiveAt(\DateTimeImmutable $dateTime): bool
    {
        return $this->period->isActive($dateTime);
    }

    public function updatePeriod(DateTimeRange $newPeriod): void
    {
        $now = new \DateTimeImmutable();
        if ($newPeriod->hasEnded($now)) {
            throw new InvalidEnrolmentPeriodException(
                'Cannot update enrolment to a period that has already ended'
            );
        }

        if ($this->period->end !== null && $newPeriod->start > $this->period->end) {
            throw new InvalidEnrolmentPeriodException(
                'New period start date cannot be after the current period end date'
            );
        }

        $this->period = $newPeriod;
    }
}
