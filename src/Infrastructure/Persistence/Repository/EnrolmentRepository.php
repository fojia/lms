<?php

declare(strict_types=1);

namespace Infrastructure\Persistence\Repository;

use Domain\Contract\Repository\EnrolmentRepositoryInterface;
use Domain\Entity\Enrolment;
use Domain\ValueObject\CourseId;
use Domain\ValueObject\EnrolmentId;
use Domain\ValueObject\StudentId;

final class EnrolmentRepository implements EnrolmentRepositoryInterface
{
    public function findById(EnrolmentId $id): Enrolment
    {
        // TODO: Implement...
    }

    public function findByStudentAndCourse(StudentId $studentId, CourseId $courseId): Enrolment
    {
        // TODO: Implement...
    }

    public function save(Enrolment $enrolment): void
    {
        // TODO: Implement...
    }
}
