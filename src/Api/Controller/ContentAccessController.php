<?php

declare(strict_types=1);

namespace Api\Controller;

use Api\Controller;
use Application\Handler\Query\CheckContentAccessQueryHandler;
use Application\Query\CheckContentAccessQuery;

final class ContentAccessController extends Controller
{
    public function __construct(
        private readonly CheckContentAccessQueryHandler $queryHandler
    ) {
    }

    public function checkAccess(array $request): array
    {
        $query = new CheckContentAccessQuery(
            studentId: $request['student_id'],
            courseId: $request['course_id'],
            contentId: $request['content_id'],
            accessTime: new \DateTimeImmutable($request['access_time'] ?? 'now')
        );

        $result = $this->queryHandler->handle($query);

        if ($result->allowed) {
            return $this->successResponse([
                'allowed' => true
            ]);
        }

        return $this->errorResponse($result->reason, 403);
    }
}
