<?php

declare(strict_types=1);

namespace Domain\Service;

use Domain\Entity\Course;
use Domain\Entity\CourseContent;
use Domain\Entity\Enrolment;
use Domain\Entity\Student;
use Domain\Exception\AccessDeniedException;

final class ContentAccessControl
{
    public function checkAccess(
        Student $student,
        Course $course,
        CourseContent $content,
        Enrolment $enrolment,
        \DateTimeImmutable $accessTime
    ): void {
        // Check if student is currently enrolled
        if (!$enrolment->isActiveAt($accessTime)) {
            throw new AccessDeniedException(
                sprintf(
                    'Student "%s" is not enrolled in course "%s" at %s',
                    $student->getName(),
                    $course->getName(),
                    $accessTime->format('Y-m-d H:i:s')
                )
            );
        }

        // Check if course has started
        if (!$course->hasStartedAt($accessTime)) {
            throw new AccessDeniedException(
                sprintf(
                    'Course "%s" has not started yet at %s',
                    $course->getName(),
                    $accessTime->format('Y-m-d H:i:s')
                )
            );
        }

        // Check if content is available
        if (!$content->isAvailableAt($accessTime)) {
            throw new AccessDeniedException(
                sprintf(
                    'Content "%s" is not available yet at %s',
                    $content->getTitle(),
                    $accessTime->format('Y-m-d H:i:s')
                )
            );
        }
    }

    public function canAccess(
        Student $student,
        Course $course,
        CourseContent $content,
        Enrolment $enrolment,
        \DateTimeImmutable $accessTime
    ): bool {
        try {
            $this->checkAccess($student, $course, $content, $enrolment, $accessTime);
            return true;
        } catch (AccessDeniedException) {
            return false;
        }
    }
}
