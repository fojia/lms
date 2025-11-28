<?php

declare(strict_types=1);

namespace Tests\Unit;

use Domain\ValueObject\DateTimeRange;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\Attributes\TestDox;
use PHPUnit\Framework\TestCase;

final class DateTimeRangeTest extends TestCase
{
    #[Test]
    #[TestDox('DateTimeRange can be constructed with start and end dates')]
    public function construction(): void
    {
        $start = new \DateTimeImmutable('2025-05-01 10:00:00');
        $end = new \DateTimeImmutable('2025-05-31 18:00:00');

        $range = new DateTimeRange($start, $end);

        $this->assertSame($start, $range->start);
        $this->assertSame($end, $range->end);
    }

    #[Test]
    #[TestDox('DateTimeRange can be constructed with only start date (no end)')]
    public function constructionWithoutEnd(): void
    {
        $start = new \DateTimeImmutable('2025-05-01 10:00:00');

        $range = new DateTimeRange($start);

        $this->assertSame($start, $range->start);
        $this->assertNull($range->end);
    }

    #[Test]
    #[TestDox('DateTimeRange throws exception if start is after end')]
    public function invalidRange(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Start date must be before or equal to end date');

        new DateTimeRange(
            new \DateTimeImmutable('2025-05-31'),
            new \DateTimeImmutable('2025-05-01')
        );
    }

    #[Test]
    #[TestDox('DateTimeRange contains returns true for date within range')]
    public function containsDateWithinRange(): void
    {
        $range = new DateTimeRange(
            new \DateTimeImmutable('2025-05-01'),
            new \DateTimeImmutable('2025-05-31')
        );

        $this->assertTrue($range->contains(new \DateTimeImmutable('2025-05-15')));
    }

    #[Test]
    #[TestDox('DateTimeRange contains returns false for date before range')]
    public function containsDateBeforeRange(): void
    {
        $range = new DateTimeRange(
            new \DateTimeImmutable('2025-05-01'),
            new \DateTimeImmutable('2025-05-31')
        );

        $this->assertFalse($range->contains(new \DateTimeImmutable('2025-04-30')));
    }

    #[Test]
    #[TestDox('DateTimeRange contains returns false for date after range')]
    public function containsDateAfterRange(): void
    {
        $range = new DateTimeRange(
            new \DateTimeImmutable('2025-05-01'),
            new \DateTimeImmutable('2025-05-31')
        );

        $this->assertFalse($range->contains(new \DateTimeImmutable('2025-06-01')));
    }

    #[Test]
    #[TestDox('DateTimeRange hasStarted returns true when date is after start')]
    public function hasStarted(): void
    {
        $range = new DateTimeRange(
            new \DateTimeImmutable('2025-05-01'),
            new \DateTimeImmutable('2025-05-31')
        );

        $this->assertTrue($range->hasStarted(new \DateTimeImmutable('2025-05-15')));
    }

    #[Test]
    #[TestDox('DateTimeRange hasStarted returns false when date is before start')]
    public function hasNotStarted(): void
    {
        $range = new DateTimeRange(
            new \DateTimeImmutable('2025-05-01'),
            new \DateTimeImmutable('2025-05-31')
        );

        $this->assertFalse($range->hasStarted(new \DateTimeImmutable('2025-04-30')));
    }

    #[Test]
    #[TestDox('DateTimeRange hasEnded returns true when date is after end')]
    public function hasEnded(): void
    {
        $range = new DateTimeRange(
            new \DateTimeImmutable('2025-05-01'),
            new \DateTimeImmutable('2025-05-31')
        );

        $this->assertTrue($range->hasEnded(new \DateTimeImmutable('2025-06-01')));
    }

    #[Test]
    #[TestDox('DateTimeRange hasEnded returns false when no end date')]
    public function hasNotEndedWhenNoEndDate(): void
    {
        $range = new DateTimeRange(
            new \DateTimeImmutable('2025-05-01')
        );

        $this->assertFalse($range->hasEnded(new \DateTimeImmutable('2025-12-31')));
    }

    #[Test]
    #[TestDox('DateTimeRange isActive returns true when date is within active period')]
    public function isActive(): void
    {
        $range = new DateTimeRange(
            new \DateTimeImmutable('2025-05-01'),
            new \DateTimeImmutable('2025-05-31')
        );

        $this->assertTrue($range->isActive(new \DateTimeImmutable('2025-05-15')));
    }

    #[Test]
    #[TestDox('DateTimeRange isActive returns false when date is before start')]
    public function isNotActiveBeforeStart(): void
    {
        $range = new DateTimeRange(
            new \DateTimeImmutable('2025-05-01'),
            new \DateTimeImmutable('2025-05-31')
        );

        $this->assertFalse($range->isActive(new \DateTimeImmutable('2025-04-30')));
    }

    #[Test]
    #[TestDox('DateTimeRange isActive returns false when date is after end')]
    public function isNotActiveAfterEnd(): void
    {
        $range = new DateTimeRange(
            new \DateTimeImmutable('2025-05-01'),
            new \DateTimeImmutable('2025-05-31')
        );

        $this->assertFalse($range->isActive(new \DateTimeImmutable('2025-06-01')));
    }
}
