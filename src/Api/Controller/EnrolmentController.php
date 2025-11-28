<?php

declare(strict_types=1);

namespace Api\Controller;

use Api\Controller;
use Application\Command\UpdateEnrolmentPeriodCommand;
use Application\Handler\Command\UpdateEnrolmentPeriodCommandHandler;

final class EnrolmentController extends Controller
{
    public function __construct(
        private readonly UpdateEnrolmentPeriodCommandHandler $commandHandler
    ) {
    }

    public function updatePeriod(array $request): array
    {
        $command = new UpdateEnrolmentPeriodCommand(
            enrolmentId: $request['enrolment_id'],
            newStartDate: new \DateTimeImmutable($request['start_date']),
            newEndDate: new \DateTimeImmutable($request['end_date'])
        );

        $this->commandHandler->handle($command);

        return $this->successResponse([
            'message' => 'Enrolment period updated successfully'
        ]);
    }
}
