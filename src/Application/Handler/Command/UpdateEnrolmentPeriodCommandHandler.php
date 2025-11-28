<?php

declare(strict_types=1);

namespace Application\Handler\Command;

use Application\Command\UpdateEnrolmentPeriodCommand;
use Domain\Contract\Repository\EnrolmentRepositoryInterface;
use Domain\ValueObject\DateTimeRange;
use Domain\ValueObject\EnrolmentId;

final readonly class UpdateEnrolmentPeriodCommandHandler
{
    public function __construct(
        private EnrolmentRepositoryInterface $enrolmentRepository
    ) {
    }

    public function handle(UpdateEnrolmentPeriodCommand $command): void
    {
        $enrolment = $this->enrolmentRepository->findById(
            new EnrolmentId($command->enrolmentId)
        );

        $newPeriod = new DateTimeRange(
            $command->newStartDate,
            $command->newEndDate
        );

        $enrolment->updatePeriod($newPeriod);

        $this->enrolmentRepository->save($enrolment);
    }
}
