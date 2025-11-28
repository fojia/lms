<?php

declare(strict_types=1);

namespace Domain\ValueObject;

use Ramsey\Uuid\Uuid;

abstract readonly class Id
{
    private string $id;

    public function __construct(?string $id = null)
    {
        $this->id = $id ?? self::generate();
    }

    public static function generate(): string
    {
        return Uuid::uuid4()->toString();
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
