<?php

namespace App\Traits;

use Illuminate\Http\JsonResponse;

trait ApiResponseTrait
{
    /**
     * Return a generic success response.
     */
    public function successResponse(mixed $data = null, string $message = 'Success', int $code = 200): JsonResponse
    {
        return response()->json([
            'success' => true,
            'message' => $message,
            'data'    => $data,
            'errors'  => null,
        ], $code);
    }

    /**
     * Return a generic error response.
     */
    public function errorResponse(string $message = 'Error', int $code = 400, mixed $errors = []): JsonResponse
    {
        return response()->json([
            'success' => false,
            'message' => $message,
            'data'    => null,
            'errors'  => $errors ?: null,
        ], $code);
    }

    /**
     * Return a 404 Not Found response.
     */
    public function notFoundResponse(string $message = 'Resource not found'): JsonResponse
    {
        return response()->json([
            'success' => false,
            'message' => $message,
            'data'    => null,
            'errors'  => null,
        ], 404);
    }

    /**
     * Return a 401 Unauthorized response.
     */
    public function unauthorizedResponse(string $message = 'Unauthorized'): JsonResponse
    {
        return response()->json([
            'success' => false,
            'message' => $message,
            'data'    => null,
            'errors'  => null,
        ], 401);
    }

    /**
     * Return a 403 Forbidden response.
     */
    public function forbiddenResponse(string $message = 'Forbidden'): JsonResponse
    {
        return response()->json([
            'success' => false,
            'message' => $message,
            'data'    => null,
            'errors'  => null,
        ], 403);
    }

    /**
     * Return a 422 Validation Error response.
     */
    public function validationErrorResponse(mixed $errors): JsonResponse
    {
        return response()->json([
            'success' => false,
            'message' => 'Validation failed',
            'data'    => null,
            'errors'  => $errors,
        ], 422);
    }

    /**
     * Return a 201 Created response.
     */
    public function createdResponse(mixed $data = null, string $message = 'Created successfully'): JsonResponse
    {
        return response()->json([
            'success' => true,
            'message' => $message,
            'data'    => $data,
            'errors'  => null,
        ], 201);
    }

    /**
     * Return a 204 No Content response.
     */
    public function noContentResponse(): JsonResponse
    {
        return response()->json(null, 204);
    }

    /**
     * Return a paginated success response.
     *
     * @param  \Illuminate\Pagination\LengthAwarePaginator  $paginator
     * @param  string                                        $message
     * @return JsonResponse
     */
    public function paginatedResponse(\Illuminate\Pagination\LengthAwarePaginator $paginator, string $message = 'Success'): JsonResponse
    {
        return response()->json([
            'success' => true,
            'message' => $message,
            'data'    => [
                'data'  => $paginator->items(),
                'meta'  => [
                    'current_page' => $paginator->currentPage(),
                    'last_page'    => $paginator->lastPage(),
                    'per_page'     => $paginator->perPage(),
                    'total'        => $paginator->total(),
                ],
                'links' => [
                    'first' => $paginator->url(1),
                    'last'  => $paginator->url($paginator->lastPage()),
                    'prev'  => $paginator->previousPageUrl(),
                    'next'  => $paginator->nextPageUrl(),
                ],
            ],
            'errors'  => null,
        ], 200);
    }
}
