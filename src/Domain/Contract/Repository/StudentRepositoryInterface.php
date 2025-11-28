<?php

declare(strict_types=1);

namespace Domain\Contract\Repository;

use Domain\Entity\Student;
use Domain\ValueObject\StudentId;

interface StudentRepositoryInterface
{
    public function findById(StudentId $id): Student;
    
    public function save(Student $student): void;
}
