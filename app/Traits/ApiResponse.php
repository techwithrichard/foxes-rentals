<?php

namespace App\Traits;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Response;

trait ApiResponse
{
    /**
     * Success response
     */
    protected function successResponse(
        $data = null,
        string $message = 'Success',
        int $code = Response::HTTP_OK,
        array $meta = []
    ): JsonResponse {
        $response = [
            'success' => true,
            'message' => $message,
            'timestamp' => now()->toISOString(),
        ];

        if ($data !== null) {
            $response['data'] = $data;
        }

        if (!empty($meta)) {
            $response['meta'] = $meta;
        }

        return response()->json($response, $code);
    }

    /**
     * Error response
     */
    protected function errorResponse(
        string $message = 'Error',
        int $code = Response::HTTP_BAD_REQUEST,
        $errors = null,
        array $meta = []
    ): JsonResponse {
        $response = [
            'success' => false,
            'message' => $message,
            'timestamp' => now()->toISOString(),
        ];

        if ($errors !== null) {
            $response['errors'] = $errors;
        }

        if (!empty($meta)) {
            $response['meta'] = $meta;
        }

        return response()->json($response, $code);
    }

    /**
     * Validation error response
     */
    protected function validationErrorResponse(
        $errors,
        string $message = 'Validation failed'
    ): JsonResponse {
        return $this->errorResponse($message, Response::HTTP_UNPROCESSABLE_ENTITY, $errors);
    }

    /**
     * Not found response
     */
    protected function notFoundResponse(
        string $message = 'Resource not found'
    ): JsonResponse {
        return $this->errorResponse($message, Response::HTTP_NOT_FOUND);
    }

    /**
     * Unauthorized response
     */
    protected function unauthorizedResponse(
        string $message = 'Unauthorized access'
    ): JsonResponse {
        return $this->errorResponse($message, Response::HTTP_UNAUTHORIZED);
    }

    /**
     * Forbidden response
     */
    protected function forbiddenResponse(
        string $message = 'Access forbidden'
    ): JsonResponse {
        return $this->errorResponse($message, Response::HTTP_FORBIDDEN);
    }

    /**
     * Server error response
     */
    protected function serverErrorResponse(
        string $message = 'Internal server error',
        $errors = null
    ): JsonResponse {
        return $this->errorResponse($message, Response::HTTP_INTERNAL_SERVER_ERROR, $errors);
    }

    /**
     * Created response
     */
    protected function createdResponse(
        $data = null,
        string $message = 'Resource created successfully'
    ): JsonResponse {
        return $this->successResponse($data, $message, Response::HTTP_CREATED);
    }

    /**
     * Updated response
     */
    protected function updatedResponse(
        $data = null,
        string $message = 'Resource updated successfully'
    ): JsonResponse {
        return $this->successResponse($data, $message, Response::HTTP_OK);
    }

    /**
     * Deleted response
     */
    protected function deletedResponse(
        string $message = 'Resource deleted successfully'
    ): JsonResponse {
        return $this->successResponse(null, $message, Response::HTTP_OK);
    }

    /**
     * Paginated response
     */
    protected function paginatedResponse(
        $data,
        string $message = 'Data retrieved successfully',
        array $meta = []
    ): JsonResponse {
        $response = [
            'success' => true,
            'message' => $message,
            'data' => $data->items(),
            'pagination' => [
                'current_page' => $data->currentPage(),
                'last_page' => $data->lastPage(),
                'per_page' => $data->perPage(),
                'total' => $data->total(),
                'from' => $data->firstItem(),
                'to' => $data->lastItem(),
                'has_more_pages' => $data->hasMorePages(),
            ],
            'timestamp' => now()->toISOString(),
        ];

        if (!empty($meta)) {
            $response['meta'] = $meta;
        }

        return response()->json($response, Response::HTTP_OK);
    }

    /**
     * Collection response
     */
    protected function collectionResponse(
        $data,
        string $message = 'Data retrieved successfully',
        array $meta = []
    ): JsonResponse {
        $response = [
            'success' => true,
            'message' => $message,
            'data' => $data,
            'count' => is_countable($data) ? count($data) : 0,
            'timestamp' => now()->toISOString(),
        ];

        if (!empty($meta)) {
            $response['meta'] = $meta;
        }

        return response()->json($response, Response::HTTP_OK);
    }
}
