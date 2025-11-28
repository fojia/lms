<?php

declare(strict_types=1);

namespace Infrastructure\Persistence\Repository;

use Domain\Contract\Repository\StudentRepositoryInterface;
use Domain\Entity\Student;
use Domain\ValueObject\StudentId;

final class StudentRepository implements StudentRepositoryInterface
{
    public function findById(StudentId $id): Student
    {
        // TODO: Implement...
    }

    public function save(Student $student): void
    {
        // TODO: Implement...
    }
}
