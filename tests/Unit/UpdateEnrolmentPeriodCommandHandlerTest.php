<?php

declare(strict_types=1);

namespace Tests\Unit;

use Application\Command\UpdateEnrolmentPeriodCommand;
use Application\Handler\Command\UpdateEnrolmentPeriodCommandHandler;
use Domain\Contract\Repository\EnrolmentRepositoryInterface;
use Domain\Entity\Enrolment;
use Domain\ValueObject\CourseId;
use Domain\ValueObject\DateTimeRange;
use Domain\ValueObject\EnrolmentId;
use Domain\ValueObject\StudentId;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

final class UpdateEnrolmentPeriodCommandHandlerTest extends TestCase
{
    #[Test]
    public function itUpdatesEnrolmentPeriod(): void
    {
        $enrolmentId = new EnrolmentId('enrolment-123');
        $enrolment = new Enrolment(
            $enrolmentId,
            new StudentId('student-123'),
            new CourseId('course-123'),
            new DateTimeRange(
                new \DateTimeImmutable('2025-01-01'),
                new \DateTimeImmutable('2025-12-31')
            )
        );

        $repository = $this->createMock(EnrolmentRepositoryInterface::class);
        $repository->expects($this->once())
            ->method('findById')
            ->with($this->equalTo($enrolmentId))
            ->willReturn($enrolment);

        $repository->expects($this->once())
            ->method('save')
            ->with($this->callback(function (Enrolment $savedEnrolment) {
                return $savedEnrolment->getPeriod()->start->format('Y-m-d') === '2025-02-01'
                    && $savedEnrolment->getPeriod()->end->format('Y-m-d') === '2025-06-30';
            }));

        $handler = new UpdateEnrolmentPeriodCommandHandler($repository);

        $command = new UpdateEnrolmentPeriodCommand(
            enrolmentId: 'enrolment-123',
            newStartDate: new \DateTimeImmutable('2025-02-01'),
            newEndDate: new \DateTimeImmutable('2025-06-30')
        );

        $handler->handle($command);
    }

    #[Test]
    public function itThrowsExceptionWhenEnrolmentNotFound(): void
    {
        $repository = $this->createMock(EnrolmentRepositoryInterface::class);
        $repository->method('findById')
            ->willThrowException(new \RuntimeException('Enrolment not found'));

        $handler = new UpdateEnrolmentPeriodCommandHandler($repository);

        $command = new UpdateEnrolmentPeriodCommand(
            enrolmentId: 'non-existent',
            newStartDate: new \DateTimeImmutable('2025-02-01'),
            newEndDate: new \DateTimeImmutable('2025-06-30')
        );

        $this->expectException(\RuntimeException::class);
        $this->expectExceptionMessage('Enrolment not found');

        $handler->handle($command);
    }
}
