<?php

declare(strict_types=1);

namespace Application\Handler\Query;

use Application\DTO\CheckContentAccessResult;
use Application\Query\CheckContentAccessQuery;
use Domain\Contract\Repository\CourseRepositoryInterface;
use Domain\Contract\Repository\EnrolmentRepositoryInterface;
use Domain\Contract\Repository\StudentRepositoryInterface;
use Domain\Exception\AccessDeniedException;
use Domain\Exception\ContentNotFoundException;
use Domain\Service\ContentAccessControl;
use Domain\ValueObject\ContentId;
use Domain\ValueObject\CourseId;
use Domain\ValueObject\StudentId;

final readonly class CheckContentAccessQueryHandler
{
    public function __construct(
        private ContentAccessControl $accessControl,
        private StudentRepositoryInterface $studentRepository,
        private CourseRepositoryInterface $courseRepository,
        private EnrolmentRepositoryInterface $enrolmentRepository
    ) {
    }

    public function handle(CheckContentAccessQuery $query): CheckContentAccessResult
    {
        try {
            $student = $this->studentRepository->findById(new StudentId($query->studentId));
            $course = $this->courseRepository->findById(new CourseId($query->courseId));
            $content = $course->getContent(new ContentId($query->contentId));
            $enrolment = $this->enrolmentRepository->findByStudentAndCourse(
                new StudentId($query->studentId),
                new CourseId($query->courseId)
            );

            $this->accessControl->checkAccess(
                $student,
                $course,
                $content,
                $enrolment,
                $query->accessTime
            );

            return new CheckContentAccessResult(true);
        } catch (AccessDeniedException | ContentNotFoundException $e) {
            return new CheckContentAccessResult(false, $e->getMessage());
        }
    }
}
