<?php

declare(strict_types=1);

namespace Domain\ValueObject;

final readonly class DateTimeRange
{
    public function __construct(
        public \DateTimeImmutable $start,
        public ?\DateTimeImmutable $end = null
    ) {
        if ($end !== null && $start > $end) {
            throw new \InvalidArgumentException('Start date must be before or equal to end date.');
        }
    }

    public function contains(\DateTimeImmutable $dateTime): bool
    {
        if ($dateTime < $this->start) {
            return false;
        }

        if ($this->end !== null && $dateTime > $this->end) {
            return false;
        }

        return true;
    }

    public function hasStarted(\DateTimeImmutable $now): bool
    {
        return $now >= $this->start;
    }

    public function hasEnded(\DateTimeImmutable $now): bool
    {
        return $this->end !== null && $now > $this->end;
    }

    public function isActive(\DateTimeImmutable $now): bool
    {
        return $this->hasStarted($now) && !$this->hasEnded($now);
    }
}
