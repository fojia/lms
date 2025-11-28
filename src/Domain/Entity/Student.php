<?php

declare(strict_types=1);

namespace Domain\Entity;

use Domain\ValueObject\StudentId;

final class Student
{
    public function __construct(
        private readonly StudentId $id,
        private readonly string $name
    ) {
    }

    public function getId(): StudentId
    {
        return $this->id;
    }

    public function getName(): string
    {
        return $this->name;
    }
}
