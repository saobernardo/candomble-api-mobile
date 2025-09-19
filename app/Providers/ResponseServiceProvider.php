<?php

namespace App\Providers;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Collection;
use Illuminate\Support\ServiceProvider;

/**
 * Class ResponseServiceProvider
 *
 * Registers custom JSON response macros for standardized API responses.
 */
class ResponseServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap any application services.
     *
     * Defines the `v1` macro on JsonResponse, which provides a consistent
     * API response structure including code, message, userMessage, and data.
     */
    public function boot(): void
    {
        JsonResponse::macro('v1', function (
            string $code,
            string $message,
            ?string $userMessage,
            JsonResource|Collection|array|null $data,
            int $status = 200
        ) {
            return response()->json([
                'code' => $code,
                'message' => $message,
                'userMessage' => $userMessage,
                'data' => $data,
            ], $status);
        });
    }
}
