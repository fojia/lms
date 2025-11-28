<?php

declare(strict_types=1);

namespace Infrastructure\Persistence\Repository;

use Domain\Contract\Repository\CourseRepositoryInterface;
use Domain\Entity\Course;
use Domain\ValueObject\CourseId;

final class CourseRepository implements CourseRepositoryInterface
{
    public function findById(CourseId $id): Course
    {
        // TODO: Implement...
    }

    public function save(Course $course): void
    {
        // TODO: Implement...
    }
}
