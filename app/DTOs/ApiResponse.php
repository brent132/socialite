<?php

namespace App\DTOs;

class ApiResponse
{
    /**
     * Create a success response
     * 
     * @param mixed $data
     * @param string $message
     * @param int $statusCode
     * @return array
     */
    public static function success($data = null, $message = 'Operation successful', $statusCode = 200)
    {
        return [
            'success' => true,
            'message' => $message,
            'data' => $data,
            'status_code' => $statusCode
        ];
    }

    /**
     * Create an error response
     * 
     * @param string $message
     * @param int $statusCode
     * @param mixed $errors
     * @return array
     */
    public static function error($message = 'Operation failed', $statusCode = 400, $errors = null)
    {
        return [
            'success' => false,
            'message' => $message,
            'errors' => $errors,
            'status_code' => $statusCode
        ];
    }

    /**
     * Create a paginated response
     * 
     * @param \Illuminate\Pagination\LengthAwarePaginator $paginator
     * @param string $message
     * @return array
     */
    public static function paginated($paginator, $message = 'Data retrieved successfully')
    {
        return [
            'success' => true,
            'message' => $message,
            'data' => $paginator->items(),
            'meta' => [
                'current_page' => $paginator->currentPage(),
                'last_page' => $paginator->lastPage(),
                'per_page' => $paginator->perPage(),
                'total' => $paginator->total()
            ],
            'status_code' => 200
        ];
    }
} 