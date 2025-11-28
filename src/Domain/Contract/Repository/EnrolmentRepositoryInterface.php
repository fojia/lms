<?php

declare(strict_types=1);

namespace Domain\Contract\Repository;

use Domain\Entity\Enrolment;
use Domain\ValueObject\CourseId;
use Domain\ValueObject\EnrolmentId;
use Domain\ValueObject\StudentId;

interface EnrolmentRepositoryInterface
{
    public function findById(EnrolmentId $id): Enrolment;
    
    public function findByStudentAndCourse(StudentId $studentId, CourseId $courseId): Enrolment;
    
    public function save(Enrolment $enrolment): void;
}
