<?php

declare(strict_types=1);

namespace Domain\Contract\Repository;

use Domain\Entity\Course;
use Domain\ValueObject\CourseId;

interface CourseRepositoryInterface
{
    public function findById(CourseId $id): Course;
    
    public function save(Course $course): void;
}
