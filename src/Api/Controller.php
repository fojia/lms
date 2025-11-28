<?php

declare(strict_types=1);

namespace Api;

abstract class Controller
{
    protected function jsonResponse(array $data, int $status = 200): array
    {
        return [
            'data' => $data,
            'status' => $status
        ];
    }

    protected function successResponse(array $data): array
    {
        return $this->jsonResponse([
            'success' => true,
            ...$data
        ], 200);
    }

    protected function errorResponse(string $message, int $status = 400): array
    {
        return $this->jsonResponse([
            'success' => false,
            'error' => $message
        ], $status);
    }
}
