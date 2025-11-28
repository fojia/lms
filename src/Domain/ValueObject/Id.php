<?php

declare(strict_types=1);

namespace Domain\ValueObject;

abstract readonly class Id
{
    private string $id;

    public function __construct(?string $id = null)
    {
        $this->id = $id ?? uniqid('id_', true);
    }

    public function getValue(): string
    {
        return $this->id;
    }

    public function equals(self $other): bool
    {
        return $this->id === $other->getValue();
    }
}
