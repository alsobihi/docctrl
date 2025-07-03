<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Http\JsonResponse;

class ApiResponse
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next): Response
    {
        $response = $next($request);

        // Only modify JSON responses
        if ($response instanceof JsonResponse) {
            $data = $response->getData(true);

            // If it's already in our format, don't modify
            if (isset($data['success'])) {
                return $response;
            }

            // Handle validation errors
            if ($response->status() === 422 && isset($data['errors'])) {
                $response->setData([
                    'success' => false,
                    'message' => 'Validation failed',
                    'errors' => $data['errors']
                ]);
                return $response;
            }

            // Handle other error responses
            if ($response->status() >= 400) {
                $response->setData([
                    'success' => false,
                    'message' => $data['message'] ?? 'An error occurred',
                    'errors' => $data['errors'] ?? null
                ]);
                return $response;
            }
        }

        return $response;
    }
}